<?php

namespace App\Http\Controllers;

use App\Models\DocumentNumberSetting;
use App\Models\N6Document;
use App\Models\Registration;
use App\Models\VillageProfile;
use App\Services\MarriageRequirementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegistrationDocumentController extends Controller
{
    /**
     * Service untuk menghitung kebutuhan dokumen
     * berdasarkan business rule aplikasi.
     */
    public function __construct(
        protected MarriageRequirementService $requirementService
    ) {
    }

    /**
     * =========================================================
     * DETAIL PENDAFTARAN
     * =========================================================
     *
     * Menampilkan seluruh data pendaftaran:
     *
     * - Registration
     * - Catin Pria
     * - Catin Wanita
     * - Parents
     * - Guardian
     * - Witnesses
     * - Checklist dokumen
     * - Arsip dokumen
     * - Requirement N5
     * - Requirement N6
     * - Surat kematian
     */
    public function show(Registration $registration): View
    {
        /*
        |--------------------------------------------------------------------------
        | LOAD RELATION
        |--------------------------------------------------------------------------
        |
        | Semua data detail diambil dari database.
        |
        | Guardian Ayah Kandung tetap menggunakan:
        |
        | guardian -> parent
        |
        | sehingga tidak mengambil salinan data guardian.
        |
        */

        $registration->load([
            'catins.parents',
            'catins.documentChecklist',
            'guardian.parent',
            'witnesses',
            'registrationDocumentChecklist',
            'documentArchives',
            'documents',
            'n6Document',
        ]);

        /*
        |--------------------------------------------------------------------------
        | EVALUATE DOCUMENT REQUIREMENT
        |--------------------------------------------------------------------------
        |
        | N5 / N6 / Surat Kematian dihitung oleh service.
        |
        | View tidak menentukan business rule sendiri.
        |
        */

        $requirements = $this->requirementService
            ->evaluate($registration);

        /*
        |--------------------------------------------------------------------------
        | CATIN
        |--------------------------------------------------------------------------
        */

        $catinPria = $registration->catins
            ->firstWhere('role', 'PRIA');

        $catinWanita = $registration->catins
            ->firstWhere('role', 'WANITA');

        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */

        return view('registrations.detail', [
            'registration' => $registration,

            'catinPria' => $catinPria,

            'catinWanita' => $catinWanita,

            'requirements' => $requirements,
        ]);
    }


    /**
     * =========================================================
     * PRINT DATA PENDAFTARAN
     * =========================================================
     */
    public function printRegistrationData(Registration $registration): View
    {
        $this->loadPrintData($registration);

        return view(
            'registrations.documents.f1',
            $this->printViewData($registration)
        );
    }


    /**
     * =========================================================
     * PRINT N1 - PENGANTAR NIKAH
     * =========================================================
     */
    public function printN1(Registration $registration): View
    {
        $this->loadPrintData($registration);

        return view(
            'registrations.print.n1',
            $this->printViewData($registration)
        );
    }


    /**
     * =========================================================
     * PRINT N2
     * =========================================================
     */
    public function printN2(Registration $registration): View
    {
        $this->loadPrintData($registration);

        return view(
            'registrations.print.n2',
            $this->printViewData($registration)
        );
    }


    /**
     * =========================================================
     * PRINT N4
     * =========================================================
     */
    public function printN4(Registration $registration): View
    {
        $this->loadPrintData($registration);

        return view(
            'registrations.print.n4',
            $this->printViewData($registration)
        );
    }


    /**
     * =========================================================
     * PRINT N5 - SURAT IZIN ORANG TUA
     * =========================================================
     */
    public function printN5(Registration $registration): View
    {
        $this->loadPrintData($registration);

        $requirements = $this->requirementService
            ->evaluate($registration);

        if (!($requirements['n5']['required'] ?? false)) {
            abort(
                404,
                'Dokumen N5 tidak diperlukan untuk pendaftaran ini.'
            );
        }

        return view(
            'registrations.print.n5',
            $this->printViewData($registration, $requirements)
        );
    }


    /**
     * =========================================================
     * PRINT SURAT PERNYATAAN WALI NIKAH
     * =========================================================
     */
    public function printSuratPernyataanWali(Registration $registration): View
    {
        $this->loadPrintData($registration);
        $data = $this->printViewData($registration);
        if (!$data['guardian']) abort(404, 'Data wali belum diisi.');
        if (($data['guardian']->relationship ?? '') === 'WALI_HAKIM') abort(404, 'Wali Hakim tidak memerlukan Surat Pernyataan Wali.');
        return view('registrations.print.surat-pernyataan-wali', $data);
    }

    /**
     * =========================================================
     * PRINT SURAT PERNYATAAN STATUS (per catin)
     * =========================================================
     */
    public function printSuratPernyataanStatus(Registration $registration, string $catinRole): View
    {
        $role = strtoupper($catinRole);
        if (!in_array($role, ['PRIA', 'WANITA'], true)) abort(404);
        $this->loadPrintData($registration);
        $data = $this->printViewData($registration);
        $catin = $role === 'PRIA' ? $data['catinPria'] : $data['catinWanita'];
        if (!$catin) abort(404, 'Data catin tidak ditemukan.');
        return view('registrations.print.surat-pernyataan-status', array_merge($data, [
            'catin' => $catin,
            'catinRole' => $role,
        ]));
    }

    /**
     * =========================================================
     * PRINT N6
     * =========================================================
     *
     * N6 bersifat kondisional.
     *
     * Sebelum print, requirement dihitung ulang dari database.
     *
     * Jadi user tidak bisa membuat N6 hanya dengan memanipulasi
     * checkbox di browser.
     */
    public function printN6(Registration $registration): View|RedirectResponse
    {
        $this->loadPrintData($registration);

        /*
        |--------------------------------------------------------------------------
        | EVALUATE REQUIREMENT
        |--------------------------------------------------------------------------
        */

        $requirements = $this->requirementService
            ->evaluate($registration);

        /*
        |--------------------------------------------------------------------------
        | N6 TIDAK DIPERLUKAN
        |--------------------------------------------------------------------------
        |
        | Jika business rule mengatakan N6 tidak diperlukan,
        | jangan membuat/menampilkan dokumen N6.
        |
        */

        if (
            !($requirements['n6']['required'] ?? false)
        ) {
            abort(
                404,
                'Dokumen N6 tidak diperlukan untuk pendaftaran ini.'
            );
        }

        $n6 = N6Document::where('registration_id', $registration->id)->first();
        if (!$n6) {
            return redirect()->route('n6.create', $registration)->with('warning', 'Data N6 belum dibuat. Silakan lengkapi data wafat dan nomor surat terlebih dahulu.');
        }

        /*
        |--------------------------------------------------------------------------
        | PRINT N6
        |--------------------------------------------------------------------------
        */

        return view(
            'registrations.print.n6',
            array_merge(
                $this->printViewData($registration, $requirements),
                ['n6' => $n6]
            )
        );
    }


    /**
     * =========================================================
     * LOAD DATA UNTUK PRINT
     * =========================================================
     *
     * Semua dokumen N1-N6 menggunakan sumber data yang sama.
     *
     * Tidak ada data baru yang disimpan hanya untuk kebutuhan print.
     */
    private function loadPrintData(
        Registration $registration
    ): void {
        $registration->load([
            'catins.parents.village',
            'catins.parents.district',
            'catins.parents.regency',
            'catins.parents.province',
            'catins.village',
            'catins.district',
            'catins.regency',
            'catins.province',
            'catins.documentChecklist',
            'guardian.parent.village',
            'guardian.parent.district',
            'guardian.parent.regency',
            'guardian.village',
            'guardian.district',
            'guardian.regency',
            'witnesses.village',
            'witnesses.district',
            'witnesses.regency',
            'registrationDocumentChecklist',
            'documentArchives',
            'documents',
        ]);
    }


    /**
     * =========================================================
     * DATA YANG DIKIRIM KE PRINT VIEW
     * =========================================================
     */
    private function printViewData(
        Registration $registration,
        ?array $requirements = null
    ): array {
        /*
        |--------------------------------------------------------------------------
        | REQUIREMENT
        |--------------------------------------------------------------------------
        |
        | Kalau belum dihitung oleh caller,
        | hitung sekarang.
        |
        */

        if ($requirements === null) {
            $requirements = $this->requirementService
                ->evaluate($registration);
        }

        /*
        |--------------------------------------------------------------------------
        | CATIN
        |--------------------------------------------------------------------------
        */

        $catinPria = $registration->catins
            ->firstWhere('role', 'PRIA');

        $catinWanita = $registration->catins
            ->firstWhere('role', 'WANITA');

        /*
        |--------------------------------------------------------------------------
        | PARENT
        |--------------------------------------------------------------------------
        */

        $parentsPria = $catinPria?->parents ?? collect();

        $parentsWanita = $catinWanita?->parents ?? collect();

        $ayahPria = $parentsPria
            ->firstWhere('role', 'AYAH');

        $ibuPria = $parentsPria
            ->firstWhere('role', 'IBU');

        $ayahWanita = $parentsWanita
            ->firstWhere('role', 'AYAH');

        $ibuWanita = $parentsWanita
            ->firstWhere('role', 'IBU');

        /*
        |--------------------------------------------------------------------------
        | GUARDIAN
        |--------------------------------------------------------------------------
        |
        | SINGLE SOURCE OF TRUTH.
        |
        | Jika:
        |
        | guardian_source = PARENT
        |
        | maka data identitas wali diambil dari:
        |
        | guardian.parent
        |
        */

        $guardian = $registration->guardian;

        $guardianParent = $guardian?->parent;

        /*
        |--------------------------------------------------------------------------
        | RETURN
        |--------------------------------------------------------------------------
        */

        return [

            /*
            |--------------------------------------------------------------------------
            | REGISTRATION
            |--------------------------------------------------------------------------
            */

            'registration' => $registration,

            /*
            |--------------------------------------------------------------------------
            | CATIN
            |--------------------------------------------------------------------------
            */

            'catinPria' => $catinPria,

            'catinWanita' => $catinWanita,

            /*
            |--------------------------------------------------------------------------
            | PARENTS
            |--------------------------------------------------------------------------
            */

            'ayahPria' => $ayahPria,

            'ibuPria' => $ibuPria,

            'ayahWanita' => $ayahWanita,

            'ibuWanita' => $ibuWanita,

            /*
            |--------------------------------------------------------------------------
            | GUARDIAN
            |--------------------------------------------------------------------------
            */

            'guardian' => $guardian,

            'guardianParent' => $guardianParent,

            /*
            |--------------------------------------------------------------------------
            | WITNESSES
            |--------------------------------------------------------------------------
            */

            'witnesses' => $registration->witnesses,

            /*
            |--------------------------------------------------------------------------
            | CHECKLIST
            |--------------------------------------------------------------------------
            */

            'registrationChecklist' =>
                $registration->registrationDocumentChecklist,

            /*
            | Checklist Catin tersedia melalui relation:
            |
            | $catinPria->documentChecklist
            | $catinWanita->documentChecklist
            */

            /*
            |--------------------------------------------------------------------------
            | ARCHIVES
            |--------------------------------------------------------------------------
            */

            'documentArchives' =>
                $registration->documentArchives,

            /*
            |--------------------------------------------------------------------------
            | REQUIREMENTS
            |--------------------------------------------------------------------------
            */

            'requirements' => $requirements,

            'documents' => $registration->documents->keyBy('document_type'),
            'villageProfile' => VillageProfile::query()->first(),
            'docSettings' => DocumentNumberSetting::query()->first(),
        ];
    }
}