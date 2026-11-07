<?php

namespace App\Http\Requests;

use App\Models\Registration;
use App\Models\RegistrationParty;
use App\Models\Warga;
use Illuminate\Foundation\Http\FormRequest;

class StorePriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // ================= DATA CALON PRIA =================
            'pria.nik' => 'required|string|size:16',
            'pria.nama_lengkap' => 'required|string|max:255',
            'pria.tempat_lahir' => 'nullable|string|max:255',
            'pria.ttl_lahir' => 'nullable|date',
            'pria.pekerjaan' => 'nullable|string|max:100',
            'pria.agama' => 'nullable|string|max:50',
            'pria.pendidikan' => 'nullable|string|max:50',
            'pria.status_kawin' => 'nullable|string|max:50',
            'pria.no_hp' => 'nullable|string|max:20',
            'pria.email' => 'nullable|email|max:255',
            'pria.alamat' => 'nullable|string|max:500',
            'pria.nama_ayah' => 'nullable|string|max:255',
            'pria.nama_ibu' => 'nullable|string|max:255',
            'pria.desa_asal' => 'nullable|string|max:150',
            'pria.status_sebelumnya' => 'nullable|string|in:cerai_mati,cerai_hidup,poligami',
            'pria.verifikasi_dokumen' => 'nullable',
            'pria.nama_ayah_kandung' => 'nullable|string|max:255',
            // ================= DATA BAPAK =================
            'bapak.nama' => 'required|string|max:255',
            'bapak.nama_ayah_kandung' => 'nullable|string|max:255',
            'bapak.tgl_meninggal' => 'nullable|date',
            'bapak.tempat_meninggal' => 'nullable|string|max:255',
            'bapak.nik' => 'nullable|string|size:16',
            'bapak.tempat_lahir' => 'nullable|string|max:255',
            'bapak.ttl_lahir' => 'nullable|date',
            'bapak.pekerjaan' => 'nullable|string|max:100',
            'bapak.agama' => 'nullable|string|max:50',
            'bapak.pendidikan' => 'nullable|string|max:50',
            'bapak.status_hubungan' => 'nullable|string|max:50',
            'bapak.status_kawin_ortu' => 'nullable|string|in:Kawin,Cerai Mati,Cerai Hidup',
            'bapak.status_hidup' => 'required|string|max:20',
            'bapak.no_hp' => 'nullable|string|max:20',
            'bapak.email' => 'nullable|email|max:255',
            'bapak.alamat' => 'nullable|string|max:500',

            // ================= DATA IBU =================
            'ibu.nama' => 'required|string|max:255',
            'ibu.nama_ayah_kandung' => 'nullable|string|max:255',
            'ibu.nik' => 'nullable|string|size:16',
            'ibu.tempat_lahir' => 'nullable|string|max:255',
            'ibu.ttl_lahir' => 'nullable|date',
            'ibu.pekerjaan' => 'nullable|string|max:100',
            'ibu.agama' => 'nullable|string|max:50',
            'ibu.pendidikan' => 'nullable|string|max:50',
            'ibu.status_hubungan' => 'nullable|string|max:50',
            'ibu.status_kawin_ortu' => 'nullable|string|in:Kawin,Cerai Mati,Cerai Hidup',
            'ibu.status_hidup' => 'required|string|max:20',
            'ibu.no_hp' => 'nullable|string|max:20',
            'ibu.email' => 'nullable|email|max:255',
            'ibu.alamat' => 'nullable|string|max:500',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $registration = $this->route('registration');
            if ($registration instanceof Registration) {
                $priaParty = $registration->parties()->where('role', 'calon_pria')->first();
                $excludedPartyId = $priaParty ? $priaParty->id : null;

                $nikPria = $this->input('pria.nik');
                $nikBapak = $this->input('bapak.nik');
                $nikIbu = $this->input('ibu.nik');

                if ($nikPria && RegistrationParty::isNikUsedInRegistration($registration->id, $nikPria, [], $excludedPartyId)) {
                    $v->errors()->add('pria.nik', 'NIK Calon Pria sudah digunakan.');
                }
                if ($nikBapak && ($nikBapak === $nikPria || RegistrationParty::isNikUsedInRegistration($registration->id, $nikBapak, [], $excludedPartyId, ['bapak_pria']))) {
                    $v->errors()->add('bapak.nik', 'NIK Bapak tidak boleh sama dengan pihak lain dalam pendaftaran ini.');
                }
                if ($nikIbu && ($nikIbu === $nikPria || $nikIbu === $nikBapak || RegistrationParty::isNikUsedInRegistration($registration->id, $nikIbu, [], $excludedPartyId, ['ibu_pria']))) {
                    $v->errors()->add('ibu.nik', 'NIK Ibu tidak boleh sama dengan pihak lain dalam pendaftaran ini.');
                }

                // NIK wali nikah tersimpan di marriage_walis, bukan di registration_parents,
                // sehingga tidak ikut terperiksa oleh isNikUsedInRegistration().
                $waliNik = $this->waliNikahNik($registration);

                if ($waliNik && $waliNik === $nikPria) {
                    $v->errors()->add('pria.nik', 'NIK Calon Pria sudah digunakan.');
                }
                if ($waliNik && $waliNik === $nikBapak) {
                    $v->errors()->add('bapak.nik', 'NIK Bapak tidak boleh sama dengan pihak lain dalam pendaftaran ini.');
                }
                if ($waliNik && $waliNik === $nikIbu) {
                    $v->errors()->add('ibu.nik', 'NIK Ibu tidak boleh sama dengan pihak lain dalam pendaftaran ini.');
                }
            }
            $this->validateVerifikasiDokumen($v);
        });
    }

    /**
     * NIK wali nikah dari marriage_walis. Wali yang menyalin data Ayah Kandung
     * sengaja diabaikan karena NIK-nya sudah terwakili baris bapak_wanita.
     */
    protected function waliNikahNik(Registration $registration): ?string
    {
        $wali = $registration->marriageWalis()->first();

        if (! $wali || ! $wali->nik_wali) {
            return null;
        }

        if ($wali->jenis_wali === 'nasab' && $wali->hubungan === 'Ayah Kandung') {
            return null;
        }

        return $wali->nik_wali;
    }

    protected function validateVerifikasiDokumen($v): void
    {
        $statusKawin = strtolower((string) $this->input('pria.status_kawin', ''));
        $requiresVerification = in_array($statusKawin, ['duda', 'janda', 'cerai'])
            || $this->punyaRiwayatPernikahanLain($this->input('pria.nik'));

        if ($requiresVerification) {
            if (empty($this->input('pria.status_sebelumnya'))) {
                $v->errors()->add('pria.status_sebelumnya', 'Status pernikahan sebelumnya wajib dipilih.');
            }
            if (! $this->boolean('pria.verifikasi_dokumen')) {
                $v->errors()->add('pria.verifikasi_dokumen', $this->messages()['pria.verifikasi_dokumen.required']);
            }
        }
    }

    /**
     * Riwayat "pernah menikah sebelumnya": NIK sudah tercatat sebagai pihak
     * pada pendaftaran lain. Pendaftaran berjalan sendiri tidak dihitung,
     * agar mode edit tidak selalu memicu validasi verifikasi dokumen.
     */
    protected function punyaRiwayatPernikahanLain(mixed $nik): bool
    {
        if (! $nik) {
            return false;
        }

        $warga = Warga::where('nik', $nik)->first();
        if (! $warga) {
            return false;
        }

        $parties = RegistrationParty::where('warga_id', $warga->id);

        $registration = $this->route('registration');
        if ($registration instanceof Registration) {
            $parties->where('registration_id', '!=', $registration->id);
        }

        return $parties->exists();
    }

    public function messages(): array
    {
        return [
            'pria.verifikasi_dokumen.required' => 'Verifikasi Akta Cerai / Akta Kematian (Fisik) wajib dicentang untuk calon berstatus Duda/Janda atau pernah menikah sebelumnya.',
            'pria.verifikasi_dokumen.accepted' => 'Verifikasi Akta Cerai / Akta Kematian (Fisik) wajib dicentang untuk calon berstatus Duda/Janda atau pernah menikah sebelumnya.',
        ];
    }
}
