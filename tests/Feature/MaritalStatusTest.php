<?php

namespace Tests\Feature;

use App\Support\MaritalStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaritalStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_normalize_variants(): void
    {
        $cases = [
            ['BELUM KAWIN', MaritalStatus::BELUM_KAWIN],
            ['BELUM_KAWIN', MaritalStatus::BELUM_KAWIN],
            ['KAWIN', MaritalStatus::KAWIN],
            ['KAWIN TERCATAT', MaritalStatus::KAWIN],
            ['KAWIN_TERCATAT', MaritalStatus::KAWIN],
            ['CERAI HIDUP', MaritalStatus::CERAI_HIDUP],
            ['CERAI_TERCATAT', MaritalStatus::CERAI_HIDUP],
            ['CERAI_HIDUP', MaritalStatus::CERAI_HIDUP],
            ['CERAI TERCATAT', MaritalStatus::CERAI_HIDUP],
            ['CERAI MATI', MaritalStatus::CERAI_MATI],
            ['CERAI_MATI', MaritalStatus::CERAI_MATI],
            ['  cerai   hidup  ', MaritalStatus::CERAI_HIDUP],
            ['kawin tercatat', MaritalStatus::KAWIN],
        ];
        foreach ($cases as [$raw, $expected]) {
            $this->assertSame($expected, MaritalStatus::normalize($raw), "normalize($raw)");
        }
    }

    public function test_unknown_returns_null_and_logs_warning(): void
    {
        $this->assertNull(MaritalStatus::normalize('UNKNOWN_VALUE'));
        $this->assertNull(MaritalStatus::normalize('KAWIN-TERSERAT'));
        $this->assertNull(MaritalStatus::normalize(''));
        $this->assertNull(MaritalStatus::normalize(null));
    }

    public function test_label_for_raw_preserves_redaksi(): void
    {
        $this->assertSame('Kawin', MaritalStatus::labelForRaw('KAWIN', 'PRIA'));
        $this->assertSame('Kawin', MaritalStatus::labelForRaw('KAWIN TERCATAT', 'PRIA'));
        $this->assertSame('Janda (Cerai Hidup)', MaritalStatus::labelForRaw('CERAI TERCATAT', 'WANITA'));
        $this->assertSame('Duda (Cerai Hidup)', MaritalStatus::labelForRaw('CERAI_TERCATAT', 'PRIA'));
    }

    public function test_label_for_canonical(): void
    {
        $this->assertSame('Belum Kawin', MaritalStatus::labelForCanonical(MaritalStatus::BELUM_KAWIN, 'PRIA'));
        $this->assertSame('Kawin', MaritalStatus::labelForCanonical(MaritalStatus::KAWIN, 'PRIA'));
        $this->assertSame('Janda (Cerai Hidup)', MaritalStatus::labelForCanonical(MaritalStatus::CERAI_HIDUP, 'WANITA'));
        $this->assertNull(MaritalStatus::normalize('typo'));
    }

    public function test_catin_accessor_canonical(): void
    {
        $province = \App\Models\Province::create(['id' => '11', 'name' => 'Aceh']);
        $regency = \App\Models\Regency::create(['id' => '1101', 'province_id' => '11', 'name' => 'Kab']);
        $district = \App\Models\District::create(['id' => '1101010', 'regency_id' => '1101', 'name' => 'Kec']);
        $village = \App\Models\Village::create(['id' => '1101010001', 'district_id' => '1101010', 'name' => 'Desa']);
        $catin = \App\Models\Catin::create([
            'registration_id' => null, 'role' => 'PRIA', 'nik' => '3201010101010099', 'name' => 'Test',
            'birth_place' => 'Semarang', 'birth_date' => '1995-01-01', 'marital_status' => 'KAWIN TERCATAT',
            'address' => 'Jl', 'province_id' => $province->id, 'regency_id' => $regency->id, 'district_id' => $district->id, 'village_id' => $village->id,
        ]);
        $this->assertSame('KAWIN', $catin->marital_status_canonical);
        $this->assertSame('KAWIN TERCATAT', $catin->marital_status_raw);
        $catin->update(['marital_status' => 'CERAI TERCATAT']);
        $this->assertSame('CERAI_HIDUP', $catin->fresh()->marital_status_canonical);
    }

    public function test_validation_accepts_all_valid_raws(): void
    {
        $province = \App\Models\Province::create(['id' => '12', 'name' => 'Sumut']);
        $regency = \App\Models\Regency::create(['id' => '1201', 'province_id' => '12', 'name' => 'Kab2']);
        $district = \App\Models\District::create(['id' => '1201010', 'regency_id' => '1201', 'name' => 'Kec2']);
        $village = \App\Models\Village::create(['id' => '1201010001', 'district_id' => '1201010', 'name' => 'Desa2']);
        foreach (['CERAI TERCATAT', 'CERAI HIDUP', 'CERAI MATI', 'BELUM KAWIN'] as $status) {
            $payload = $this->validPayload($province, $regency, $district, $village, $status);
            $response = $this->post(route('registrations.store'), $payload);
            $response->assertRedirect();
        }
    }

    public function test_validation_rejects_unknown(): void
    {
        $province = \App\Models\Province::create(['id' => '13', 'name' => 'Sumsel']);
        $regency = \App\Models\Regency::create(['id' => '1301', 'province_id' => '13', 'name' => 'Kab3']);
        $district = \App\Models\District::create(['id' => '1301010', 'regency_id' => '1301', 'name' => 'Kec3']);
        $village = \App\Models\Village::create(['id' => '1301010001', 'district_id' => '1301010', 'name' => 'Desa3']);
        $payload = $this->validPayload($province, $regency, $district, $village, 'KAWIN_TERSERAT');
        $this->post(route('registrations.store'), $payload)->assertSessionHasErrors('pria_marital_status');
    }

    private function validPayload($p, $r, $d, $v, string $status): array
    {
        static $counter = 0;
        $counter++;
        $priaNik = '3201' . str_pad((string) (100000000 + $counter * 2), 12, '0', STR_PAD_LEFT);
        $wanitaNik = '3201' . str_pad((string) (100000001 + $counter * 2), 12, '0', STR_PAD_LEFT);
        return [
            'registration_path' => 'NIKAH_BEDOL', 'marriage_date' => '2026-12-20', 'marriage_time' => '09:00', 'marriage_dowry' => 'Emas', 'marriage_location_address' => 'Masjid',
            'pria_nik' => $priaNik, 'pria_name' => 'Pria', 'pria_birth_place' => 'Semarang', 'pria_birth_date' => '1995-01-01', 'pria_marital_status' => $status, 'pria_address' => 'Jl', 'pria_province_id' => $p->id, 'pria_regency_id' => $r->id, 'pria_district_id' => $d->id, 'pria_village_id' => $v->id,
            'wanita_nik' => $wanitaNik, 'wanita_name' => 'Wanita', 'wanita_birth_place' => 'Semarang', 'wanita_birth_date' => '1997-01-01', 'wanita_marital_status' => 'BELUM KAWIN', 'wanita_address' => 'Jl', 'wanita_province_id' => $p->id, 'wanita_regency_id' => $r->id, 'wanita_district_id' => $d->id, 'wanita_village_id' => $v->id,
            'pria_father_status' => 'HIDUP', 'pria_father_name' => 'Ayah Pria', 'pria_mother_status' => 'HIDUP', 'pria_mother_name' => 'Ibu Pria',
            'wanita_father_status' => 'HIDUP', 'wanita_father_name' => 'Ayah Wanita', 'wanita_mother_status' => 'HIDUP', 'wanita_mother_name' => 'Ibu Wanita',
            'guardian_relationship' => 'AYAH_KANDUNG',
        ];
    }
}
