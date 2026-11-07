<?php

namespace App\Services;

use App\Models\Registration;
use App\Models\RegistrationParent;
use App\Models\RegistrationParty;
use App\Models\Setting;
use App\Models\Warga;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class RegistrationService
{
    /**
     * Format nomor surat bawaan bila operator belum mengatur format sendiri.
     */
    public const DEFAULT_FORMAT_NOMOR = '{NO}/DS/{BLN}/{THN}';

    /**
     * Otomatis generate dan urutkan ulang nomor surat berdasarkan format yang diatur
     * operator di menu pengaturan, agar berurutan (001, 002, dst) tanpa celah
     * meskipun ada data yang dihapus. Nomor pendaftaran yang sudah difinalisasi
     * dibekukan dan tidak ikut disusun ulang.
     */
    public function resequenceNomorSurat(): void
    {
        $settings = Setting::allAsArray();
        $format = (string) ($settings['format_nomor_surat'] ?? '');

        $registrations = Registration::orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        // Nomor pendaftaran yang sudah difinalisasi dibekukan: dipakai sebagai
        // penanda nomor terpakai agar pendaftaran lain tidak bertabrakan dengannya.
        $nomorTerkunci = $registrations
            ->filter(fn (Registration $reg): bool => $reg->nomorSuratTerkunci() && filled($reg->nomor_surat))
            ->map(fn (Registration $reg): string => (string) $reg->nomor_surat)
            ->values()
            ->all();

        $seq = 1;
        foreach ($registrations as $reg) {
            if ($reg->nomorSuratTerkunci() && filled($reg->nomor_surat)) {
                continue;
            }

            do {
                $newNomor = $this->formatNomorSurat(
                    $format,
                    str_pad((string) $seq, 3, '0', STR_PAD_LEFT),
                    $reg->created_at,
                );
                $seq++;
            } while (in_array($newNomor, $nomorTerkunci, true));

            if ($reg->nomor_surat !== $newNomor) {
                $reg->updateQuietly(['nomor_surat' => $newNomor]);
            }
        }
    }

    /**
     * Susun nomor surat dari format bebas yang diatur operator.
     *
     * Token yang dikenali: {NO} nomor urut, {BLN} bulan Romawi, {THN} tahun.
     * Teks lain pada format dicetak apa adanya, sehingga operator bisa menulis
     * sendiri kode desa/kecamatan tanpa digandakan aplikasi.
     *
     * @param  CarbonInterface|null  $tanggal  Tanggal surat; bawaan sekarang.
     */
    public function formatNomorSurat(string $format, string $nomorUrut, ?CarbonInterface $tanggal = null): string
    {
        $tanggal ??= now();
        $format = trim($format) !== '' ? trim($format) : self::DEFAULT_FORMAT_NOMOR;

        return str_replace(
            ['{NO}', '{BLN}', '{THN}'],
            [$nomorUrut, $this->getRomawiMonth($tanggal->format('m')), $tanggal->format('Y')],
            $format,
        );
    }

    /**
     * Contoh nomor untuk format tertentu pada urutan berikutnya, dipakai
     * sebagai pratinjau di menu pengaturan.
     */
    public function contohNomorSurat(string $format): string
    {
        $nomorBerikutnya = str_pad((string) (Registration::count() + 1), 3, '0', STR_PAD_LEFT);

        return $this->formatNomorSurat($format, $nomorBerikutnya);
    }

    private function getRomawiMonth(string $month): string
    {
        $map = [
            '01' => 'I', '02' => 'II', '03' => 'III', '04' => 'IV',
            '05' => 'V', '06' => 'VI', '07' => 'VII', '08' => 'VIII',
            '09' => 'IX', '10' => 'X', '11' => 'XI', '12' => 'XII',
        ];

        return $map[$month] ?? 'I';
    }

    /**
     * Logika Pengecekan NIK
     */
    public function cekNikWarga(string $nik, int|string|null $currentRegistrationId = null): array
    {
        $warga = Warga::where('nik', $nik)->first();

        if (! $warga) {
            return ['status' => 'not_found', 'is_married_before' => false];
        }

        $isMarriedBefore = RegistrationParty::whereHas('warga', function ($q) use ($nik) {
            $q->where('nik', $nik);
        })->whereIn('role', ['calon_pria', 'calon_wanita']);

        if ($currentRegistrationId !== null && $currentRegistrationId !== '') {
            $isMarriedBefore->where('registration_id', '!=', $currentRegistrationId);
        }

        $isMarriedBefore = $isMarriedBefore->exists();

        return [
            'status' => 'found',
            'data' => [
                'nik' => $warga->nik,
                'nama_lengkap' => $warga->nama_lengkap,
                'tempat_lahir' => $warga->tempat_lahir,
                'tgl_lahir' => $warga->tgl_lahir?->format('Y-m-d'),
                'pekerjaan' => $warga->pekerjaan,
                'alamat' => $warga->alamat,
                'no_hp' => $warga->no_hp,
                'email' => $warga->email,
                'pendidikan' => $warga->pendidikan,
                'agama' => $warga->agama,
                'status_kawin' => $warga->status_kawin,
                'nama_ayah' => $warga->nama_ayah,
                'nama_ibu' => $warga->nama_ibu,
            ],
            'is_married_before' => $isMarriedBefore,
        ];
    }

    /**
     * Logika Simpan Data Pria & Orang Tua (Tahap 1)
     */
    public function simpanPria(Registration $registration, array $data): void
    {
        DB::transaction(function () use ($registration, $data) {
            $pria = $data['pria'] ?? [];
            $bapak = $data['bapak'] ?? [];
            $ibu = $data['ibu'] ?? [];

            // 1. Simpan/Update Master Warga (Calon Pria)
            $warga = Warga::updateOrCreate(
                ['nik' => $pria['nik']],
                [
                    'nama_lengkap' => $pria['nama_lengkap'],
                    'tempat_lahir' => $pria['tempat_lahir'] ?? null,
                    'tgl_lahir' => $pria['ttl_lahir'] ?? null,
                    'jenis_kelamin' => 'L',
                    'agama' => $pria['agama'] ?? 'Islam',
                    'pekerjaan' => $pria['pekerjaan'] ?? null,
                    'pendidikan' => $pria['pendidikan'] ?? null,
                    'no_hp' => $pria['no_hp'] ?? null,
                    'email' => $pria['email'] ?? null,
                    'alamat' => $this->mergeAlamatWithRtRw($pria['alamat'] ?? null, $pria['rt'] ?? null, $pria['rw'] ?? null),
                    'rt' => $this->normalizeRtRw($pria['rt'] ?? null),
                    'rw' => $this->normalizeRtRw($pria['rw'] ?? null),
                    'nama_ayah' => $this->normalizePersonName($pria['nama_ayah'] ?? null)
                        ?: $this->normalizePersonName($bapak['nama'] ?? null),
                    'nama_ibu' => $this->normalizePersonName($pria['nama_ibu'] ?? null),
                    'status_kawin' => $pria['status_kawin'] ?? null,
                ]
            );

            // 2. Hubungkan Warga ke Registrasi (Role: Calon Pria)
            $registration->parties()->updateOrCreate(
                ['role' => 'calon_pria'],
                ['warga_id' => $warga->id, 'nama_sementara' => $pria['nama_lengkap'], 'desa_asal' => $pria['desa_asal'] ?? null, 'rt' => $this->normalizeRtRw($pria['rt'] ?? null), 'rw' => $this->normalizeRtRw($pria['rw'] ?? null)]
            );

            // 3. Simpan Data Bapak Calon Pria
            $registration->parents()->updateOrCreate(
                ['type' => 'bapak_pria'],
                [
                    'nama' => $this->normalizePersonName($bapak['nama']),
                    'nama_ayah_kandung' => $this->normalizePersonName($bapak['nama_ayah_kandung'] ?? $pria['nama_ayah_kandung'] ?? null),
                    'tgl_meninggal' => $bapak['tgl_meninggal'] ?? null,
                    'tempat_meninggal' => $bapak['tempat_meninggal'] ?? null,
                    'nik' => $bapak['nik'] ?? null,
                    'tempat_lahir' => $bapak['tempat_lahir'] ?? null,
                    'tgl_lahir' => $bapak['ttl_lahir'] ?? null,
                    'pekerjaan' => $bapak['pekerjaan'] ?? null,
                    'agama' => $bapak['agama'] ?? 'Islam',
                    'pendidikan' => $bapak['pendidikan'] ?? null,
                    'status_hubungan' => $bapak['status_hubungan'] ?? null,
                    'status_kawin_ortu' => $bapak['status_kawin_ortu'] ?? null,
                    'status_hidup' => $bapak['status_hidup'] ?? 'hidup',
                    'no_hp' => $bapak['no_hp'] ?? null,
                    'email' => $bapak['email'] ?? null,
                    'alamat' => $this->mergeAlamatWithRtRw($bapak['alamat'] ?? null, $bapak['rt'] ?? null, $bapak['rw'] ?? null),
                    'rt' => $this->normalizeRtRw($bapak['rt'] ?? null),
                    'rw' => $this->normalizeRtRw($bapak['rw'] ?? null),
                ]
            );

            // 4. Simpan Data Ibu Calon Pria
            $registration->parents()->updateOrCreate(
                ['type' => 'ibu_pria'],
                [
                    'nama' => $this->normalizePersonName($ibu['nama']),
                    'nama_ayah_kandung' => $this->normalizePersonName($ibu['nama_ayah_kandung'] ?? null),
                    'nik' => $ibu['nik'] ?? null,
                    'tempat_lahir' => $ibu['tempat_lahir'] ?? null,
                    'tgl_lahir' => $ibu['ttl_lahir'] ?? null,
                    'pekerjaan' => $ibu['pekerjaan'] ?? null,
                    'agama' => $ibu['agama'] ?? 'Islam',
                    'pendidikan' => $ibu['pendidikan'] ?? null,
                    'status_hubungan' => $ibu['status_hubungan'] ?? null,
                    'status_kawin_ortu' => $ibu['status_kawin_ortu'] ?? null,
                    'status_hidup' => $ibu['status_hidup'] ?? 'hidup',
                    'no_hp' => $ibu['no_hp'] ?? null,
                    'email' => $ibu['email'] ?? null,
                    'alamat' => $this->mergeAlamatWithRtRw($ibu['alamat'] ?? null, $ibu['rt'] ?? null, $ibu['rw'] ?? null),
                    'rt' => $this->normalizeRtRw($ibu['rt'] ?? null),
                    'rw' => $this->normalizeRtRw($ibu['rw'] ?? null),
                ]
            );

            // 5. Update Status & Progres Step Registrasi
            $this->updateRegistrationStep($registration, 2);
        });
    }

    /**
     * Logika Simpan Data Wanita & Orang Tua (Tahap 2)
     */
    public function simpanWanita(Registration $registration, array $data): void
    {
        DB::transaction(function () use ($registration, $data) {
            $wanita = $data['wanita'] ?? [];
            $bapak = $data['bapak'] ?? [];
            $ibu = $data['ibu'] ?? [];

            // 1. Simpan/Update Master Warga (Calon Wanita)
            $warga = Warga::updateOrCreate(
                ['nik' => $wanita['nik']],
                [
                    'nama_lengkap' => $wanita['nama_lengkap'],
                    'tempat_lahir' => $wanita['tempat_lahir'] ?? null,
                    'tgl_lahir' => $wanita['ttl_lahir'] ?? null,
                    'jenis_kelamin' => 'P',
                    'agama' => $wanita['agama'] ?? 'Islam',
                    'pekerjaan' => $wanita['pekerjaan'] ?? null,
                    'pendidikan' => $wanita['pendidikan'] ?? null,
                    'no_hp' => $wanita['no_hp'] ?? null,
                    'email' => $wanita['email'] ?? null,
                    'alamat' => $this->mergeAlamatWithRtRw($wanita['alamat'] ?? null, $wanita['rt'] ?? null, $wanita['rw'] ?? null),
                    'rt' => $this->normalizeRtRw($wanita['rt'] ?? null),
                    'rw' => $this->normalizeRtRw($wanita['rw'] ?? null),
                    'nama_ayah' => $this->normalizePersonName($wanita['nama_ayah'] ?? null)
                        ?: $this->normalizePersonName($bapak['nama'] ?? null),
                    'nama_ibu' => $this->normalizePersonName($wanita['nama_ibu'] ?? null),
                    'status_kawin' => $wanita['status_kawin'] ?? null,
                ]
            );

            // 2. Hubungkan Warga ke Registrasi (Role: Calon Wanita)
            $registration->parties()->updateOrCreate(
                ['role' => 'calon_wanita'],
                ['warga_id' => $warga->id, 'nama_sementara' => $wanita['nama_lengkap'], 'desa_asal' => $wanita['desa_asal'] ?? null, 'rt' => $this->normalizeRtRw($wanita['rt'] ?? null), 'rw' => $this->normalizeRtRw($wanita['rw'] ?? null)]
            );

            // 3. Simpan Data Bapak Wanita (type: bapak_wanita)
            $registration->parents()->updateOrCreate(
                ['type' => 'bapak_wanita'],
                [
                    'nama' => $this->normalizePersonName($bapak['nama']),
                    'nama_ayah_kandung' => $this->normalizePersonName($bapak['nama_ayah_kandung'] ?? $wanita['nama_ayah_kandung'] ?? null),
                    'tgl_meninggal' => $bapak['tgl_meninggal'] ?? null,
                    'tempat_meninggal' => $bapak['tempat_meninggal'] ?? null,
                    'nik' => $bapak['nik'] ?? null,
                    'tempat_lahir' => $bapak['tempat_lahir'] ?? null,
                    'tgl_lahir' => $bapak['ttl_lahir'] ?? null,
                    'pekerjaan' => $bapak['pekerjaan'] ?? null,
                    'agama' => $bapak['agama'] ?? 'Islam',
                    'pendidikan' => $bapak['pendidikan'] ?? null,
                    'status_hubungan' => $bapak['status_hubungan'] ?? null,
                    'status_kawin_ortu' => $bapak['status_kawin_ortu'] ?? null,
                    'status_hidup' => $bapak['status_hidup'] ?? 'hidup',
                    'no_hp' => $bapak['no_hp'] ?? null,
                    'email' => $bapak['email'] ?? null,
                    'alamat' => $this->mergeAlamatWithRtRw($bapak['alamat'] ?? null, $bapak['rt'] ?? null, $bapak['rw'] ?? null),
                    'rt' => $this->normalizeRtRw($bapak['rt'] ?? null),
                    'rw' => $this->normalizeRtRw($bapak['rw'] ?? null),
                ]
            );

            // 4. Simpan Data Ibu Wanita (type: ibu_wanita)
            $registration->parents()->updateOrCreate(
                ['type' => 'ibu_wanita'],
                [
                    'nama' => $this->normalizePersonName($ibu['nama']),
                    'nama_ayah_kandung' => $this->normalizePersonName($ibu['nama_ayah_kandung'] ?? null),
                    'nik' => $ibu['nik'] ?? null,
                    'tempat_lahir' => $ibu['tempat_lahir'] ?? null,
                    'tgl_lahir' => $ibu['ttl_lahir'] ?? null,
                    'pekerjaan' => $ibu['pekerjaan'] ?? null,
                    'agama' => $ibu['agama'] ?? 'Islam',
                    'pendidikan' => $ibu['pendidikan'] ?? null,
                    'status_hubungan' => $ibu['status_hubungan'] ?? null,
                    'status_kawin_ortu' => $ibu['status_kawin_ortu'] ?? null,
                    'status_hidup' => $ibu['status_hidup'] ?? 'hidup',
                    'no_hp' => $ibu['no_hp'] ?? null,
                    'email' => $ibu['email'] ?? null,
                    'alamat' => $this->mergeAlamatWithRtRw($ibu['alamat'] ?? null, $ibu['rt'] ?? null, $ibu['rw'] ?? null),
                    'rt' => $this->normalizeRtRw($ibu['rt'] ?? null),
                    'rw' => $this->normalizeRtRw($ibu['rw'] ?? null),
                ]
            );

            // 5. Update Status & Progres Step Registrasi
            $this->updateRegistrationStep($registration, 3);
        });
    }

    /**
     * Gabungkan alamat utama dengan RT/RW bila tersedia.
     */
    private function mergeAlamatWithRtRw(?string $alamat, mixed $rt = null, mixed $rw = null): ?string
    {
        $address = trim((string) ($alamat ?? ''));
        $rt = trim((string) ($rt ?? ''));
        $rw = trim((string) ($rw ?? ''));

        $parts = [];
        if ($address !== '') {
            $parts[] = $address;
        }

        $rtRw = [];
        if ($rt !== '') {
            $rtRw[] = 'RT '.$rt;
        }
        if ($rw !== '') {
            $rtRw[] = 'RW '.$rw;
        }

        if ($rtRw !== []) {
            $parts[] = implode(' / ', $rtRw);
        }

        if ($parts === []) {
            return null;
        }

        return implode(', ', $parts);
    }

    private function normalizeRtRw(mixed $value): ?string
    {
        $normalized = trim((string) ($value ?? ''));

        return $normalized === '' ? null : substr($normalized, 0, 3);
    }

    /**
     * Bersihkan awalan "bin"/"binti" dari nama agar database hanya menyimpan
     * nama asli (penulisan bin/binti dilakukan oleh template dokumen).
     */
    private function normalizePersonName(?string $name): ?string
    {
        $name = trim((string) $name);

        if ($name === '') {
            return null;
        }

        $cleaned = preg_replace('/^(bin|binti)\b\.?\s+/i', '', $name);

        return trim((string) $cleaned) ?: $name;
    }

    /**
     * Logika Simpan Data Wali Nikah & Saksi (Tahap 3)
     */
    public function simpanWaliSaksi(Registration $registration, array $data): void
    {
        DB::transaction(function () use ($registration, $data) {
            $jenisWali = $data['jenis_wali'];
            $inputWali = $data['wali'] ?? [];
            $isPengecualian = ! empty($inputWali['is_pengecualian_wali']);
            $alasanHakim = $inputWali['alasan_wali_hakim'] ?? null;

            // Jika toggle pengecualian dicentang, otomatis dialihkan ke Wali Hakim
            if ($isPengecualian) {
                $jenisWali = 'hakim';
            }

            $bapakWanita = $registration->parents()->where('type', 'bapak_wanita')->first();

            // Simpan riwayat kematian ke data bapak wanita jika ada input tanggal/tempat meninggal
            if (! empty($inputWali['tgl_meninggal']) || ! empty($inputWali['tempat_meninggal'])) {
                if ($bapakWanita) {
                    $bapakWanita->update([
                        'tgl_meninggal' => $inputWali['tgl_meninggal'] ?? $bapakWanita->tgl_meninggal,
                        'tempat_meninggal' => $inputWali['tempat_meninggal'] ?? $bapakWanita->tempat_meninggal,
                    ]);
                }
            }

            $waliAlamat = $this->mergeAlamatWithRtRw($inputWali['alamat'] ?? null, $inputWali['rt'] ?? null, $inputWali['rw'] ?? null);

            $isAyahKandung = ($jenisWali === 'nasab') && (($inputWali['hubungan'] ?? '') === 'Ayah Kandung') && ! $isPengecualian;

            if ($jenisWali === 'hakim') {
                $waliPayload = [
                    'jenis_wali' => 'hakim',
                    'hubungan' => 'Wali Hakim',
                    'nama_wali' => null,
                    'nik_wali' => null,
                    'tempat_lahir' => null,
                    'tgl_lahir' => null,
                    'pekerjaan' => null,
                    'agama' => null,
                    'pendidikan' => null,
                    'no_hp' => null,
                    'alamat' => null,
                    'rt' => null,
                    'rw' => null,
                    'tgl_meninggal' => $inputWali['tgl_meninggal'] ?? null,
                    'tempat_meninggal' => $inputWali['tempat_meninggal'] ?? null,
                    'is_pengecualian_wali' => $isPengecualian,
                    'alasan_wali_hakim' => $alasanHakim,
                ];
            } elseif ($isAyahKandung) {
                // Wali Nasab (Ayah Kandung): salin data dari Bapak Calon Wanita
                $waliPayload = [
                    'jenis_wali' => 'nasab',
                    'hubungan' => 'Ayah Kandung',
                    'nama_wali' => $bapakWanita?->nama ?? ($inputWali['nama'] ?? null),
                    'nik_wali' => $bapakWanita?->nik ?? ($inputWali['nik'] ?? null),
                    'tempat_lahir' => $bapakWanita?->tempat_lahir ?? ($inputWali['tempat_lahir'] ?? null),
                    'tgl_lahir' => $bapakWanita?->tgl_lahir ?? ($inputWali['ttl_lahir'] ?? null),
                    'pekerjaan' => $bapakWanita?->pekerjaan ?? ($inputWali['pekerjaan'] ?? null),
                    'agama' => $bapakWanita?->agama ?? ($inputWali['agama'] ?? null),
                    'pendidikan' => $bapakWanita?->pendidikan ?? ($inputWali['pendidikan'] ?? null),
                    'no_hp' => $bapakWanita?->no_hp ?? ($inputWali['no_hp'] ?? null),
                    'alamat' => $this->mergeAlamatWithRtRw($bapakWanita?->alamat ?? ($inputWali['alamat'] ?? null), $bapakWanita?->rt ?? ($inputWali['rt'] ?? null), $bapakWanita?->rw ?? ($inputWali['rw'] ?? null)),
                    'rt' => $this->normalizeRtRw($bapakWanita?->rt ?? ($inputWali['rt'] ?? null)),
                    'rw' => $this->normalizeRtRw($bapakWanita?->rw ?? ($inputWali['rw'] ?? null)),
                    'tgl_meninggal' => null,
                    'tempat_meninggal' => null,
                    'is_pengecualian_wali' => false,
                    'alasan_wali_hakim' => null,
                ];
            } else {
                // Wali Nasab lainnya (Kakek Kandung, Saudara, Paman, dll)
                $waliPayload = [
                    'jenis_wali' => 'nasab',
                    'hubungan' => $inputWali['hubungan'] ?? null,
                    'nama_wali' => $inputWali['nama'] ?? null,
                    'nik_wali' => $inputWali['nik'] ?? null,
                    'tempat_lahir' => $inputWali['tempat_lahir'] ?? null,
                    'tgl_lahir' => $inputWali['ttl_lahir'] ?? null,
                    'pekerjaan' => $inputWali['pekerjaan'] ?? null,
                    'agama' => $inputWali['agama'] ?? null,
                    'pendidikan' => $inputWali['pendidikan'] ?? null,
                    'no_hp' => $inputWali['no_hp'] ?? null,
                    'alamat' => $this->mergeAlamatWithRtRw($inputWali['alamat'] ?? null, $inputWali['rt'] ?? null, $inputWali['rw'] ?? null),
                    'rt' => $this->normalizeRtRw($inputWali['rt'] ?? null),
                    'rw' => $this->normalizeRtRw($inputWali['rw'] ?? null),
                    'tgl_meninggal' => $inputWali['tgl_meninggal'] ?? null,
                    'tempat_meninggal' => $inputWali['tempat_meninggal'] ?? null,
                    'is_pengecualian_wali' => false,
                    'alasan_wali_hakim' => null,
                ];
            }

            $wali = $registration->marriageWalis()->first();
            $wali ? $wali->update($waliPayload) : $registration->marriageWalis()->create($waliPayload);

            // Saksi 1 & 2 disimpan sebagai registration_parties
            foreach (['saksi_1', 'saksi_2'] as $role) {
                $this->simpanSaksi($registration, $role, $data[$role] ?? []);
            }

            // Update Status & Progres Step Registrasi
            $this->updateRegistrationStep($registration, 4);
        });
    }

    /**
     * Simpan satu saksi
     */
    private function simpanSaksi(Registration $registration, string $role, array $payload): void
    {
        $nama = trim((string) ($payload['nama'] ?? ''));
        $nik = $payload['nik'] ?? null;

        if ($nama === '') {
            $registration->parties()->where('role', $role)->delete();

            return;
        }

        if ($nik) {
            $warga = Warga::updateOrCreate(
                ['nik' => $nik],
                [
                    'nama_lengkap' => $nama,
                    'tempat_lahir' => $payload['tempat_lahir'] ?? null,
                    'tgl_lahir' => $payload['ttl_lahir'] ?? null,
                    'pekerjaan' => $payload['pekerjaan'] ?? null,
                    'pendidikan' => $payload['pendidikan'] ?? null,
                    'status_kawin' => $payload['status'] ?? null,
                    'no_hp' => $payload['no_hp'] ?? null,
                    'alamat' => $this->mergeAlamatWithRtRw($payload['alamat'] ?? null, $payload['rt'] ?? null, $payload['rw'] ?? null),
                    'rt' => $this->normalizeRtRw($payload['rt'] ?? null),
                    'rw' => $this->normalizeRtRw($payload['rw'] ?? null),
                ]
            );

            $registration->parties()->updateOrCreate(
                ['role' => $role],
                ['warga_id' => $warga->id, 'nama_sementara' => $nama, 'rt' => $this->normalizeRtRw($payload['rt'] ?? null), 'rw' => $this->normalizeRtRw($payload['rw'] ?? null)]
            );

            return;
        }

        $registration->parties()->updateOrCreate(
            ['role' => $role],
            ['warga_id' => null, 'nama_sementara' => $nama]
        );
    }

    /**
     * Logika Simpan Jadwal Akad & Mahar (Form Akad)
     */
    public function simpanAkad(Registration $registration, array $data): void
    {
        $dowryDisplay = $data['marriage_dowry_display'] ?? null;
        if (! $dowryDisplay) {
            $jenis = match ($data['jenis_mahar'] ?? '') {
                'uang' => 'Uang Tunai: Rp '.number_format((float) ($data['nominal_uang'] ?? 0), 0, ',', '.'),
                'emas' => 'Emas/Perhiasan: '.($data['rincian_mahar'] ?? '-'),
                'lainnya' => $data['rincian_mahar'] ?? '-',
                default => $data['rincian_mahar'] ?? '-',
            };
            $dowryDisplay = $jenis;
        }

        $registration->update([
            'tanggal_akad' => $data['tanggal_akad'],
            'jam_akad' => $data['jam_akad'],
            'marriage_dowry_display' => $dowryDisplay,
            'marriage_location_address' => $data['marriage_location_address'] ?? null,
            'province_id' => $data['province_id'] ?? null,
            'regency_id' => $data['regency_id'] ?? null,
            'district_id' => $data['district_id'] ?? null,
            'village_id' => $data['village_id'] ?? null,
        ]);

        // Update Status & Progres Step Registrasi
        $this->updateRegistrationStep($registration, 4);
    }

    /**
     * Hitung kelengkapan form dan sinkronkan kolom step dan status pada tabel registrations.
     */
    public function updateRegistrationStep(Registration $registration, int $minStep = 1): void
    {
        $partyPria = $registration->parties()->where('role', 'calon_pria')->first();
        $hasPria = $partyPria && ! empty($partyPria->warga_id);

        $partyWanita = $registration->parties()->where('role', 'calon_wanita')->first();
        $hasWanita = $partyWanita && ! empty($partyWanita->warga_id);

        $hasWali = $registration->marriageWalis()->exists();
        $hasAkad = ! empty($registration->tanggal_akad);

        $step = max($registration->step, $minStep);

        if ($hasPria && $hasWanita && $hasWali && $hasAkad) {
            $step = 4;
            $status = in_array($registration->status, ['valid', 'selesai']) ? $registration->status : 'lengkap';
        } else {
            $status = in_array($registration->status, ['valid', 'selesai']) ? $registration->status : 'draft';
        }

        $registration->update([
            'step' => $step,
            'status' => $status,
        ]);
    }

    /**
     * Cek apakah seorang warga terdaftar sebagai warga desa operator.
     *
     * Pencocokan dilakukan dengan membandingkan nama desa dari Setting
     * (`nama_desa`, setelah strip "Desa "/"Kelurahan ") dengan alamat warga.
     * Jika desa operator belum dikonfigurasi, tidak ada pembatasan (true).
     */
    public function wargaNegaraDesa(?Warga $warga): bool
    {
        $operatorDesa = trim((string) Setting::villageProfile()->village_name);

        // Belum ada konfigurasi desa -> tidak ada pembatasan pencocokan.
        if ($operatorDesa === '') {
            return true;
        }

        $stripLabel = static fn (string $s): string => trim(preg_replace('/^\s*(desa|kelurahan|desa\/kelurahan)\s+/i', '', $s));

        $desa = $stripLabel($operatorDesa);
        if ($desa === '') {
            return true;
        }

        $alamat = $stripLabel((string) ($warga?->alamat ?? ''));
        if ($alamat === '') {
            return false;
        }

        $desa = strtolower($desa);
        $alamat = strtolower($alamat);

        // Cocok jika nama desa muncul di dalam alamat (mis. "Jl. ..., Desa Uji")
        // atau alamat yang sudah distrip sama dengan nama desa.
        return str_contains($alamat, $desa)
            || str_contains($alamat, ' '.$desa)
            || $alamat === $desa;
    }

    /**
     * Tentukan "role" dokumen dari keanggotaan desa masing-masing pihak.
     *
     * @return array{role: 'pria'|'wanita'|'both'|'invalid', priaHere: bool, wanitaHere: bool, catinRole: 'PRIA'|'WANITA'}
     */
    public function roleDokumen(?Warga $pria, ?Warga $wanita, ?string $priaDesaAsal = null, ?string $wanitaDesaAsal = null, bool $manualSelection = false): array
    {
        // Layanan Andon tidak dapat dicocokkan otomatis dari desa asal, sehingga
        // operator memilih sendiri pihak yang mengajukan surat lewat ?role=.
        if ($manualSelection) {
            $catinRole = request()->query('role') === 'wanita' ? 'WANITA' : 'PRIA';

            return [
                'role' => 'both',
                'priaHere' => true,
                'wanitaHere' => true,
                'catinRole' => $catinRole,
            ];
        }

        $priaHere = $this->memberDiDesa($pria, $priaDesaAsal);
        $wanitaHere = $this->memberDiDesa($wanita, $wanitaDesaAsal);

        $role = ($priaHere && $wanitaHere)
            ? 'both'
            : ($priaHere ? 'pria' : ($wanitaHere ? 'wanita' : 'invalid'));

        // Pilihan eksplisit lewat query ?role= (dari dropdown "Pilih Format").
        $queryRole = request()->query('role');
        $catinRole = match (true) {
            $queryRole === 'pria' => 'PRIA',
            $queryRole === 'wanita' => 'WANITA',
            $role === 'wanita' => 'WANITA',
            default => 'PRIA', // pria | both | invalid -> default pria
        };

        return compact('role', 'priaHere', 'wanitaHere', 'catinRole');
    }

    /**
     * Apakah seorang warga/party termasuk warga desa operator (kop surat).
     *
     * Jika `desa_asal` partai tersedia dan terisi, lakukan pencocokan langsung
     * terhadap nama desa pada kop surat (operator). Jika kosong, gunakan
     * pemindaian alamat (`wargaNegaraDesa`) agar data lama tetap berfungsi.
     */
    public function memberDiDesa(?Warga $warga, ?string $desaAsal = null): bool
    {
        $kopDesa = trim((string) Setting::villageProfile()->village_name);

        // Belum ada konfigurasi desa operator -> tidak ada pembatasan pencocokan.
        if ($kopDesa === '') {
            return true;
        }

        if ($desaAsal !== null && $desaAsal !== '') {
            return $this->normalisasiNamaDesa($desaAsal) === $this->normalisasiNamaDesa($kopDesa);
        }

        // Fallback: pemindaian alamat (data lama).
        return $this->wargaNegaraDesa($warga);
    }

    /**
     * Normalisasi nama desa: trim, hapus prefix "Desa "/"Kelurahan "/"Desa/Kelurahan",
     * serta perbandingan tidak membedakan huruf besar/kecil.
     */
    private function normalisasiNamaDesa(string $desa): string
    {
        $s = trim($desa);
        $s = preg_replace('/^\s*(desa|kelurahan|desa\/kelurahan)\s+/i', '', $s);

        return strtolower(trim($s));
    }

    /**
     * Data dokumen (eager loading seluruh relasi) untuk semua template N1-N7.
     */
    public function documentData(Registration $registration): array
    {
        $registration->load([
            'parties' => fn ($query) => $query->with('warga'),
            'parents',
            'marriageWalis' => fn ($query) => $query->with('warga'),
        ]);

        $parties = $registration->parties->keyBy('role');
        $parents = $registration->parents->keyBy('type');

        $partyPria = $parties->get('calon_pria');
        $partyWanita = $parties->get('calon_wanita');

        $pria = $partyPria?->warga ?? $partyPria;
        $wanita = $partyWanita?->warga ?? $partyWanita;

        $roleInfo = $this->roleDokumen(
            $pria,
            $wanita,
            $partyPria?->desa_asal,
            $partyWanita?->desa_asal,
            $registration->service_type === 'andon',
        );
        $catinRole = $roleInfo['catinRole'];
        $catin = $catinRole === 'PRIA' ? $pria : $wanita;

        // Orang tua pihak utama (sesuai catinRole) dan pihak lainnya.
        $ayahUtama = $catinRole === 'PRIA' ? $parents->get('bapak_pria') : $parents->get('bapak_wanita');
        $ibuUtama = $catinRole === 'PRIA' ? $parents->get('ibu_pria') : $parents->get('ibu_wanita');
        $ayahLain = $catinRole === 'PRIA' ? $parents->get('bapak_wanita') : $parents->get('bapak_pria');
        $ibuLain = $catinRole === 'PRIA' ? $parents->get('ibu_wanita') : $parents->get('ibu_pria');

        // Logika N6: Butuh N6 jika Ayah Kandung Calon Wanita meninggal
        $bapakWanita = $parents->get('bapak_wanita');
        $butuhN6 = ($bapakWanita && $this->isMeninggal($bapakWanita->status_hidup));

        // Penandatangan N5: ayah hidup menandatangani; bila ayah meninggal, gunakan ibu.
        // Jika kedua orang tua satu sisi sudah meninggal, tidak ada penandatangan (N5 diblokir).
        $n5SignerPria = $this->parentSigner($parents->get('bapak_pria'), $parents->get('ibu_pria'));
        $n5SignerWanita = $this->parentSigner($parents->get('bapak_wanita'), $parents->get('ibu_wanita'));

        return [
            'registration' => $registration,
            'villageProfile' => Setting::villageProfile(),
            'catinPria' => $pria,
            'catinWanita' => $wanita,
            'catin' => $catin,
            'catinRole' => $catinRole,
            'role' => $roleInfo['role'],
            'priaHere' => $roleInfo['priaHere'],
            'wanitaHere' => $roleInfo['wanitaHere'],
            'ayahPria' => $parents->get('bapak_pria'),
            'ibuPria' => $parents->get('ibu_pria'),
            'ayahWanita' => $parents->get('bapak_wanita'),
            'ibuWanita' => $parents->get('ibu_wanita'),
            'ayahUtama' => $ayahUtama,
            'ibuUtama' => $ibuUtama,
            'ayahLain' => $ayahLain,
            'ibuLain' => $ibuLain,
            'guardian' => $registration->marriageWalis->first(),
            'saksi1' => $parties->get('saksi_1'),
            'saksi2' => $parties->get('saksi_2'),
            'n5SignerPria' => $n5SignerPria,
            'n5SignerWanita' => $n5SignerWanita,
            'n5SignerBlocked' => ! $n5SignerPria || ! $n5SignerWanita,
            'butuhN6' => $butuhN6,
        ];
    }

    /**
     * Apakah form N5 terblokir karena tidak ada penandatangan hidup (ayah maupun
     * ibu) di salah satu sisi calon pengantin.
     */
    public function n5SignerBlocked(Registration $registration): bool
    {
        $parents = $registration->parents->keyBy('type');
        $priaSigner = $this->parentSigner($parents->get('bapak_pria'), $parents->get('ibu_pria'));
        $wanitaSigner = $this->parentSigner($parents->get('bapak_wanita'), $parents->get('ibu_wanita'));

        return ! $priaSigner || ! $wanitaSigner;
    }

    /**
     * Cek apakah wali nikah adalah Wali Hakim (format KUA).
     * Jika ya, formulir Surat Pernyataan Wali (N7) dinonaktifkan karena
     * data wali hakim tidak boleh diedit/ucapan di ranah desa.
     */
    public function isWaliHakim(?Registration $registration): bool
    {
        $guardian = $registration->marriageWalis->first();
        if (! $guardian) {
            return false;
        }

        return $guardian->jenis_wali === 'hakim';
    }

    /**
     * Penandatangan N5 pada satu sisi: ayah hidup menandatangani; bila ayah
     * meninggal gunakan ibu. Jika keduanya meninggal, kembalikan null sehingga
     * N5 diblokir (tidak ada penandatangan yang hidup).
     */
    private function parentSigner(?RegistrationParent $ayah, ?RegistrationParent $ibu): ?RegistrationParent
    {
        return ($ayah && ! $this->isMeninggal($ayah->status_hidup))
            ? $ayah
            : ($ibu && ! $this->isMeninggal($ibu->status_hidup) ? $ibu : null);
    }

    private function isMeninggal(?string $status): bool
    {
        return in_array(strtolower(trim((string) $status)), ['meninggal', 'wafat'], true);
    }
}
