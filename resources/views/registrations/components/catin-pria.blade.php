<div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">

    <div class="mb-5">
        <h3 class="text-lg font-bold text-slate-800">
            Catin Pria
        </h3>

        <p class="text-sm text-slate-500">
            Data calon pengantin pria sesuai dokumen identitas.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">

        {{-- NIK --}}
        <div>
            <label class="block font-medium text-slate-700">
                NIK <span class="text-red-500">*</span>
            </label>

            <input
                id="pria_nik"
                type="text"
                name="pria_nik"
                value="{{ old('pria_nik', $pria?->nik ?? '') }}"
                maxlength="16"
                inputmode="numeric"
                placeholder="16 digit NIK"
                autocomplete="off"
                class="mt-1 block w-full rounded-md border-slate-300
                       shadow-sm focus:border-emerald-500
                       focus:ring-emerald-500"
                required
            >
            <p id="pria_nik_status" class="mt-1 hidden text-xs"></p>
            @error('pria_nik')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Nama --}}
        <div>
            <label class="block font-medium text-slate-700">
                Nama Lengkap <span class="text-red-500">*</span>
            </label>

            <input
                type="text"
                name="pria_name"
                value="{{ old('pria_name', $pria?->name ?? '') }}"
                placeholder="Sesuai KTP/KK"
                class="mt-1 block w-full rounded-md border-slate-300
                       shadow-sm focus:border-emerald-500
                       focus:ring-emerald-500"
                required
            >

            @error('pria_name')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Bin --}}
        <div>
            <label class="block font-medium text-slate-700">
                Bin (Ayah)
            </label>

            <input
                type="text"
                name="pria_bin_binti"
                value="{{ old('pria_bin_binti', $pria?->bin_binti ?? '') }}"
                placeholder="Bin ... (nullable)"
                class="mt-1 block w-full rounded-md border-slate-300
                       shadow-sm focus:border-emerald-500
                       focus:ring-emerald-500"
            >

            @error('pria_bin_binti')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Tempat Lahir --}}
        <div>
            <label class="block font-medium text-slate-700">
                Tempat Lahir <span class="text-red-500">*</span>
            </label>

            <input
                type="text"
                name="pria_birth_place"
                value="{{ old('pria_birth_place', $pria?->birth_place ?? '') }}"
                class="mt-1 block w-full rounded-md border-slate-300
                       shadow-sm focus:border-emerald-500
                       focus:ring-emerald-500"
                required
            >

            @error('pria_birth_place')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Tanggal Lahir --}}
        <div>
            <label class="block font-medium text-slate-700">
                Tanggal Lahir <span class="text-red-500">*</span>
            </label>

            <input
                type="date"
                name="pria_birth_date"
                value="{{ old('pria_birth_date', $pria?->birth_date?->format('Y-m-d') ?? '') }}"
                class="mt-1 block w-full rounded-md border-slate-300
                       shadow-sm focus:border-emerald-500
                       focus:ring-emerald-500"
                required
            >

            @error('pria_birth_date')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Kewarganegaraan --}}
        <div>
            <label class="block font-medium text-slate-700">
                Kewarganegaraan
            </label>

            <input
                type="text"
                name="pria_nationality"
                value="{{ old('pria_nationality', $pria?->nationality ?? 'WNI') }}"
                placeholder="WNI / WNA"
                class="mt-1 block w-full rounded-md border-slate-300
                       shadow-sm focus:border-emerald-500
                       focus:ring-emerald-500"
            >

            @error('pria_nationality')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Agama --}}
        <div>
            <label class="block font-medium text-slate-700">
                Agama
            </label>

            <select
                name="pria_religion"
                class="mt-1 block w-full rounded-md border-slate-300
                       shadow-sm focus:border-emerald-500
                       focus:ring-emerald-500"
            >
                <option value="">-- Pilih Agama --</option>
                <option value="Islam" @selected(old('pria_religion', $pria?->religion ?? '') === 'Islam')>Islam</option>
                <option value="Kristen" @selected(old('pria_religion', $pria?->religion ?? '') === 'Kristen')>Kristen</option>
                <option value="Katolik" @selected(old('pria_religion', $pria?->religion ?? '') === 'Katolik')>Katolik</option>
                <option value="Hindu" @selected(old('pria_religion', $pria?->religion ?? '') === 'Hindu')>Hindu</option>
                <option value="Buddha" @selected(old('pria_religion', $pria?->religion ?? '') === 'Buddha')>Buddha</option>
                <option value="Konghucu" @selected(old('pria_religion', $pria?->religion ?? '') === 'Konghucu')>Konghucu</option>
            </select>

            @error('pria_religion')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Nomor HP --}}
        <div>
            <label class="block font-medium text-slate-700">
                Nomor HP
            </label>

            <input
                type="text"
                name="pria_no_hp"
                value="{{ old('pria_no_hp', $pria?->no_hp ?? '') }}"
                placeholder="08xxxxxxxxxx"
                class="mt-1 block w-full rounded-md border-slate-300
                       shadow-sm focus:border-emerald-500
                       focus:ring-emerald-500"
            >

            @error('pria_no_hp')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label class="block font-medium text-slate-700">
                Email
            </label>

            <input
                type="text"
                name="pria_email"
                value="{{ old('pria_email', $pria?->email ?? '') }}"
                placeholder="nama@email.com"
                class="mt-1 block w-full rounded-md border-slate-300
                       shadow-sm focus:border-emerald-500
                       focus:ring-emerald-500"
            >

            @error('pria_email')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Status Perkawinan --}}
        <div>
            <label class="block font-medium text-slate-700">
                Status Perkawinan <span class="text-red-500">*</span>
                <span class="text-xs font-normal text-slate-400">— Sesuai redaksi KK (lama/baru)</span>
            </label>

            <select
                name="pria_marital_status"
                class="mt-1 block w-full rounded-md border-slate-300
                       shadow-sm focus:border-emerald-500
                       focus:ring-emerald-500"
                required
            >
                <option value="">-- Pilih Status --</option>
                @foreach(\App\Support\MaritalStatus::dropdownOptions() as $val => $label)
                    <option value="{{ $val }}" @selected(in_array(old('pria_marital_status', $pria?->marital_status ?? ''), [$val, str_replace('_',' ',$val)], true))>{{ $label }}</option>
                @endforeach
            </select>

            @error('pria_marital_status')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Pendidikan --}}
        <div>
            <label class="block font-medium text-slate-700">
                Pendidikan
            </label>

            <input
                type="text"
                name="pria_education"
                value="{{ old('pria_education', $pria?->education ?? '') }}"
                placeholder="Contoh: SD / SMP / SMA / SMK / D3 / S1"
                class="mt-1 block w-full rounded-md border-slate-300
                       shadow-sm focus:border-emerald-500
                       focus:ring-emerald-500"
            >

            @error('pria_education')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Pekerjaan --}}
        <div>
            <label class="block font-medium text-slate-700">
                Pekerjaan
            </label>

            <input
                type="text"
                name="pria_occupation"
                value="{{ old('pria_occupation', $pria?->occupation ?? '') }}"
                placeholder="Contoh: Petani / Wiraswasta"
                class="mt-1 block w-full rounded-md border-slate-300
                       shadow-sm focus:border-emerald-500
                       focus:ring-emerald-500"
            >

            @error('pria_occupation')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Alamat --}}
        <div class="md:col-span-2">
            <label class="block font-medium text-slate-700">
                Jalan / Blok / Dusun <span class="text-red-500">*</span>
            </label>

            <textarea
                name="pria_address"
                rows="2"
                class="mt-1 block w-full rounded-md border-slate-300
                       shadow-sm focus:border-emerald-500
                       focus:ring-emerald-500"
                required
            >{{ old('pria_address', $pria?->address ?? '') }}</textarea>

            @error('pria_address')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block font-medium text-slate-700">RT</label>
            <input type="text" name="pria_address_rt" value="{{ old('pria_address_rt', '') }}" maxlength="3" placeholder="01" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
        </div>
        <div>
            <label class="block font-medium text-slate-700">RW</label>
            <input type="text" name="pria_address_rw" value="{{ old('pria_address_rw', '') }}" maxlength="3" placeholder="02" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
        </div>

        {{-- Wilayah --}}
        <div class="md:col-span-2 bg-slate-50 p-4 rounded-lg border border-slate-200">

            <h4 class="font-semibold text-slate-700 mb-3 text-xs uppercase tracking-wider">
                Wilayah Domisili Catin Pria
            </h4>

            @include('components.region-selector', [
                'prefix' => 'pria',
                'provinces' => $provinces,
                'selectedProvince' => old('pria_province_id', $pria?->province_id ?? ''),
                'selectedRegency' => old('pria_regency_id', $pria?->regency_id ?? ''),
                'selectedDistrict' => old('pria_district_id', $pria?->district_id ?? ''),
                'selectedVillage' => old('pria_village_id', $pria?->village_id ?? ''),
            ])

        </div>

    </div>
</div>