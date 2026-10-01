<div class="space-y-4 text-sm">
    <!-- Informasi -->
    <div class="bg-amber-50 border border-amber-200 p-3 rounded-lg text-amber-800 text-xs">
        <strong>Informasi:</strong>
        Pilih wali nikah sesuai hubungan wali dengan Catin Wanita.
        <span x-show="isFatherMeninggal" class="block mt-1 text-amber-900 font-semibold">
            ⚠️ Ayah Kandung Catin Wanita berstatus Meninggal.
            N6 diperlukan untuk memenuhi persyaratan dokumen.
        </span>
    </div>

    <!-- Dropdown Wali -->
    <div>
        <label class="block font-medium text-slate-700">
            Hubungan dengan Catin Wanita
            <span class="text-red-500">*</span>
        </label>

        <select
            name="guardian_relationship"
            x-model="relationship"
            @change="handleRelationshipChange()"
            class="mt-1 block w-full rounded-md border-slate-300 shadow-sm
                   focus:border-emerald-500 focus:ring-emerald-500"
            required
        >
            <option value="">-- Pilih Wali --</option>

            <option
                value="AYAH_KANDUNG"
                :disabled="isFatherMeninggal"
                :class="isFatherMeninggal ? 'text-gray-400 bg-gray-100' : ''"
            >
                Ayah Kandung
                <span x-show="isFatherMeninggal" class="text-xs text-red-500">
                    (Meninggal - tidak dapat dipilih)
                </span>
            </option>

            <option value="KAKEK">Kakek (Dari Ayah)</option>
            <option value="SAUDARA_KANDUNG_LAKI">Saudara Kandung Laki-laki</option>
            <option value="PAMAN">Paman (Pihak Ayah)</option>
            <option value="WALI_HAKIM">Wali Hakim</option>
        </select>

        <p x-show="isFatherMeninggal" class="mt-1 text-xs text-red-600">
            ⚠️ Ayah Kandung berstatus Meninggal, tidak dapat dipilih sebagai Wali.
        </p>
        <div x-show="relationship !== '' && relationship !== 'WALI_HAKIM'" x-transition class="mt-3">
            <label for="guardian_sebab_wali" class="block font-medium text-slate-700">Sebab / Alasan Peralihan Wali <span class="text-xs font-normal text-slate-500">(otomatis, dapat diubah)</span></label>
            <textarea id="guardian_sebab_wali" name="guardian_sebab_wali" x-model="sebabWali" rows="2" maxlength="500" placeholder="Otomatis terisi sesuai hubungan wali, dapat disesuaikan untuk kasus khusus (berhalangan/adlal)" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm text-sm focus:border-emerald-500 focus:ring-emerald-500"></textarea>
            <p class="mt-1 text-[11px] text-slate-500">Otomatis terisi berdasarkan pilihan hubungan wali & status ayah kandung; edit manual untuk kasus khusus (mis. adlal/berhalangan spesifik).</p>
        </div>
    </div>

    <!-- AYAH KANDUNG (SSoT) -->
    <div
        x-show="relationship === 'AYAH_KANDUNG'"
        x-transition
        class="bg-emerald-50 border border-emerald-200 rounded-lg p-4"
    >
        <div class="flex items-start gap-3">
            <div class="text-emerald-600 text-lg">✓</div>
            <div>
                <p class="font-semibold text-emerald-800">
                    Wali adalah Ayah Kandung
                </p>
                <p class="text-xs text-emerald-700 mt-1">
                    Data wali akan menggunakan data Ayah Kandung
                    yang sudah diisi pada bagian Orang Tua Catin Wanita.
                    Tidak perlu mengisi NIK, nama, alamat, dan data lainnya lagi.
                </p>
            </div>
        </div>
        <input type="hidden" name="guardian_source" value="PARENT">
    </div>

    <!-- WALI HAKIM -->
    <div
        x-show="relationship === 'WALI_HAKIM'"
        x-transition
        class="bg-blue-50 border border-blue-200 rounded-lg p-4"
    >
        <div class="flex items-start gap-3">
            <div class="text-blue-600 text-lg">🏛️</div>
            <div>
                <p class="font-semibold text-blue-800">
                    Wali Hakim
                </p>
                <p class="text-xs text-blue-700 mt-1">
                    Wali Hakim terdaftar di KUA setempat.
                    Data akan diisi oleh petugas KUA.
                    <span class="block mt-1 font-medium">
                        Tidak memerlukan Surat Pernyataan Wali.
                    </span>
                </p>
            </div>
        </div>
        <input type="hidden" name="guardian_source" value="OTHER">
        <input type="hidden" name="guardian_name" value="Wali Hakim (KUA)">
        <input type="hidden" name="guardian_nik" value="">
        <input type="hidden" name="guardian_bin_binti" value="">
        <input type="hidden" name="guardian_address" value="Kantor KUA">
    </div>

    <!-- FORM WALI LAIN -->
    <div
        x-show="relationship !== '' && relationship !== 'AYAH_KANDUNG' && relationship !== 'WALI_HAKIM'"
        x-transition
        class="grid grid-cols-1 md:grid-cols-2 gap-4"
    >
        <div>
            <label class="block font-medium text-slate-700">
                Nama Lengkap Wali
                <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                name="guardian_name"
                value="{{ old('guardian_name', $guardian?->name ?? '') }}"
                placeholder="Nama lengkap wali"
                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm
                       focus:border-emerald-500 focus:ring-emerald-500"
                required
            >
        </div>

        <div>
            <label class="block font-medium text-slate-700">
                NIK Wali
                <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                name="guardian_nik"
                value="{{ old('guardian_nik', $guardian?->nik ?? '') }}"
                maxlength="16"
                placeholder="16 digit NIK"
                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm
                       focus:border-emerald-500 focus:ring-emerald-500"
                required
            >
        </div>

        <div>
            <label class="block font-medium text-slate-700">
                Bin / Binti
                <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                name="guardian_bin_binti"
                value="{{ old('guardian_bin_binti', $guardian?->bin_binti ?? '') }}"
                placeholder="Nama ayah wali"
                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm
                       focus:border-emerald-500 focus:ring-emerald-500"
                required
            >
        </div>

        <div>
            <label class="block font-medium text-slate-700">
                Tempat & Tanggal Lahir
                <span class="text-red-500">*</span>
            </label>
            <div class="grid grid-cols-2 gap-2 mt-1">
                <input
                    type="text"
                    name="guardian_birth_place"
                    value="{{ old('guardian_birth_place', $guardian?->birth_place ?? '') }}"
                    placeholder="Tempat"
                    class="rounded-md border-slate-300 shadow-sm text-sm
                           focus:border-emerald-500 focus:ring-emerald-500"
                    required
                >
                <input
                    type="date"
                    name="guardian_birth_date"
                    value="{{ old('guardian_birth_date', $guardian?->birth_date?->format('Y-m-d') ?? '') }}"
                    class="rounded-md border-slate-300 shadow-sm text-sm
                           focus:border-emerald-500 focus:ring-emerald-500"
                    required
                >
            </div>
            <p class="mt-1 text-[11px] text-slate-500">TTL diperlukan untuk wali keluarga agar cetak N1–N6 valid.</p>
        </div>

        <div>
            <label class="block font-medium text-slate-700">
                Pekerjaan
            </label>
            <input
                type="text"
                name="guardian_occupation"
                value="{{ old('guardian_occupation', $guardian?->occupation ?? '') }}"
                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm
                       focus:border-emerald-500 focus:ring-emerald-500"
            >
        </div>

        <div>
            <label class="block font-medium text-slate-700">
                Status Perkawinan <span class="text-red-500">*</span>
            </label>
            <select name="guardian_marital_status" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                <option value="">-- Pilih --</option>
                <option value="BELUM_KAWIN" @selected(old('guardian_marital_status', $guardian?->marital_status ?? '') === 'BELUM_KAWIN')>Belum Kawin</option>
                <option value="KAWIN" @selected(old('guardian_marital_status', $guardian?->marital_status ?? '') === 'KAWIN')>Kawin</option>
                <option value="CERAI_HIDUP" @selected(old('guardian_marital_status', $guardian?->marital_status ?? '') === 'CERAI_HIDUP')>Cerai Hidup</option>
                <option value="CERAI_MATI" @selected(old('guardian_marital_status', $guardian?->marital_status ?? '') === 'CERAI_MATI')>Cerai Mati</option>
            </select>
        </div>

        <div class="md:col-span-2">
            <label class="block font-medium text-slate-700">
                Alamat Lengkap Wali
                <span class="text-red-500">*</span>
            </label>
            <textarea
                name="guardian_address"
                rows="2"
                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm
                       focus:border-emerald-500 focus:ring-emerald-500"
                required
            >{{ old('guardian_address', $guardian?->address ?? '') }}</textarea>
        </div>

        {{-- REGION SELECTOR UNTUK WALI LAIN --}}
        <div class="rounded-lg border border-gray-200 bg-white p-4 md:col-span-2">
            <h4 class="mb-3 text-xs font-semibold uppercase tracking-wider text-gray-700">Wilayah Administratif Wali</h4>
            @include('components.region-selector', [
                'prefix' => 'guardian',
                'provinces' => $provinces,
                'selectedProvince' => old('guardian_province_id', $guardian?->province_id ?? ''),
                'selectedRegency' => old('guardian_regency_id', $guardian?->regency_id ?? ''),
                'selectedDistrict' => old('guardian_district_id', $guardian?->district_id ?? ''),
                'selectedVillage' => old('guardian_village_id', $guardian?->village_id ?? ''),
            ])
        </div>
    </div>

    <!-- =========================================================
         ALERT N6 POPUP
    ========================================================== -->
    <div
        x-show="showN6Alert"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
        @click.away="showN6Alert = false"
    >
        <div class="w-full max-w-lg rounded-2xl bg-white p-8 shadow-2xl" @click.stop>
            <div class="mb-6 text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-amber-100 text-amber-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900">
                    Dokumen N6 Diperlukan
                </h3>
                <p class="mt-2 text-sm text-gray-600">
                    Karena Ayah Kandung Catin Wanita berstatus <strong>Meninggal</strong>,
                    diperlukan <strong>Surat Keterangan Wali (N6)</strong> untuk melanjutkan pendaftaran.
                </p>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:justify-center">
                <button
                    type="button"
                    @click="handleN6Action('buat_sekarang')"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Buat Sekarang
                </button>

                <button
                    type="button"
                    @click="handleN6Action('nanti_dulu')"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
                >
                    Nanti Dulu
                </button>
            </div>
        </div>
    </div>

</div>