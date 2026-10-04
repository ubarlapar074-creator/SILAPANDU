<?php

namespace Tests\Feature;

use App\Models\Catin;
use App\Models\District;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Registration;
use App\Models\RegistrationKtpCopy;
use App\Models\Village;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RegionHierarchyAndKtpRegressionTest extends TestCase
{
    use RefreshDatabase;

    private function region(): array
    {
        $p = Province::create(['id' => '11', 'name' => 'Jawa Tengah']);
        $r = Regency::create(['id' => '11.01', 'province_id' => '11', 'name' => 'Kab Semarang']);
        $d = District::create(['id' => '11.01.01', 'regency_id' => '11.01', 'name' => 'Kec A']);
        $v = Village::create(['id' => '11.01.01.2001', 'district_id' => '11.01.01', 'name' => 'Desa A']);
        return compact('p', 'r', 'd', 'v');
    }

    private function basePayload($reg): array
    {
        return [
            'registration_path' => 'NIKAH_BEDOL', 'marriage_date' => '2026-12-20', 'marriage_time' => '09:00', 'marriage_dowry' => 'Emas 10 gram', 'marriage_location_address' => 'Masjid',
            'pria_nik' => '3201010101010091', 'pria_name' => 'Pria', 'pria_birth_place' => 'Semarang', 'pria_birth_date' => '1995-01-01', 'pria_marital_status' => 'BELUM_KAWIN', 'pria_address' => 'Jl Pria', 'pria_province_id' => $reg['p']->id, 'pria_regency_id' => $reg['r']->id, 'pria_district_id' => $reg['d']->id, 'pria_village_id' => $reg['v']->id,
            'wanita_nik' => '3201010101010092', 'wanita_name' => 'Wanita', 'wanita_birth_place' => 'Semarang', 'wanita_birth_date' => '1997-01-01', 'wanita_marital_status' => 'BELUM_KAWIN', 'wanita_address' => 'Jl Wanita', 'wanita_province_id' => $reg['p']->id, 'wanita_regency_id' => $reg['r']->id, 'wanita_district_id' => $reg['d']->id, 'wanita_village_id' => $reg['v']->id,
            'pria_father_status' => 'HIDUP', 'pria_father_name' => 'Ayah Pria', 'pria_mother_status' => 'HIDUP', 'pria_mother_name' => 'Ibu Pria',
            'wanita_father_status' => 'HIDUP', 'wanita_father_name' => 'Ayah Wanita', 'wanita_mother_status' => 'HIDUP', 'wanita_mother_name' => 'Ibu Wanita',
            'guardian_relationship' => 'AYAH_KANDUNG',
            'witnesses' => [
                ['name' => 'Saksi 1', 'gender' => 'L', 'nik' => '3201010101010011', 'birth_place' => 'Semarang', 'birth_date' => '1990-01-01', 'marital_status' => 'BELUM_KAWIN', 'address' => 'Jl Saksi', 'province_id' => $reg['p']->id, 'regency_id' => $reg['r']->id, 'district_id' => $reg['d']->id, 'village_id' => $reg['v']->id],
                ['name' => 'Saksi 2', 'gender' => 'L', 'nik' => '3201010101010012', 'birth_place' => 'Semarang', 'birth_date' => '1990-01-02', 'marital_status' => 'BELUM_KAWIN', 'address' => 'Jl Saksi', 'province_id' => $reg['p']->id, 'regency_id' => $reg['r']->id, 'district_id' => $reg['d']->id, 'village_id' => $reg['v']->id],
            ],
        ];
    }

    public function test_partial_region_is_rejected(): void
    {
        $reg = $this->region();
        $payload = $this->basePayload($reg);
        $payload['pria_village_id'] = null;
        $payload['pria_district_id'] = null;
        $resp = $this->post(route('registrations.store'), $payload);
        $resp->assertSessionHasErrors(['pria_district_id', 'pria_village_id']);
        $this->assertDatabaseCount('registrations', 0);
    }

    public function test_merge_rt_rw_normalization_on_store(): void
    {
        $reg = $this->region();
        $payload = $this->basePayload($reg);
        $payload['pria_address'] = 'Jl Mawar';
        $payload['pria_address_rt'] = '001';
        $payload['pria_address_rw'] = '002';
        $payload['wanita_address'] = 'Jl Melati';
        $payload['wanita_address_rt'] = '000';
        $payload['wanita_address_rw'] = '000';
        $this->post(route('registrations.store'), $payload)->assertRedirect();
        $catinPria = Catin::where('nik', '3201010101010091')->first();
        $this->assertStringContainsString('RT1', $catinPria->address);
        $this->assertStringContainsString('/RW2', $catinPria->address);
        $catinWanita = Catin::where('nik', '3201010101010092')->first();
        $this->assertStringContainsString('RT000', $catinWanita->address);
        $this->assertStringContainsString('/RW000', $catinWanita->address);
        $this->assertStringNotContainsString(' RT ', $catinWanita->address . 'x');
    }

    public function test_autosave_dowry_money_without_type_does_not_null(): void
    {
        $reg = $this->region();
        $this->post(route('registrations.store'), $this->basePayload($reg))->assertRedirect();
        $registration = Registration::latest('id')->first();
        $registration->update(['marriage_dowry_type' => 'UANG', 'marriage_dowry_money' => 'Rp 1jt', 'marriage_dowry' => 'Uang Rp 1jt']);
        $this->patchJson(route('registrations.autosave', $registration), ['marriage_dowry_money' => 'Rp 2jt'])->assertOk();
        $registration->refresh();
        $this->assertEquals('UANG', $registration->marriage_dowry_type);
        $this->assertEquals('Rp 2jt', $registration->marriage_dowry_money);
        $this->assertStringContainsString('Rp 2jt', $registration->marriage_dowry);
    }

    public function test_ktp_webp_and_overwrite_old_ext_is_cleaned(): void
    {
        Storage::fake('local');
        $registration = Registration::create(['registration_number' => 'REG-KTP-1', 'registration_path' => 'NIKAH_BEDOL', 'status' => 'DRAFT', 'marriage_date' => '2026-12-20', 'marriage_time' => '09:00', 'marriage_dowry' => 'Emas', 'marriage_location_address' => 'Test']);
        $fileJpg = UploadedFile::fake()->image('ktp.jpg', 100, 100);
        $this->put(route('registrations.ktp.update', $registration), ['ktp' => ['pria' => $fileJpg]])->assertRedirect();
        Storage::disk('local')->assertExists('ktp/'.$registration->id.'/pria.jpg');
        $filePng = UploadedFile::fake()->image('ktp.png', 100, 100);
        $this->put(route('registrations.ktp.update', $registration), ['ktp' => ['pria' => $filePng]])->assertRedirect();
        Storage::disk('local')->assertExists('ktp/'.$registration->id.'/pria.png');
        $this->assertFalse(Storage::disk('local')->exists('ktp/'.$registration->id.'/pria.jpg'));
    }

    public function test_created_by_nullable_no_login(): void
    {
        $reg = Registration::create(['registration_number' => 'REG-NOLOGIN-1', 'registration_path' => 'NIKAH_BEDOL', 'status' => 'DRAFT', 'marriage_date' => '2026-12-20', 'marriage_time' => '09:00', 'marriage_dowry' => 'Emas', 'marriage_location_address' => 'Test']);
        $res = $this->post(route('n6.store'), [
            'registration_id' => $reg->id, 'deceased_name' => 'Alm', 'deceased_bin_binti' => 'Bin X', 'deceased_nik' => '3201010101010099', 'deceased_birth_place' => 'Semarang', 'deceased_birth_date' => '1960-01-01', 'deceased_address' => 'Jl', 'death_date' => '2025-01-01', 'document_number' => 'N6-NOLOGIN-1', 'issue_date' => '2025-01-02', 'signed_by' => 'Lurah', 'signed_by_position' => 'Kepala Desa',
        ]);
        $res->assertRedirect();
        $this->assertDatabaseHas('n6_documents', ['document_number' => 'N6-NOLOGIN-1', 'created_by' => null]);
    }
}
