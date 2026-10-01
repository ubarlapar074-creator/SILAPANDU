<?php

namespace App\Actions\Registration;

use App\Http\Requests\RegistrationRules;
use App\Models\Catin;
use App\Models\CatinParent;
use App\Models\DocumentChecklist;
use App\Models\Guardian;
use App\Models\Registration;
use App\Models\RegistrationDocumentChecklist;
use App\Models\Witness;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpsertRegistrationAction
{
    public static function validateGuardian(array $validated): void
    {
        if (($validated['guardian_relationship'] ?? null) === 'AYAH_KANDUNG') {
            if (($validated['wanita_father_status'] ?? null) !== 'HIDUP') {
                throw ValidationException::withMessages(['guardian_relationship' => 'Ayah kandung yang berstatus meninggal tidak dapat dipilih sebagai wali.']);
            }
        } elseif (($validated['guardian_relationship'] ?? null) !== 'WALI_HAKIM' && !empty($validated['guardian_relationship'])) {
            foreach (['guardian_name' => 'Nama lengkap wali', 'guardian_nik' => 'NIK wali (16 digit)', 'guardian_bin_binti' => 'Bin/Binti wali', 'guardian_birth_place' => 'Tempat lahir wali', 'guardian_birth_date' => 'Tanggal lahir wali', 'guardian_marital_status' => 'Status perkawinan wali', 'guardian_address' => 'Alamat wali'] as $field => $label) {
                if (empty($validated[$field])) throw ValidationException::withMessages([$field => $label.' wajib diisi untuk wali keluarga (non Hakim).']);
            }
        }
        if (isset($validated['pria_nik'], $validated['wanita_nik']) && $validated['pria_nik'] === $validated['wanita_nik']) {
            throw ValidationException::withMessages(['wanita_nik' => 'NIK Catin Pria dan Wanita tidak boleh sama.']);
        }
    }

    public static function create(Registration $registration, array $validated): void
    {
        $catinPria = self::resolveCatin($registration, 'PRIA', [
            'nik' => $validated['pria_nik'], 'name' => $validated['pria_name'], 'bin_binti' => $validated['pria_bin_binti'] ?? null,
            'birth_place' => $validated['pria_birth_place'], 'birth_date' => $validated['pria_birth_date'], 'nationality' => $validated['pria_nationality'] ?? null,
            'religion' => $validated['pria_religion'] ?? null, 'marital_status' => $validated['pria_marital_status'], 'no_hp' => $validated['pria_no_hp'] ?? null,
            'email' => $validated['pria_email'] ?? null, 'occupation' => $validated['pria_occupation'] ?? null, 'education' => $validated['pria_education'] ?? null,
            'address' => $validated['pria_address'], 'province_id' => $validated['pria_province_id'], 'regency_id' => $validated['pria_regency_id'],
            'district_id' => $validated['pria_district_id'], 'village_id' => $validated['pria_village_id'],
        ]);
        $catinWanita = self::resolveCatin($registration, 'WANITA', [
            'nik' => $validated['wanita_nik'], 'name' => $validated['wanita_name'], 'bin_binti' => $validated['wanita_bin_binti'] ?? null,
            'birth_place' => $validated['wanita_birth_place'], 'birth_date' => $validated['wanita_birth_date'], 'nationality' => $validated['wanita_nationality'] ?? null,
            'religion' => $validated['wanita_religion'] ?? null, 'marital_status' => $validated['wanita_marital_status'], 'no_hp' => $validated['wanita_no_hp'] ?? null,
            'email' => $validated['wanita_email'] ?? null, 'occupation' => $validated['wanita_occupation'] ?? null, 'education' => $validated['wanita_education'] ?? null,
            'address' => $validated['wanita_address'], 'province_id' => $validated['wanita_province_id'], 'regency_id' => $validated['wanita_regency_id'],
            'district_id' => $validated['wanita_district_id'], 'village_id' => $validated['wanita_village_id'],
        ]);
        $fatherWanita = self::upsertParents($catinPria, 'pria', $validated);
        $fatherWanita = self::upsertParents($catinWanita, 'wanita', $validated, true);
        self::createGuardian($registration, $validated, $fatherWanita);
        self::createWitnesses($registration, $validated['witnesses'] ?? []);
        self::upsertDocumentChecklist($catinPria, $validated['catin_pria_checklist'] ?? []);
        self::upsertDocumentChecklist($catinWanita, $validated['catin_wanita_checklist'] ?? []);
        RegistrationDocumentChecklist::create(['registration_id' => $registration->id, 'doc_n1_n4' => (bool)($validated['registration_checklist']['doc_n1_n4'] ?? false), 'doc_n6' => (bool)($validated['registration_checklist']['doc_n6'] ?? false)]);
    }

    private static function resolveCatin(Registration $registration, string $role, array $data): Catin
    {
        $existing = Catin::where('nik', $data['nik'])->lockForUpdate()->first();
        if ($existing) {
            $existing->fill(array_intersect_key($data, array_flip(['name','bin_binti','birth_place','birth_date','nationality','religion','marital_status','no_hp','email','occupation','education','address','province_id','regency_id','district_id','village_id'])));
            $existing->save();
            $registration->catins()->syncWithoutDetaching([$existing->id]);
            return $existing;
        }
        $catin = new Catin(array_merge($data, ['role' => $role, 'registration_id' => $registration->id]));
        $catin->save();
        $registration->catins()->syncWithoutDetaching([$catin->id]);
        return $catin;
    }

    private static function upsertParents(Catin $catin, string $prefix, array $validated, bool $returnFather = false): ?CatinParent
    {
        $father = self::upsertParent($catin, 'AYAH', $prefix.'_father', $validated);
        self::upsertParent($catin, 'IBU', $prefix.'_mother', $validated);
        return $returnFather ? $father : null;
    }

    private static function upsertParent(Catin $catin, string $role, string $keyPrefix, array $validated): CatinParent
    {
        $status = $validated[$keyPrefix.'_status'] ?? null;
        $name = $validated[$keyPrefix.'_name'] ?? null;
        $parent = CatinParent::firstOrNew(['catin_id' => $catin->id, 'role' => $role]);
        if ($status !== null) $parent->status = $status;
        if ($name !== null) $parent->name = $name;
        $currentStatus = $parent->status ?? $status;
        if ($currentStatus === 'MENINGGAL') {
            foreach (['bin_binti','nik','birth_place','birth_date','address','education','occupation','marital_status','phone','email','province_id','regency_id','district_id','village_id'] as $f) $parent->$f = null;
        } else {
            foreach (['bin_binti','nik','birth_place','birth_date','address','education','occupation','marital_status','phone','email','province_id','regency_id','district_id','village_id'] as $f) {
                $k = $keyPrefix.'_'.$f;
                if (array_key_exists($k, $validated)) $parent->$f = $validated[$k];
            }
        }
        $parent->catin_id = $catin->id; $parent->role = $role;
        if (empty($parent->status)) $parent->status = 'HIDUP';
        $parent->save();
        return $parent;
    }

    private static function createGuardian(Registration $registration, array $validated, ?CatinParent $fatherWanita): void
    {
        $guardian = new Guardian(['registration_id' => $registration->id]);
        $sebab = trim((string) ($validated['guardian_sebab_wali'] ?? ''));
        if ($sebab === '') $sebab = \App\Support\WaliHelper::defaultSebab($validated['guardian_relationship'], $fatherWanita?->status ?? null);
        if ($validated['guardian_relationship'] === 'AYAH_KANDUNG') {
            $guardian->fill(['guardian_source' => 'PARENT', 'parent_id' => $fatherWanita?->id, 'relationship' => 'AYAH_KANDUNG', 'sebab_wali' => $sebab, 'marital_status' => null, 'name' => null, 'nik' => null, 'bin_binti' => null, 'birth_place' => null, 'birth_date' => null, 'occupation' => null, 'address' => null, 'province_id' => null, 'regency_id' => null, 'district_id' => null, 'village_id' => null]);
        } elseif ($validated['guardian_relationship'] === 'WALI_HAKIM') {
            $guardian->fill(['guardian_source' => 'OTHER', 'parent_id' => null, 'relationship' => 'WALI_HAKIM', 'sebab_wali' => $sebab, 'marital_status' => null, 'name' => 'Wali Hakim (KUA)', 'nik' => null, 'bin_binti' => null, 'birth_place' => null, 'birth_date' => null, 'occupation' => null, 'address' => 'Kantor KUA Setempat', 'province_id' => null, 'regency_id' => null, 'district_id' => null, 'village_id' => null]);
        } else {
            $guardian->fill(['guardian_source' => 'OTHER', 'parent_id' => null, 'relationship' => $validated['guardian_relationship'], 'sebab_wali' => $sebab, 'marital_status' => $validated['guardian_marital_status'] ?? null, 'name' => $validated['guardian_name'], 'nik' => $validated['guardian_nik'], 'bin_binti' => $validated['guardian_bin_binti'], 'birth_place' => $validated['guardian_birth_place'] ?? null, 'birth_date' => $validated['guardian_birth_date'] ?? null, 'occupation' => $validated['guardian_occupation'] ?? null, 'address' => $validated['guardian_address'], 'province_id' => $validated['guardian_province_id'] ?? null, 'regency_id' => $validated['guardian_regency_id'] ?? null, 'district_id' => $validated['guardian_district_id'] ?? null, 'village_id' => $validated['guardian_village_id'] ?? null]);
        }
        $guardian->save();
    }

    private static function createWitnesses(Registration $registration, array $witnesses): void
    {
        foreach ($witnesses as $w) {
            if (empty(array_filter($w, fn($v) => $v !== null && $v !== ''))) continue;
            if (trim((string)($w['name'] ?? '')) === '') continue;
            $wit = new Witness(array_merge(['registration_id' => $registration->id], array_intersect_key($w, array_flip(['name','gender','bin_binti','nik','birth_place','birth_date','nationality','religion','marital_status','education','occupation','phone','email','address','province_id','regency_id','district_id','village_id']))));
            $wit->save();
        }
    }

    private static function upsertDocumentChecklist(Catin $catin, array $checklist): void
    {
        $data = ['doc_ktp' => (bool)($checklist['doc_ktp'] ?? false), 'doc_kk' => (bool)($checklist['doc_kk'] ?? false), 'doc_birth' => (bool)($checklist['doc_birth'] ?? false), 'doc_immunization' => (bool)($checklist['doc_immunization'] ?? false), 'doc_photo' => (bool)($checklist['doc_photo'] ?? false)];
        $existing = DocumentChecklist::where('catin_id', $catin->id)->first();
        $existing ? $existing->update($data) : DocumentChecklist::create(array_merge(['catin_id' => $catin->id], $data));
    }
}
