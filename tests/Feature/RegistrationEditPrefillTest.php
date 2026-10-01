<?php

namespace Tests\Feature;

use App\Models\Catin;
use App\Models\CatinParent;
use App\Models\Guardian;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;
use App\Models\Province;
use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationEditPrefillTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_edit_form_prefills_existing_registration_data(): void
    {
        $province = Province::create([
            'id' => '11',
            'name' => 'Jawa Tengah',
        ]);

        $regency = Regency::create([
            'id' => '1101',
            'province_id' => $province->id,
            'name' => 'Kabupaten Semarang',
        ]);

        $district = District::create([
            'id' => '1101010',
            'regency_id' => $regency->id,
            'name' => 'Kecamatan Semarang',
        ]);

        $village = Village::create([
            'id' => '1101010001',
            'district_id' => $district->id,
            'name' => 'Desa Sukamaju',
        ]);

        $registration = Registration::create([
            'registration_number' => 'REG-20260920-ABCDE1',
            'registration_path' => 'DALAM_DESA',
            'status' => 'DRAFT',
            'marriage_date' => '2026-10-15',
            'marriage_time' => '09:00',
            'marriage_dowry' => 'Emas 2 gram',
            'marriage_location_address' => 'Masjid Desa Sukamaju',
        ]);

        $pria = Catin::create([
            'registration_id' => $registration->id,
            'role' => 'PRIA',
            'nik' => '3201010101010001',
            'name' => 'Budi Santoso',
            'bin_binti' => 'Bin Sutrisno',
            'birth_place' => 'Bandung',
            'birth_date' => '1995-05-05',
            'nationality' => 'WNI',
            'religion' => 'Islam',
            'marital_status' => 'BELUM_KAWIN',
            'no_hp' => '081234567890',
            'email' => 'budi@example.com',
            'occupation' => 'Karyawan',
            'education' => 'S1',
            'address' => 'Jl. Merdeka 1',
            'province_id' => $province->id,
            'regency_id' => $regency->id,
            'district_id' => $district->id,
            'village_id' => $village->id,
        ]);

        $wanita = Catin::create([
            'registration_id' => $registration->id,
            'role' => 'WANITA',
            'nik' => '3201010101010002',
            'name' => 'Siti Aminah',
            'bin_binti' => 'Binti Rahmat',
            'birth_place' => 'Bogor',
            'birth_date' => '1997-07-07',
            'nationality' => 'WNI',
            'religion' => 'Islam',
            'marital_status' => 'BELUM_KAWIN',
            'no_hp' => '081234567891',
            'email' => 'siti@example.com',
            'occupation' => 'Guru',
            'education' => 'S1',
            'address' => 'Jl. Mawar 2',
            'province_id' => $province->id,
            'regency_id' => $regency->id,
            'district_id' => $district->id,
            'village_id' => $village->id,
        ]);

        $priaFather = CatinParent::create([
            'catin_id' => $pria->id,
            'role' => 'AYAH',
            'status' => 'HIDUP',
            'name' => 'Sutrisno',
            'nik' => '3201010101010003',
            'birth_place' => 'Semarang',
            'birth_date' => '1968-01-01',
            'address' => 'Jl. Ayah 3',
            'province_id' => $province->id,
            'regency_id' => $regency->id,
            'district_id' => $district->id,
            'village_id' => $village->id,
            'education' => 'SMA',
            'occupation' => 'Petani',
        ]);

        $wanitaFather = CatinParent::create([
            'catin_id' => $wanita->id,
            'role' => 'AYAH',
            'status' => 'MENINGGAL',
            'name' => 'Rahmat',
            'nik' => '3201010101010004',
            'birth_place' => 'Magelang',
            'birth_date' => '1965-02-02',
            'address' => 'Jl. Orang Tua 4',
            'province_id' => $province->id,
            'regency_id' => $regency->id,
            'district_id' => $district->id,
            'village_id' => $village->id,
            'education' => 'SMP',
            'occupation' => 'Pedagang',
        ]);

        Guardian::create([
            'registration_id' => $registration->id,
            'guardian_source' => 'PARENT',
            'parent_id' => $wanitaFather->id,
            'relationship' => 'AYAH_KANDUNG',
            'name' => null,
            'nik' => null,
            'bin_binti' => null,
            'birth_place' => null,
            'birth_date' => null,
            'occupation' => null,
            'address' => null,
        ]);

        $response = $this->get(route('registrations.edit', $registration));

        $response->assertOk();
        $response->assertSee('placeholder="dd/mm/yyyy"', false);
        $response->assertSee('Budi Santoso');
        $response->assertSee('Siti Aminah');
        $response->assertSee('Emas 2 gram');
        $response->assertSee('Masjid Desa Sukamaju');
        $response->assertSee('Sutrisno');
    }

    public function test_edit_form_prefills_first_step_registration_fields_from_existing_data(): void
    {
        $registration = Registration::create([
            'registration_number' => 'REG-20260920-ABCDE2',
            'registration_path' => 'DALAM_DESA',
            'status' => 'DRAFT',
            'marriage_date' => '2026-10-15',
            'marriage_time' => '09:00',
            'marriage_dowry' => 'Emas 2 gram',
            'marriage_location_address' => 'Masjid Desa Sukamaju',
        ]);

        $response = $this->get(route('registrations.edit', $registration));

        $response->assertOk();
        $response->assertSee('value="DALAM_DESA"', false);
        $response->assertSee('value="2026-10-15"', false);
        $response->assertSee('value="09:00"', false);
        $response->assertSee('value="Emas 2 gram"', false);
        $response->assertSee('Masjid Desa Sukamaju');
    }
}
