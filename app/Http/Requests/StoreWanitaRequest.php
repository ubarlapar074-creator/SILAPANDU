<?php

namespace App\Http\Requests;

use App\Models\Registration;
use App\Models\RegistrationParty;
use App\Models\Warga;
use Illuminate\Foundation\Http\FormRequest;

class StoreWanitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // ================= DATA CALON WANITA =================
            'wanita.nik' => 'required|string|size:16',
            'wanita.nama_lengkap' => 'required|string|max:255',
            'wanita.tempat_lahir' => 'nullable|string|max:255',
            'wanita.ttl_lahir' => 'nullable|date',
            'wanita.pekerjaan' => 'nullable|string|max:100',
            'wanita.agama' => 'nullable|string|max:50',
            'wanita.pendidikan' => 'nullable|string|max:50',
            'wanita.status_kawin' => 'nullable|string|max:50',
            'wanita.no_hp' => 'nullable|string|max:20',
            'wanita.email' => 'nullable|email|max:255',
            'wanita.alamat' => 'nullable|string|max:500',
            'wanita.nama_ayah' => 'nullable|string|max:255',
            'wanita.nama_ibu' => 'nullable|string|max:255',
            'wanita.desa_asal' => 'nullable|string|max:150',
            'wanita.nama_ayah_kandung' => 'nullable|string|max:255',
            'wanita.status_sebelumnya' => 'nullable|string|in:cerai_mati,cerai_hidup,poligami',
            'wanita.verifikasi_dokumen' => 'nullable',

            // ================= DATA BAPAK =================
            'bapak.nama' => 'required|string|max:255',
            'bapak.nama_ayah_kandung' => 'nullable|string|max:255',
            'bapak.tgl_meninggal' => 'nullable|date',
            'bapak.tempat_meninggal' => 'nullable|string|max:255',
            'bapak.nik' => 'nullable|string|size:16',
            'bapak.tempat_lahir' => 'nullable|string|max:255',
            'bapak.ttl_lahir' => 'nullable|date',
            'bapak.pekerjaan' => 'nullable|string|max:100',
            'bapak.pendidikan' => 'nullable|string|max:50',
            'bapak.agama' => 'nullable|string|max:50',
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
            'ibu.pendidikan' => 'nullable|string|max:50',
            'ibu.agama' => 'nullable|string|max:50',
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
                $wanitaParty = $registration->parties()->where('role', 'calon_wanita')->first();
                $excludedPartyId = $wanitaParty ? $wanitaParty->id : null;

                $nikWanita = $this->input('wanita.nik');
                $nikBapak = $this->input('bapak.nik');
                $nikIbu = $this->input('ibu.nik');

                if ($nikWanita && RegistrationParty::isNikUsedInRegistration($registration->id, $nikWanita, [], $excludedPartyId)) {
                    $v->errors()->add('wanita.nik', 'NIK Calon Wanita sudah digunakan.');
                }
                if ($nikBapak && ($nikBapak === $nikWanita || RegistrationParty::isNikUsedInRegistration($registration->id, $nikBapak, [], $excludedPartyId, ['bapak_wanita']))) {
                    $v->errors()->add('bapak.nik', 'NIK Bapak tidak boleh sama dengan pihak lain dalam pendaftaran ini.');
                }
                if ($nikIbu && ($nikIbu === $nikWanita || $nikIbu === $nikBapak || RegistrationParty::isNikUsedInRegistration($registration->id, $nikIbu, [], $excludedPartyId, ['ibu_wanita']))) {
                    $v->errors()->add('ibu.nik', 'NIK Ibu tidak boleh sama dengan pihak lain dalam pendaftaran ini.');
                }

                // NIK wali nikah tersimpan di marriage_walis, bukan di registration_parents,
                // sehingga tidak ikut terperiksa oleh isNikUsedInRegistration().
                $waliNik = $this->waliNikahNik($registration);

                if ($waliNik && $waliNik === $nikWanita) {
                    $v->errors()->add('wanita.nik', 'NIK Calon Wanita sudah digunakan.');
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
        $statusKawin = strtolower((string) $this->input('wanita.status_kawin', ''));
        $requiresVerification = in_array($statusKawin, ['duda', 'janda', 'cerai'])
            || $this->punyaRiwayatPernikahanLain($this->input('wanita.nik'));

        if ($requiresVerification) {
            if (empty($this->input('wanita.status_sebelumnya'))) {
                $v->errors()->add('wanita.status_sebelumnya', 'Status pernikahan sebelumnya wajib dipilih.');
            }
            if (! $this->boolean('wanita.verifikasi_dokumen')) {
                $v->errors()->add('wanita.verifikasi_dokumen', $this->messages()['wanita.verifikasi_dokumen.required']);
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
            'wanita.verifikasi_dokumen.required' => 'Verifikasi Akta Cerai / Akta Kematian (Fisik) wajib dicentang untuk calon berstatus Duda/Janda atau pernah menikah sebelumnya.',
            'wanita.verifikasi_dokumen.accepted' => 'Verifikasi Akta Cerai / Akta Kematian (Fisik) wajib dicentang untuk calon berstatus Duda/Janda atau pernah menikah sebelumnya.',
        ];
    }
}
