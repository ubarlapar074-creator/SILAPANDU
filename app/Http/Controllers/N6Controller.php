<?php

namespace App\Http\Controllers;

use App\Models\N6Document;
use App\Models\Province;
use App\Models\Registration;
use App\Models\RegistrationDocument;
use App\Services\MarriageRequirementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class N6Controller extends Controller
{
    public function __construct(
        protected MarriageRequirementService $requirementService
    ) {
    }

    public function manualIndex()
    {
        return view('n6.manual-index', [
            'documents' => N6Document::query()
                ->latest()
                ->paginate(10),
        ]);
    }

    public function manualCreate()
    {
        return view('n6.manual-create');
    }

    /**
     * Menampilkan form buat N6
     */
    public function create(Registration $registration)
    {
        if (! $this->requirementService->evaluate($registration)['n6']['required']) {
            abort(404, 'Dokumen N6 tidak diperlukan untuk pendaftaran ini.');
        }

        $registration->load([
            'catins' => function ($query) {
                $query->where('role', 'WANITA');
            },
            'catins.parents' => function ($query) {
                $query->where('role', 'AYAH');
            },
            'guardian',
        ]);

        $wanita = $registration->catins->firstWhere('role', 'WANITA');
        $fatherWanita = $wanita?->parents->firstWhere('role', 'AYAH');
        $guardian = $registration->guardian;

        $provinces = Province::orderBy('name')->get();

        // Cek apakah N6 sudah ada
        $existingN6 = N6Document::where('registration_id', $registration->id)->first();

        return view('n6.create', [
            'registration' => $registration,
            'wanita' => $wanita,
            'fatherWanita' => $fatherWanita,
            'guardian' => $guardian,
            'provinces' => $provinces,
            'existingN6' => $existingN6,
        ]);
    }

    /**
     * Menyimpan N6
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'registration_id' => ['nullable', 'exists:registrations,id'],
            
            // Bagian A - Data Almarhum
            'deceased_name' => ['required', 'string', 'max:255'],
            'deceased_bin_binti' => ['required', 'string', 'max:255'],
            'deceased_nik' => ['required', 'digits:16'],
            'deceased_birth_place' => ['required', 'string', 'max:255'],
            'deceased_birth_date' => ['required', 'date'],
            'deceased_nationality' => ['nullable', 'string', 'max:255'],
            'deceased_religion' => ['nullable', 'string', 'max:255'],
            'deceased_occupation' => ['nullable', 'string', 'max:255'],
            'deceased_address' => ['required', 'string'],
            
            // Informasi Kematian
            'death_date' => ['required', 'date'],
            'death_place' => ['nullable', 'string', 'max:255'],
            
            // Informasi Surat
            'document_number' => ['required', 'string', 'max:255', 'unique:n6_documents,document_number'],
            'issue_date' => ['required', 'date'],
            'signed_by' => ['required', 'string', 'max:255'],
            'signed_by_position' => ['required', 'string', 'max:255'],
        ]);

        $registration = !empty($validated['registration_id'])
            ? Registration::findOrFail($validated['registration_id'])
            : null;

        if ($registration && ! $this->requirementService->evaluate($registration)['n6']['required']) {
            throw ValidationException::withMessages([
                'registration_id' => 'Dokumen N6 tidak diperlukan untuk pendaftaran ini.',
            ]);
        }

        // Cek apakah N6 sudah ada untuk registration ini
        if ($registration && N6Document::where('registration_id', $registration->id)->exists()) {
            throw ValidationException::withMessages([
                'registration_id' => 'N6 sudah dibuat untuk pendaftaran ini.',
            ]);
        }

        $n6 = DB::transaction(function () use ($validated, $registration) {
            $n6 = new N6Document();

            $n6->registration_id = $registration?->id;
            
            // Bagian A
            $n6->deceased_name = $validated['deceased_name'];
            $n6->deceased_bin_binti = $validated['deceased_bin_binti'];
            $n6->deceased_nik = $validated['deceased_nik'];
            $n6->deceased_birth_place = $validated['deceased_birth_place'];
            $n6->deceased_birth_date = $validated['deceased_birth_date'];
            $n6->deceased_nationality = $validated['deceased_nationality'] ?? null;
            $n6->deceased_religion = $validated['deceased_religion'] ?? null;
            $n6->deceased_occupation = $validated['deceased_occupation'] ?? null;
            $n6->deceased_address = $validated['deceased_address'];
            
            // Informasi Kematian
            $n6->death_date = $validated['death_date'];
            $n6->death_place = $validated['death_place'] ?? null;
            
            // Informasi Surat
            $n6->document_number = $validated['document_number'];
            $n6->issue_date = $validated['issue_date'];
            $n6->signed_by = $validated['signed_by'];
            $n6->signed_by_position = $validated['signed_by_position'];
            
            $n6->status = 'PUBLISHED';
            $n6->created_by = auth()->id() ?? null;

            $n6->save();

            if ($registration) {
                RegistrationDocument::updateOrCreate(
                    [
                        'registration_id' => $registration->id,
                        'document_type' => 'N6',
                    ],
                    [
                        'document_number' => $n6->document_number,
                        'issue_date' => $n6->issue_date,
                        'signed_by' => $n6->signed_by,
                        'signed_by_position' => $n6->signed_by_position,
                        'status' => 'PUBLISHED',
                        'created_by' => auth()->id() ?? null,
                    ]
                );

                $registration->registrationDocumentChecklist()->updateOrCreate(
                    ['registration_id' => $registration->id],
                    ['doc_n6' => true]
                );
            }

            return $n6;
        });

        return redirect()
            ->route('n6.show', $n6)
            ->with('success', 'N6 berhasil dibuat: ' . $n6->document_number);
    }

    /**
     * Menampilkan detail N6
     */
    public function show(N6Document $n6)
    {
        $n6->load(['registration', 'createdBy', 'registration.catins']);
        return view('n6.show', compact('n6'));
    }

    /**
     * Menampilkan form edit N6
     */
    public function edit(N6Document $n6)
    {
        $n6->load('registration');
        return view('n6.edit', compact('n6'));
    }

    public function print(N6Document $n6)
    {
        $n6->load('registration');

        return view('registrations.print.n6', [
            'n6' => $n6,
            'catinWanita' => null,
        ]);
    }

    /**
     * Update N6
     */
    public function update(Request $request, N6Document $n6)
    {
        $validated = $request->validate([
            // Bagian A - Data Almarhum
            'deceased_name' => ['required', 'string', 'max:255'],
            'deceased_bin_binti' => ['required', 'string', 'max:255'],
            'deceased_nik' => ['required', 'digits:16'],
            'deceased_birth_place' => ['required', 'string', 'max:255'],
            'deceased_birth_date' => ['required', 'date'],
            'deceased_nationality' => ['nullable', 'string', 'max:255'],
            'deceased_religion' => ['nullable', 'string', 'max:255'],
            'deceased_occupation' => ['nullable', 'string', 'max:255'],
            'deceased_address' => ['required', 'string'],
            
            // Informasi Kematian
            'death_date' => ['required', 'date'],
            'death_place' => ['nullable', 'string', 'max:255'],
            
            // Informasi Surat
            'document_number' => ['required', 'string', 'max:255', 'unique:n6_documents,document_number,' . $n6->id],
            'issue_date' => ['required', 'date'],
            'signed_by' => ['required', 'string', 'max:255'],
            'signed_by_position' => ['required', 'string', 'max:255'],
            'status' => ['in:DRAFT,PUBLISHED,ARCHIVED'],
        ]);

        DB::transaction(function () use ($n6, $validated) {
            $n6->update($validated);

            if ($n6->registration_id) {
                RegistrationDocument::updateOrCreate(
                    ['registration_id' => $n6->registration_id, 'document_type' => 'N6'],
                    [
                        'document_number' => $n6->document_number,
                        'issue_date' => $n6->issue_date,
                        'signed_by' => $n6->signed_by,
                        'signed_by_position' => $n6->signed_by_position,
                        'status' => $n6->status,
                        'created_by' => $n6->created_by,
                    ]
                );
                $n6->registration?->registrationDocumentChecklist()->updateOrCreate(
                    ['registration_id' => $n6->registration_id],
                    ['doc_n6' => $n6->status !== 'ARCHIVED']
                );
            }
        });

        return redirect()
            ->route('n6.show', $n6)
            ->with('success', 'N6 berhasil diperbarui.');
    }

    /**
     * Hapus N6
     */
    public function destroy(N6Document $n6)
    {
        $registrationId = $n6->registration_id;
        DB::transaction(function () use ($n6, $registrationId) {
            if ($registrationId) {
                RegistrationDocument::where('registration_id', $registrationId)
                    ->where('document_type', 'N6')
                    ->delete();

                $n6->registration->registrationDocumentChecklist()
                    ->update(['doc_n6' => false]);
            }

            $n6->delete();
        });

        return redirect()
            ->route($registrationId ? 'registrations.detail' : 'n6.manual.index', $registrationId ? [$registrationId] : [])
            ->with('success', 'N6 berhasil dihapus.');
    }
}