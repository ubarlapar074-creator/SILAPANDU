<?php

namespace Tests\Feature;

use App\Models\Registration;
use App\Services\RegistrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NomorSuratFormatTest extends TestCase
{
    use RefreshDatabase;

    private function buatPendaftaran(string $createdAt, string $nomorLama = 'LAMA/DS/001/XI/2026'): Registration
    {
        $registration = Registration::create([
            'registration_number' => 'REG/'.$createdAt,
            'nomor_surat' => $nomorLama,
            'service_type' => 'kantor',
            'step' => 1,
            'status' => 'draft',
            // Bukan draft kosong, agar tidak dibersihkan saat dashboard/riwayat dibuka.
            'tanggal_akad' => '2026-12-01',
        ]);

        $registration->forceFill(['created_at' => $createdAt])->saveQuietly();

        return $registration;
    }

    private function simpanPenomoran(array $data): void
    {
        $this->post(route('settings.update'), $data)
            ->assertRedirect(route('settings.index'));
    }

    public function test_format_bebas_dipakai_untuk_seluruh_nomor_surat(): void
    {
        $oktober = $this->buatPendaftaran('2026-10-15 08:00:00');
        $november = $this->buatPendaftaran('2026-11-02 08:00:00');

        $this->simpanPenomoran(['format_nomor_surat' => '475/DS/{NO}/{BLN}/{THN}']);

        $this->assertSame('475/DS/001/X/2026', $oktober->fresh()->nomor_surat);
        $this->assertSame('475/DS/002/XI/2026', $november->fresh()->nomor_surat);
    }

    public function test_nomor_baru_tampil_di_dashboard_riwayat_dan_surat(): void
    {
        $registration = $this->buatPendaftaran('2026-11-02 08:00:00');

        $this->simpanPenomoran(['format_nomor_surat' => '475/DS/{NO}/{BLN}/{THN}']);

        $this->get(route('dashboard'))->assertOk()->assertSee('475/DS/001/XI/2026');
        $this->get(route('registration.index'))->assertOk()->assertSee('475/DS/001/XI/2026');

        $html = view('documents.templates.n1', app(RegistrationService::class)->documentData($registration->fresh()))->render();
        $this->assertStringContainsString('Nomor : 475/DS/001/XI/2026', $html);
    }

    public function test_format_kosong_memakai_format_bawaan(): void
    {
        $registration = $this->buatPendaftaran('2026-11-02 08:00:00');

        $this->simpanPenomoran(['format_nomor_surat' => '']);

        $this->assertSame('001/DS/XI/2026', $registration->fresh()->nomor_surat);
    }

    public function test_nomor_surat_tidak_berubah_hanya_karena_membuka_riwayat(): void
    {
        $registration = $this->buatPendaftaran('2026-11-02 08:00:00', '475/DS/009/XI/2026');

        $this->get(route('registration.index'))->assertOk();

        $this->assertSame('475/DS/009/XI/2026', $registration->fresh()->nomor_surat);
    }

    public function test_nomor_surat_pendaftaran_final_tidak_berubah_saat_data_lain_dihapus_atau_ditambah(): void
    {
        $this->travelTo('2026-11-20 10:00:00');

        $draft = $this->buatPendaftaran('2026-10-01 08:00:00');
        $final = $this->buatPendaftaran('2026-10-02 08:00:00');
        $this->simpanPenomoran(['format_nomor_surat' => '475/DS/{NO}/{BLN}/{THN}']);
        $this->assertSame('475/DS/002/X/2026', $final->fresh()->nomor_surat);

        $this->post(route('registration.finalize', $final))->assertRedirect();
        $this->assertSame('valid', $final->fresh()->status);

        // Draft sebelumnya dihapus: tanpa pembekuan, nomor final akan bergeser ke 001.
        $this->delete(route('registration.destroy', $draft))->assertRedirect();
        $this->assertSame('475/DS/002/X/2026', $final->fresh()->nomor_surat);

        $this->get(route('registration.start.service', 'kantor'))->assertRedirect();

        $this->assertSame('475/DS/002/X/2026', $final->fresh()->nomor_surat);

        $baru = Registration::query()->whereKeyNot($final->id)->firstOrFail();
        $this->assertSame('475/DS/001/XI/2026', $baru->nomor_surat);

        $this->travelBack();
    }

    public function test_tahun_mengikuti_tanggal_surat_bukan_setting_tahun(): void
    {
        $registration = $this->buatPendaftaran('2026-11-02 08:00:00');

        $this->simpanPenomoran([
            'tahun' => 2025,
            'format_nomor_surat' => '{NO}/DS/{BLN}/{THN}',
        ]);

        $this->assertSame('001/DS/XI/2026', $registration->fresh()->nomor_surat);
    }
}
