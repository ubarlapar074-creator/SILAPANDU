<div class="space-y-6">

    <div class="mb-6">
        <h2 class="text-lg font-bold text-gray-900">
            Data Orang Tua Catin Wanita
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            Lengkapi data ayah dan ibu kandung calon pengantin wanita.
            Jika orang tua meninggal, cukup isi nama dan statusnya.
        </p>
    </div>

    {{-- =========================================================
         AYAH WANITA
    ========================================================== --}}
    <div class="rounded-xl border border-gray-200 bg-gray-50 p-5">

        <div class="mb-5 flex items-center justify-between border-b border-gray-200 pb-3">
            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-800">
                Ayah Kandung Wanita
            </h3>

            <div class="flex items-center">
                <label class="mr-2 text-xs font-medium text-gray-600">Status:</label>
                <select
                    name="wanita_father_status"
                    x-model="fatherStatusWanita"
                    required
                    class="rounded-md border-gray-300 py-1 text-xs focus:border-emerald-500 focus:ring-emerald-500"
                >
                    <option value="HIDUP">Hidup</option>
                    <option value="MENINGGAL">Meninggal</option>
                </select>
            </div>
        </div>

        {{-- NOTIFIKASI UNTUK AYAH MENINGGAL --}}
        <div
            x-show="fatherStatusWanita === 'MENINGGAL'"
            x-cloak
            class="mb-5 rounded-lg border border-amber-200 bg-amber-50 p-4 text-xs text-amber-800"
        >
            <div class="flex items-start gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <span class="font-bold">Perhatian:</span> Ayah Kandung berstatus Meninggal.
                    <span class="block mt-1">
                        Surat Keterangan Wali (N6) akan diperlukan pada tahap pemilihan Wali.
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 text-sm md:grid-cols-2">

            {{-- NAMA --}}
            <div>
                <label class="block font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                <input
                    type="text"
                    name="wanita_father_name"
                    value="{{ old('wanita_father_name', $wanita?->father?->name ?? '') }}"
                    required
                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                >
            </div>

            <div>
                <label for="wanita_father_bin_binti" class="block font-medium text-gray-700">Bin</label>
                <input id="wanita_father_bin_binti" type="text" name="wanita_father_bin_binti" value="{{ old('wanita_father_bin_binti', $wanita?->father?->bin_binti ?? '') }}" placeholder="Bin ..." :disabled="fatherStatusWanita === 'MENINGGAL'" :class="fatherStatusWanita === 'MENINGGAL' ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white'" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm">
            </div>

            {{-- NIK --}}
            <div>
                <label class="block font-medium text-gray-700">NIK</label>
                <input
                    type="text"
                    name="wanita_father_nik"
                    value="{{ old('wanita_father_nik', $wanita?->father?->nik ?? '') }}"
                    maxlength="16"
                    inputmode="numeric"
                    :required="fatherStatusWanita === 'HIDUP'"
                    :disabled="fatherStatusWanita === 'MENINGGAL'"
                    :class="fatherStatusWanita === 'MENINGGAL' ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white'"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm"
                >
            </div>

            {{-- TEMPAT LAHIR --}}
            <div>
                <label class="block font-medium text-gray-700">Tempat Lahir</label>
                <input
                    type="text"
                    name="wanita_father_birth_place"
                    value="{{ old('wanita_father_birth_place', $wanita?->father?->birth_place ?? '') }}"
                    :required="fatherStatusWanita === 'HIDUP'"
                    :disabled="fatherStatusWanita === 'MENINGGAL'"
                    :class="fatherStatusWanita === 'MENINGGAL' ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white'"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm"
                >
            </div>

            {{-- TANGGAL LAHIR --}}
            <div>
                <label class="block font-medium text-gray-700">Tanggal Lahir</label>
                <input
                    type="date"
                    name="wanita_father_birth_date"
                    value="{{ old('wanita_father_birth_date', $wanita?->father?->birth_date?->format('Y-m-d') ?? '') }}"
                    :required="fatherStatusWanita === 'HIDUP'"
                    :disabled="fatherStatusWanita === 'MENINGGAL'"
                    :class="fatherStatusWanita === 'MENINGGAL' ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white'"
                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm"
                >
            </div>

            {{-- PENDIDIKAN --}}
            <div>
                <label class="block font-medium text-gray-700">Pendidikan</label>
                <input
                    type="text"
                    name="wanita_father_education"
                    value="{{ old('wanita_father_education', $wanita?->father?->education ?? '') }}"
                    placeholder="Contoh: SD / SMP / SMA / S1"
                    :required="fatherStatusWanita === 'HIDUP'"
                    :disabled="fatherStatusWanita === 'MENINGGAL'"
                    :class="fatherStatusWanita === 'MENINGGAL' ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white'"
                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm"
                >
            </div>

            {{-- PEKERJAAN --}}
            <div>
                <label class="block font-medium text-gray-700">Pekerjaan</label>
                <input
                    type="text"
                    name="wanita_father_occupation"
                    value="{{ old('wanita_father_occupation', $wanita?->father?->occupation ?? '') }}"
                    placeholder="Contoh: Petani / Wiraswasta"
                    :required="fatherStatusWanita === 'HIDUP'"
                    :disabled="fatherStatusWanita === 'MENINGGAL'"
                    :class="fatherStatusWanita === 'MENINGGAL' ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white'"
                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm"
                >
            </div>

            <div class="md:col-span-2">
                <label for="wanita_father_address" class="block font-medium text-gray-700">Jalan / Blok / Dusun</label>
                <textarea id="wanita_father_address" name="wanita_father_address" rows="2" :disabled="fatherStatusWanita === 'MENINGGAL'" :class="fatherStatusWanita === 'MENINGGAL' ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white'" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm">{{ old('wanita_father_address', $wanita?->father?->address ?? '') }}</textarea>
            </div>
            <div><label for="wanita_father_address_rt" class="block font-medium text-gray-700">RT</label><input id="wanita_father_address_rt" type="text" name="wanita_father_address_rt" value="{{ old('wanita_father_address_rt', '') }}" maxlength="3" placeholder="01" :disabled="fatherStatusWanita === 'MENINGGAL'" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm"></div>
            <div><label for="wanita_father_address_rw" class="block font-medium text-gray-700">RW</label><input id="wanita_father_address_rw" type="text" name="wanita_father_address_rw" value="{{ old('wanita_father_address_rw', '') }}" maxlength="3" placeholder="02" :disabled="fatherStatusWanita === 'MENINGGAL'" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm"></div>
            <div><label for="wanita_father_marital_status" class="block font-medium text-gray-700">Status Perkawinan</label><select id="wanita_father_marital_status" name="wanita_father_marital_status" :disabled="fatherStatusWanita === 'MENINGGAL'" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm"><option value="">-- Pilih --</option><option value="KAWIN" @selected(old('wanita_father_marital_status', $wanita?->father?->marital_status ?? '')==='KAWIN')>Kawin</option><option value="CERAI_HIDUP" @selected(old('wanita_father_marital_status', $wanita?->father?->marital_status ?? '')==='CERAI_HIDUP')>Cerai Hidup</option><option value="CERAI_MATI" @selected(old('wanita_father_marital_status', $wanita?->father?->marital_status ?? '')==='CERAI_MATI')>Cerai Mati</option></select></div>
            <div><label for="wanita_father_phone" class="block font-medium text-gray-700">No. HP</label><input id="wanita_father_phone" type="text" name="wanita_father_phone" value="{{ old('wanita_father_phone', $wanita?->father?->phone ?? '') }}" :disabled="fatherStatusWanita === 'MENINGGAL'" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm"></div>
            <div class="md:col-span-2"><label for="wanita_father_email" class="block font-medium text-gray-700">Email</label><input id="wanita_father_email" type="text" name="wanita_father_email" value="{{ old('wanita_father_email', $wanita?->father?->email ?? '') }}" :disabled="fatherStatusWanita === 'MENINGGAL'" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm"></div>

            {{-- WILAYAH --}}
            <div class="rounded-lg border border-gray-200 bg-white p-4 md:col-span-2" x-show="fatherStatusWanita === 'HIDUP'" x-cloak>
                <h4 class="mb-3 text-xs font-semibold uppercase tracking-wider text-gray-700">Wilayah Administratif Ayah Wanita</h4>
                @include('components.region-selector', ['prefix' => 'wanita_father','provinces' => $provinces,'disabledExpression' => "fatherStatusWanita === 'MENINGGAL'",'selectedProvince' => old('wanita_father_province_id', $wanita?->father?->province_id ?? ''),'selectedRegency' => old('wanita_father_regency_id', $wanita?->father?->regency_id ?? ''),'selectedDistrict' => old('wanita_father_district_id', $wanita?->father?->district_id ?? ''),'selectedVillage' => old('wanita_father_village_id', $wanita?->father?->village_id ?? ''),])
            </div>

        </div>
    </div>

    {{-- =========================================================
         IBU WANITA - TANPA ALERT N6
    ========================================================== --}}
    <div class="rounded-xl border border-gray-200 bg-gray-50 p-5 mt-6">

        <div class="mb-5 flex items-center justify-between border-b border-gray-200 pb-3">
            <h3 class="text-sm font-bold uppercase tracking-wider text-gray-800">Ibu Kandung Wanita</h3>

            <div class="flex items-center">
                <label class="mr-2 text-xs font-medium text-gray-600">Status:</label>
                <select
                    name="wanita_mother_status"
                    x-model="motherStatusWanita"
                    required
                    class="rounded-md border-gray-300 py-1 text-xs focus:border-emerald-500 focus:ring-emerald-500"
                >
                    <option value="HIDUP">Hidup</option>
                    <option value="MENINGGAL">Meninggal</option>
                </select>
            </div>
        </div>

        {{-- TIDAK ADA ALERT UNTUK IBU --}}

        <div class="grid grid-cols-1 gap-4 text-sm md:grid-cols-2">

            <div>
                <label class="block font-medium text-gray-700">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="wanita_mother_name" value="{{ old('wanita_mother_name', $wanita?->mother?->name ?? '') }}" required class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
            </div>

            <div>
                <label for="wanita_mother_bin_binti" class="block font-medium text-gray-700">Binti</label>
                <input id="wanita_mother_bin_binti" type="text" name="wanita_mother_bin_binti" value="{{ old('wanita_mother_bin_binti', $wanita?->mother?->bin_binti ?? '') }}" placeholder="Binti ..." :disabled="motherStatusWanita === 'MENINGGAL'" :class="motherStatusWanita === 'MENINGGAL' ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white'" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm">
            </div>

            {{-- NIK --}}
            <div>
                <label class="block font-medium text-gray-700">NIK</label>
                <input
                    type="text"
                    name="wanita_mother_nik"
                    value="{{ old('wanita_mother_nik', $wanita?->mother?->nik ?? '') }}"
                    maxlength="16"
                    inputmode="numeric"
                    :required="motherStatusWanita === 'HIDUP'"
                    :disabled="motherStatusWanita === 'MENINGGAL'"
                    :class="motherStatusWanita === 'MENINGGAL' ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white'"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm"
                >
            </div>

            {{-- TEMPAT LAHIR --}}
            <div>
                <label class="block font-medium text-gray-700">Tempat Lahir</label>
                <input
                    type="text"
                    name="wanita_mother_birth_place"
                    value="{{ old('wanita_mother_birth_place', $wanita?->mother?->birth_place ?? '') }}"
                    :required="motherStatusWanita === 'HIDUP'"
                    :disabled="motherStatusWanita === 'MENINGGAL'"
                    :class="motherStatusWanita === 'MENINGGAL' ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white'"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm"
                >
            </div>

            {{-- TANGGAL LAHIR --}}
            <div>
                <label class="block font-medium text-gray-700">Tanggal Lahir</label>
                <input
                    type="date"
                    name="wanita_mother_birth_date"
                    value="{{ old('wanita_mother_birth_date', $wanita?->mother?->birth_date?->format('Y-m-d') ?? '') }}"
                    :required="motherStatusWanita === 'HIDUP'"
                    :disabled="motherStatusWanita === 'MENINGGAL'"
                    :class="motherStatusWanita === 'MENINGGAL' ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white'"
                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm"
                >
            </div>

            {{-- PENDIDIKAN --}}
            <div>
                <label class="block font-medium text-gray-700">Pendidikan</label>
                <input
                    type="text"
                    name="wanita_mother_education"
                    value="{{ old('wanita_mother_education', $wanita?->mother?->education ?? '') }}"
                    placeholder="Contoh: SD / SMP / SMA / S1"
                    :required="motherStatusWanita === 'HIDUP'"
                    :disabled="motherStatusWanita === 'MENINGGAL'"
                    :class="motherStatusWanita === 'MENINGGAL' ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white'"
                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm"
                >
            </div>

            {{-- PEKERJAAN --}}
            <div>
                <label class="block font-medium text-gray-700">Pekerjaan</label>
                <input
                    type="text"
                    name="wanita_mother_occupation"
                    value="{{ old('wanita_mother_occupation', $wanita?->mother?->occupation ?? '') }}"
                    placeholder="Contoh: Ibu Rumah Tangga / Wiraswasta"
                    :required="motherStatusWanita === 'HIDUP'"
                    :disabled="motherStatusWanita === 'MENINGGAL'"
                    :class="motherStatusWanita === 'MENINGGAL' ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white'"
                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm"
                >
            </div>

            <div class="md:col-span-2">
                <label for="wanita_mother_address" class="block font-medium text-gray-700">Jalan / Blok / Dusun</label>
                <textarea id="wanita_mother_address" name="wanita_mother_address" rows="2" :disabled="motherStatusWanita === 'MENINGGAL'" :class="motherStatusWanita === 'MENINGGAL' ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white'" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm">{{ old('wanita_mother_address', $wanita?->mother?->address ?? '') }}</textarea>
            </div>
            <div><label for="wanita_mother_address_rt" class="block font-medium text-gray-700">RT</label><input id="wanita_mother_address_rt" type="text" name="wanita_mother_address_rt" value="{{ old('wanita_mother_address_rt', '') }}" maxlength="3" placeholder="01" :disabled="motherStatusWanita === 'MENINGGAL'" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm"></div>
            <div><label for="wanita_mother_address_rw" class="block font-medium text-gray-700">RW</label><input id="wanita_mother_address_rw" type="text" name="wanita_mother_address_rw" value="{{ old('wanita_mother_address_rw', '') }}" maxlength="3" placeholder="02" :disabled="motherStatusWanita === 'MENINGGAL'" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm"></div>
            <div><label for="wanita_mother_marital_status" class="block font-medium text-gray-700">Status Perkawinan</label><select id="wanita_mother_marital_status" name="wanita_mother_marital_status" :disabled="motherStatusWanita === 'MENINGGAL'" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm"><option value="">-- Pilih --</option><option value="KAWIN" @selected(old('wanita_mother_marital_status', $wanita?->mother?->marital_status ?? '')==='KAWIN')>Kawin</option><option value="CERAI_HIDUP" @selected(old('wanita_mother_marital_status', $wanita?->mother?->marital_status ?? '')==='CERAI_HIDUP')>Cerai Hidup</option><option value="CERAI_MATI" @selected(old('wanita_mother_marital_status', $wanita?->mother?->marital_status ?? '')==='CERAI_MATI')>Cerai Mati</option></select></div>
            <div><label for="wanita_mother_phone" class="block font-medium text-gray-700">No. HP</label><input id="wanita_mother_phone" type="text" name="wanita_mother_phone" value="{{ old('wanita_mother_phone', $wanita?->mother?->phone ?? '') }}" :disabled="motherStatusWanita === 'MENINGGAL'" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm"></div>
            <div class="md:col-span-2"><label for="wanita_mother_email" class="block font-medium text-gray-700">Email</label><input id="wanita_mother_email" type="text" name="wanita_mother_email" value="{{ old('wanita_mother_email', $wanita?->mother?->email ?? '') }}" :disabled="motherStatusWanita === 'MENINGGAL'" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm"></div>

            {{-- WILAYAH --}}
            <div class="rounded-lg border border-gray-200 bg-white p-4 md:col-span-2" x-show="motherStatusWanita === 'HIDUP'" x-cloak>
                <h4 class="mb-3 text-xs font-semibold uppercase tracking-wider text-gray-700">Wilayah Administratif Ibu Wanita</h4>
                @include('components.region-selector', ['prefix' => 'wanita_mother','provinces' => $provinces,'disabledExpression' => "motherStatusWanita === 'MENINGGAL'",'selectedProvince' => old('wanita_mother_province_id', $wanita?->mother?->province_id ?? ''),'selectedRegency' => old('wanita_mother_regency_id', $wanita?->mother?->regency_id ?? ''),'selectedDistrict' => old('wanita_mother_district_id', $wanita?->mother?->district_id ?? ''),'selectedVillage' => old('wanita_mother_village_id', $wanita?->mother?->village_id ?? ''),])
            </div>

        </div>
    </div>

</div>