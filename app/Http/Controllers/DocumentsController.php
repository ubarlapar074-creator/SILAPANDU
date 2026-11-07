<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\Setting;
use App\Services\RegistrationService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DocumentsController extends Controller
{
    public function __construct(protected RegistrationService $registrationService) {}

    public function index(Request $request)
    {
        $registration = null;

        if ($registrationId = $request->query('registration_id')) {
            $registration = Registration::with(['parties.warga', 'parents'])->find($registrationId);
        }

        if (! $registration) {
            return view('documents.index', [
                'registration' => null,
                'villageProfile' => Setting::villageProfile(),
            ]);
        }

        $parties = $registration->parties->keyBy('role');
        $priaParty = $parties->get('calon_pria');
        $wanitaParty = $parties->get('calon_wanita');
        $pria = $priaParty?->warga;
        $wanita = $wanitaParty?->warga;

        $isAndon = $registration->service_type === 'andon';

        $roleInfo = $this->registrationService->roleDokumen(
            $pria,
            $wanita,
            $priaParty?->desa_asal,
            $wanitaParty?->desa_asal,
            $isAndon,
        );

        $ayahWanita = $registration->parents->firstWhere('type', 'bapak_wanita');

        $orangTuaMeninggal = $registration->parents
            ->filter(fn ($parent) => $this->isMeninggal($parent->status_hidup))
            ->map(fn ($parent) => $parent->nama)
            ->filter()
            ->values();

        $usiaPria = $pria?->tgl_lahir?->age;
        $usiaWanita = $wanita?->tgl_lahir?->age;

        $calonDibawahUmur = collect([
            'Calon Pria' => $usiaPria,
            'Calon Wanita' => $usiaWanita,
        ])->filter(fn ($usia) => $usia !== null && $usia < 21);

        return view('documents.index', [
            'registration' => $registration,
            'villageProfile' => Setting::villageProfile(),
            'namaPria' => $pria?->nama_lengkap ?: ($parties->get('calon_pria')?->nama_sementara ?: 'Calon Pria'),
            'namaWanita' => $wanita?->nama_lengkap ?: ($parties->get('calon_wanita')?->nama_sementara ?: 'Calon Wanita'),
            'ayahWanita' => $ayahWanita,
            'butuhN6' => $this->isMeninggal($ayahWanita?->status_hidup),
            'orangTuaMeninggal' => $orangTuaMeninggal,
            'usiaPria' => $usiaPria,
            'usiaWanita' => $usiaWanita,
            'calonDibawahUmur' => $calonDibawahUmur,
            'butuhIzinOrtu' => $calonDibawahUmur->isNotEmpty(),
            'n5SignerBlocked' => $this->registrationService->n5SignerBlocked($registration),
            'role' => $roleInfo['role'],
            'isAndon' => $isAndon,
            'selectedRole' => strtolower($roleInfo['catinRole']),
            'priaHere' => $roleInfo['priaHere'],
            'wanitaHere' => $roleInfo['wanitaHere'],
            'waliHakim' => $this->registrationService->isWaliHakim($registration),
        ]);
    }

    private function isMeninggal(?string $status): bool
    {
        if ($status === null) {
            return false;
        }
        $status = strtolower(trim((string) $status));

        return in_array($status, ['meninggal', 'wafat'], true);
    }

    public function preview(Request $request, string $template)
    {
        $template = str_replace(['/', '\\', '..'], '', $template);
        $templatePath = "documents.templates.{$template}";

        if (! view()->exists($templatePath)) {
            abort(404, 'Template dokumen tidak ditemukan.');
        }

        $villageProfile = Setting::villageProfile();

        // Jika ada ID registrasi, tampilkan data asli
        if ($registrationId = $request->query('registration_id')) {
            $registration = Registration::find($registrationId);

            if ($registration) {
                return view($templatePath, $this->registrationService->documentData($registration) + [
                    'villageProfile' => $villageProfile,
                ]);
            }
        }

        // Jika tidak ada ID, tampilkan blanko (fallback)
        return view($templatePath, [
            'registration' => new Registration,
            'villageProfile' => $villageProfile,
            'catinPria' => null,
            'catinWanita' => null,
            'catin' => null,
            'catinRole' => 'PRIA',
            'role' => 'both',
            'priaHere' => true,
            'wanitaHere' => true,
            'ayahPria' => null,
            'ibuPria' => null,
            'ayahWanita' => null,
            'ibuWanita' => null,
            'ayahUtama' => null,
            'ibuUtama' => null,
            'ayahLain' => null,
            'ibuLain' => null,
            'guardian' => null,
            'saksi1' => null,
            'saksi2' => null,
            'n5SignerPria' => null,
            'n5SignerWanita' => null,
            'n5SignerBlocked' => false,
        ]);
    }
}
