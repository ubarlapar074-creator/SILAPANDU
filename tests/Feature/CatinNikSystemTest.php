<?php

namespace Tests\Feature;

use App\Models\Catin;
use App\Models\CatinParent;
use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use App\Models\Village;
use App\Models\Registration;
use App\Models\RegistrationDocument;
use App\Models\RegistrationDocumentChecklist;
use App\Models\N6Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CatinNikSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    private function region(): array
    {
        $province = Province::create(['id' => '11', 'name' => 'Jawa Tengah']);
        $regency = Regency::create(['id' => '11.01', 'province_id' => '11', 'name' => 'Kab Semarang']);
        $district = District::create(['id' => '11.01.01', 'regency_id' => '11.01', 'name' => 'Kec A']);
        $village = Village::create(['id' => '11.01.01.2001', 'district_id' => '11.01.01', 'name' => 'Desa A']);
        return compact('province', 'regency', 'district', 'village');
    }

    private function basePayload(array $region, string $priaNik, string $wanitaNik): array
    {
        $r = $region;
        return [
            'registration_path' => 'NIKAH_BEDOL',
            'marriage_date' => '2026-12-20',
            'marriage_time' => '09:00',
            'marriage_dowry' => 'Emas 10 gram',
            'marriage_location_address' => 'Masjid Test',
            'pria_nik' => $priaNik,
            'pria_name' => 'Pria Test',
            'pria_birth_place' => 'Semarang',
            'pria_birth_date' => '1995-01-01',
            'pria_marital_status' => 'BELUM_KAWIN',
            'pria_address' => 'Jl Pria',
            'pria_province_id' => $r['province']->id,
            'pria_regency_id' => $r['regency']->id,
            'pria_district_id' => $r['district']->id,
            'pria_village_id' => $r['village']->id,
            'wanita_nik' => $wanitaNik,
            'wanita_name' => 'Wanita Test',
            'wanita_birth_place' => 'Semarang',
            'wanita_birth_date' => '1997-01-01',
            'wanita_marital_status' => 'BELUM_KAWIN',
            'wanita_address' => 'Jl Wanita',
            'wanita_province_id' => $r['province']->id,
            'wanita_regency_id' => $r['regency']->id,
            'wanita_district_id' => $r['district']->id,
            'wanita_village_id' => $r['village']->id,
            'pria_father_status' => 'HIDUP',
            'pria_father_name' => 'Ayah Pria',
            'pria_mother_status' => 'HIDUP',
            'pria_mother_name' => 'Ibu Pria',
            'wanita_father_status' => 'HIDUP',
            'wanita_father_name' => 'Ayah Wanita',
            'wanita_mother_status' => 'HIDUP',
            'wanita_mother_name' => 'Ibu Wanita',
            'guardian_relationship' => 'AYAH_KANDUNG',
            'witnesses' => [
                ['name' => 'Saksi 1', 'gender' => 'L', 'nik' => '3201010101010011', 'birth_place' => 'Semarang', 'birth_date' => '1990-01-01', 'marital_status' => 'BELUM_KAWIN', 'address' => 'Jl Saksi', 'province_id' => $r['province']->id, 'regency_id' => $r['regency']->id, 'district_id' => $r['district']->id, 'village_id' => $r['village']->id],
                ['name' => 'Saksi 2', 'gender' => 'L', 'nik' => '3201010101010012', 'birth_place' => 'Semarang', 'birth_date' => '1990-01-02', 'marital_status' => 'BELUM_KAWIN', 'address' => 'Jl Saksi', 'province_id' => $r['province']->id, 'regency_id' => $r['regency']->id, 'district_id' => $r['district']->id, 'village_id' => $r['village']->id],
            ],
        ];
    }

    public function test_store_reuses_existing_catin_lintas_registration(): void
    {
        $r = $this->region();
        $payload1 = $this->basePayload($r, '3201010101010091', '3201010101010092');
        $this->post(route('registrations.store'), $payload1)->assertRedirect();
        $this->assertEquals(2, Catin::count());
        $catinPriaId = Catin::where('nik', '3201010101010091')->value('id');

        $payload2 = $this->basePayload($r, '3201010101010091', '3201010101010093');
        $payload2['pria_name'] = 'Pria Test Updated';
        $this->post(route('registrations.store'), $payload2)->assertRedirect();
        $this->assertEquals(3, Catin::count());
        $this->assertDatabaseHas('catins', ['id' => $catinPriaId, 'nik' => '3201010101010091', 'name' => 'Pria Test Updated']);
        $reg2 = Registration::orderByDesc('id')->first();
        $this->assertTrue($reg2->catins()->where('catins.id', $catinPriaId)->exists());
        $reg1 = Registration::orderBy('id')->first();
        $this->assertTrue($reg1->catins()->where('catins.id', $catinPriaId)->exists());
    }

    public function test_unique_nik_db_constraint(): void
    {
        $r = $this->region();
        Catin::create([
            'registration_id' => null, 'role' => 'PRIA', 'nik' => '3201010101010099', 'name' => 'A', 'birth_place' => 'Semarang', 'birth_date' => '1995-01-01', 'marital_status' => 'BELUM_KAWIN', 'address' => 'Jl', 'province_id' => $r['province']->id, 'regency_id' => $r['regency']->id, 'district_id' => $r['district']->id, 'village_id' => $r['village']->id,
        ]);
        $this->expectException(\Illuminate\Database\QueryException::class);
        Catin::create([
            'registration_id' => null, 'role' => 'WANITA', 'nik' => '3201010101010099', 'name' => 'B', 'birth_place' => 'Semarang', 'birth_date' => '1995-01-01', 'marital_status' => 'BELUM_KAWIN', 'address' => 'Jl', 'province_id' => $r['province']->id, 'regency_id' => $r['regency']->id, 'district_id' => $r['district']->id, 'village_id' => $r['village']->id,
        ]);
    }

    public function test_dedup_migrasi_no_duplicate_nik(): void
    {
        $dups = DB::table('catins')->select('nik', DB::raw('COUNT(*) c'))->groupBy('nik')->having('c', '>', 1)->get();
        $this->assertCount(0, $dups);
        $hasUnique = collect(DB::select("SELECT sql FROM sqlite_master WHERE type='index' AND tbl_name='catins'"))->pluck('sql')->implode(' ');
        $this->assertStringContainsString('catins_nik_unique', $hasUnique);
    }

    public function test_lookup_returns_existing_data_with_parents_and_region(): void
    {
        $r = $this->region();
        $reg = Registration::create(['registration_number' => 'REG-LOOKUP-1', 'registration_path' => 'NIKAH_BEDOL', 'status' => 'DRAFT', 'marriage_date' => '2026-12-20', 'marriage_time' => '09:00', 'marriage_dowry' => 'Emas', 'marriage_location_address' => 'Test']);
        $catin = Catin::create(['registration_id' => $reg->id, 'role' => 'PRIA', 'nik' => '3201010101010088', 'name' => 'Lookup Pria', 'birth_place' => 'Semarang', 'birth_date' => '1995-01-01', 'marital_status' => 'BELUM_KAWIN', 'address' => 'Jl', 'province_id' => $r['province']->id, 'regency_id' => $r['regency']->id, 'district_id' => $r['district']->id, 'village_id' => $r['village']->id]);
        CatinParent::create(['catin_id' => $catin->id, 'role' => 'AYAH', 'status' => 'HIDUP', 'name' => 'Ayah Lookup', 'nik' => '3201010101010098', 'birth_place' => 'Semarang', 'birth_date' => '1970-01-01', 'address' => 'Jl Ayah', 'province_id' => $r['province']->id, 'regency_id' => $r['regency']->id, 'district_id' => $r['district']->id, 'village_id' => $r['village']->id]);
        CatinParent::create(['catin_id' => $catin->id, 'role' => 'IBU', 'status' => 'HIDUP', 'name' => 'Ibu Lookup', 'address' => 'Jl Ibu', 'province_id' => $r['province']->id, 'regency_id' => $r['regency']->id, 'district_id' => $r['district']->id, 'village_id' => $r['village']->id]);

        $res = $this->postJson('/internal/catins/lookup', ['nik' => '3201010101010088']);
        $res->assertOk()->assertJsonPath('found', true)->assertJsonPath('data.nik', '3201010101010088')->assertJsonPath('data.province_id', '11')->assertJsonPath('data.regency_id', '11.01')->assertJsonPath('data.father.name', 'Ayah Lookup')->assertJsonPath('data.mother.name', 'Ibu Lookup');
        $this->postJson('/internal/catins/lookup', ['nik' => '3201010101010000'])->assertOk()->assertJsonPath('found', false);
    }

    public function test_autosave_creates_parent_when_missing(): void
    {
        $r = $this->region();
        $payload = $this->basePayload($r, '3201010101010071', '3201010101010072');
        $this->post(route('registrations.store'), $payload)->assertRedirect();
        $reg = Registration::orderByDesc('id')->first();
        $catinPria = $reg->catins()->where('role', 'PRIA')->first() ?? Catin::where('nik', '3201010101010071')->first();
        CatinParent::where('catin_id', $catinPria->id)->delete();
        $this->assertEquals(0, CatinParent::where('catin_id', $catinPria->id)->count());

        $this->patchJson(route('registrations.autosave', $reg), [
            'pria_father_status' => 'HIDUP',
            'pria_father_name' => 'Ayah Baru Autosave',
            'pria_father_province_id' => $r['province']->id,
            'pria_father_regency_id' => $r['regency']->id,
            'pria_father_district_id' => $r['district']->id,
            'pria_father_village_id' => $r['village']->id,
        ])->assertOk();

        $this->assertDatabaseHas('parents', ['catin_id' => $catinPria->id, 'role' => 'AYAH', 'name' => 'Ayah Baru Autosave']);
    }

    public function test_autosave_relink_to_existing_nik_and_new_nik(): void
    {
        $r = $this->region();
        $existing = Catin::create(['registration_id' => null, 'role' => 'PRIA', 'nik' => '3201010101010061', 'name' => 'Existing Pria', 'birth_place' => 'Semarang', 'birth_date' => '1995-01-01', 'marital_status' => 'BELUM_KAWIN', 'address' => 'Jl', 'province_id' => $r['province']->id, 'regency_id' => $r['regency']->id, 'district_id' => $r['district']->id, 'village_id' => $r['village']->id]);
        $payload = $this->basePayload($r, '3201010101010062', '3201010101010063');
        $this->post(route('registrations.store'), $payload)->assertRedirect();
        $reg = Registration::orderByDesc('id')->first();
        $oldPriaId = $reg->catins()->where('role', 'PRIA')->first()->id ?? Catin::where('nik', '3201010101010062')->value('id');

        $this->patchJson(route('registrations.autosave', $reg), ['pria_nik' => '3201010101010061'])->assertOk();
        $reg->refresh();
        $this->assertTrue($reg->catins()->where('catins.id', $existing->id)->exists());
        $this->assertFalse($reg->catins()->where('catins.id', $oldPriaId)->exists());
        $this->assertEquals('PRIA', $existing->fresh()->role);

        $newNik = '3201010101010069';
        $this->patchJson(route('registrations.autosave', $reg), [
            'pria_nik' => $newNik,
            'pria_name' => 'Pria Baru',
            'pria_birth_place' => 'Semarang',
            'pria_birth_date' => '1995-01-01',
            'pria_marital_status' => 'BELUM_KAWIN',
            'pria_address' => 'Jl Baru',
            'pria_province_id' => $r['province']->id,
            'pria_regency_id' => $r['regency']->id,
            'pria_district_id' => $r['district']->id,
            'pria_village_id' => $r['village']->id,
        ])->assertOk();
        $this->assertDatabaseHas('catins', ['nik' => $newNik, 'name' => 'Pria Baru']);
        $newId = Catin::where('nik', $newNik)->value('id');
        $this->assertTrue($reg->fresh()->catins()->where('catins.id', $newId)->exists());
    }

    public function test_view_contains_autosave_900ms_and_dialog_options(): void
    {
        $reg = Registration::create(['registration_number' => 'REG-DIALOG-1', 'registration_path' => 'NIKAH_BEDOL', 'status' => 'DRAFT', 'marriage_date' => '2026-12-20', 'marriage_time' => '09:00', 'marriage_dowry' => 'Emas', 'marriage_location_address' => 'Test']);
        $r = $this->region();
        $catin = Catin::create(['registration_id' => $reg->id, 'role' => 'PRIA', 'nik' => '3201010101010051', 'name' => 'Dialog Pria', 'birth_place' => 'Semarang', 'birth_date' => '1995-01-01', 'marital_status' => 'BELUM_KAWIN', 'address' => 'Jl', 'province_id' => $r['province']->id, 'regency_id' => $r['regency']->id, 'district_id' => $r['district']->id, 'village_id' => $r['village']->id]);
        $reg->catins()->syncWithoutDetaching([$catin->id]);
        $wanita = Catin::create(['registration_id' => $reg->id, 'role' => 'WANITA', 'nik' => '3201010101010052', 'name' => 'Dialog Wanita', 'birth_place' => 'Semarang', 'birth_date' => '1997-01-01', 'marital_status' => 'BELUM_KAWIN', 'address' => 'Jl', 'province_id' => $r['province']->id, 'regency_id' => $r['regency']->id, 'district_id' => $r['district']->id, 'village_id' => $r['village']->id]);
        $reg->catins()->syncWithoutDetaching([$wanita->id]);

        $res = $this->get(route('registrations.edit', $reg));
        $res->assertOk();
        $res->assertSee('setTimeout(() => this.doAutosave(), 900)', false);
        $res->assertSee('Kembali ke halaman sebelumnya menyebabkan data perubahan tidak tersimpan', false);
        $res->assertSee('Kembali dengan menyimpan data', false);
        $res->assertSee('Hanya kembali saja tanpa menyimpan data', false);
        $res->assertSee('/internal/catins/lookup', false);
        $res->assertSee('regency_id', false);
        $res->assertSee('district_id', false);
        $res->assertSee('village_id', false);
    }

    public function test_destroy_registration_removes_unshared_catins_but_preserves_shared_catins(): void
    {
        $r = $this->region();
        $payload = $this->basePayload($r, '3201010101010041', '3201010101010042');
        $this->post(route('registrations.store'), $payload)->assertRedirect();

        $registration = Registration::latest('id')->firstOrFail();
        $sharedCatin = $registration->catins()->where('nik', '3201010101010041')->firstOrFail();
        $otherRegistration = Registration::create([
            'registration_number' => 'REG-SHARED-CATIN',
            'registration_path' => 'NIKAH_BEDOL',
            'status' => 'DRAFT',
            'marriage_date' => '2026-12-21',
            'marriage_time' => '10:00',
            'marriage_dowry' => 'Emas',
            'marriage_location_address' => 'Test',
        ]);
        $otherRegistration->catins()->attach($sharedCatin);

        $this->delete(route('registrations.destroy', $registration))->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('catins', ['id' => $sharedCatin->id]);
        $this->assertDatabaseMissing('catins', ['nik' => '3201010101010042']);
        $this->assertDatabaseHas('registration_catin', [
            'registration_id' => $otherRegistration->id,
            'catin_id' => $sharedCatin->id,
        ]);
    }

    public function test_n6_cannot_be_created_when_requirement_is_not_met(): void
    {
        $r = $this->region();
        $payload = $this->basePayload($r, '3201010101010031', '3201010101010032');
        $this->post(route('registrations.store'), $payload)->assertRedirect();
        $registration = Registration::latest('id')->firstOrFail();

        $this->get(route('n6.create', $registration))->assertNotFound();
    }

    public function test_store_rejects_region_ids_from_different_hierarchies(): void
    {
        $r = $this->region();
        $otherProvince = Province::create(['id' => '12', 'name' => 'Jawa Barat']);
        $otherRegency = Regency::create(['id' => '12.01', 'province_id' => $otherProvince->id, 'name' => 'Kab Bandung']);
        $payload = $this->basePayload($r, '3201010101010021', '3201010101010022');
        $payload['pria_regency_id'] = $otherRegency->id;

        $this->from(route('registrations.create'))
            ->post(route('registrations.store'), $payload)
            ->assertSessionHasErrors('pria_province_id');

        $this->assertDatabaseCount('registrations', 0);
    }

    public function test_store_persists_parent_region_ids(): void
    {
        $r = $this->region();
        $payload = $this->basePayload($r, '3201010101010011', '3201010101010012');
        foreach (['pria_father', 'pria_mother', 'wanita_father', 'wanita_mother'] as $prefix) {
            $payload[$prefix . '_province_id'] = $r['province']->id;
            $payload[$prefix . '_regency_id'] = $r['regency']->id;
            $payload[$prefix . '_district_id'] = $r['district']->id;
            $payload[$prefix . '_village_id'] = $r['village']->id;
        }

        $this->post(route('registrations.store'), $payload)->assertRedirect();

        $this->assertDatabaseHas('parents', [
            'name' => 'Ayah Pria',
            'province_id' => $r['province']->id,
            'village_id' => $r['village']->id,
        ]);
    }

    public function test_put_edit_persists_catin_and_parent_changes(): void
    {
        $r = $this->region();
        $payload = $this->basePayload($r, '3201010101010001', '3201010101010002');
        $this->post(route('registrations.store'), $payload)->assertRedirect();
        $registration = Registration::latest('id')->firstOrFail();

        $payload['pria_name'] = 'Nama Pria Diperbarui';
        $payload['pria_father_name'] = 'Ayah Diperbarui';
        $payload['pria_father_status'] = 'MENINGGAL';

        $this->put(route('registrations.update', $registration), $payload)
            ->assertRedirect(route('registrations.detail', $registration));

        $this->assertDatabaseHas('catins', ['nik' => '3201010101010001', 'name' => 'Nama Pria Diperbarui']);
        $this->assertDatabaseHas('parents', ['name' => 'Ayah Diperbarui', 'status' => 'MENINGGAL']);
    }

    public function test_autosave_relink_does_not_mutate_catin_role_used_elsewhere(): void
    {
        $r = $this->region();
        $otherRegistration = Registration::create([
            'registration_number' => 'REG-ROLE-SOURCE',
            'registration_path' => 'NIKAH_BEDOL',
            'status' => 'DRAFT',
            'marriage_date' => '2026-12-21',
            'marriage_time' => '10:00',
            'marriage_dowry' => 'Emas',
            'marriage_location_address' => 'Test',
        ]);
        $source = Catin::create([
            'registration_id' => null,
            'role' => 'WANITA',
            'nik' => '3201010101010090',
            'name' => 'Sumber Wanita',
            'birth_place' => 'Semarang',
            'birth_date' => '1997-01-01',
            'marital_status' => 'BELUM_KAWIN',
            'address' => 'Jl',
            'province_id' => $r['province']->id,
            'regency_id' => $r['regency']->id,
            'district_id' => $r['district']->id,
            'village_id' => $r['village']->id,
        ]);
        $otherRegistration->catins()->attach($source);

        $payload = $this->basePayload($r, '3201010101010081', '3201010101010082');
        $this->post(route('registrations.store'), $payload)->assertRedirect();
        $registration = Registration::latest('id')->firstOrFail();

        $this->patchJson(route('registrations.autosave', $registration), ['pria_nik' => $source->nik])
            ->assertStatus(422)
            ->assertJsonValidationErrors('pria_nik');

        $this->assertSame('WANITA', $source->fresh()->role);
        $this->assertFalse($registration->fresh()->catins()->where('catins.nik', $source->nik)->exists());
    }

    public function test_n6_update_and_delete_keep_related_records_in_sync(): void
    {
        $registration = Registration::create([
            'registration_number' => 'REG-N6-SYNC',
            'registration_path' => 'NIKAH_BEDOL',
            'status' => 'DRAFT',
            'marriage_date' => '2026-12-20',
            'marriage_time' => '09:00',
            'marriage_dowry' => 'Emas',
            'marriage_location_address' => 'Test',
        ]);
        RegistrationDocumentChecklist::create(['registration_id' => $registration->id, 'doc_n6' => true]);
        $n6 = N6Document::create([
            'registration_id' => $registration->id,
            'deceased_name' => 'Almarhum',
            'deceased_bin_binti' => 'Bin Ayah',
            'deceased_nik' => '3201010101010077',
            'deceased_birth_place' => 'Semarang',
            'deceased_birth_date' => '1960-01-01',
            'deceased_address' => 'Jl',
            'death_date' => '2025-01-01',
            'document_number' => 'N6-OLD',
            'issue_date' => '2025-01-02',
            'signed_by' => 'Lama',
            'signed_by_position' => 'Kepala Desa',
            'status' => 'PUBLISHED',
        ]);
        RegistrationDocument::create([
            'registration_id' => $registration->id,
            'document_type' => 'N6',
            'document_number' => 'N6-OLD',
            'status' => 'PUBLISHED',
        ]);

        $update = $this->put(route('n6.update', $n6), [
            'deceased_name' => 'Almarhum', 'deceased_bin_binti' => 'Bin Ayah', 'deceased_nik' => '3201010101010077',
            'deceased_birth_place' => 'Semarang', 'deceased_birth_date' => '1960-01-01', 'deceased_address' => 'Jl',
            'death_date' => '2025-01-01', 'document_number' => 'N6-NEW', 'issue_date' => '2025-01-03',
            'signed_by' => 'Baru', 'signed_by_position' => 'Kepala Desa', 'status' => 'PUBLISHED',
        ]);
        $update->assertRedirect(route('n6.show', $n6));
        $this->assertDatabaseHas('registration_documents', ['registration_id' => $registration->id, 'document_number' => 'N6-NEW', 'signed_by' => 'Baru']);

        $this->delete(route('n6.destroy', $n6))->assertRedirect(route('registrations.detail', $registration));
        $this->assertDatabaseMissing('registration_documents', ['registration_id' => $registration->id, 'document_type' => 'N6']);
        $this->assertDatabaseHas('registration_document_checklists', ['registration_id' => $registration->id, 'doc_n6' => false]);
    }

    public function test_witness_form_uses_nested_field_names_without_bin_binti_and_email(): void
    {
        $res = $this->get(route('registrations.create'));
        $res->assertOk();

        $res->assertSee('name="witnesses[0][province_id]"', false);
        $res->assertSee('name="witnesses[0][regency_id]"', false);
        $res->assertSee('name="witnesses[0][district_id]"', false);
        $res->assertSee('name="witnesses[0][village_id]"', false);
        $res->assertDontSee('witness_0_province_id');
        $res->assertDontSee('Bin/Binti');
        $res->assertDontSee('name="witnesses[0][email]"');
        $res->assertDontSee('type="email"');
    }

    public function test_wizard_blocks_next_when_required_fields_missing_and_uses_dmy_birth_date_format(): void
    {
        $res = $this->get(route('registrations.create'));
        $res->assertOk();

        $res->assertSee('reportValidity()', false);
        $res->assertSee('checkValidity()', false);
        $res->assertSee('dd/mm/yyyy', false);
        $res->assertSee('placeholder="dd/mm/yyyy"', false);
    }
}
