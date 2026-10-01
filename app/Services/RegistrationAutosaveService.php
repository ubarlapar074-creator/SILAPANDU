<?php

namespace App\Services;

use App\Models\Catin;
use App\Models\CatinParent;
use App\Models\Registration;
use App\Models\Witness;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class RegistrationAutosaveService
{
    /**
     * Simpan perubahan parsial pendaftaran (autosave).
     * Dipanggil dari RegistrationController@autosave — dipisah agar controller tetap ramping.
     * Semua tulis dibungkus transaksi; validasi hierarki wilayah & NIK ganda ditangani di sini.
     */
    public function autosave(Registration $registration, array $validated, \Illuminate\Http\Request $request): void
    {
        if (empty($validated) && empty($request->input('witnesses'))) return;

        if (isset($validated['pria_nik'], $validated['wanita_nik']) && $validated['pria_nik'] === $validated['wanita_nik']) {
            throw ValidationException::withMessages(['wanita_nik' => 'NIK Catin Pria dan Wanita tidak boleh sama.']);
        }

        $registration->load(['catins.parents', 'guardian', 'witnesses']);
        \App\Support\AddressHelper::normalizeValidatedAddresses($validated);

        try {
            DB::transaction(function () use ($registration, $validated) {
                $this->ensureNikNotDuplicated($validated, $registration);
                $this->updateRegistrationFields($registration, $validated);
                $this->updateCatins($registration, $validated);
                $this->updateParents($registration, $validated);
                $this->updateGuardian($registration, $validated);
                $this->updateWitnesses($registration, $validated);
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Autosave failed', ['registration_id' => $registration->id, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }

    private function ensureNikNotDuplicated(array $validated, Registration $registration): void
    {
        $priaNik = $validated['pria_nik'] ?? $registration->catins->firstWhere('role', 'PRIA')?->nik;
        $wanitaNik = $validated['wanita_nik'] ?? $registration->catins->firstWhere('role', 'WANITA')?->nik;
        if ($priaNik && $wanitaNik && $priaNik === $wanitaNik) {
            throw ValidationException::withMessages(['wanita_nik' => 'NIK Catin Pria dan Wanita tidak boleh sama.']);
        }
    }

    /** Update field level Registration (tanggal, mas kawin, alamat akad). */
    private function updateRegistrationFields(Registration $registration, array $validated): void
    {
        $fields = array_intersect_key($validated, array_flip(['registration_path', 'marriage_date', 'marriage_time', 'marriage_dowry', 'marriage_dowry_type', 'marriage_dowry_money', 'marriage_dowry_gold', 'marriage_dowry_other', 'marriage_location_address']));
        $hasDowry = array_key_exists('marriage_dowry_type', $fields) || array_key_exists('marriage_dowry_money', $fields) || array_key_exists('marriage_dowry_gold', $fields) || array_key_exists('marriage_dowry_other', $fields);
        if ($hasDowry) {
            $hasMoneyEtc = array_key_exists('marriage_dowry_money', $fields) || array_key_exists('marriage_dowry_gold', $fields) || array_key_exists('marriage_dowry_other', $fields);
            if (!array_key_exists('marriage_dowry_type', $fields) && $hasMoneyEtc && empty($registration->marriage_dowry_type)) {
                unset($fields['marriage_dowry_money'], $fields['marriage_dowry_gold'], $fields['marriage_dowry_other']);
            } else {
                if (!array_key_exists('marriage_dowry_type', $fields)) $fields['marriage_dowry_type'] = $registration->marriage_dowry_type;
                $this->assignDowry($registration, $fields);
                unset($fields['marriage_dowry_type'], $fields['marriage_dowry_money'], $fields['marriage_dowry_gold'], $fields['marriage_dowry_other']);
                $fields['marriage_dowry'] = $registration->marriage_dowry;
                $fields['marriage_dowry_type'] = $registration->marriage_dowry_type;
                $fields['marriage_dowry_money'] = $registration->marriage_dowry_money;
                $fields['marriage_dowry_gold'] = $registration->marriage_dowry_gold;
                $fields['marriage_dowry_other'] = $registration->marriage_dowry_other;
            }
        }
        if (!empty($fields)) $registration->update($fields);
    }

    private function assignDowry(Registration $registration, array $data): void
    {
        $type = $data['marriage_dowry_type'] ?? $registration->marriage_dowry_type ?? null;
        if (!$type) {
            $registration->marriage_dowry_type = $registration->marriage_dowry_money = $registration->marriage_dowry_gold = $registration->marriage_dowry_other = $registration->marriage_dowry = null;
            return;
        }
        $registration->marriage_dowry_type = $type;
        $registration->marriage_dowry_money = $data['marriage_dowry_money'] ?? null;
        $registration->marriage_dowry_gold = $data['marriage_dowry_gold'] ?? null;
        $registration->marriage_dowry_other = $data['marriage_dowry_other'] ?? null;
        $registration->marriage_dowry = match ($type) {
            'UANG_EMAS' => trim(collect([$registration->marriage_dowry_money ? 'Uang ' . $registration->marriage_dowry_money : null, $registration->marriage_dowry_gold ? 'Emas ' . $registration->marriage_dowry_gold : null])->filter()->implode(' dan ')),
            'UANG' => 'Uang ' . ($registration->marriage_dowry_money ?? ''),
            'EMAS' => 'Emas ' . ($registration->marriage_dowry_gold ?? ''),
            'LAINNYA' => (string) ($registration->marriage_dowry_other ?? ''),
            default => (string) ($registration->marriage_dowry ?? ''),
        };
    }

    /** Handle ganti NIK catin: reuse existing atau buat baru, lalu sync pivot. */
    private function updateCatins(Registration $registration, array $validated): void
    {
        $pria = $registration->catins->firstWhere('role', 'PRIA');
        $wanita = $registration->catins->firstWhere('role', 'WANITA');
        foreach ([['key' => 'pria_nik', 'catin' => $pria, 'role' => 'PRIA'], ['key' => 'wanita_nik', 'catin' => $wanita, 'role' => 'WANITA']] as $item) {
            $key = $item['key']; $catin = $item['catin']; $role = $item['role'];
            if (!isset($validated[$key]) || !$catin || $validated[$key] === $catin->nik) continue;
            $newNik = $validated[$key];
            $existing = Catin::where('nik', $newNik)->lockForUpdate()->first();
            if ($existing) {
                $this->swapToExistingCatin($registration, $catin, $existing, $role, $key);
            } else {
                $this->createNewCatinForNikChange($registration, $catin, $validated, $role, $newNik);
            }
        }
        $pria = $registration->catins->firstWhere('role', 'PRIA');
        $wanita = $registration->catins->firstWhere('role', 'WANITA');
        if ($pria) $this->applyCatinFieldUpdates($pria, $validated, 'pria');
        if ($wanita) $this->applyCatinFieldUpdates($wanita, $validated, 'wanita');
    }

    private function swapToExistingCatin(Registration $registration, Catin $old, Catin $existing, string $role, string $key): void
    {
        if ($existing->id !== $old->id) {
            if ($existing->role !== $role && !$existing->registrations()->exists()) {
                $existing->update(['role' => $role]);
            } elseif ($existing->role !== $role) {
                throw ValidationException::withMessages([$key => 'NIK tersebut sudah digunakan dengan peran berbeda pada pendaftaran lain.']);
            }
            $registration->catins()->detach($old->id);
            $registration->catins()->syncWithoutDetaching([$existing->id]);
        }
    }

    private function createNewCatinForNikChange(Registration $registration, Catin $old, array $validated, string $role, string $newNik): void
    {
        $prefix = $role === 'PRIA' ? 'pria' : 'wanita';
        $mapKeys = ['name' => $prefix.'_name', 'bin_binti' => $prefix.'_bin_binti', 'birth_place' => $prefix.'_birth_place', 'birth_date' => $prefix.'_birth_date', 'nationality' => $prefix.'_nationality', 'religion' => $prefix.'_religion', 'marital_status' => $prefix.'_marital_status', 'no_hp' => $prefix.'_no_hp', 'email' => $prefix.'_email', 'occupation' => $prefix.'_occupation', 'education' => $prefix.'_education', 'address' => $prefix.'_address', 'province_id' => $prefix.'_province_id', 'regency_id' => $prefix.'_regency_id', 'district_id' => $prefix.'_district_id', 'village_id' => $prefix.'_village_id'];
        $hasEnough = true;
        foreach (['name','birth_place','birth_date','address','province_id','regency_id','district_id','village_id'] as $need) {
            if (!array_key_exists($mapKeys[$need], $validated) && empty($old->$need)) $hasEnough = false;
        }
        if (!$hasEnough) return;
        try {
            $new = new Catin();
            $new->role = $role; $new->nik = $newNik;
            foreach (['name','bin_binti','birth_place','birth_date','nationality','religion','marital_status','no_hp','email','occupation','education','address','province_id','regency_id','district_id','village_id'] as $col) {
                $new->$col = $validated[$mapKeys[$col]] ?? $old->$col;
            }
            $new->registration_id = null; $new->save();
            $registration->catins()->detach($old->id);
            $registration->catins()->syncWithoutDetaching([$new->id]);
            foreach ($old->parents as $pr) {
                CatinParent::firstOrCreate(['catin_id' => $new->id, 'role' => $pr->role], $pr->only(['status','name','nik','birth_place','birth_date','address','education','occupation','phone','email','bin_binti','marital_status','province_id','regency_id','district_id','village_id']));
            }
        } catch (\Illuminate\Database\UniqueConstraintViolationException|\Illuminate\Database\QueryException $e) {
            if (str_contains($e->getMessage(), 'catins') || str_contains($e->getMessage(), 'nik')) {
                throw ValidationException::withMessages([$prefix.'_nik' => 'NIK tersebut baru saja dipakai, silakan coba lagi.']);
            }
            throw $e;
        }
    }

    private function applyCatinFieldUpdates(Catin $catin, array $validated, string $prefix): void
    {
        $map = ['nik' => $prefix.'_nik', 'name' => $prefix.'_name', 'bin_binti' => $prefix.'_bin_binti', 'birth_place' => $prefix.'_birth_place', 'birth_date' => $prefix.'_birth_date', 'nationality' => $prefix.'_nationality', 'religion' => $prefix.'_religion', 'marital_status' => $prefix.'_marital_status', 'no_hp' => $prefix.'_no_hp', 'email' => $prefix.'_email', 'occupation' => $prefix.'_occupation', 'education' => $prefix.'_education', 'address' => $prefix.'_address', 'province_id' => $prefix.'_province_id', 'regency_id' => $prefix.'_regency_id', 'district_id' => $prefix.'_district_id', 'village_id' => $prefix.'_village_id'];
        $required = ['nik','name','birth_place','birth_date','marital_status','address','province_id','regency_id','district_id','village_id'];
        $data = [];
        foreach ($map as $col => $key) {
            if (!array_key_exists($key, $validated)) continue;
            $val = $validated[$key];
            if (in_array($col, $required, true) && ($val === null || $val === '')) continue;
            $data[$col] = $val;
        }
        if (isset($data['nik']) && Catin::where('nik', $data['nik'])->where('id', '!=', $catin->id)->exists()) unset($data['nik']);
        if (!empty($data)) $catin->update($data);
    }

    /** Update orang tua (4 slot) — jika meninggal, kosongkan field identitas. */
    private function updateParents(Registration $registration, array $validated): void
    {
        $pria = $registration->catins->firstWhere('role', 'PRIA');
        $wanita = $registration->catins->firstWhere('role', 'WANITA');
        foreach ([['catin' => $pria, 'role' => 'AYAH', 'prefix' => 'pria_father'], ['catin' => $pria, 'role' => 'IBU', 'prefix' => 'pria_mother'], ['catin' => $wanita, 'role' => 'AYAH', 'prefix' => 'wanita_father'], ['catin' => $wanita, 'role' => 'IBU', 'prefix' => 'wanita_mother']] as $cfg) {
            if (!$cfg['catin']) continue;
            $prefix = $cfg['prefix'];
            $hasAny = false;
            foreach (['status','name','nik','birth_place','birth_date','address','education','occupation','province_id','regency_id','district_id','village_id'] as $f) {
                if (array_key_exists($prefix.'_'.$f, $validated)) { $hasAny = true; break; }
            }
            if (!$hasAny) continue;
            $parent = $cfg['catin']->parents()->firstOrNew(['role' => $cfg['role']]);
            if (!$parent->exists && empty($validated[$prefix.'_name'] ?? $parent->name)) continue;
            if (array_key_exists($prefix.'_status', $validated) && $validated[$prefix.'_status'] !== null && $validated[$prefix.'_status'] !== '') $parent->status = $validated[$prefix.'_status'];
            if (array_key_exists($prefix.'_name', $validated) && $validated[$prefix.'_name'] !== null && $validated[$prefix.'_name'] !== '') $parent->name = $validated[$prefix.'_name'];
            $status = $validated[$prefix.'_status'] ?? $parent->status ?? 'HIDUP';
            if ($status === 'MENINGGAL') {
                foreach (['nik','birth_place','birth_date','address','education','occupation','province_id','regency_id','district_id','village_id'] as $f) $parent->$f = null;
            } else {
                foreach (['nik','birth_place','birth_date','address','education','occupation','province_id','regency_id','district_id','village_id'] as $f) {
                    $k = $prefix.'_'.$f;
                    if (array_key_exists($k, $validated) && $validated[$k] !== '' && $validated[$k] !== null) $parent->$f = $validated[$k];
                    elseif (array_key_exists($k, $validated) && $validated[$k] === null) $parent->$f = null;
                }
            }
            if (empty($parent->status) || empty($parent->name)) continue;
            $parent->save();
            $cfg['catin']->load('parents');
        }
    }

    /** Update wali — SSOT: AYAH_KANDUNG hanya simpan parent_id, tidak duplikasi identitas. */
    private function updateGuardian(Registration $registration, array $validated): void
    {
        if (!array_key_exists('guardian_relationship', $validated) && !array_key_exists('guardian_sebab_wali', $validated) && !array_key_exists('guardian_marital_status', $validated) && !array_key_exists('guardian_name', $validated) && !array_key_exists('guardian_nik', $validated) && !array_key_exists('guardian_address', $validated)) return;
        $guardian = $registration->guardian;
        if (!$guardian) return;
        $wanita = $registration->catins->firstWhere('role', 'WANITA');
        $rel = $validated['guardian_relationship'] ?? $guardian->relationship;
        $ayahStatus = $wanita?->parents->firstWhere('role', 'AYAH')?->status ?? null;
        $sebabFromInput = array_key_exists('guardian_sebab_wali', $validated) ? trim((string) $validated['guardian_sebab_wali']) : null;
        if ($rel === 'AYAH_KANDUNG') {
            $father = $wanita?->parents->firstWhere('role', 'AYAH');
            $sebab = ($sebabFromInput !== null && $sebabFromInput !== '') ? $sebabFromInput : \App\Support\WaliHelper::defaultSebab('AYAH_KANDUNG', $ayahStatus);
            $guardian->update(['relationship' => 'AYAH_KANDUNG', 'guardian_source' => 'PARENT', 'parent_id' => $father?->id, 'sebab_wali' => $sebab, 'marital_status' => null, 'name' => null, 'nik' => null, 'bin_binti' => null, 'birth_place' => null, 'birth_date' => null, 'occupation' => null, 'address' => null, 'province_id' => null, 'regency_id' => null, 'district_id' => null, 'village_id' => null]);
        } elseif ($rel === 'WALI_HAKIM') {
            $sebab = ($sebabFromInput !== null && $sebabFromInput !== '') ? $sebabFromInput : \App\Support\WaliHelper::defaultSebab('WALI_HAKIM', $ayahStatus);
            $payload = ['relationship' => 'WALI_HAKIM', 'guardian_source' => 'OTHER', 'parent_id' => null, 'sebab_wali' => $sebab, 'marital_status' => null, 'name' => 'Wali Hakim (KUA)', 'address' => 'Kantor KUA Setempat', 'province_id' => null, 'regency_id' => null, 'district_id' => null, 'village_id' => null];
            if (array_key_exists('guardian_sebab_wali', $validated)) $payload['sebab_wali'] = $sebab;
            $guardian->update($payload);
        } else {
            $data = [];
            if (array_key_exists('guardian_relationship', $validated)) $data['relationship'] = $validated['guardian_relationship'];
            if ($sebabFromInput !== null) $data['sebab_wali'] = $sebabFromInput !== '' ? $sebabFromInput : \App\Support\WaliHelper::defaultSebab($rel, $ayahStatus);
            if (array_key_exists('guardian_marital_status', $validated)) $data['marital_status'] = $validated['guardian_marital_status'];
            foreach (['name' => 'guardian_name', 'nik' => 'guardian_nik', 'bin_binti' => 'guardian_bin_binti', 'birth_place' => 'guardian_birth_place', 'birth_date' => 'guardian_birth_date', 'occupation' => 'guardian_occupation', 'address' => 'guardian_address', 'province_id' => 'guardian_province_id', 'regency_id' => 'guardian_regency_id', 'district_id' => 'guardian_district_id', 'village_id' => 'guardian_village_id'] as $col => $key) {
                if (array_key_exists($key, $validated)) $data[$col] = $validated[$key];
            }
            if (!empty($data)) { $data['guardian_source'] = 'OTHER'; $data['parent_id'] = null; $guardian->update($data); }
            elseif ($sebabFromInput !== null || array_key_exists('guardian_marital_status', $validated)) { $fallback = $data['sebab_wali'] ?? \App\Support\WaliHelper::defaultSebab($rel, $ayahStatus); $upd = []; if ($sebabFromInput !== null) $upd['sebab_wali'] = $fallback; if (array_key_exists('guardian_marital_status', $validated)) $upd['marital_status'] = $validated['guardian_marital_status']; if (!empty($upd)) $guardian->update($upd); }
        }
    }

    /** Sinkron saksi: hapus kelebihan, update existing, tambah baru. */
    private function updateWitnesses(Registration $registration, array $validated): void
    {
        if (!array_key_exists('witnesses', $validated) || !is_array($validated['witnesses'])) return;
        $input = array_values(array_filter($validated['witnesses'], fn($w) => is_array($w) && !empty(array_filter($w, fn($v) => $v !== null && $v !== ''))));
        $input = array_values(array_filter($input, fn($w) => trim((string)($w['name'] ?? '')) !== ''));
        $existing = $registration->witnesses->sortBy('id')->values();
        if (count($input) < $existing->count()) {
            foreach ($existing->slice(count($input)) as $del) $del->delete();
            $existing = $existing->take(count($input));
        }
        $fillable = ['name','gender','bin_binti','nik','birth_place','birth_date','nationality','religion','marital_status','education','occupation','phone','email','address','province_id','regency_id','district_id','village_id'];
        $required = ['name','gender','nik','birth_place','birth_date','marital_status','address','province_id','regency_id','district_id','village_id'];
        foreach ($input as $idx => $wData) {
            $data = [];
            foreach ($fillable as $col) {
                if (!array_key_exists($col, $wData)) continue;
                $val = $wData[$col] === '' ? null : $wData[$col];
                if (in_array($col, $required, true) && ($val === null || $val === '')) continue;
                $data[$col] = $val;
            }
            if (empty($data)) continue;
            $target = $existing->get($idx);
            if ($target) { if (!empty($data)) $target->update($data); }
            elseif (!empty($data['name'])) Witness::create(array_merge(['registration_id' => $registration->id], $data));
        }
    }
}
