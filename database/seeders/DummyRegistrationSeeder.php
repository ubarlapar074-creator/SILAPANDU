<?php

namespace Database\Seeders;

use App\Models\Catin;
use App\Models\CatinParent;
use App\Models\DocumentChecklist;
use App\Models\Guardian;
use App\Models\Registration;
use App\Models\RegistrationDocumentChecklist;
use App\Models\Witness;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyRegistrationSeeder extends Seeder
{
    public function run(): void
    {
        $region = $this->region();

        DB::transaction(function () use ($region): void {
            $this->seedRegistration(
                'REG-DUMMY-LENGKAP',
                'Budi Santoso',
                'Siti Aminah',
                'HIDUP',
                'AYAH_KANDUNG',
                $region
            );

            $this->seedRegistration(
                'REG-DUMMY-AYAH-MENINGGAL',
                'Dedi Pratama',
                'Rina Marlina',
                'MENINGGAL',
                'WALI_HAKIM',
                $region
            );
        });
    }

    private function region(): array
    {
        $province = DB::table('provinces')->orderBy('id')->first();
        $regency = DB::table('regencies')->where('province_id', $province->id)->orderBy('id')->first();
        $district = DB::table('districts')->where('regency_id', $regency->id)->orderBy('id')->first();
        $village = DB::table('villages')->where('district_id', $district->id)->orderBy('id')->first();

        return [
            'province_id' => $province->id,
            'regency_id' => $regency->id,
            'district_id' => $district->id,
            'village_id' => $village->id,
        ];
    }

    private function seedRegistration(
        string $number,
        string $maleName,
        string $femaleName,
        string $femaleFatherStatus,
        string $guardianRelationship,
        array $region
    ): void {
        Registration::where('registration_number', $number)->delete();

        $registration = Registration::create([
            'registration_number' => $number,
            'registration_path' => 'NIKAH_BEDOL',
            'status' => 'DRAFT',
            'marriage_date' => '2026-12-20',
            'marriage_time' => '09:00',
            'marriage_dowry' => 'Emas 10 gram',
            'marriage_location_address' => 'Masjid Al-Ikhlas Desa Sukamaju',
        ]);

        $male = $this->catin($registration, 'PRIA', $maleName, '1995-03-12', $region, 'Bin Haryono');
        $female = $this->catin($registration, 'WANITA', $femaleName, '1997-07-21', $region, 'Binti Rahmat');

        $this->parent($male, 'AYAH', 'HIDUP', 'Haryono', $region);
        $this->parent($male, 'IBU', 'HIDUP', 'Sri Wahyuni', $region);
        $femaleFather = $this->parent($female, 'AYAH', $femaleFatherStatus, 'Rahmat', $region);
        $this->parent($female, 'IBU', 'HIDUP', 'Murniati', $region);

        $guardian = [
            'registration_id' => $registration->id,
            'guardian_source' => $guardianRelationship === 'AYAH_KANDUNG' ? 'PARENT' : 'OTHER',
            'parent_id' => $guardianRelationship === 'AYAH_KANDUNG' ? $femaleFather->id : null,
            'relationship' => $guardianRelationship,
            'name' => $guardianRelationship === 'AYAH_KANDUNG' ? null : 'Wali Hakim (KUA)',
            'address' => $guardianRelationship === 'AYAH_KANDUNG' ? null : 'Kantor KUA Setempat',
        ];
        Guardian::create($guardian);

        foreach ([
            ['Saksi Ahmad', '3201010101010011', 'Bin Abdullah'],
            ['Saksi Hasan', '3201010101010012', 'Bin Ibrahim'],
        ] as [$name, $nik, $binBinti]) {
            Witness::create(array_merge([
                'registration_id' => $registration->id,
                'gender' => 'L',
                'bin_binti' => $binBinti,
                'nik' => $nik,
                'name' => $name,
                'birth_place' => 'Semarang',
                'birth_date' => '1990-01-10',
                'nationality' => 'WNI',
                'religion' => 'Islam',
                'marital_status' => 'KAWIN_TERCATAT',
                'education' => 'SMA',
                'occupation' => 'Wiraswasta',
                'phone' => null,
                'email' => null,
                'address' => 'Jl. Merdeka No. 10',
            ], $region));
        }

        foreach ([$male, $female] as $catin) {
            DocumentChecklist::updateOrCreate(
                ['catin_id' => $catin->id],
                ['doc_ktp' => true, 'doc_kk' => true, 'doc_birth' => true, 'doc_immunization' => true, 'doc_photo' => true]
            );
        }

        RegistrationDocumentChecklist::create([
            'registration_id' => $registration->id,
            'doc_n1_n4' => true,
            'doc_death' => $femaleFatherStatus === 'MENINGGAL',
            'doc_n5' => false,
            'doc_n6' => false,
            'n5_status' => null,
            'n6_status' => null,
        ]);
    }

    private function catin(
        Registration $registration,
        string $role,
        string $name,
        string $birthDate,
        array $region,
        string $binBinti
    ): Catin {
        $nik = $role === 'PRIA' ? '3201010101010001' : '3201010101010002';
        $catin = Catin::firstOrCreate(
            ['nik' => $nik],
            array_merge([
                'registration_id' => $registration->id,
                'role' => $role,
                'name' => $name,
                'bin_binti' => $binBinti,
                'birth_place' => 'Semarang',
                'birth_date' => $birthDate,
                'nationality' => 'WNI',
                'religion' => 'Islam',
                'marital_status' => 'BELUM_KAWIN',
                'no_hp' => null,
                'email' => null,
                'occupation' => $role === 'PRIA' ? 'Karyawan' : 'Guru',
                'education' => 'S1',
                'address' => 'Jl. Mawar No. 5',
            ], $region)
        );
        $registration->catins()->syncWithoutDetaching([$catin->id]);
        $catin->update(['name' => $name]);
        return $catin;
    }

    private function parent(
        Catin $catin,
        string $role,
        string $status,
        string $name,
        array $region
    ): CatinParent {
        return CatinParent::updateOrCreate(
            ['catin_id' => $catin->id, 'role' => $role],
            array_merge([
                'status' => $status,
                'name' => $name,
                'nik' => $status === 'HIDUP' ? '3201010101010099' : null,
                'birth_place' => $status === 'HIDUP' ? 'Semarang' : null,
                'birth_date' => $status === 'HIDUP' ? '1965-02-10' : null,
                'address' => $status === 'HIDUP' ? 'Jl. Orang Tua No. 1' : null,
                'education' => $status === 'HIDUP' ? 'SMA' : null,
                'occupation' => $status === 'HIDUP' ? 'Petani' : null,
            ], $status === 'HIDUP' ? $region : ['province_id' => null, 'regency_id' => null, 'district_id' => null, 'village_id' => null])
        );
    }
}