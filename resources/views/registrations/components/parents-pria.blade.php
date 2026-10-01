<div class="space-y-6">


<div class="mb-6">
    <h2 class="text-lg font-bold text-gray-900">
        Data Orang Tua Catin Pria
    </h2>

    <p class="mt-1 text-sm text-gray-500">
        Lengkapi data ayah dan ibu kandung calon pengantin pria.
        Jika orang tua meninggal, cukup isi nama dan statusnya.
    </p>
</div>


{{-- =========================================================
     AYAH PRIA
========================================================== --}}
<div class="rounded-xl border border-gray-200 bg-gray-50 p-5">

    <div class="mb-5 flex items-center justify-between border-b border-gray-200 pb-3">

        <h3 class="text-sm font-bold uppercase tracking-wider text-gray-800">
            Ayah Kandung Pria
        </h3>

        <div class="flex items-center">
            <label
                for="pria_father_status"
                class="mr-2 text-xs font-medium text-gray-600"
            >
                Status:
            </label>

            <select
                id="pria_father_status"
                name="pria_father_status"
                x-model="fatherStatusPria"
                required
                class="rounded-md border-gray-300 py-1 text-xs focus:border-emerald-500 focus:ring-emerald-500"
            >
                <option value="HIDUP">Hidup</option>
                <option value="MENINGGAL">Meninggal</option>
            </select>
        </div>

    </div>


    <div class="grid grid-cols-1 gap-4 text-sm md:grid-cols-2">

        {{-- NAMA --}}
        <div>
            <label
                for="pria_father_name"
                class="block font-medium text-gray-700"
            >
                Nama Lengkap
                <span class="text-red-500">*</span>
            </label>

            <input
                id="pria_father_name"
                type="text"
                name="pria_father_name"
                value="{{ old('pria_father_name', $pria?->father?->name ?? '') }}"
                required
                class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
            >
        </div>

        <div>
            <label for="pria_father_bin_binti" class="block font-medium text-gray-700">Bin</label>
            <input id="pria_father_bin_binti" type="text" name="pria_father_bin_binti" value="{{ old('pria_father_bin_binti', $pria?->father?->bin_binti ?? '') }}" placeholder="Bin ..." :disabled="fatherStatusPria === 'MENINGGAL'" :class="fatherStatusPria === 'MENINGGAL' ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white'" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm">
        </div>


        {{-- NIK --}}
        <div>
            <label
                for="pria_father_nik"
                class="block font-medium text-gray-700"
            >
                NIK
            </label>

            <input
                id="pria_father_nik"
                type="text"
                name="pria_father_nik"
                value="{{ old('pria_father_nik', $pria?->father?->nik ?? '') }}"
                maxlength="16"
                inputmode="numeric"
                :required="fatherStatusPria === 'HIDUP'"
                :disabled="fatherStatusPria === 'MENINGGAL'"
                :class="fatherStatusPria === 'MENINGGAL'
                    ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                    : 'bg-white'"
                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm"
            >
        </div>


        {{-- TEMPAT LAHIR --}}
        <div>
            <label
                for="pria_father_birth_place"
                class="block font-medium text-gray-700"
            >
                Tempat Lahir
            </label>

            <input
                id="pria_father_birth_place"
                type="text"
                name="pria_father_birth_place"
                value="{{ old('pria_father_birth_place', $pria?->father?->birth_place ?? '') }}"
                :required="fatherStatusPria === 'HIDUP'"
                :disabled="fatherStatusPria === 'MENINGGAL'"
                :class="fatherStatusPria === 'MENINGGAL'
                    ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                    : 'bg-white'"
                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm"
            >
        </div>


        {{-- TANGGAL LAHIR --}}
        <div>
            <label
                for="pria_father_birth_date"
                class="block font-medium text-gray-700"
            >
                Tanggal Lahir
            </label>

            <input
                id="pria_father_birth_date"
                type="date"
                name="pria_father_birth_date"
                value="{{ old('pria_father_birth_date', $pria?->father?->birth_date?->format('Y-m-d') ?? '') }}"
                :required="fatherStatusPria === 'HIDUP'"
                :disabled="fatherStatusPria === 'MENINGGAL'"
                :class="fatherStatusPria === 'MENINGGAL'
                    ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                    : 'bg-white'"
                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm"
            >
        </div>


        {{-- PENDIDIKAN --}}
        <div>
            <label
                for="pria_father_education"
                class="block font-medium text-gray-700"
            >
                Pendidikan
            </label>

            <input
                id="pria_father_education"
                type="text"
                name="pria_father_education"
                value="{{ old('pria_father_education', $pria?->father?->education ?? '') }}"
                placeholder="Contoh: SD / SMP / SMA / S1"
                :required="fatherStatusPria === 'HIDUP'"
                :disabled="fatherStatusPria === 'MENINGGAL'"
                :class="fatherStatusPria === 'MENINGGAL'
                    ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                    : 'bg-white'"
                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm"
            >
        </div>


        {{-- PEKERJAAN --}}
        <div>
            <label
                for="pria_father_occupation"
                class="block font-medium text-gray-700"
            >
                Pekerjaan
            </label>

            <input
                id="pria_father_occupation"
                type="text"
                name="pria_father_occupation"
                value="{{ old('pria_father_occupation', $pria?->father?->occupation ?? '') }}"
                placeholder="Contoh: Petani / Wiraswasta"
                :required="fatherStatusPria === 'HIDUP'"
                :disabled="fatherStatusPria === 'MENINGGAL'"
                :class="fatherStatusPria === 'MENINGGAL'
                    ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                    : 'bg-white'"
                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm"
            >
        </div>


        {{-- ALAMAT --}}
        <div class="md:col-span-2">
            <label for="pria_father_address" class="block font-medium text-gray-700">Jalan / Blok / Dusun</label>
            <textarea id="pria_father_address" name="pria_father_address" rows="2" placeholder="Jl. ... Dusun ..." :disabled="fatherStatusPria === 'MENINGGAL'" :class="fatherStatusPria === 'MENINGGAL' ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white'" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm">{{ old('pria_father_address', $pria?->father?->address ?? '') }}</textarea>
        </div>
        <div>
            <label for="pria_father_address_rt" class="block font-medium text-gray-700">RT</label>
            <input id="pria_father_address_rt" type="text" name="pria_father_address_rt" value="{{ old('pria_father_address_rt', '') }}" maxlength="3" placeholder="01" :disabled="fatherStatusPria === 'MENINGGAL'" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm">
        </div>
        <div>
            <label for="pria_father_address_rw" class="block font-medium text-gray-700">RW</label>
            <input id="pria_father_address_rw" type="text" name="pria_father_address_rw" value="{{ old('pria_father_address_rw', '') }}" maxlength="3" placeholder="02" :disabled="fatherStatusPria === 'MENINGGAL'" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm">
        </div>
        <div>
            <label for="pria_father_marital_status" class="block font-medium text-gray-700">Status Perkawinan</label>
            <select id="pria_father_marital_status" name="pria_father_marital_status" :disabled="fatherStatusPria === 'MENINGGAL'" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm"><option value="">-- Pilih --</option><option value="KAWIN" @selected(old('pria_father_marital_status', $pria?->father?->marital_status ?? '')==='KAWIN')>Kawin</option><option value="CERAI_HIDUP" @selected(old('pria_father_marital_status', $pria?->father?->marital_status ?? '')==='CERAI_HIDUP')>Cerai Hidup</option><option value="CERAI_MATI" @selected(old('pria_father_marital_status', $pria?->father?->marital_status ?? '')==='CERAI_MATI')>Cerai Mati</option></select>
        </div>
        <div>
            <label for="pria_father_phone" class="block font-medium text-gray-700">No. HP</label>
            <input id="pria_father_phone" type="text" name="pria_father_phone" value="{{ old('pria_father_phone', $pria?->father?->phone ?? '') }}" :disabled="fatherStatusPria === 'MENINGGAL'" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm">
        </div>
        <div class="md:col-span-2">
            <label for="pria_father_email" class="block font-medium text-gray-700">Email</label>
            <input id="pria_father_email" type="text" name="pria_father_email" value="{{ old('pria_father_email', $pria?->father?->email ?? '') }}" :disabled="fatherStatusPria === 'MENINGGAL'" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm">
        </div>

        {{-- WILAYAH --}}
        <div class="rounded-lg border border-gray-200 bg-white p-4 md:col-span-2" x-show="fatherStatusPria === 'HIDUP'" x-cloak>
            <h4 class="mb-3 text-xs font-semibold uppercase tracking-wider text-gray-700">Wilayah Administratif Ayah Pria</h4>
            @include('components.region-selector', ['prefix' => 'pria_father','provinces' => $provinces,'disabledExpression' => "fatherStatusPria === 'MENINGGAL'",'selectedProvince' => old('pria_father_province_id', $pria?->father?->province_id ?? ''),'selectedRegency' => old('pria_father_regency_id', $pria?->father?->regency_id ?? ''),'selectedDistrict' => old('pria_father_district_id', $pria?->father?->district_id ?? ''),'selectedVillage' => old('pria_father_village_id', $pria?->father?->village_id ?? ''),])
        </div>

    </div>

</div>


{{-- =========================================================
     IBU PRIA
========================================================== --}}
<div class="rounded-xl border border-gray-200 bg-gray-50 p-5">

    <div class="mb-5 flex items-center justify-between border-b border-gray-200 pb-3">

        <h3 class="text-sm font-bold uppercase tracking-wider text-gray-800">
            Ibu Kandung Pria
        </h3>

        <div class="flex items-center">
            <label
                for="pria_mother_status"
                class="mr-2 text-xs font-medium text-gray-600"
            >
                Status:
            </label>

            <select
                id="pria_mother_status"
                name="pria_mother_status"
                x-model="motherStatusPria"
                required
                class="rounded-md border-gray-300 py-1 text-xs focus:border-emerald-500 focus:ring-emerald-500"
            >
                <option value="HIDUP">Hidup</option>
                <option value="MENINGGAL">Meninggal</option>
            </select>
        </div>

    </div>


    <div class="grid grid-cols-1 gap-4 text-sm md:grid-cols-2">

        {{-- NAMA --}}
        <div>
            <label
                for="pria_mother_name"
                class="block font-medium text-gray-700"
            >
                Nama Lengkap
                <span class="text-red-500">*</span>
            </label>

            <input
                id="pria_mother_name"
                type="text"
                name="pria_mother_name"
                value="{{ old('pria_mother_name', $pria?->mother?->name ?? '') }}"
                required
                class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
            >
        </div>

        <div>
            <label for="pria_mother_bin_binti" class="block font-medium text-gray-700">Binti</label>
            <input id="pria_mother_bin_binti" type="text" name="pria_mother_bin_binti" value="{{ old('pria_mother_bin_binti', $pria?->mother?->bin_binti ?? '') }}" placeholder="Binti ..." :disabled="motherStatusPria === 'MENINGGAL'" :class="motherStatusPria === 'MENINGGAL' ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white'" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm">
        </div>


        {{-- NIK --}}
        <div>
            <label
                for="pria_mother_nik"
                class="block font-medium text-gray-700"
            >
                NIK
            </label>

            <input
                id="pria_mother_nik"
                type="text"
                name="pria_mother_nik"
                value="{{ old('pria_mother_nik', $pria?->mother?->nik ?? '') }}"
                maxlength="16"
                inputmode="numeric"
                :required="motherStatusPria === 'HIDUP'"
                :disabled="motherStatusPria === 'MENINGGAL'"
                :class="motherStatusPria === 'MENINGGAL'
                    ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                    : 'bg-white'"
                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm"
            >
        </div>


        {{-- TEMPAT LAHIR --}}
        <div>
            <label
                for="pria_mother_birth_place"
                class="block font-medium text-gray-700"
            >
                Tempat Lahir
            </label>

            <input
                id="pria_mother_birth_place"
                type="text"
                name="pria_mother_birth_place"
                value="{{ old('pria_mother_birth_place', $pria?->mother?->birth_place ?? '') }}"
                :required="motherStatusPria === 'HIDUP'"
                :disabled="motherStatusPria === 'MENINGGAL'"
                :class="motherStatusPria === 'MENINGGAL'
                    ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                    : 'bg-white'"
                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm"
            >
        </div>


        {{-- TANGGAL LAHIR --}}
        <div>
            <label
                for="pria_mother_birth_date"
                class="block font-medium text-gray-700"
            >
                Tanggal Lahir
            </label>

            <input
                id="pria_mother_birth_date"
                type="date"
                name="pria_mother_birth_date"
                value="{{ old('pria_mother_birth_date', $pria?->mother?->birth_date?->format('Y-m-d') ?? '') }}"
                :required="motherStatusPria === 'HIDUP'"
                :disabled="motherStatusPria === 'MENINGGAL'"
                :class="motherStatusPria === 'MENINGGAL'
                    ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                    : 'bg-white'"
                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm"
            >
        </div>


        {{-- PENDIDIKAN --}}
        <div>
            <label
                for="pria_mother_education"
                class="block font-medium text-gray-700"
            >
                Pendidikan
            </label>

            <input
                id="pria_mother_education"
                type="text"
                name="pria_mother_education"
                value="{{ old('pria_mother_education', $pria?->mother?->education ?? '') }}"
                placeholder="Contoh: SD / SMP / SMA / S1"
                :required="motherStatusPria === 'HIDUP'"
                :disabled="motherStatusPria === 'MENINGGAL'"
                :class="motherStatusPria === 'MENINGGAL'
                    ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                    : 'bg-white'"
                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm"
            >
        </div>


        {{-- PEKERJAAN --}}
        <div>
            <label
                for="pria_mother_occupation"
                class="block font-medium text-gray-700"
            >
                Pekerjaan
            </label>

            <input
                id="pria_mother_occupation"
                type="text"
                name="pria_mother_occupation"
                value="{{ old('pria_mother_occupation', $pria?->mother?->occupation ?? '') }}"
                placeholder="Contoh: Ibu Rumah Tangga / Wiraswasta"
                :required="motherStatusPria === 'HIDUP'"
                :disabled="motherStatusPria === 'MENINGGAL'"
                :class="motherStatusPria === 'MENINGGAL'
                    ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                    : 'bg-white'"
                class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm"
            >
        </div>


        {{-- ALAMAT --}}
        <div class="md:col-span-2">
            <label for="pria_mother_address" class="block font-medium text-gray-700">Jalan / Blok / Dusun</label>
            <textarea id="pria_mother_address" name="pria_mother_address" rows="2" :disabled="motherStatusPria === 'MENINGGAL'" :class="motherStatusPria === 'MENINGGAL' ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-white'" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm">{{ old('pria_mother_address', $pria?->mother?->address ?? '') }}</textarea>
        </div>
        <div><label for="pria_mother_address_rt" class="block font-medium text-gray-700">RT</label><input id="pria_mother_address_rt" type="text" name="pria_mother_address_rt" value="{{ old('pria_mother_address_rt', '') }}" maxlength="3" placeholder="01" :disabled="motherStatusPria === 'MENINGGAL'" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm"></div>
        <div><label for="pria_mother_address_rw" class="block font-medium text-gray-700">RW</label><input id="pria_mother_address_rw" type="text" name="pria_mother_address_rw" value="{{ old('pria_mother_address_rw', '') }}" maxlength="3" placeholder="02" :disabled="motherStatusPria === 'MENINGGAL'" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm"></div>
        <div><label for="pria_mother_marital_status" class="block font-medium text-gray-700">Status Perkawinan</label><select id="pria_mother_marital_status" name="pria_mother_marital_status" :disabled="motherStatusPria === 'MENINGGAL'" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm"><option value="">-- Pilih --</option><option value="KAWIN" @selected(old('pria_mother_marital_status', $pria?->mother?->marital_status ?? '')==='KAWIN')>Kawin</option><option value="CERAI_HIDUP" @selected(old('pria_mother_marital_status', $pria?->mother?->marital_status ?? '')==='CERAI_HIDUP')>Cerai Hidup</option><option value="CERAI_MATI" @selected(old('pria_mother_marital_status', $pria?->mother?->marital_status ?? '')==='CERAI_MATI')>Cerai Mati</option></select></div>
        <div><label for="pria_mother_phone" class="block font-medium text-gray-700">No. HP</label><input id="pria_mother_phone" type="text" name="pria_mother_phone" value="{{ old('pria_mother_phone', $pria?->mother?->phone ?? '') }}" :disabled="motherStatusPria === 'MENINGGAL'" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm"></div>
        <div class="md:col-span-2"><label for="pria_mother_email" class="block font-medium text-gray-700">Email</label><input id="pria_mother_email" type="text" name="pria_mother_email" value="{{ old('pria_mother_email', $pria?->mother?->email ?? '') }}" :disabled="motherStatusPria === 'MENINGGAL'" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm"></div>
        {{-- WILAYAH --}}
        <div class="rounded-lg border border-gray-200 bg-white p-4 md:col-span-2" x-show="motherStatusPria === 'HIDUP'" x-cloak>
            <h4 class="mb-3 text-xs font-semibold uppercase tracking-wider text-gray-700">Wilayah Administratif Ibu Pria</h4>
            @include('components.region-selector', ['prefix' => 'pria_mother','provinces' => $provinces,'disabledExpression' => "motherStatusPria === 'MENINGGAL'",'selectedProvince' => old('pria_mother_province_id', $pria?->mother?->province_id ?? ''),'selectedRegency' => old('pria_mother_regency_id', $pria?->mother?->regency_id ?? ''),'selectedDistrict' => old('pria_mother_district_id', $pria?->mother?->district_id ?? ''),'selectedVillage' => old('pria_mother_village_id', $pria?->mother?->village_id ?? ''),])
        </div>

    </div>

</div>


</div>
