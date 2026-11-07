<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAkadRequest;
use App\Http\Requests\StorePriaRequest;
use App\Http\Requests\StoreWaliSaksiRequest;
use App\Http\Requests\StoreWanitaRequest;
use App\Models\Registration;
use App\Services\RegistrationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    protected $registrationService;

    public function __construct(RegistrationService $registrationService)
    {
        $this->registrationService = $registrationService;
    }

    /**
     * Tampilkan formulir calon pria
     */
    public function formPria(Registration $registration)
    {
        $provinces = DB::table('provinces')->orderBy('name')->get(['id', 'name']);

        return view('registration.forms.form-pria', compact('registration', 'provinces'));
    }

    /**
     * API Pengecekan NIK
     */
    public function checkNik(Request $request, string $nik): JsonResponse
    {
        if (strlen($nik) !== 16 || ! ctype_digit($nik)) {
            return response()->json(['status' => 'error', 'message' => 'NIK harus 16 digit angka.'], 422);
        }

        $result = $this->registrationService->cekNikWarga($nik, $this->currentRegistrationId($request));

        return response()->json($result);
    }

    /**
     * Simpan Data Pria
     */
    public function storePria(StorePriaRequest $request, Registration $registration)
    {
        // Data sudah otomatis tervalidasi oleh StorePriaRequest!
        $this->registrationService->simpanPria($registration, $request->validated());

        return redirect()
            ->route('registration.dashboard', $registration)
            ->with('success', 'Data calon pengantin pria & orang tuanya berhasil disimpan.');
    }

    /**
     * Tampilkan formulir calon wanita
     */
    public function formWanita(Registration $registration)
    {
        $provinces = DB::table('provinces')->orderBy('name')->get(['id', 'name']);

        return view('registration.forms.form-wanita', compact('registration', 'provinces'));
    }

    /**
     * Simpan Data Calon Wanita & Orang Tua
     */
    public function storeWanita(StoreWanitaRequest $request, Registration $registration): RedirectResponse
    {
        $this->registrationService->simpanWanita($registration, $request->validated());

        return redirect()
            ->route('registration.dashboard', $registration)
            ->with('success', 'Data calon pengantin wanita & orang tuanya berhasil disimpan.');
    }

    /**
     * API wilayah (provinsi/kabupaten/kecamatan/desaa)
     * Digunakan dropdown dinamis pada halaman pemilihan layanan.
     */
    public function region(string $table, ?string $parentId = null): JsonResponse
    {
        $allowedTables = ['provinces', 'regencies', 'districts', 'villages'];

        if (! in_array($table, $allowedTables)) {
            return response()->json([]);
        }

        $parentColumn = [
            'regencies' => 'province_id',
            'districts' => 'regency_id',
            'villages' => 'district_id',
        ][$table] ?? null;

        $query = DB::table($table)->select('id', 'name');

        if ($parentColumn && $parentId) {
            $query->where($parentColumn, $parentId);
        }

        return response()->json($query->orderBy('name')->get());
    }

    /**
     * API lookup warga berdasarkan NIK
     */
    public function lookupNik(Request $request, string $nik): JsonResponse
    {
        $result = $this->registrationService->cekNikWarga($nik, $this->currentRegistrationId($request));

        return response()->json($result);
    }

    /**
     * Mulai pendaftaran dari sidebar (tanpa modal).
     * Membuat draft langsung lalu mengarahkan ke dashboard pendaftaran.
     */
    public function start(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'service_type' => 'required|in:bedol,kantor,andon',
        ]);

        $registration = Registration::create([
            'registration_number' => $this->generateRegistrationNumber(),
            'service_type' => $data['service_type'],
            'step' => 1,
            'status' => 'draft',
        ]);

        $this->registrationService->resequenceNomorSurat();

        return redirect()->route('registration.dashboard', $registration);
    }

    /**
     * Mulai pendaftaran langsung dari sidebar (GET) berdasarkan type layanan.
     */
    public function startService(string $serviceType): RedirectResponse
    {
        if (! in_array($serviceType, ['bedol', 'kantor', 'andon'], true)) {
            abort(404);
        }

        $registration = Registration::create([
            'registration_number' => $this->generateRegistrationNumber(),
            'service_type' => $serviceType,
            'step' => 1,
            'status' => 'draft',
        ]);

        $this->registrationService->resequenceNomorSurat();

        return redirect()->route('registration.dashboard', $registration);
    }

    /**
     * Formulir Jadwal Akad & Mahar (Card 5 dashboard)
     */
    public function formAkad(Registration $registration): View
    {
        abort_if($registration->service_type === 'andon', 404, 'Andon Nikah tidak memerlukan data akad.');

        $provinces = DB::table('provinces')->orderBy('name')->get(['id', 'name']);
        $regencies = $registration->province_id
            ? DB::table('regencies')->where('province_id', $registration->province_id)->orderBy('name')->get(['id', 'name'])
            : collect();
        $districts = $registration->regency_id
            ? DB::table('districts')->where('regency_id', $registration->regency_id)->orderBy('name')->get(['id', 'name'])
            : collect();
        $villages = $registration->district_id
            ? DB::table('villages')->where('district_id', $registration->district_id)->orderBy('name')->get(['id', 'name'])
            : collect();

        return view('registration.forms.form-akad', compact('registration', 'provinces', 'regencies', 'districts', 'villages'));
    }

    /**
     * Simpan Formulir Jadwal Akad & Mahar (Card 5 dashboard)
     */
    public function storeAkad(StoreAkadRequest $request, Registration $registration): RedirectResponse
    {
        $this->registrationService->simpanAkad($registration, $request->validated());

        return redirect()
            ->route('registration.dashboard', $registration)
            ->with('success', 'Jadwal akad dan mahar berhasil disimpan.');
    }

    /**
     * Formulir Wali & Saksi
     */
    public function formWaliSaksi(?Registration $registration = null): View
    {
        return view('registration.forms.form-wali-saksi', compact('registration'));
    }

    /**
     * Simpan Data Wali Nikah & Saksi
     */
    public function storeWaliSaksi(StoreWaliSaksiRequest $request, Registration $registration): RedirectResponse
    {
        $this->registrationService->simpanWaliSaksi($registration, $request->validated());

        return redirect()
            ->route('registration.dashboard', $registration)
            ->with('success', 'Data wali nikah & saksi berhasil disimpan.');
    }

    /**
     * Halaman Review Pendaftaran (seperti struk cetak)
     */
    public function review(Registration $registration): View
    {
        $priaParty = $registration->parties()->where('role', 'calon_pria')->with('warga')->first();
        $wanitaParty = $registration->parties()->where('role', 'calon_wanita')->with('warga')->first();
        $pria = $priaParty?->warga;
        $wanita = $wanitaParty?->warga;

        $bapakPria = $registration->parents()->where('type', 'bapak_pria')->first();
        $ibuPria = $registration->parents()->where('type', 'ibu_pria')->first();
        $bapakWanita = $registration->parents()->where('type', 'bapak_wanita')->first();
        $ibuWanita = $registration->parents()->where('type', 'ibu_wanita')->first();

        $wali = $registration->marriageWalis()->first();
        $saksi = $registration->parties()->whereIn('role', ['saksi_1', 'saksi_2'])->with('warga')->get();

        // Tombol cetak N5 hanya untuk usia < 21 tahun (butuh izin orang tua)
        $usiaPria = $pria?->tgl_lahir?->age;
        $usiaWanita = $wanita?->tgl_lahir?->age;
        $butuhIzinOrtu = ($usiaPria !== null && $usiaPria < 21) || ($usiaWanita !== null && $usiaWanita < 21);

        // Tombol cetak N6 hanya jika ayah kandung calon wanita meninggal
        $butuhN6 = strtolower((string) $bapakWanita?->status_hidup) === 'meninggal';
        $n6BelumLengkap = $this->n6BelumLengkap($registration);

        $wilayah = [];
        if ($registration->province_id) {
            $wilayah['provinsi'] = DB::table('provinces')->where('id', $registration->province_id)->value('name');
        }
        if ($registration->regency_id) {
            $wilayah['kabupaten'] = DB::table('regencies')->where('id', $registration->regency_id)->value('name');
        }
        if ($registration->district_id) {
            $wilayah['kecamatan'] = DB::table('districts')->where('id', $registration->district_id)->value('name');
        }
        if ($registration->village_id) {
            $wilayah['desa'] = DB::table('villages')->where('id', $registration->village_id)->value('name');
        }

        return view('registration.review', compact(
            'registration',
            'priaParty',
            'wanitaParty',
            'pria',
            'wanita',
            'bapakPria',
            'ibuPria',
            'bapakWanita',
            'ibuWanita',
            'wali',
            'saksi',
            'wilayah',
            'butuhIzinOrtu',
            'butuhN6',
            'n6BelumLengkap'
        ));
    }

    /**
     * Template dokumen yang boleh dicetak.
     *
     * @var string[]
     */
    private const PRINTABLE_TEMPLATES = [
        'n1',
        'n2',
        'n4',
        'n5',
        'n6',
        'f1',
        'ktp',
        'surat-pernyataan-status',
        'surat-pernyataan-wali',
    ];

    /**
     * Cetak satu dokumen pendaftaran sebagai PDF (DomPDF).
     */
    public function cetakDokumen(Registration $registration, string $template)
    {
        abort_unless(in_array(strtolower($template), array_map('strtolower', self::PRINTABLE_TEMPLATES), true), 404, 'Template dokumen tidak ditemukan.');

        $data = $this->registrationService->documentData($registration);
        $role = $data['role'];
        $queryRole = request()->query('role');
        $isAndon = $registration->service_type === 'andon';

        // Smart Kop Surat: operator hanya boleh mencetak untuk warga desanya.
        // Layanan Andon dikecualikan karena pihak pemohon dipilih manual oleh operator.
        if (! $isAndon && $role === 'invalid') {
            return back()->with('error', 'Anda hanya dapat mencetak dokumen untuk warga desa yang terdaftar pada sistem.');
        }

        // N6 & Surat Pernyataan Wali (N7) adalah wanita-sampingan: wajib wanita dari desa operator.
        if (! $isAndon && in_array($template, ['n6', 'surat-pernyataan-wali'], true) && ! $data['wanitaHere']) {
            return back()->with('error', 'Dokumen ini hanya dapat dicetak bila calon wanita terdaftar di desa Anda.');
        }

        // Jika memilih pihak lewat ?role=, pihak yang dipilih harus terdaftar di desa operator.
        if (! $isAndon && $queryRole === 'pria' && ! $data['priaHere']) {
            return back()->with('error', 'Calon pria tidak terdaftar di desa Anda.');
        }
        if (! $isAndon && $queryRole === 'wanita' && ! $data['wanitaHere']) {
            return back()->with('error', 'Calon wanita tidak terdaftar di desa Anda.');
        }

        return Pdf::loadView('documents.templates.'.$template, $data)
            ->setPaper('a4', 'portrait')
            ->stream();
    }

    /**
     * Konfirmasi & kunci data pendaftaran (status berubah jadi valid)
     */
    public function finalize(Registration $registration): RedirectResponse
    {
        if ($this->n6BelumLengkap($registration)) {
            return back()->withErrors([
                'n6' => 'Finalisasi diblokir: data kematian ayah calon wanita (tanggal & tempat meninggal) belum lengkap untuk Form N6.',
            ]);
        }

        $registration->update([
            'status' => 'valid',
        ]);

        return redirect()
            ->route('registration.dashboard', $registration)
            ->with('success', 'Data pendaftaran telah dikonfirmasi dan dikunci (valid).');
    }

    /**
     * Form N6 wajib (ayah kandung calon wanita meninggal) tetapi data kematian belum lengkap.
     */
    private function n6BelumLengkap(Registration $registration): bool
    {
        $bapakWanita = $registration->parents()->where('type', 'bapak_wanita')->first();

        if (strtolower((string) $bapakWanita?->status_hidup) !== 'meninggal') {
            return false;
        }

        return ! $bapakWanita->tgl_meninggal || ! $bapakWanita->tempat_meninggal;
    }

    /**
     * Hapus pendaftaran (draft/belum lengkap).
     */
    public function destroy(Registration $registration): RedirectResponse
    {
        $registration->delete();

        $this->registrationService->resequenceNomorSurat();

        // Kembali ke halaman asal (Dashboard Utama atau Riwayat), dengan fallback Riwayat.
        return redirect()->back(fallback: route('registration.index'))->with('success', 'Pendaftaran berhasil dihapus.');
    }

    /**
     * Riwayat & Data Nikah — arsip seluruh pendaftaran dengan pencarian & filter.
     */
    public function index(Request $request)
    {
        // Bersihkan draft kosong yang ditinggalkan operator sebelum tabel dimuat.
        // Nomor surat tidak disusun ulang di sini: halaman Riwayat hanya membaca data
        // yang sama seperti Dashboard Utama, agar nomor tidak berubah saat dibuka.
        Registration::emptyDrafts()->delete();

        $search = trim((string) $request->query('q'));
        $type = in_array($request->query('type'), ['kantor', 'bedol', 'andon'], true)
            ? $request->query('type')
            : null;
        $status = in_array($request->query('status'), ['lengkap', 'belum'], true)
            ? $request->query('status')
            : null;

        $registrations = Registration::with(['parties.warga'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('registration_number', 'like', "%{$search}%")
                        ->orWhere('nomor_surat', 'like', "%{$search}%")
                        ->orWhereHas('parties', function ($party) use ($search) {
                            $party->where('nama_sementara', 'like', "%{$search}%")
                                ->orWhereHas('warga', function ($warga) use ($search) {
                                    $warga->where('nama_lengkap', 'like', "%{$search}%");
                                });
                        });
                });
            })
            ->when($type, fn ($query, $serviceType) => $query->where('service_type', $serviceType))
            ->when($status === 'lengkap', function ($query) {
                $query->where(fn ($q) => $q->where('step', '>=', 4)
                    ->orWhereIn('status', ['lengkap', 'valid', 'selesai']));
            })
            ->when($status === 'belum', function ($query) {
                $query->where('step', '<', 4)
                    ->whereNotIn('status', ['lengkap', 'valid', 'selesai']);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $counts = Registration::selectRaw('service_type, COUNT(*) as total')
            ->groupBy('service_type')
            ->pluck('total', 'service_type');

        return view('registration.index', [
            'registrations' => $registrations,
            'counts' => $counts,
            'search' => $search,
            'activeType' => $type,
            'activeStatus' => $status,
        ]);
    }

    /**
     * Tampilkan halaman layanan / detail registrasi
     */
    public function show($id = null)
    {
        if ($id === null) {
            return redirect()->route('dashboard');
        }

        $registration = Registration::findOrFail($id);

        return view('registration.show', compact('registration'));
    }

    /**
     * Dashboard pendaftaran (4 Card ringkasan)
     */
    public function dashboard(Registration $registration)
    {
        $isAndon = $registration->service_type === 'andon';
        $priaParty = $registration->parties()->where('role', 'calon_pria')->with('warga')->first();
        $wanitaParty = $registration->parties()->where('role', 'calon_wanita')->with('warga')->first();

        $wali = $registration->marriageWalis()->first();
        $saksiParties = $registration->parties()->whereIn('role', ['saksi_1', 'saksi_2'])->with('warga')->get()->keyBy('role');

        $adaAkad = (bool) $registration->tanggal_akad;
        $siapCetak = $isAndon
            ? ($priaParty && $wanitaParty && $wali)
            : ($priaParty && $wanitaParty && $wali && $adaAkad);

        $completeSteps = 0;
        if ($priaParty) {
            $completeSteps++;
        }
        if ($wanitaParty) {
            $completeSteps++;
        }
        if ($wali) {
            $completeSteps++;
        }

        if ($isAndon) {
            $progressPercent = (int) round(($completeSteps / 3) * 100);
            $progressText = $completeSteps.' dari 3 Data Terisi';

            $cekKelengkapan = [
                ['label' => 'Calon Pria', 'ok' => (bool) $priaParty],
                ['label' => 'Calon Wanita', 'ok' => (bool) $wanitaParty],
                ['label' => 'Wali & Saksi', 'ok' => (bool) $wali],
            ];
        } else {
            if ($adaAkad) {
                $completeSteps++;
            }
            $progressPercent = $completeSteps * 25;
            $progressText = $completeSteps.' dari 4 Data Terisi';

            $cekKelengkapan = [
                ['label' => 'Calon Pria', 'ok' => (bool) $priaParty],
                ['label' => 'Calon Wanita', 'ok' => (bool) $wanitaParty],
                ['label' => 'Wali & Saksi', 'ok' => (bool) $wali],
                ['label' => 'Jadwal Akad', 'ok' => $adaAkad],
            ];
        }

        $n6BelumLengkap = $this->n6BelumLengkap($registration);

        return view('registration.dashboard', compact(
            'registration',
            'priaParty',
            'wanitaParty',
            'wali',
            'saksiParties',
            'adaAkad',
            'siapCetak',
            'progressPercent',
            'progressText',
            'cekKelengkapan',
            'n6BelumLengkap'
        ));
    }

    /**
     * Preview data pendaftaran sebelum disimpan
     */
    public function preview(Request $request)
    {
        $data = $request->validate([
            'service_type' => 'required|in:bedol,kantor,andon',
            'tanggal_akad' => 'required|date',
            'jam_akad' => 'required|date_format:H:i',
            'nama_pria' => 'required|string|max:255',
            'nama_wanita' => 'required|string|max:255',
            'marriage_dowry_display' => 'nullable|string',
            'marriage_location_address' => 'nullable|string',
            'province_id' => 'nullable|string',
            'regency_id' => 'nullable|string',
            'district_id' => 'nullable|string',
            'village_id' => 'nullable|string',
        ]);

        $regionNames = [];
        if (! empty($data['province_id'])) {
            $regionNames['province'] = DB::table('provinces')->where('id', $data['province_id'])->value('name');
        }
        if (! empty($data['regency_id'])) {
            $regionNames['regency'] = DB::table('regencies')->where('id', $data['regency_id'])->value('name');
        }
        if (! empty($data['district_id'])) {
            $regionNames['district'] = DB::table('districts')->where('id', $data['district_id'])->value('name');
        }
        if (! empty($data['village_id'])) {
            $regionNames['village'] = DB::table('villages')->where('id', $data['village_id'])->value('name');
        }

        $serviceLabel = match ($data['service_type']) {
            'bedol' => 'Nikah Bedol (Luar Kantor)',
            'kantor' => 'Nikah Kantor (KUA)',
            'andon' => 'Andon / Numpang Nikah',
            default => 'Tidak diketahui',
        };

        $tanggalDisplay = now()->parse($data['tanggal_akad'])->format('d F Y');
        $dowryDisplay = $data['marriage_dowry_display'] ?? '-';

        return view('registration.preview', [
            'data' => $data,
            'regionNames' => $regionNames,
            'serviceLabel' => $serviceLabel,
            'tanggalDisplay' => $tanggalDisplay,
            'dowryDisplay' => $dowryDisplay,
        ]);
    }

    /**
     * Simpan pendaftaran baru (dari preview page atau AJAX)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'service_type' => 'required|in:bedol,kantor,andon',
            'tanggal_akad' => 'required|date',
            'jam_akad' => 'required|date_format:H:i',
            'nama_pria' => 'nullable|string|max:255',
            'nama_wanita' => 'nullable|string|max:255',
            'jenis_mahar' => 'nullable|string',
            'nominal_uang' => 'nullable|numeric',
            'rincian_mahar' => 'nullable|string',
            'marriage_dowry_display' => 'nullable|string',
            'marriage_location_address' => 'nullable|string',
            'province_id' => 'nullable|string',
            'regency_id' => 'nullable|string',
            'district_id' => 'nullable|string',
            'village_id' => 'nullable|string',
        ]);

        $data['registration_number'] = $this->generateRegistrationNumber();
        $data['step'] = 1;
        $data['status'] = 'draft';

        $registration = Registration::create($data);

        $this->registrationService->resequenceNomorSurat();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'registration' => $registration,
            ]);
        }

        return redirect()->route('registration.dashboard', $registration);
    }

    /**
     * Registrasi yang sedang diedit dari query string `registration_id`,
     * agar tidak dihitung sebagai riwayat pernikahan sebelumnya.
     */
    private function currentRegistrationId(Request $request): ?int
    {
        $registrationId = $request->query('registration_id');

        return is_numeric($registrationId) ? (int) $registrationId : null;
    }

    /**
     * Buat nomor pendaftaran unik untuk tahun berjalan
     */
    private function generateRegistrationNumber(): string
    {
        $year = now()->year;
        $seq = (int) Registration::whereYear('created_at', $year)->max('id') + 1;

        return 'REG/'.str_pad($seq, 3, '0', STR_PAD_LEFT).'/'.$year;
    }
}
