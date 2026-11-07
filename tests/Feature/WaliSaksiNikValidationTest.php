<?php

namespace Tests\Feature;

use App\Models\Registration;
use App\Models\Warga;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WaliSaksiNikValidationTest extends TestCase
{
    use RefreshDatabase;

    private Registration $registration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registration = Registration::create([
            'registration_number' => 'REG/001/2026',
            'service_type' => 'kantor',
            'step' => 3,
            'status' => 'draft',
        ]);

        $pria = Warga::create(['nik' => '1111111111110001', 'nama_lengkap' => 'Calon Pria']);
        $wanita = Warga::create(['nik' => '1111111111110002', 'nama_lengkap' => 'Calon Wanita']);

        $this->registration->parties()->create(['warga_id' => $pria->id, 'role' => 'calon_pria']);
        $this->registration->parties()->create(['warga_id' => $wanita->id, 'role' => 'calon_wanita']);

        $this->registration->parents()->create([
            'type' => 'bapak_wanita',
            'nama' => 'Budi Sanjaya',
            'nik' => '1111111111110003',
            'status_hidup' => 'hidup',
        ]);
    }

    private function payload(array $wali = [], array $saksi1 = [], array $saksi2 = []): array
    {
        return [
            'jenis_wali' => 'nasab',
            'wali' => array_merge(['hubungan' => 'Ayah Kandung'], $wali),
            'saksi_1' => array_merge(['nama' => 'Saksi Satu'], $saksi1),
            'saksi_2' => array_merge(['nama' => 'Saksi Dua'], $saksi2),
        ];
    }

    private function submit(array $payload)
    {
        return $this->post(route('registration.store-wali-saksi', $this->registration), $payload);
    }

    /** Saksi 1 dan Saksi 2 memakai NIK yang sama (dalam satu form). */
    public function test_saksi_satu_dan_dua_tidak_boleh_nik_sama(): void
    {
        $this->submit($this->payload([], ['nik' => '1111111111110011'], ['nik' => '1111111111110011']))
            ->assertSessionHasErrors('saksi_2.nik');

        $this->assertSame(0, $this->registration->parties()->whereIn('role', ['saksi_1', 'saksi_2'])->count());
    }

    /** Saksi memakai NIK calon pengantin pria. */
    public function test_saksi_tidak_boleh_memakai_nik_calon_pria(): void
    {
        $this->submit($this->payload([], ['nik' => '1111111111110001']))
            ->assertSessionHasErrors('saksi_1.nik');
    }

    /** Saksi memakai NIK calon pengantin wanita. */
    public function test_saksi_tidak_boleh_memakai_nik_calon_wanita(): void
    {
        $this->submit($this->payload([], [], ['nik' => '1111111111110002']))
            ->assertSessionHasErrors('saksi_2.nik');
    }

    /** Saksi memakai NIK orang tua (bapak calon wanita). */
    public function test_saksi_tidak_boleh_memakai_nik_bapak_wanita(): void
    {
        // Wali hakim agar NIK bapak tidak ikut dikecualikan sebagai wali.
        $this->submit([
            'jenis_wali' => 'hakim',
            'wali' => ['hubungan' => 'Wali Hakim', 'is_pengecualian_wali' => '1', 'alasan_wali_hakim' => 'Ayah menolak (Adhol)'],
            'saksi_1' => ['nama' => 'Saksi Satu', 'nik' => '1111111111110003'],
            'saksi_2' => ['nama' => 'Saksi Dua'],
        ])->assertSessionHasErrors('saksi_1.nik');
    }

    /** Saksi memakai NIK wali nikah (wali non-ayah). */
    public function test_saksi_tidak_boleh_memakai_nik_wali(): void
    {
        $this->submit($this->payload(
            ['hubungan' => 'Kakek Kandung', 'nama' => 'Kakek', 'nik' => '1111111111110044'],
            ['nik' => '1111111111110044']
        ))->assertSessionHasErrors('saksi_1.nik');
    }

    /** Wali dan saksi memakai NIK yang sama. */
    public function test_wali_dan_saksi_tidak_boleh_nik_sama(): void
    {
        $this->submit($this->payload(
            ['hubungan' => 'Kakek Kandung', 'nama' => 'Kakek', 'nik' => '1111111111110045'],
            [],
            ['nik' => '1111111111110045']
        ))->assertSessionHasErrors('saksi_2.nik');
    }

    /** Tukar NIK antar saksi (saksi 1 ambil NIK lama saksi 2, dan sebaliknya) harus boleh. */
    public function test_tukar_nik_antar_saksi_tidak_dianggap_duplikat(): void
    {
        $this->submit($this->payload([], ['nik' => '1111111111110011'], ['nik' => '1111111111110012']))
            ->assertSessionHasNoErrors();

        $this->submit($this->payload([], ['nik' => '1111111111110012'], ['nik' => '1111111111110011']))
            ->assertSessionHasNoErrors();

        $this->assertSame('1111111111110012', $this->saksiNik('saksi_1'));
        $this->assertSame('1111111111110011', $this->saksiNik('saksi_2'));
    }

    /** Menyimpan ulang saksi yang sama (tanpa perubahan) tidak boleh dianggap duplikat. */
    public function test_simpan_ulang_saksi_yang_sama_tidak_dianggap_duplikat(): void
    {
        $payload = $this->payload([], ['nik' => '1111111111110011'], ['nik' => '1111111111110012']);

        $this->submit($payload)->assertSessionHasNoErrors();
        $this->submit($payload)->assertSessionHasNoErrors();

        $this->assertSame(2, $this->registration->parties()->whereIn('role', ['saksi_1', 'saksi_2'])->count());
    }

    /** Reset sesi error lalu simpan ulang saksi tanpa nama: NIK-nya tidak dianggap bentrok. */
    public function test_saksi_yang_dikosongkan_niknya_boleh_dipakai_saksi_lain(): void
    {
        $this->submit($this->payload([], ['nik' => '1111111111110011']))->assertSessionHasNoErrors();

        $this->submit($this->payload(
            [],
            ['nama' => '', 'nik' => '1111111111110011'],
            ['nik' => '1111111111110011']
        ))->assertSessionHasNoErrors();

        $this->assertSame(0, $this->registration->parties()->where('role', 'saksi_1')->count());
        $this->assertSame('1111111111110011', $this->saksiNik('saksi_2'));
    }

    private function saksiNik(string $role): ?string
    {
        return $this->registration->parties()->where('role', $role)->with('warga')->first()?->warga?->nik;
    }
}
