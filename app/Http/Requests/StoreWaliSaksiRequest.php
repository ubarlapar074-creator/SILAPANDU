<?php

namespace App\Http\Requests;

use App\Models\Registration;
use App\Models\RegistrationParty;
use Illuminate\Foundation\Http\FormRequest;

class StoreWaliSaksiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'jenis_wali' => 'required|in:nasab,hakim',

            // ================= DATA WALI =================
            'wali.hubungan' => 'nullable|string|max:50',
            'wali.nik' => 'nullable|string|size:16',
            'wali.nama' => 'nullable|string|max:255',
            'wali.tempat_lahir' => 'nullable|string|max:255',
            'wali.ttl_lahir' => 'nullable|date',
            'wali.pekerjaan' => 'nullable|string|max:100',
            'wali.agama' => 'nullable|string|max:50',
            'wali.pendidikan' => 'nullable|string|max:50',
            'wali.no_hp' => 'nullable|string|max:20',
            'wali.alamat' => 'nullable|string|max:500',
            'wali.is_pengecualian_wali' => 'nullable|boolean',
            'wali.alasan_wali_hakim' => 'nullable|string|max:255',
            'wali.tgl_meninggal' => 'nullable|date',
            'wali.tempat_meninggal' => 'nullable|string|max:255',

            // ================= DATA SAKSI 1 & 2 =================
            'saksi_1.nik' => 'nullable|string|size:16',
            'saksi_1.nama' => 'nullable|string|max:255',
            'saksi_1.tempat_lahir' => 'nullable|string|max:255',
            'saksi_1.ttl_lahir' => 'nullable|date',
            'saksi_1.pekerjaan' => 'nullable|string|max:100',
            'saksi_1.pendidikan' => 'nullable|string|max:50',
            'saksi_1.status' => 'nullable|string|max:50',
            'saksi_1.no_hp' => 'nullable|string|max:20',
            'saksi_1.alamat' => 'nullable|string|max:500',

            'saksi_2.nik' => 'nullable|string|size:16',
            'saksi_2.nama' => 'nullable|string|max:255',
            'saksi_2.tempat_lahir' => 'nullable|string|max:255',
            'saksi_2.ttl_lahir' => 'nullable|date',
            'saksi_2.pekerjaan' => 'nullable|string|max:100',
            'saksi_2.pendidikan' => 'nullable|string|max:50',
            'saksi_2.status' => 'nullable|string|max:50',
            'saksi_2.no_hp' => 'nullable|string|max:20',
            'saksi_2.alamat' => 'nullable|string|max:500',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            // Mengumpulkan semua NIK yang akan disimpan oleh formulir ini
            $niks = [
                'Wali' => $this->input('wali.nik') ?: null,
                'Saksi 1' => $this->saksiNik('saksi_1'),
                'Saksi 2' => $this->saksiNik('saksi_2'),
            ];

            // Cek duplikasi NIK antar field pada formulir ini (wali, saksi 1, saksi 2)
            $seen = [];
            foreach ($niks as $label => $nik) {
                if (! $nik) {
                    continue;
                }

                if (isset($seen[$nik])) {
                    $v->errors()->add(
                        strtolower(str_replace(' ', '_', $label)).'.nik',
                        'NIK Wali, Saksi 1, dan Saksi 2 tidak boleh sama dalam satu sesi pendaftaran.'
                    );

                    continue;
                }

                $seen[$nik] = $label;
            }

            // Cek duplikasi dengan pihak lain di pendaftaran yang sama (Catin Pria/Wanita & orang tua)
            $registration = $this->route('registration');
            if ($registration instanceof Registration) {
                // Baris saksi lama tidak dihitung karena formulir ini selalu mengirim ulang
                // Saksi 1 & 2 sekaligus (NIK antar saksi boleh ditukar). Duplikat antar saksi
                // sudah diperiksa dari nilai yang dikirim di atas.
                $excludedRoles = ['saksi_1', 'saksi_2'];

                // Wali Ayah Kandung memang disalin dari data Bapak Calon Wanita,
                // jadi baris orang tua bapak_wanita harus dikecualikan.
                $isWaliAyahKandung = $this->input('jenis_wali') === 'nasab'
                    && $this->input('wali.hubungan') === 'Ayah Kandung'
                    && ! $this->boolean('wali.is_pengecualian_wali');

                foreach ($niks as $label => $nik) {
                    if (! $nik) {
                        continue;
                    }

                    $excludedParentTypes = $label === 'Wali' && $isWaliAyahKandung ? ['bapak_wanita'] : [];

                    if (RegistrationParty::isNikUsedInRegistration($registration->id, $nik, $excludedRoles, null, $excludedParentTypes)) {
                        $fieldKey = strtolower(str_replace(' ', '_', $label)).'.nik';
                        $v->errors()->add($fieldKey, "NIK {$label} sudah digunakan oleh pihak lain dalam pendaftaran ini.");
                    }
                }
            }

            // Validasi Khusus Wali
            $hubungan = $this->input('wali.hubungan');
            $jenisWali = $this->input('jenis_wali');

            // Larangan Ayah Sambung menjadi wali
            if ($hubungan === 'Ayah Sambung') {
                $v->errors()->add('wali.hubungan', 'Ayah Sambung tidak berhak menjadi wali. Silakan pilih wali lain.');
            }

            // Jika toggle pengecualian dicentang, wajib ada alasan
            $isPengecualian = $this->boolean('wali.is_pengecualian_wali');
            if ($isPengecualian && empty($this->input('wali.alasan_wali_hakim'))) {
                $v->errors()->add('wali.alasan_wali_hakim', 'Alasan wali hakim wajib dipilih jika status pengecualian hukum dicentang.');
            }
        });
    }

    /**
     * NIK saksi yang akan disimpan: saksi tanpa nama akan dihapus saat
     * penyimpanan, sehingga NIK-nya tidak perlu ikut diperiksa duplikatnya.
     */
    private function saksiNik(string $role): ?string
    {
        if (trim((string) $this->input($role.'.nama', '')) === '') {
            return null;
        }

        return $this->input($role.'.nik') ?: null;
    }

    public function messages(): array
    {
        return [
            'wali.hubungan.required_if' => 'Hubungan perwalian wajib dipilih untuk Wali Nasab.',
        ];
    }
}
