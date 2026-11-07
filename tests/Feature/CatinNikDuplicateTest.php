<?php

namespace Tests\Feature;

use App\Models\Registration;
use App\Models\Warga;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatinNikDuplicateTest extends TestCase
{
    use RefreshDatabase;

    private Registration $registration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registration = Registration::create([
            'registration_number' => 'REG/002/2026',
            'service_type' => 'kantor',
            'step' => 2,
            'status' => 'draft',
        ]);
    }

    private function priaPayload(array $pria = [], array $bapak = [], array $ibu = []): array
    {
        return [
            'pria' => array_merge(['nik' => '1111111111110001', 'nama_lengkap' => 'Calon Pria'], $pria),
            'bapak' => array_merge(['nama' => 'Bapak Pria', 'status_hidup' => 'hidup'], $bapak),
            'ibu' => array_merge(['nama' => 'Ibu Pria', 'status_hidup' => 'hidup'], $ibu),
        ];
    }

    private function wanitaPayload(array $wanita = [], array $bapak = [], array $ibu = []): array
    {
        return [
            'wanita' => array_merge(['nik' => '1111111111110002', 'nama_lengkap' => 'Calon Wanita'], $wanita),
            'bapak' => array_merge(['nama' => 'Bapak Wanita', 'status_hidup' => 'hidup'], $bapak),
            'ibu' => array_merge(['nama' => 'Ibu Wanita', 'status_hidup' => 'hidup'], $ibu),
        ];
    }

    private function submitPria(array $payload)
    {
        return $this->post(route('registration.store-pria', $this->registration), $payload);
    }

    private function submitWanita(array $payload)
    {
        return $this->post(route('registration.store-wanita', $this->registration), $payload);
    }

    private function waliNikahDenganNik(string $nik): void
    {
        $this->registration->marriageWalis()->create([
            'jenis_wali' => 'nasab',
            'hubungan' => 'Kakek Kandung',
            'nama_wali' => 'Kakek',
            'nik_wali' => $nik,
        ]);
    }

    // ================= CALON PRIA =================

    public function test_bapak_pria_tidak_boleh_sama_dengan_pria(): void
    {
        $this->submitPria($this->priaPayload([], ['nik' => '1111111111110001']))
            ->assertSessionHasErrors('bapak.nik');
    }

    public function test_ibu_pria_tidak_boleh_sama_dengan_pria(): void
    {
        $this->submitPria($this->priaPayload([], [], ['nik' => '1111111111110001']))
            ->assertSessionHasErrors('ibu.nik');
    }

    public function test_bapak_dan_ibu_pria_tidak_boleh_nik_sama(): void
    {
        $this->submitPria($this->priaPayload([], ['nik' => '1111111111110010'], ['nik' => '1111111111110010']))
            ->assertSessionHasErrors('ibu.nik');
    }

    public function test_pria_tidak_boleh_memakai_nik_bapak_wanita_yang_tersimpan(): void
    {
        $this->registration->parents()->create([
            'type' => 'bapak_wanita',
            'nama' => 'Bapak Wanita',
            'nik' => '1111111111110003',
            'status_hidup' => 'hidup',
        ]);

        $this->submitPria($this->priaPayload(['nik' => '1111111111110003']))
            ->assertSessionHasErrors('pria.nik');
    }

    public function test_pria_tidak_boleh_memakai_nik_saksi_yang_tersimpan(): void
    {
        $saksi = Warga::create(['nik' => '1111111111110004', 'nama_lengkap' => 'Saksi']);
        $this->registration->parties()->create(['warga_id' => $saksi->id, 'role' => 'saksi_1']);

        $this->submitPria($this->priaPayload(['nik' => '1111111111110004']))
            ->assertSessionHasErrors('pria.nik');
    }

    public function test_pria_tidak_boleh_memakai_nik_wali(): void
    {
        $this->waliNikahDenganNik('1111111111110005');

        $this->submitPria($this->priaPayload(['nik' => '1111111111110005']))
            ->assertSessionHasErrors('pria.nik');
    }

    public function test_bapak_pria_tidak_boleh_memakai_nik_wali(): void
    {
        $this->waliNikahDenganNik('1111111111110006');

        $this->submitPria($this->priaPayload([], ['nik' => '1111111111110006']))
            ->assertSessionHasErrors('bapak.nik');
    }

    public function test_ibu_pria_tidak_boleh_memakai_nik_wali(): void
    {
        $this->waliNikahDenganNik('1111111111110007');

        $this->submitPria($this->priaPayload([], [], ['nik' => '1111111111110007']))
            ->assertSessionHasErrors('ibu.nik');
    }

    public function test_simpan_ulang_data_pria_yang_sama_tidak_dianggap_duplikat(): void
    {
        $payload = $this->priaPayload([], ['nik' => '1111111111110011'], ['nik' => '1111111111110012']);

        $this->submitPria($payload)->assertSessionHasNoErrors();
        $this->submitPria($payload)->assertSessionHasNoErrors();
    }

    // ================= CALON WANITA =================

    public function test_wanita_tidak_boleh_memakai_nik_wali(): void
    {
        $this->waliNikahDenganNik('1111111111110008');

        $this->submitWanita($this->wanitaPayload(['nik' => '1111111111110008']))
            ->assertSessionHasErrors('wanita.nik');
    }

    public function test_ibu_wanita_tidak_boleh_memakai_nik_wali(): void
    {
        $this->waliNikahDenganNik('1111111111110009');

        $this->submitWanita($this->wanitaPayload([], [], ['nik' => '1111111111110009']))
            ->assertSessionHasErrors('ibu.nik');
    }

    /** Wali hasil salinan Ayah Kandung memang berbagi NIK dengan bapak calon wanita. */
    public function test_wali_salinan_ayah_kandung_tidak_menghalangi_bapak_wanita(): void
    {
        $this->registration->parents()->create([
            'type' => 'bapak_wanita',
            'nama' => 'Bapak Wanita',
            'nik' => '1111111111110013',
            'status_hidup' => 'hidup',
        ]);

        $this->registration->marriageWalis()->create([
            'jenis_wali' => 'nasab',
            'hubungan' => 'Ayah Kandung',
            'nama_wali' => 'Bapak Wanita',
            'nik_wali' => '1111111111110013',
        ]);

        $this->submitWanita($this->wanitaPayload([], ['nik' => '1111111111110013']))
            ->assertSessionHasNoErrors();
    }
}
