<?php

namespace App\Http\Controllers;

use App\Models\DocumentNumberSetting;
use App\Models\Registration;
use App\Models\RegistrationDocument;
use App\Models\N6Document;
use App\Services\DocumentNumberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationDocumentNumberController extends Controller
{
    private const DOCUMENT_TYPES = ['N1', 'N2', 'N4', 'N5', 'N6'];

    public function edit(Registration $registration, DocumentNumberService $service): View
    {
        $registration->load('documents');
        $documents = $registration->documents->keyBy('document_type');
        $settings = DocumentNumberSetting::query()->first();
        $profile = \App\Models\VillageProfile::query()->first();
        $globalSuggestion = $service->suggestedGlobal();
        $suggestions = [];
        $defaults = $service->defaults($profile);
        foreach (self::DOCUMENT_TYPES as $type) {
            $suggestions[$type] = $documents->get($type)?->document_number ?: $globalSuggestion;
        }

        return view('registrations.documents.numbers', [
            'registration' => $registration,
            'documents' => $documents,
            'documentTypes' => self::DOCUMENT_TYPES,
            'suggestions' => $suggestions,
            'docDefaults' => $defaults,
            'docSettings' => $settings,
            'villageProfile' => $profile,
        ]);
    }

    public function update(Request $request, Registration $registration): RedirectResponse
    {
        $validated = $request->validate([
            'documents' => ['required', 'array'],
            'documents.*.number' => ['required', 'string', 'max:255', 'distinct'],
            'documents.*.issue_date' => ['nullable', 'date'],
            'documents.*.signed_by' => ['nullable', 'string', 'max:255'],
            'documents.*.signed_by_position' => ['nullable', 'string', 'max:255'],
        ]);

        $numbers = collect($validated['documents'])->pluck('number')->filter()->values();
        $hasDuplicateWithinForm = $numbers->count() !== $numbers->unique()->count();
        $uniqueNumbers = $numbers->unique()->values();
        foreach ($uniqueNumbers as $num) {
            $existsOtherReg = RegistrationDocument::where('document_number', $num)->where('registration_id', '!=', $registration->id)->exists();
            if ($existsOtherReg) {
                return back()->withErrors(['documents' => "Nomor surat {$num} sudah dipakai pada pendaftaran lain. Silakan ubah agar sinkron dengan buku fisik."])->withInput();
            }
            if (N6Document::where('document_number', $num)->where('registration_id', '!=', $registration->id)->exists()) {
                return back()->withErrors(['documents' => "Nomor surat {$num} sudah dipakai pada dokumen N6 lain."])->withInput();
            }
        }
        if ($hasDuplicateWithinForm) {
            // Duplikat dalam form (mis. N1=N2 global number) diperbolehkan untuk model global; tetap valid
            // Hanya cegah jika antar-pendaftaran duplikat (sudah dicek di atas)
        }

        foreach ($validated['documents'] as $documentType => $data) {
            if (!in_array($documentType, self::DOCUMENT_TYPES, true)) {
                continue;
            }

            RegistrationDocument::updateOrCreate(
                [
                    'registration_id' => $registration->id,
                    'document_type' => $documentType,
                ],
                [
                    'document_number' => $data['number'],
                    'issue_date' => $data['issue_date'] ?? null,
                    'signed_by' => $data['signed_by'] ?? null,
                    'signed_by_position' => $data['signed_by_position'] ?? null,
                    'status' => 'PUBLISHED',
                    'created_by' => auth()->id() ?? null,
                ]
            );

            if ($documentType === 'N6') {
                N6Document::where('registration_id', $registration->id)
                    ->update(['document_number' => $data['number']]);
            }
        }

        return redirect()
            ->route('registrations.detail', $registration)
            ->with('success', 'Nomor surat berhasil disimpan.');
    }
}
