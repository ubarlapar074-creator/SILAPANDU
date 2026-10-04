<?php

namespace Tests\Feature;

use App\Models\DocumentNumberSetting;
use App\Models\N6Document;
use App\Models\Registration;
use App\Models\RegistrationDocument;
use App\Models\VillageProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PersistenceSilentLossTest extends TestCase
{
    use RefreshDatabase;

    public function test_village_profile_fillable_persists_all_columns(): void
    {
        $profile = VillageProfile::updateOrCreate(['id' => 1], [
            'village_name' => 'Desa Test',
            'district_name' => 'Kec Test',
            'regency_name' => 'Kab Test',
            'office_address' => 'Jl Kantor 1',
            'village_head_name' => 'Pak Lurah',
            'village_head_id' => '3201010101010001',
            'service_officer_name' => 'Bu Officer',
            'service_officer_id' => '3201010101010002',
        ]);
        $profile->refresh();
        $this->assertEquals('Desa Test', $profile->village_name);
        $this->assertEquals('Jl Kantor 1', $profile->office_address);
        $this->assertEquals('Pak Lurah', $profile->village_head_name);
        $this->assertEquals('3201010101010001', $profile->village_head_id);
        $this->assertEquals('Bu Officer', $profile->service_officer_name);
        $this->assertEquals('3201010101010002', $profile->service_officer_id);

        $profile->update(['village_name' => 'Desa Update', 'village_head_name' => 'Pak Baru']);
        $this->assertDatabaseHas('village_profiles', ['id' => 1, 'village_name' => 'Desa Update', 'village_head_name' => 'Pak Baru']);
    }

    public function test_document_number_setting_fillable(): void
    {
        $s = DocumentNumberSetting::updateOrCreate(['id' => 1], [
            'prefix_n1' => '470', 'prefix_n2' => '471', 'prefix_n4' => '474', 'prefix_n5' => '475', 'prefix_n6' => '472',
            'format' => '{prefix}/{seq:3}/{type}/{roman}/{year}',
            'kode_klasifikasi_arsip' => '470',
            'kode_desa_kecamatan' => '33.01/DS',
        ]);
        $s->refresh();
        $this->assertEquals('470', $s->kode_klasifikasi_arsip);
        $this->assertEquals('33.01/DS', $s->kode_desa_kecamatan);
        $this->assertEquals('470', $s->prefix_n1);
        $this->assertEquals('{prefix}/{seq:3}/{type}/{roman}/{year}', $s->format);
        $this->assertDatabaseHas('document_number_settings', ['id' => 1, 'kode_klasifikasi_arsip' => '470', 'kode_desa_kecamatan' => '33.01/DS']);
    }

    public function test_registration_document_fillable_issue_date_and_created_by(): void
    {
        $reg = Registration::create(['registration_number' => 'REG-PERS-1', 'registration_path' => 'NIKAH_BEDOL', 'status' => 'DRAFT', 'marriage_date' => '2026-12-20', 'marriage_time' => '09:00', 'marriage_dowry' => 'Emas', 'marriage_location_address' => 'Test']);
        $doc = RegistrationDocument::create(['registration_id' => $reg->id, 'document_type' => 'N1', 'document_number' => 'N1-001', 'issue_date' => '2026-09-01', 'signed_by' => 'Lurah', 'signed_by_position' => 'Kepala', 'status' => 'PUBLISHED', 'created_by' => null]);
        $doc->refresh();
        $this->assertEquals('2026-09-01', $doc->issue_date->format('Y-m-d'));
        $this->assertNull($doc->created_by);

        RegistrationDocument::updateOrCreate(['registration_id' => $reg->id, 'document_type' => 'N1'], ['document_number' => 'N1-002', 'issue_date' => '2026-09-02', 'created_by' => null, 'status' => 'PUBLISHED']);
        $this->assertDatabaseHas('registration_documents', ['registration_id' => $reg->id, 'document_type' => 'N1', 'document_number' => 'N1-002']);
    }

    public function test_n6_fillable_extra_fields_and_update(): void
    {
        $reg = Registration::create(['registration_number' => 'REG-PERS-2', 'registration_path' => 'NIKAH_BEDOL', 'status' => 'DRAFT', 'marriage_date' => '2026-12-20', 'marriage_time' => '09:00', 'marriage_dowry' => 'Emas', 'marriage_location_address' => 'Test']);
        $n6 = N6Document::create([
            'registration_id' => $reg->id, 'deceased_name' => 'Alm', 'deceased_bin_binti' => 'Bin X', 'deceased_nik' => '3201010101010099', 'deceased_birth_place' => 'Semarang', 'deceased_birth_date' => '1960-01-01',
            'deceased_nationality' => 'WNI', 'deceased_religion' => 'Islam', 'deceased_occupation' => 'Petani', 'deceased_address' => 'Jl', 'death_date' => '2025-01-01', 'death_place' => 'RS',
            'document_number' => 'N6-001', 'issue_date' => '2025-01-02', 'signed_by' => 'Lurah', 'signed_by_position' => 'Kepala Desa', 'status' => 'PUBLISHED', 'created_by' => null,
        ]);
        $n6->refresh();
        $this->assertEquals('WNI', $n6->deceased_nationality);
        $this->assertEquals('Islam', $n6->deceased_religion);
        $this->assertEquals('Petani', $n6->deceased_occupation);
        $this->assertEquals('RS', $n6->death_place);
        $this->assertNull($n6->created_by);

        $n6->update(['deceased_nationality' => 'WNA', 'deceased_religion' => 'Kristen', 'deceased_occupation' => 'Guru', 'death_place' => 'Rumah']);
        $n6->refresh();
        $this->assertEquals('WNA', $n6->deceased_nationality);
        $this->assertEquals('Kristen', $n6->deceased_religion);
        $this->assertEquals('Guru', $n6->deceased_occupation);
        $this->assertEquals('Rumah', $n6->death_place);
    }
}
