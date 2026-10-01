<?php

namespace App\Http\Controllers;

use App\Actions\Registration\UpsertRegistrationAction;
use App\Http\Requests\RegistrationRules;
use App\Models\Province;
use App\Models\Registration;
use App\Models\Catin;
use App\Models\CatinParent;
use App\Models\Guardian;
use App\Models\Witness;
use App\Models\DocumentChecklist;
use App\Models\RegistrationDocumentChecklist;
use App\Services\MarriageRequirementService;
use App\Services\RegistrationAutosaveService;
use App\Support\AddressHelper;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class RegistrationController extends Controller
{
    public function create()
    {
        $provinces = Province::orderBy('name')->get();

        return view('registrations.create', [
            'provinces' => $provinces,
            'registration' => null,
            'pria' => null,
            'wanita' => null,
            'guardian' => null,
        ]);
    }

    public function store(
        Request $request,
        MarriageRequirementService $requirementService
    ) {
        AddressHelper::sanitizeNikInputs($request);

        $validated = $request->validate(RegistrationRules::store(false)); UpsertRegistrationAction::validateGuardian($validated); 
        /*
        |--------------------------------------------------------------------------
        | VALIDASI WALI
        |--------------------------------------------------------------------------
        */

        if ($validated['guardian_relationship'] === 'AYAH_KANDUNG') {

            if ($validated['wanita_father_status'] !== 'HIDUP') {
                throw ValidationException::withMessages([
                    'guardian_relationship' =>
                        'Ayah kandung yang berstatus meninggal tidak dapat dipilih sebagai wali.',
                ]);
            }

        } elseif ($validated['guardian_relationship'] === 'WALI_HAKIM') {

            // Wali Hakim TIDAK perlu mengisi data identitas
            // Data akan diisi oleh KUA setempat

        } else {

            foreach ([
                'guardian_name' => 'Nama lengkap wali',
                'guardian_nik' => 'NIK wali (16 digit)',
                'guardian_bin_binti' => 'Bin/Binti wali',
                'guardian_birth_place' => 'Tempat lahir wali',
                'guardian_birth_date' => 'Tanggal lahir wali',
                'guardian_marital_status' => 'Status perkawinan wali',
                'guardian_address' => 'Alamat wali',
            ] as $field => $label) {

                if (empty($validated[$field])) {
                    throw ValidationException::withMessages([
                        $field => $label . ' wajib diisi untuk wali keluarga (non Hakim).',
                    ]);
                }
            }
        }

        if ($validated['pria_nik'] === $validated['wanita_nik']) {
            throw ValidationException::withMessages([
                'wanita_nik' => 'NIK Catin Pria dan Wanita tidak boleh sama.',
            ]);
        }

        // Gabung RT/RW ke alamat utama (helper terpusat — lihat AddressHelper)
        AddressHelper::normalizeValidatedAddresses($validated);

        $this->validateRegionHierarchy($validated);

        $registration = null;
        $attempts = 0;
        do {
            try {
                // Transaksi pendaftaran penuh — semua entitas dibuat atomik
                $registration = DB::transaction(function () use ($validated) {
                    $registration = new Registration();
                    $registration->registration_number = $this->generateRegistrationNumber();
                    $registration->registration_path = $validated['registration_path'];
                    $registration->status = 'DRAFT';
                    $registration->marriage_date = $validated['marriage_date'];
                    $registration->marriage_time = $validated['marriage_time'];
                    $registration->marriage_dowry = $validated['marriage_dowry'] ?? null;
                    $this->assignDowry($registration, $validated);
                    $registration->marriage_location_address = $validated['marriage_location_address'];
                    $registration->save();

            $catinPria = $this->resolveCatinForRegistration($registration, 'PRIA', [
                'nik' => $validated['pria_nik'],
                'name' => $validated['pria_name'],
                'bin_binti' => $validated['pria_bin_binti'] ?? null,
                'birth_place' => $validated['pria_birth_place'],
                'birth_date' => $validated['pria_birth_date'],
                'nationality' => $validated['pria_nationality'] ?? null,
                'religion' => $validated['pria_religion'] ?? null,
                'marital_status' => $validated['pria_marital_status'],
                'no_hp' => $validated['pria_no_hp'] ?? null,
                'email' => $validated['pria_email'] ?? null,
                'occupation' => $validated['pria_occupation'] ?? null,
                'education' => $validated['pria_education'] ?? null,
                'address' => $validated['pria_address'],
                'province_id' => $validated['pria_province_id'],
                'regency_id' => $validated['pria_regency_id'],
                'district_id' => $validated['pria_district_id'],
                'village_id' => $validated['pria_village_id'],
            ]);

            $catinWanita = $this->resolveCatinForRegistration($registration, 'WANITA', [
                'nik' => $validated['wanita_nik'],
                'name' => $validated['wanita_name'],
                'bin_binti' => $validated['wanita_bin_binti'] ?? null,
                'birth_place' => $validated['wanita_birth_place'],
                'birth_date' => $validated['wanita_birth_date'],
                'nationality' => $validated['wanita_nationality'] ?? null,
                'religion' => $validated['wanita_religion'] ?? null,
                'marital_status' => $validated['wanita_marital_status'],
                'no_hp' => $validated['wanita_no_hp'] ?? null,
                'email' => $validated['wanita_email'] ?? null,
                'occupation' => $validated['wanita_occupation'] ?? null,
                'education' => $validated['wanita_education'] ?? null,
                'address' => $validated['wanita_address'],
                'province_id' => $validated['wanita_province_id'],
                'regency_id' => $validated['wanita_regency_id'],
                'district_id' => $validated['wanita_district_id'],
                'village_id' => $validated['wanita_village_id'],
            ]);

            $this->upsertParents($catinPria, 'pria', $validated);
            $fatherWanita = $this->upsertParents($catinWanita, 'wanita', $validated, true);

            /*
            |--------------------------------------------------------------------------
            | 6. GUARDIAN
            |--------------------------------------------------------------------------
            */

            $guardian = new Guardian();

            $guardian->registration_id =
                $registration->id;

            if ($validated['guardian_relationship'] === 'AYAH_KANDUNG') {

                /*
                |--------------------------------------------------------------------------
                | SINGLE SOURCE OF TRUTH
                |--------------------------------------------------------------------------
                |
                | Wali Ayah Kandung TIDAK menyimpan identitas ulang.
                | Identitas tetap berada di parents.
                |--------------------------------------------------------------------------
                */

                $guardian->guardian_source = 'PARENT';
                $guardian->parent_id = $fatherWanita->id;
                $guardian->relationship = 'AYAH_KANDUNG';
                $guardian->sebab_wali = trim((string) ($validated['guardian_sebab_wali'] ?? '')) !== '' ? trim((string) $validated['guardian_sebab_wali']) : \App\Support\WaliHelper::defaultSebab('AYAH_KANDUNG', $fatherWanita?->status ?? null);

                $guardian->name = null;
                $guardian->nik = null;
                $guardian->bin_binti = null;
                $guardian->birth_place = null;
                $guardian->birth_date = null;
                $guardian->occupation = null;
                $guardian->address = null;
                $guardian->province_id = null;
                $guardian->regency_id = null;
                $guardian->district_id = null;
                $guardian->village_id = null;

            } elseif ($validated['guardian_relationship'] === 'WALI_HAKIM') {

                /*
                |--------------------------------------------------------------------------
                | WALI HAKIM
                |--------------------------------------------------------------------------
                |
                | Wali Hakim tidak perlu mengisi data identitas.
                | Data akan diisi oleh KUA setempat.
                |--------------------------------------------------------------------------
                */

                $guardian->guardian_source = 'OTHER';
                $guardian->parent_id = null;
                $guardian->relationship = 'WALI_HAKIM';
                $guardian->sebab_wali = trim((string) ($validated['guardian_sebab_wali'] ?? '')) !== '' ? trim((string) $validated['guardian_sebab_wali']) : \App\Support\WaliHelper::defaultSebab('WALI_HAKIM', $fatherWanita?->status ?? null);

                // Data identitas diisi dengan nilai default
                $guardian->name = 'Wali Hakim (KUA)';
                $guardian->nik = null;
                $guardian->bin_binti = null;
                $guardian->birth_place = null;
                $guardian->birth_date = null;
                $guardian->occupation = null;
                $guardian->address = 'Kantor KUA Setempat';
                $guardian->province_id = null;
                $guardian->regency_id = null;
                $guardian->district_id = null;
                $guardian->village_id = null;

            } else {

                $guardian->guardian_source = 'OTHER';
                $guardian->parent_id = null;
                $guardian->relationship =
                    $validated['guardian_relationship'];
                $guardian->sebab_wali = trim((string) ($validated['guardian_sebab_wali'] ?? '')) !== '' ? trim((string) $validated['guardian_sebab_wali']) : \App\Support\WaliHelper::defaultSebab($validated['guardian_relationship'], $fatherWanita?->status ?? null);
                $guardian->marital_status = $validated['guardian_marital_status'] ?? null;

                $guardian->name =
                    $validated['guardian_name'];

                $guardian->nik =
                    $validated['guardian_nik'];

                $guardian->bin_binti =
                    $validated['guardian_bin_binti'];

                $guardian->birth_place =
                    $validated['guardian_birth_place'] ?? null;

                $guardian->birth_date =
                    $validated['guardian_birth_date'] ?? null;

                $guardian->occupation =
                    $validated['guardian_occupation'] ?? null;

                $guardian->address =
                    $validated['guardian_address'];

                $guardian->province_id =
                    $validated['guardian_province_id'] ?? null;

                $guardian->regency_id =
                    $validated['guardian_regency_id'] ?? null;

                $guardian->district_id =
                    $validated['guardian_district_id'] ?? null;

                $guardian->village_id =
                    $validated['guardian_village_id'] ?? null;
            }

            $guardian->save();

            /*
            |--------------------------------------------------------------------------
            | 7. WITNESSES
            |--------------------------------------------------------------------------
            */

            foreach (($validated['witnesses'] ?? []) as $witnessData) {
                if (empty(array_filter($witnessData, fn($v) => $v !== null && $v !== ''))) continue;
                if (trim((string) ($witnessData['name'] ?? '')) === '') continue;

                $witness = new Witness();

                $witness->registration_id =
                    $registration->id;

                $witness->name =
                    $witnessData['name'] ?? null;

                $witness->gender =
                    $witnessData['gender'] ?? null;

                $witness->bin_binti =
                    $witnessData['bin_binti'] ?? null;

                $witness->nik =
                    $witnessData['nik'] ?? null;

                $witness->birth_place =
                    $witnessData['birth_place'] ?? null;

                $witness->birth_date =
                    $witnessData['birth_date'] ?? null;

                $witness->nationality =
                    $witnessData['nationality'] ?? null;

                $witness->religion =
                    $witnessData['religion'] ?? null;

                $witness->marital_status =
                    $witnessData['marital_status'] ?? null;

                $witness->occupation =
                    $witnessData['occupation'] ?? null;

                $witness->education =
                    $witnessData['education'] ?? null;

                $witness->phone =
                    $witnessData['phone'] ?? null;

                $witness->email =
                    $witnessData['email'] ?? null;

                $witness->address =
                    $witnessData['address'] ?? null;

                $witness->province_id =
                    $witnessData['province_id'] ?? null;

                $witness->regency_id =
                    $witnessData['regency_id'] ?? null;

                $witness->district_id =
                    $witnessData['district_id'] ?? null;

                $witness->village_id =
                    $witnessData['village_id'] ?? null;

                $witness->save();
            }

            /*
            |--------------------------------------------------------------------------
            | 8. CHECKLIST CATIN PRIA
            |--------------------------------------------------------------------------
            */

            $priaChecklist =
                $validated['catin_pria_checklist'] ?? [];

            $wanitaChecklist =
                $validated['catin_wanita_checklist'] ?? [];

            $this->upsertDocumentChecklist($catinPria, $priaChecklist);
            $this->upsertDocumentChecklist($catinWanita, $wanitaChecklist);

            /*
            |--------------------------------------------------------------------------
            | 10. REGISTRATION CHECKLIST
            |--------------------------------------------------------------------------
            */

            $registrationChecklist =
                $validated['registration_checklist'] ?? [];

            RegistrationDocumentChecklist::create([
                'registration_id' =>
                    $registration->id,

                'doc_n1_n4' =>
                    (bool) ($registrationChecklist['doc_n1_n4'] ?? false),

                /*
                |--------------------------------------------------------------------------
                | Catatan:
                | Nilai checkbox N6 hanya status dokumen yang sudah diterima.
                | BUKAN penentu requirement N6.
                |--------------------------------------------------------------------------
                */

                'doc_n6' =>
                    (bool) ($registrationChecklist['doc_n6'] ?? false),
            ]);

            return $registration;
                });
                break;
            } catch (UniqueConstraintViolationException|QueryException $e) {
                $msg = $e->getMessage();
                // NIK duplikat: beri pesan ramah, bukan 500
                if (str_contains($msg, 'catins') && (str_contains($msg, 'nik') || str_contains($msg, 'catins_nik_unique'))) {
                    Log::warning('Store failed: duplicate NIK', ['nik' => $validated['pria_nik'] ?? null, 'error' => $msg]);
                    throw ValidationException::withMessages(['pria_nik' => 'NIK sudah dipakai pada pendaftaran lain. Silakan periksa kembali.']);
                }
                if ($attempts++ >= 2 || !str_contains($msg, 'registration_number')) {
                    Log::error('Store failed: unexpected DB error', ['attempt' => $attempts, 'error' => $msg]);
                    throw $e;
                }
            }
        } while ($attempts <= 2);

        /*
        |--------------------------------------------------------------------------
        | EVALUASI REQUIREMENT SETELAH DATA MASUK DATABASE
        |--------------------------------------------------------------------------
        */

        $requirements = $requirementService->evaluate($registration);

        /*
        |--------------------------------------------------------------------------
        | SIMPAN REQUIREMENT KE SESSION / REDIRECT
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route('registrations.success', $registration)
            ->with('success', 'Pendaftaran kedua pengantin berhasil disimpan.')
            ->with('requirements', $requirements);
    }

    /*
    |--------------------------------------------------------------------------
    | SUCCESS
    |--------------------------------------------------------------------------
    */

    public function success(
        Registration $registration,
        MarriageRequirementService $requirementService
    ) {
        $registration->load([
            'catins.parents',
            'guardian.parent',
            'witnesses',
        ]);

        $pria = $registration->catins
            ->firstWhere('role', 'PRIA');

        $wanita = $registration->catins
            ->firstWhere('role', 'WANITA');

        // Evaluasi ulang berdasarkan data SSOT dari database
        $requirements = $requirementService->evaluate($registration);

        return view('registrations.success', [
            'registration' => $registration,
            'pria' => $pria,
            'wanita' => $wanita,
            'requirements' => $requirements,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE CATIN
    |--------------------------------------------------------------------------
    */

    private function resolveCatinForRegistration(Registration $registration, string $role, array $data): Catin
    {
        $existing = Catin::where('nik', $data['nik'])->lockForUpdate()->first();

        if ($existing) {
            $existing->fill([
                'name' => $data['name'],
                'bin_binti' => $data['bin_binti'] ?? $existing->bin_binti,
                'birth_place' => $data['birth_place'],
                'birth_date' => $data['birth_date'],
                'nationality' => $data['nationality'] ?? $existing->nationality,
                'religion' => $data['religion'] ?? $existing->religion,
                'marital_status' => $data['marital_status'],
                'no_hp' => $data['no_hp'] ?? $existing->no_hp,
                'email' => $data['email'] ?? $existing->email,
                'occupation' => $data['occupation'] ?? $existing->occupation,
                'education' => $data['education'] ?? $existing->education,
                'address' => $data['address'],
                'province_id' => $data['province_id'],
                'regency_id' => $data['regency_id'],
                'district_id' => $data['district_id'],
                'village_id' => $data['village_id'],
            ]);
            $existing->save();
            $registration->catins()->syncWithoutDetaching([$existing->id]);
            return $existing;
        }

        $catin = new Catin();
        $catin->registration_id = $registration->id;
        $catin->role = $role;
        $catin->nik = $data['nik'];
        $catin->name = $data['name'];
        $catin->bin_binti = $data['bin_binti'] ?? null;
        $catin->birth_place = $data['birth_place'];
        $catin->birth_date = $data['birth_date'];
        $catin->nationality = $data['nationality'] ?? null;
        $catin->religion = $data['religion'] ?? null;
        $catin->marital_status = $data['marital_status'];
        $catin->no_hp = $data['no_hp'] ?? null;
        $catin->email = $data['email'] ?? null;
        $catin->occupation = $data['occupation'] ?? null;
        $catin->education = $data['education'] ?? null;
        $catin->address = $data['address'];
        $catin->province_id = $data['province_id'];
        $catin->regency_id = $data['regency_id'];
        $catin->district_id = $data['district_id'];
        $catin->village_id = $data['village_id'];
        $catin->save();
        $registration->catins()->syncWithoutDetaching([$catin->id]);
        return $catin;
    }

    private function upsertParents(Catin $catin, string $prefix, array $validated, bool $returnFather = false)
    {
        $father = $this->upsertParent($catin, 'AYAH', $prefix . '_father', $validated);
        $this->upsertParent($catin, 'IBU', $prefix . '_mother', $validated);
        if ($returnFather) return $father;
        return null;
    }

    private function upsertParent(Catin $catin, string $role, string $keyPrefix, array $validated): CatinParent
    {
        $status = $validated[$keyPrefix . '_status'] ?? null;
        $name = $validated[$keyPrefix . '_name'] ?? null;

        $parent = CatinParent::firstOrNew([
            'catin_id' => $catin->id,
            'role' => $role,
        ]);

        if ($status !== null) $parent->status = $status;
        if ($name !== null) $parent->name = $name;

        $currentStatus = $parent->status ?? $status;

        if ($currentStatus === 'MENINGGAL') {
            $parent->bin_binti = null;
            $parent->nik = null;
            $parent->birth_place = null;
            $parent->birth_date = null;
            $parent->address = null;
            $parent->education = null;
            $parent->occupation = null;
            $parent->marital_status = null;
            $parent->phone = null;
            $parent->email = null;
            $parent->province_id = null;
            $parent->regency_id = null;
            $parent->district_id = null;
            $parent->village_id = null;
        } else {
            foreach (['bin_binti', 'nik', 'birth_place', 'birth_date', 'address', 'education', 'occupation', 'marital_status', 'phone', 'email', 'province_id', 'regency_id', 'district_id', 'village_id'] as $f) {
                $k = $keyPrefix . '_' . $f;
                if (array_key_exists($k, $validated)) {
                    $parent->$f = $validated[$k];
                }
            }
            foreach (['rt', 'rw'] as $suf) {
                $k = $keyPrefix . '_address_' . $suf;
                if (array_key_exists($k, $validated) && $validated[$k] !== null && $validated[$k] !== '') {
                    $cur = $parent->address ?? '';
                    $tag = ' RT' . $validated[$k];
                    if ($suf === 'rw') $tag = '/RW' . $validated[$k];
                    if (strpos($cur, $tag) === false) $parent->address = trim($cur . $tag);
                }
            }
        }

        $parent->catin_id = $catin->id;
        $parent->role = $role;
        if (empty($parent->status)) $parent->status = 'HIDUP';
        if (empty($parent->name) && isset($validated[$keyPrefix . '_name'])) $parent->name = $validated[$keyPrefix . '_name'];
        $parent->save();
        return $parent;
    }

    private function upsertDocumentChecklist(Catin $catin, array $checklist): void
    {
        $existing = DocumentChecklist::where('catin_id', $catin->id)->first();
        $data = [
            'doc_ktp' => (bool) ($checklist['doc_ktp'] ?? false),
            'doc_kk' => (bool) ($checklist['doc_kk'] ?? false),
            'doc_birth' => (bool) ($checklist['doc_birth'] ?? false),
            'doc_immunization' => (bool) ($checklist['doc_immunization'] ?? false),
            'doc_photo' => (bool) ($checklist['doc_photo'] ?? false),
        ];
        if ($existing) {
            $existing->update($data);
        } else {
            DocumentChecklist::create(array_merge(['catin_id' => $catin->id], $data));
        }
    }

    private function createCatin(
        Registration $registration,
        string $role,
        array $data
    ): Catin {
        $catin = new Catin();

        $catin->registration_id = $registration->id;
        $catin->role = $role;
        $catin->nik = $data['nik'];
        $catin->name = $data['name'];
        $catin->bin_binti = $data['bin_binti'] ?? null;
        $catin->birth_place = $data['birth_place'];
        $catin->birth_date = $data['birth_date'];
        $catin->nationality = $data['nationality'] ?? null;
        $catin->religion = $data['religion'] ?? null;
        $catin->marital_status = $data['marital_status'];
        $catin->no_hp = $data['no_hp'] ?? null;
        $catin->email = $data['email'] ?? null;
        $catin->occupation = $data['occupation'] ?? null;
        $catin->education = $data['education'] ?? null;
        $catin->address = $data['address'];
        $catin->province_id = $data['province_id'];
        $catin->regency_id = $data['regency_id'];
        $catin->district_id = $data['district_id'];
        $catin->village_id = $data['village_id'];

        $catin->save();
        $registration->catins()->syncWithoutDetaching([$catin->id]);

        return $catin;
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE PARENT
    |--------------------------------------------------------------------------
    */

    private function createParent(
        Catin $catin,
        string $role,
        string $status,
        string $name,
        ?string $nik,
        ?string $birthPlace,
        ?string $birthDate,
        ?string $address,
        ?string $education,
        ?string $occupation
    ): CatinParent {

        $parent = new CatinParent();

        $parent->catin_id = $catin->id;
        $parent->role = $role;
        $parent->status = $status;
        $parent->name = $name;

        /*
        |--------------------------------------------------------------------------
        | SINGLE SOURCE OF TRUTH
        |--------------------------------------------------------------------------
        |
        | Jika meninggal, identitas tidak disimpan ulang.
        |--------------------------------------------------------------------------
        */

        if ($status === 'MENINGGAL') {

            $parent->nik = null;
            $parent->birth_place = null;
            $parent->birth_date = null;
            $parent->address = null;
            $parent->education = null;
            $parent->occupation = null;

        } else {

            $parent->nik = $nik;
            $parent->birth_place = $birthPlace;
            $parent->birth_date = $birthDate;
            $parent->address = $address;
            $parent->education = $education;
            $parent->occupation = $occupation;
        }

        $parent->save();

        return $parent;
    }

    /*
    |--------------------------------------------------------------------------
    | REGISTRATION NUMBER
    |--------------------------------------------------------------------------
    */

    private function generateRegistrationNumber(): string
    {
        do {

            $number =
                'REG-' .
                now()->format('Ymd') .
                '-' .
                strtoupper(Str::random(6));

        } while (
            Registration::where(
                'registration_number',
                $number
            )->exists()
        );

        return $number;
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT & UPDATE (CRUD)
    |--------------------------------------------------------------------------
    */

    public function edit(Registration $registration)
    {
        // Muat semua relasi yang diperlukan untuk mengisi form
        $registration->load([
            'catins.parents',
            'guardian',
            'witnesses',
            'registrationDocumentChecklist',
        ]);

        $provinces = Province::orderBy('name')->get();

        $pria = $registration->catins->firstWhere('role', 'PRIA');
        $wanita = $registration->catins->firstWhere('role', 'WANITA');

        if ($pria) {
            $pria->father = $pria->parents->firstWhere('role', 'AYAH');
            $pria->mother = $pria->parents->firstWhere('role', 'IBU');
        }

        if ($wanita) {
            $wanita->father = $wanita->parents->firstWhere('role', 'AYAH');
            $wanita->mother = $wanita->parents->firstWhere('role', 'IBU');
        }

        return view('registrations.create', [
            'registration' => $registration,
            'provinces' => $provinces,
            'pria' => $pria,
            'wanita' => $wanita,
            'guardian' => $registration->guardian,
        ]);
    }

    public function autosave(Request $request, Registration $registration, RegistrationAutosaveService $autosaveService)
    {
        AddressHelper::sanitizeNikInputs($request);

        $rules = [
            'registration_path' => ['sometimes', 'nullable', 'in:DALAM_DESA,ANDON'],
            'marriage_date' => ['sometimes', 'nullable', 'date'],
            'marriage_time' => ['sometimes', 'nullable', 'date_format:H:i'],
            'marriage_dowry' => ['sometimes', 'nullable', 'string', 'max:255'],
            'marriage_dowry_type' => ['sometimes', 'nullable', 'in:UANG_EMAS,UANG,EMAS,LAINNYA'],
            'marriage_dowry_money' => ['sometimes', 'nullable', 'string', 'max:100'],
            'marriage_dowry_gold' => ['sometimes', 'nullable', 'string', 'max:100'],
            'marriage_dowry_other' => ['sometimes', 'nullable', 'string', 'max:255'],
            'marriage_location_address' => ['sometimes', 'nullable', 'string', 'max:255'],
            'pria_nik' => ['sometimes', 'nullable', 'digits:16', 'different:wanita_nik'],
            'pria_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'pria_bin_binti' => ['sometimes', 'nullable', 'string', 'max:255'],
            'pria_birth_place' => ['sometimes', 'nullable', 'string', 'max:255'],
            'pria_birth_date' => ['sometimes', 'nullable', 'date'],
            'pria_nationality' => ['sometimes', 'nullable', 'string', 'max:255'],
            'pria_religion' => ['sometimes', 'nullable', 'string', 'max:255'],
            'pria_marital_status' => ['sometimes', 'nullable', 'string', 'max:50', 'in:BELUM KAWIN,BELUM_KAWIN,CERAI HIDUP,CERAI_HIDUP,CERAI MATI,CERAI_MATI'],
            'pria_no_hp' => ['sometimes', 'nullable', 'string', 'max:20'],
            'pria_email' => ['sometimes', 'nullable', 'string', 'max:255'],
            'pria_occupation' => ['sometimes', 'nullable', 'string', 'max:255'],
            'pria_education' => ['sometimes', 'nullable', 'string', 'max:255'],
            'pria_address' => ['sometimes', 'nullable', 'string'],
            'pria_address_rt' => ['sometimes', 'nullable', 'string', 'max:3'],
            'pria_address_rw' => ['sometimes', 'nullable', 'string', 'max:3'],
            'pria_province_id' => ['sometimes', 'nullable', 'exists:provinces,id'],
            'pria_regency_id' => ['sometimes', 'nullable', 'exists:regencies,id'],
            'pria_district_id' => ['sometimes', 'nullable', 'exists:districts,id'],
            'pria_village_id' => ['sometimes', 'nullable', 'exists:villages,id'],
            'wanita_nik' => ['sometimes', 'nullable', 'digits:16', 'different:pria_nik'],
            'wanita_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'wanita_bin_binti' => ['sometimes', 'nullable', 'string', 'max:255'],
            'wanita_birth_place' => ['sometimes', 'nullable', 'string', 'max:255'],
            'wanita_birth_date' => ['sometimes', 'nullable', 'date'],
            'wanita_nationality' => ['sometimes', 'nullable', 'string', 'max:255'],
            'wanita_religion' => ['sometimes', 'nullable', 'string', 'max:255'],
            'wanita_marital_status' => ['sometimes', 'nullable', 'string', 'max:50', 'in:BELUM KAWIN,BELUM_KAWIN,CERAI HIDUP,CERAI_HIDUP,CERAI MATI,CERAI_MATI'],
            'wanita_no_hp' => ['sometimes', 'nullable', 'string', 'max:20'],
            'wanita_email' => ['sometimes', 'nullable', 'string', 'max:255'],
            'wanita_occupation' => ['sometimes', 'nullable', 'string', 'max:255'],
            'wanita_education' => ['sometimes', 'nullable', 'string', 'max:255'],
            'wanita_address' => ['sometimes', 'nullable', 'string'],
            'wanita_address_rt' => ['sometimes', 'nullable', 'string', 'max:3'],
            'wanita_address_rw' => ['sometimes', 'nullable', 'string', 'max:3'],
            'wanita_province_id' => ['sometimes', 'nullable', 'exists:provinces,id'],
            'wanita_regency_id' => ['sometimes', 'nullable', 'exists:regencies,id'],
            'wanita_district_id' => ['sometimes', 'nullable', 'exists:districts,id'],
            'wanita_village_id' => ['sometimes', 'nullable', 'exists:villages,id'],
            'pria_father_status' => ['sometimes', 'nullable', 'in:HIDUP,MENINGGAL'],
            'pria_father_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'pria_father_bin_binti' => ['sometimes', 'nullable', 'string', 'max:255'],
            'pria_father_nik' => ['sometimes', 'nullable', 'digits:16'],
            'pria_father_birth_place' => ['sometimes', 'nullable', 'string', 'max:255'],
            'pria_father_birth_date' => ['sometimes', 'nullable', 'date'],
            'pria_father_address' => ['sometimes', 'nullable', 'string'],
            'pria_father_address_rt' => ['sometimes', 'nullable', 'string', 'max:3'],
            'pria_father_address_rw' => ['sometimes', 'nullable', 'string', 'max:3'],
            'pria_father_education' => ['sometimes', 'nullable', 'string', 'max:255'],
            'pria_father_occupation' => ['sometimes', 'nullable', 'string', 'max:255'],
            'pria_father_marital_status' => ['sometimes', 'nullable', 'string', 'max:50'],
            'pria_father_phone' => ['sometimes', 'nullable', 'string', 'max:20'],
            'pria_father_email' => ['sometimes', 'nullable', 'string', 'max:255'],
            'pria_father_province_id' => ['sometimes', 'nullable', 'exists:provinces,id'],
            'pria_father_regency_id' => ['sometimes', 'nullable', 'exists:regencies,id'],
            'pria_father_district_id' => ['sometimes', 'nullable', 'exists:districts,id'],
            'pria_father_village_id' => ['sometimes', 'nullable', 'exists:villages,id'],
            'pria_mother_status' => ['sometimes', 'nullable', 'in:HIDUP,MENINGGAL'],
            'pria_mother_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'pria_mother_bin_binti' => ['sometimes', 'nullable', 'string', 'max:255'],
            'pria_mother_nik' => ['sometimes', 'nullable', 'digits:16'],
            'pria_mother_birth_place' => ['sometimes', 'nullable', 'string', 'max:255'],
            'pria_mother_birth_date' => ['sometimes', 'nullable', 'date'],
            'pria_mother_address' => ['sometimes', 'nullable', 'string'],
            'pria_mother_address_rt' => ['sometimes', 'nullable', 'string', 'max:3'],
            'pria_mother_address_rw' => ['sometimes', 'nullable', 'string', 'max:3'],
            'pria_mother_education' => ['sometimes', 'nullable', 'string', 'max:255'],
            'pria_mother_occupation' => ['sometimes', 'nullable', 'string', 'max:255'],
            'pria_mother_marital_status' => ['sometimes', 'nullable', 'string', 'max:50'],
            'pria_mother_phone' => ['sometimes', 'nullable', 'string', 'max:20'],
            'pria_mother_email' => ['sometimes', 'nullable', 'string', 'max:255'],
            'pria_mother_province_id' => ['sometimes', 'nullable', 'exists:provinces,id'],
            'pria_mother_regency_id' => ['sometimes', 'nullable', 'exists:regencies,id'],
            'pria_mother_district_id' => ['sometimes', 'nullable', 'exists:districts,id'],
            'pria_mother_village_id' => ['sometimes', 'nullable', 'exists:villages,id'],
            'wanita_father_status' => ['sometimes', 'nullable', 'in:HIDUP,MENINGGAL'],
            'wanita_father_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'wanita_father_bin_binti' => ['sometimes', 'nullable', 'string', 'max:255'],
            'wanita_father_nik' => ['sometimes', 'nullable', 'digits:16'],
            'wanita_father_birth_place' => ['sometimes', 'nullable', 'string', 'max:255'],
            'wanita_father_birth_date' => ['sometimes', 'nullable', 'date'],
            'wanita_father_address' => ['sometimes', 'nullable', 'string'],
            'wanita_father_address_rt' => ['sometimes', 'nullable', 'string', 'max:3'],
            'wanita_father_address_rw' => ['sometimes', 'nullable', 'string', 'max:3'],
            'wanita_father_education' => ['sometimes', 'nullable', 'string', 'max:255'],
            'wanita_father_occupation' => ['sometimes', 'nullable', 'string', 'max:255'],
            'wanita_father_marital_status' => ['sometimes', 'nullable', 'string', 'max:50'],
            'wanita_father_phone' => ['sometimes', 'nullable', 'string', 'max:20'],
            'wanita_father_email' => ['sometimes', 'nullable', 'string', 'max:255'],
            'wanita_father_province_id' => ['sometimes', 'nullable', 'exists:provinces,id'],
            'wanita_father_regency_id' => ['sometimes', 'nullable', 'exists:regencies,id'],
            'wanita_father_district_id' => ['sometimes', 'nullable', 'exists:districts,id'],
            'wanita_father_village_id' => ['sometimes', 'nullable', 'exists:villages,id'],
            'wanita_mother_status' => ['sometimes', 'nullable', 'in:HIDUP,MENINGGAL'],
            'wanita_mother_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'wanita_mother_bin_binti' => ['sometimes', 'nullable', 'string', 'max:255'],
            'wanita_mother_nik' => ['sometimes', 'nullable', 'digits:16'],
            'wanita_mother_birth_place' => ['sometimes', 'nullable', 'string', 'max:255'],
            'wanita_mother_birth_date' => ['sometimes', 'nullable', 'date'],
            'wanita_mother_address' => ['sometimes', 'nullable', 'string'],
            'wanita_mother_address_rt' => ['sometimes', 'nullable', 'string', 'max:3'],
            'wanita_mother_address_rw' => ['sometimes', 'nullable', 'string', 'max:3'],
            'wanita_mother_education' => ['sometimes', 'nullable', 'string', 'max:255'],
            'wanita_mother_occupation' => ['sometimes', 'nullable', 'string', 'max:255'],
            'wanita_mother_marital_status' => ['sometimes', 'nullable', 'string', 'max:50'],
            'wanita_mother_phone' => ['sometimes', 'nullable', 'string', 'max:20'],
            'wanita_mother_email' => ['sometimes', 'nullable', 'string', 'max:255'],
            'wanita_mother_province_id' => ['sometimes', 'nullable', 'exists:provinces,id'],
            'wanita_mother_regency_id' => ['sometimes', 'nullable', 'exists:regencies,id'],
            'wanita_mother_district_id' => ['sometimes', 'nullable', 'exists:districts,id'],
            'wanita_mother_village_id' => ['sometimes', 'nullable', 'exists:villages,id'],
            'guardian_relationship' => ['sometimes', 'nullable', 'in:AYAH_KANDUNG,KAKEK,SAUDARA_KANDUNG_LAKI,PAMAN,WALI_HAKIM'],
            'guardian_sebab_wali' => ['sometimes', 'nullable', 'string', 'max:500'],
            'guardian_marital_status' => ['sometimes', 'nullable', 'string', 'max:50'],
            'guardian_name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'guardian_nik' => ['sometimes', 'nullable', 'digits:16'],
            'guardian_bin_binti' => ['sometimes', 'nullable', 'string', 'max:255'],
            'guardian_birth_place' => ['sometimes', 'nullable', 'string', 'max:255'],
            'guardian_birth_date' => ['sometimes', 'nullable', 'date'],
            'guardian_occupation' => ['sometimes', 'nullable', 'string', 'max:255'],
            'guardian_address' => ['sometimes', 'nullable', 'string'],
            'witnesses' => ['sometimes', 'nullable', 'array', 'max:3'],
            'witnesses.*.address_rt' => ['sometimes', 'nullable', 'string', 'max:3'],
            'witnesses.*.address_rw' => ['sometimes', 'nullable', 'string', 'max:3'],
            'witnesses.*.name' => ['sometimes', 'nullable', 'string', 'max:255'],
            'witnesses.*.gender' => ['sometimes', 'nullable', 'in:L,P'],
            'witnesses.*.bin_binti' => ['sometimes', 'nullable', 'string', 'max:255'],
            'witnesses.*.nik' => ['sometimes', 'nullable', 'digits:16'],
            'witnesses.*.birth_place' => ['sometimes', 'nullable', 'string', 'max:255'],
            'witnesses.*.birth_date' => ['sometimes', 'nullable', 'date'],
            'witnesses.*.nationality' => ['sometimes', 'nullable', 'string', 'max:255'],
            'witnesses.*.religion' => ['sometimes', 'nullable', 'string', 'max:255'],
            'witnesses.*.marital_status' => ['sometimes', 'nullable', 'string', 'max:50', 'in:BELUM KAWIN,BELUM_KAWIN,CERAI HIDUP,CERAI_HIDUP,CERAI MATI,CERAI_MATI'],
            'witnesses.*.education' => ['sometimes', 'nullable', 'string', 'max:255'],
            'witnesses.*.occupation' => ['sometimes', 'nullable', 'string', 'max:255'],
            'witnesses.*.phone' => ['sometimes', 'nullable', 'string', 'max:20'],
            'witnesses.*.email' => ['sometimes', 'nullable', 'email', 'max:255'],
            'witnesses.*.address' => ['sometimes', 'nullable', 'string'],
            'witnesses.*.province_id' => ['sometimes', 'nullable', 'exists:provinces,id'],
            'witnesses.*.regency_id' => ['sometimes', 'nullable', 'exists:regencies,id'],
            'witnesses.*.district_id' => ['sometimes', 'nullable', 'exists:districts,id'],
            'witnesses.*.village_id' => ['sometimes', 'nullable', 'exists:villages,id'],
        ];

        $validated = $request->validate($rules);

        $this->validateRegionHierarchy($validated);

        if (empty($validated) && empty($request->input('witnesses'))) {
            return response()->json(['message' => 'Tidak ada perubahan']);
        }

        // Delegasikan ke service terpisah — controller hanya orkestrasi (Clean Code: hindari long method)
        $autosaveService->autosave($registration, $validated, $request);
        return response()->json(['message' => 'Perubahan tersimpan otomatis']);
    }

    // Legacy autosave body kept for readability audit — now delegated to RegistrationAutosaveService above
    private function autosaveLegacyBody(Request $request, Registration $registration, array $validated): void
    {
        DB::transaction(function () use ($registration, $validated, $request) {
            $tmpPriaNik = $validated['pria_nik'] ?? $registration->catins->firstWhere('role', 'PRIA')?->nik;
            $tmpWanitaNik = $validated['wanita_nik'] ?? $registration->catins->firstWhere('role', 'WANITA')?->nik;
            if ($tmpPriaNik && $tmpWanitaNik && $tmpPriaNik === $tmpWanitaNik) {
                throw ValidationException::withMessages(['wanita_nik' => 'NIK Catin Pria dan Wanita tidak boleh sama.']);
            }
            $regFields = array_intersect_key($validated, array_flip(['registration_path', 'marriage_date', 'marriage_time', 'marriage_dowry', 'marriage_dowry_type', 'marriage_dowry_money', 'marriage_dowry_gold', 'marriage_dowry_other', 'marriage_location_address']));
            $hasDowryPayload = array_key_exists('marriage_dowry_type', $regFields) || array_key_exists('marriage_dowry_money', $regFields) || array_key_exists('marriage_dowry_gold', $regFields) || array_key_exists('marriage_dowry_other', $regFields);
            if ($hasDowryPayload) {
                $hasMoneyEtc = array_key_exists('marriage_dowry_money', $regFields) || array_key_exists('marriage_dowry_gold', $regFields) || array_key_exists('marriage_dowry_other', $regFields);
                if (!array_key_exists('marriage_dowry_type', $regFields) && $hasMoneyEtc && empty($registration->marriage_dowry_type)) {
                    unset($regFields['marriage_dowry_money'], $regFields['marriage_dowry_gold'], $regFields['marriage_dowry_other']);
                    $hasDowryPayload = false;
                } else {
                    if (!array_key_exists('marriage_dowry_type', $regFields)) {
                        $regFields['marriage_dowry_type'] = $registration->marriage_dowry_type;
                    }
                    $this->assignDowry($registration, $regFields);
                    unset($regFields['marriage_dowry_type'], $regFields['marriage_dowry_money'], $regFields['marriage_dowry_gold'], $regFields['marriage_dowry_other']);
                    $regFields['marriage_dowry'] = $registration->marriage_dowry;
                    $regFields['marriage_dowry_type'] = $registration->marriage_dowry_type;
                    $regFields['marriage_dowry_money'] = $registration->marriage_dowry_money;
                    $regFields['marriage_dowry_gold'] = $registration->marriage_dowry_gold;
                    $regFields['marriage_dowry_other'] = $registration->marriage_dowry_other;
                }
            }
            if (!empty($regFields)) {
                $registration->update($regFields);
            }

            $pria = $registration->catins->firstWhere('role', 'PRIA');
            $wanita = $registration->catins->firstWhere('role', 'WANITA');

            foreach ([
                ['key' => 'pria_nik', 'catin' => $pria, 'role' => 'PRIA'],
                ['key' => 'wanita_nik', 'catin' => $wanita, 'role' => 'WANITA'],
            ] as $item) {
                $key = $item['key'];
                $catin = $item['catin'];
                $role = $item['role'];
                if (!isset($validated[$key]) || !$catin || $validated[$key] === $catin->nik) continue;
                $newNik = $validated[$key];
                $existing = Catin::where('nik', $newNik)->lockForUpdate()->first();
                if ($existing) {
                    if ($existing->id !== $catin->id) {
                        if ($existing->role !== $role && ! $existing->registrations()->exists()) {
                            $existing->update(['role' => $role]);
                        } elseif ($existing->role !== $role) {
                            throw ValidationException::withMessages([
                                $key => 'NIK tersebut sudah digunakan dengan peran berbeda pada pendaftaran lain.',
                            ]);
                        }
                        $registration->catins()->detach($catin->id);
                        $registration->catins()->syncWithoutDetaching([$existing->id]);
                        if ($role === 'PRIA') { $pria = $existing; $pria->load('parents'); }
                        else { $wanita = $existing; $wanita->load('parents'); }
                    }
                    unset($validated[$key]);
                } else {
                    $dataForNew = [];
                    $prefix = $role === 'PRIA' ? 'pria' : 'wanita';
                    $mapKeys = ['name' => $prefix.'_name', 'bin_binti' => $prefix.'_bin_binti', 'birth_place' => $prefix.'_birth_place', 'birth_date' => $prefix.'_birth_date', 'nationality' => $prefix.'_nationality', 'religion' => $prefix.'_religion', 'marital_status' => $prefix.'_marital_status', 'no_hp' => $prefix.'_no_hp', 'email' => $prefix.'_email', 'occupation' => $prefix.'_occupation', 'education' => $prefix.'_education', 'address' => $prefix.'_address', 'province_id' => $prefix.'_province_id', 'regency_id' => $prefix.'_regency_id', 'district_id' => $prefix.'_district_id', 'village_id' => $prefix.'_village_id'];
                    $hasEnough = true;
                    foreach (['name','birth_place','birth_date','address','province_id','regency_id','district_id','village_id'] as $need) {
                        if (!array_key_exists($mapKeys[$need], $validated) && empty($catin->$need)) $hasEnough = false;
                    }
                    if ($hasEnough) {
                        try {
                            $newCatin = new Catin();
                            $newCatin->role = $role;
                            $newCatin->nik = $newNik;
                            $newCatin->name = $validated[$prefix.'_name'] ?? $catin->name;
                            $newCatin->bin_binti = $validated[$prefix.'_bin_binti'] ?? $catin->bin_binti;
                            $newCatin->birth_place = $validated[$prefix.'_birth_place'] ?? $catin->birth_place;
                            $newCatin->birth_date = $validated[$prefix.'_birth_date'] ?? $catin->birth_date;
                            $newCatin->nationality = $validated[$prefix.'_nationality'] ?? $catin->nationality;
                            $newCatin->religion = $validated[$prefix.'_religion'] ?? $catin->religion;
                            $newCatin->marital_status = $validated[$prefix.'_marital_status'] ?? $catin->marital_status;
                            $newCatin->no_hp = $validated[$prefix.'_no_hp'] ?? $catin->no_hp;
                            $newCatin->email = $validated[$prefix.'_email'] ?? $catin->email;
                            $newCatin->occupation = $validated[$prefix.'_occupation'] ?? $catin->occupation;
                            $newCatin->education = $validated[$prefix.'_education'] ?? $catin->education;
                            $newCatin->address = $validated[$prefix.'_address'] ?? $catin->address;
                            $newCatin->province_id = $validated[$prefix.'_province_id'] ?? $catin->province_id;
                            $newCatin->regency_id = $validated[$prefix.'_regency_id'] ?? $catin->regency_id;
                            $newCatin->district_id = $validated[$prefix.'_district_id'] ?? $catin->district_id;
                            $newCatin->village_id = $validated[$prefix.'_village_id'] ?? $catin->village_id;
                            $newCatin->registration_id = null;
                            $newCatin->save();
                            $registration->catins()->detach($catin->id);
                            $registration->catins()->syncWithoutDetaching([$newCatin->id]);
                            if ($role === 'PRIA') { $pria = $newCatin; $pria->load('parents'); }
                            else { $wanita = $newCatin; $wanita->load('parents'); }
                            foreach ($catin->parents as $pr) {
                                \App\Models\CatinParent::firstOrCreate(['catin_id' => $newCatin->id, 'role' => $pr->role], $pr->only(['status','name','nik','birth_place','birth_date','address','education','occupation','phone','email','bin_binti','marital_status','province_id','regency_id','district_id','village_id']));
                            }
                        } catch (\Illuminate\Database\UniqueConstraintViolationException|\Illuminate\Database\QueryException $e) {
                            if (str_contains($e->getMessage(), 'catins') || str_contains($e->getMessage(), 'nik')) {
                                throw ValidationException::withMessages([$key => 'NIK tersebut baru saja dipakai, silakan coba lagi.']);
                            }
                            throw $e;
                        }
                    }
                }
            }

            if ($pria) {
                $priaMap = ['nik' => 'pria_nik', 'name' => 'pria_name', 'bin_binti' => 'pria_bin_binti', 'birth_place' => 'pria_birth_place', 'birth_date' => 'pria_birth_date', 'nationality' => 'pria_nationality', 'religion' => 'pria_religion', 'marital_status' => 'pria_marital_status', 'no_hp' => 'pria_no_hp', 'email' => 'pria_email', 'occupation' => 'pria_occupation', 'education' => 'pria_education', 'address' => 'pria_address', 'province_id' => 'pria_province_id', 'regency_id' => 'pria_regency_id', 'district_id' => 'pria_district_id', 'village_id' => 'pria_village_id'];
                $priaRequired = ['nik','name','birth_place','birth_date','marital_status','address','province_id','regency_id','district_id','village_id'];
                $priaData = [];
                foreach ($priaMap as $col => $key) {
                    if (array_key_exists($key, $validated)) {
                        $val = $validated[$key];
                        if (in_array($col, $priaRequired, true) && ($val === null || $val === '')) continue;
                        $priaData[$col] = $val;
                    }
                }
                if (isset($priaData['nik']) && Catin::where('nik', $priaData['nik'])->where('id', '!=', $pria->id)->exists()) {
                    unset($priaData['nik']);
                }
                if (!empty($priaData)) $pria->update($priaData);
            }

            if ($wanita) {
                $wanitaMap = ['nik' => 'wanita_nik', 'name' => 'wanita_name', 'bin_binti' => 'wanita_bin_binti', 'birth_place' => 'wanita_birth_place', 'birth_date' => 'wanita_birth_date', 'nationality' => 'wanita_nationality', 'religion' => 'wanita_religion', 'marital_status' => 'wanita_marital_status', 'no_hp' => 'wanita_no_hp', 'email' => 'wanita_email', 'occupation' => 'wanita_occupation', 'education' => 'wanita_education', 'address' => 'wanita_address', 'province_id' => 'wanita_province_id', 'regency_id' => 'wanita_regency_id', 'district_id' => 'wanita_district_id', 'village_id' => 'wanita_village_id'];
                $wanitaRequired = ['nik','name','birth_place','birth_date','marital_status','address','province_id','regency_id','district_id','village_id'];
                $wanitaData = [];
                foreach ($wanitaMap as $col => $key) {
                    if (array_key_exists($key, $validated)) {
                        $val = $validated[$key];
                        if (in_array($col, $wanitaRequired, true) && ($val === null || $val === '')) continue;
                        $wanitaData[$col] = $val;
                    }
                }
                if (isset($wanitaData['nik']) && Catin::where('nik', $wanitaData['nik'])->where('id', '!=', $wanita->id)->exists()) {
                    unset($wanitaData['nik']);
                }
                if (!empty($wanitaData)) $wanita->update($wanitaData);
            }

            $parentConfigs = [
                ['catin' => $pria, 'role' => 'AYAH', 'prefix' => 'pria_father'],
                ['catin' => $pria, 'role' => 'IBU', 'prefix' => 'pria_mother'],
                ['catin' => $wanita, 'role' => 'AYAH', 'prefix' => 'wanita_father'],
                ['catin' => $wanita, 'role' => 'IBU', 'prefix' => 'wanita_mother'],
            ];
            foreach ($parentConfigs as $cfg) {
                if (!$cfg['catin']) continue;
                $prefix = $cfg['prefix'];
                $hasAny = false;
                foreach (['status', 'name', 'nik', 'birth_place', 'birth_date', 'address', 'education', 'occupation', 'province_id', 'regency_id', 'district_id', 'village_id'] as $f) {
                    if (array_key_exists($prefix . '_' . $f, $validated)) { $hasAny = true; break; }
                }
                if (!$hasAny) continue;
                $parent = $cfg['catin']->parents()->firstOrNew(['role' => $cfg['role']]);
                if (!$parent->exists && empty($validated[$prefix . '_name'] ?? $parent->name)) continue;
                $status = $validated[$prefix . '_status'] ?? $parent->status ?? 'HIDUP';
                if (array_key_exists($prefix . '_status', $validated) && $validated[$prefix . '_status'] !== null && $validated[$prefix . '_status'] !== '') $parent->status = $validated[$prefix . '_status'];
                if (array_key_exists($prefix . '_name', $validated) && $validated[$prefix . '_name'] !== null && $validated[$prefix . '_name'] !== '') $parent->name = $validated[$prefix . '_name'];
                if ($status === 'MENINGGAL') {
                    $parent->nik = null; $parent->birth_place = null; $parent->birth_date = null; $parent->address = null; $parent->education = null; $parent->occupation = null;
                    $parent->province_id = null; $parent->regency_id = null; $parent->district_id = null; $parent->village_id = null;
                } else {
                    foreach (['nik', 'birth_place', 'birth_date', 'address', 'education', 'occupation', 'province_id', 'regency_id', 'district_id', 'village_id'] as $f) {
                        $k = $prefix . '_' . $f;
                        if (array_key_exists($k, $validated) && $validated[$k] !== '' && $validated[$k] !== null) $parent->$f = $validated[$k];
                        elseif (array_key_exists($k, $validated) && $validated[$k] === null) $parent->$f = null;
                    }
                }
                if (empty($parent->status) || empty($parent->name)) continue;
                $parent->save();
                $cfg['catin']->load('parents');
            }

            $guardianKeys = ['guardian_relationship', 'guardian_sebab_wali', 'guardian_marital_status', 'guardian_name', 'guardian_nik', 'guardian_bin_binti', 'guardian_birth_place', 'guardian_birth_date', 'guardian_occupation', 'guardian_address', 'guardian_province_id', 'guardian_regency_id', 'guardian_district_id', 'guardian_village_id'];
            $hasGuardianInput = array_intersect_key($validated, array_flip($guardianKeys));
            if (!empty($hasGuardianInput)) {
                $guardian = $registration->guardian;
                if ($guardian) {
                    $rel = $validated['guardian_relationship'] ?? $guardian->relationship;
                    $ayahStatusG = $wanita?->parents->firstWhere('role', 'AYAH')?->status ?? null;
                    $sebabG = array_key_exists('guardian_sebab_wali', $validated) ? trim((string) $validated['guardian_sebab_wali']) : null;
                    $resolveSebab = fn($r) => ($sebabG !== null && $sebabG !== '') ? $sebabG : \App\Support\WaliHelper::defaultSebab($r, $ayahStatusG);
                    if ($rel === 'AYAH_KANDUNG') {
                        $wanitaFather = $wanita?->parents->firstWhere('role', 'AYAH');
                        $guardian->update(['relationship' => 'AYAH_KANDUNG', 'guardian_source' => 'PARENT', 'parent_id' => $wanitaFather?->id, 'sebab_wali' => $resolveSebab('AYAH_KANDUNG'), 'marital_status' => null, 'name' => null, 'nik' => null, 'bin_binti' => null, 'birth_place' => null, 'birth_date' => null, 'occupation' => null, 'address' => null, 'province_id' => null, 'regency_id' => null, 'district_id' => null, 'village_id' => null]);
                    } elseif ($rel === 'WALI_HAKIM') {
                        $guardian->update(['relationship' => 'WALI_HAKIM', 'guardian_source' => 'OTHER', 'parent_id' => null, 'sebab_wali' => $resolveSebab('WALI_HAKIM'), 'marital_status' => null, 'name' => 'Wali Hakim (KUA)', 'address' => 'Kantor KUA Setempat', 'province_id' => null, 'regency_id' => null, 'district_id' => null, 'village_id' => null]);
                    } else {
                        $gData = [];
                        if (array_key_exists('guardian_relationship', $validated)) $gData['relationship'] = $validated['guardian_relationship'];
                        if ($sebabG !== null) $gData['sebab_wali'] = $sebabG !== '' ? $sebabG : \App\Support\WaliHelper::defaultSebab($rel, $ayahStatusG);
                        if (array_key_exists('guardian_marital_status', $validated)) $gData['marital_status'] = $validated['guardian_marital_status'];
                        foreach (['name' => 'guardian_name', 'nik' => 'guardian_nik', 'bin_binti' => 'guardian_bin_binti', 'birth_place' => 'guardian_birth_place', 'birth_date' => 'guardian_birth_date', 'occupation' => 'guardian_occupation', 'address' => 'guardian_address', 'province_id' => 'guardian_province_id', 'regency_id' => 'guardian_regency_id', 'district_id' => 'guardian_district_id', 'village_id' => 'guardian_village_id'] as $col => $key) {
                            if (array_key_exists($key, $validated)) $gData[$col] = $validated[$key];
                        }
                        if (!empty($gData)) {
                            $gData['guardian_source'] = 'OTHER';
                            $gData['parent_id'] = null;
                            $guardian->update($gData);
                        } elseif ($sebabG !== null || array_key_exists('guardian_marital_status', $validated)) {
                            $updMS = []; if ($sebabG !== null) $updMS['sebab_wali'] = $sebabG !== '' ? $sebabG : \App\Support\WaliHelper::defaultSebab($rel, $ayahStatusG); if (array_key_exists('guardian_marital_status', $validated)) $updMS['marital_status'] = $validated['guardian_marital_status']; if (!empty($updMS)) $guardian->update($updMS);
                        }
                    }
                }
            }

            if (array_key_exists('witnesses', $validated) && is_array($validated['witnesses'])) {
                $witnessesInput = array_values(array_filter($validated['witnesses'], fn($w) => is_array($w) && !empty(array_filter($w, fn($v) => $v !== null && $v !== ''))));
                $witnessesInput = array_values(array_filter($witnessesInput, fn($w) => trim((string) ($w['name'] ?? '')) !== ''));
                $existing = $registration->witnesses->sortBy('id')->values();
                if (count($witnessesInput) < $existing->count()) {
                    foreach ($existing->slice(count($witnessesInput)) as $del) $del->delete();
                    $existing = $existing->take(count($witnessesInput));
                }
                $fillable = ['name','gender','bin_binti','nik','birth_place','birth_date','nationality','religion','marital_status','education','occupation','phone','email','address','province_id','regency_id','district_id','village_id'];
                $witnessRequired = ['name','gender','nik','birth_place','birth_date','marital_status','address','province_id','regency_id','district_id','village_id'];
                foreach ($witnessesInput as $idx => $wData) {
                    $data = [];
                    foreach ($fillable as $col) {
                        if (!array_key_exists($col, $wData)) continue;
                        $val = $wData[$col] === '' ? null : $wData[$col];
                        if (in_array($col, $witnessRequired, true) && ($val === null || $val === '')) continue;
                        $data[$col] = $val;
                    }
                    if (empty($data)) continue;
                    $target = $existing->get($idx);
                    if ($target) {
                        if (!empty($data)) $target->update($data);
                    } elseif (!empty($data['name'])) {
                        Witness::create(array_merge(['registration_id' => $registration->id], $data));
                    }
                }
            }
        });
    }

    public function update(
        Request $request,
        Registration $registration,
        MarriageRequirementService $requirementService,
        RegistrationAutosaveService $autosaveService
    ) {
        $this->autosave($request, $registration, $autosaveService);

        $requirements = $requirementService->evaluate($registration->load(['catins.parents', 'guardian', 'witnesses']));

        return redirect()
            ->route('registrations.detail', $registration->id)
            ->with('success', 'Data pendaftaran berhasil diperbarui.')
            ->with('requirements', $requirements);
    }

    private function assignDowry(Registration $registration, array $data): void
    {
        $type = $data['marriage_dowry_type'] ?? $registration->marriage_dowry_type ?? null;

        if (!$type) {
            $registration->marriage_dowry_type = null;
            $registration->marriage_dowry_money = null;
            $registration->marriage_dowry_gold = null;
            $registration->marriage_dowry_other = null;
            $registration->marriage_dowry = null;
            return;
        }

        $registration->marriage_dowry_type = $type;
        $registration->marriage_dowry_money = $data['marriage_dowry_money'] ?? null;
        $registration->marriage_dowry_gold = $data['marriage_dowry_gold'] ?? null;
        $registration->marriage_dowry_other = $data['marriage_dowry_other'] ?? null;

        $registration->marriage_dowry = match ($type) {
            'UANG_EMAS' => trim(collect([
                $registration->marriage_dowry_money ? 'Uang ' . $registration->marriage_dowry_money : null,
                $registration->marriage_dowry_gold ? 'Emas ' . $registration->marriage_dowry_gold : null,
            ])->filter()->implode(' dan ')),
            'UANG' => 'Uang ' . ($registration->marriage_dowry_money ?? ''),
            'EMAS' => 'Emas ' . ($registration->marriage_dowry_gold ?? ''),
            'LAINNYA' => (string) ($registration->marriage_dowry_other ?? ''),
            default => (string) ($registration->marriage_dowry ?? ''),
        };
    }

    public function destroy(Registration $registration)
    {
        DB::transaction(function () use ($registration) {
            $catinIds = $registration->catins()->pluck('catins.id');

            $registration->catins()->detach();
            Catin::whereIn('id', $catinIds)
                ->whereHas('registrations')
                ->update(['registration_id' => null]);
            foreach ($registration->ktpCopies as $copy) {
                if ($copy->path && Storage::disk('local')->exists($copy->path)) Storage::disk('local')->delete($copy->path);
                if ($copy->path && Storage::disk('public')->exists($copy->path)) Storage::disk('public')->delete($copy->path);
            }
            $registration->ktpCopies()->delete();
            $registration->documents()->delete();
            $registration->n6Document()->delete();
            $registration->documentArchives()->delete();
            $registration->guardian()->delete();
            $registration->witnesses()->delete();
            $registration->registrationDocumentChecklist()->delete();
            $registration->delete();

            Catin::whereIn('id', $catinIds)
                ->whereDoesntHave('registrations')
                ->eachById(function (Catin $catin) {
                    $catin->delete();
                });
        });

        return redirect()
            ->route('dashboard')
            ->with('success', 'Data pendaftaran berhasil dihapus.');
    }

    private function applyRtRwNormalizationToValidated(array &$validated): void
    {
        AddressHelper::normalizeValidatedAddresses($validated);
    }

    private function sanitizeNikInputs(Request $request): void
    {
        AddressHelper::sanitizeNikInputs($request);
    }

    private function validateRegionHierarchy(array $validated): void
    {
        $tuples = [
            'pria' => ['province_id' => 'pria_province_id', 'regency_id' => 'pria_regency_id', 'district_id' => 'pria_district_id', 'village_id' => 'pria_village_id'],
            'wanita' => ['province_id' => 'wanita_province_id', 'regency_id' => 'wanita_regency_id', 'district_id' => 'wanita_district_id', 'village_id' => 'wanita_village_id'],
        ];

        foreach (['pria_father', 'pria_mother', 'wanita_father', 'wanita_mother', 'guardian'] as $prefix) {
            $tuples[$prefix] = [
                'province_id' => $prefix . '_province_id',
                'regency_id' => $prefix . '_regency_id',
                'district_id' => $prefix . '_district_id',
                'village_id' => $prefix . '_village_id',
            ];
        }

        foreach ($validated['witnesses'] ?? [] as $index => $witness) {
            $tuples['witnesses.' . $index] = [
                'province_id' => 'province_id',
                'regency_id' => 'regency_id',
                'district_id' => 'district_id',
                'village_id' => 'village_id',
            ];

            $validated['witnesses.' . $index] = $witness;
        }

        foreach ($tuples as $prefix => $fields) {
            $values = [];
            foreach ($fields as $type => $field) {
                $values[$type] = str_contains($prefix, 'witnesses.')
                    ? ($validated[$prefix][$field] ?? null)
                    : ($validated[$field] ?? null);
            }

            $present = array_filter($values, static fn ($value) => $value !== null && $value !== '');
            $countPresent = count($present);
            $errorKey = str_contains($prefix, 'witnesses.')
                ? $prefix . '.province_id'
                : $fields['province_id'];
            if ($countPresent > 0 && $countPresent < 4) {
                throw ValidationException::withMessages([$errorKey => 'Data wilayah harus lengkap (provinsi, kabupaten, kecamatan, desa).']);
            }
            if ($countPresent === 0) continue;

            if (
                isset($values['province_id'], $values['regency_id'])
                && ! DB::table('regencies')->where('id', $values['regency_id'])->where('province_id', $values['province_id'])->exists()
            ) {
                throw ValidationException::withMessages([$errorKey => 'Kabupaten/Kota tidak berada di provinsi yang dipilih.']);
            }

            if (
                isset($values['regency_id'], $values['district_id'])
                && ! DB::table('districts')->where('id', $values['district_id'])->where('regency_id', $values['regency_id'])->exists()
            ) {
                throw ValidationException::withMessages([$errorKey => 'Kecamatan tidak berada di kabupaten/kota yang dipilih.']);
            }

            if (
                isset($values['district_id'], $values['village_id'])
                && ! DB::table('villages')->where('id', $values['village_id'])->where('district_id', $values['district_id'])->exists()
            ) {
                throw ValidationException::withMessages([$errorKey => 'Desa/Kelurahan tidak berada di kecamatan yang dipilih.']);
            }
        }
    }
}