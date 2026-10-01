<div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">

    <div class="mb-5">
        <h3 class="text-lg font-bold text-slate-800">
            Catin Wanita
        </h3>

        <p class="text-sm text-slate-500">
            Data calon pengantin wanita sesuai dokumen identitas.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">

        {{-- NIK --}}
        <div>
            <label class="block font-medium text-slate-700">
                NIK <span class="text-red-500">*</span>
            </label>

            <input
                id="wanita_nik"
                type="text"
                name="wanita_nik"
                value="{{ old('wanita_nik', $wanita?->nik ?? '') }}"
                maxlength="16"
                inputmode="numeric"
                placeholder="16 digit NIK"
                autocomplete="off"
                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm
                       focus:border-emerald-500 focus:ring-emerald-500"
                required
            >
            <p id="wanita_nik_status" class="mt-1 hidden text-xs"></p>
            @error('wanita_nik')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Nama Lengkap --}}
        <div>
            <label class="block font-medium text-slate-700">
                Nama Lengkap <span class="text-red-500">*</span>
            </label>

            <input
                type="text"
                name="wanita_name"
                value="{{ old('wanita_name', $wanita?->name ?? '') }}"
                placeholder="Sesuai KTP/KK"
                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm
                       focus:border-emerald-500 focus:ring-emerald-500"
                required
            >

            @error('wanita_name')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Binti --}}
        <div>
            <label class="block font-medium text-slate-700">
                Binti (Ayah)
            </label>

            <input
                type="text"
                name="wanita_bin_binti"
                value="{{ old('wanita_bin_binti', $wanita?->bin_binti ?? '') }}"
                placeholder="Binti ... (nullable)"
                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm
                       focus:border-emerald-500 focus:ring-emerald-500"
            >

            @error('wanita_bin_binti')
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
                name="wanita_birth_place"
                value="{{ old('wanita_birth_place', $wanita?->birth_place ?? '') }}"
                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm
                       focus:border-emerald-500 focus:ring-emerald-500"
                required
            >

            @error('wanita_birth_place')
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
                name="wanita_birth_date"
                value="{{ old('wanita_birth_date', $wanita?->birth_date?->format('Y-m-d') ?? '') }}"
                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm
                       focus:border-emerald-500 focus:ring-emerald-500"
                required
            >

            @error('wanita_birth_date')
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
                name="wanita_nationality"
                value="{{ old('wanita_nationality', $wanita?->nationality ?? 'WNI') }}"
                placeholder="WNI / WNA"
                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm
                       focus:border-emerald-500 focus:ring-emerald-500"
            >

            @error('wanita_nationality')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Agama --}}
        <div>
            <label class="block font-medium text-slate-700">
                Agama
            </label>

            <select
                name="wanita_religion"
                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm
                       focus:border-emerald-500 focus:ring-emerald-500"
            >
                <option value="">-- Pilih Agama --</option>
                <option value="Islam" @selected(old('wanita_religion', $wanita?->religion ?? '') === 'Islam')>Islam</option>
                <option value="Kristen" @selected(old('wanita_religion', $wanita?->religion ?? '') === 'Kristen')>Kristen</option>
                <option value="Katolik" @selected(old('wanita_religion', $wanita?->religion ?? '') === 'Katolik')>Katolik</option>
                <option value="Hindu" @selected(old('wanita_religion', $wanita?->religion ?? '') === 'Hindu')>Hindu</option>
                <option value="Buddha" @selected(old('wanita_religion', $wanita?->religion ?? '') === 'Buddha')>Buddha</option>
                <option value="Konghucu" @selected(old('wanita_religion', $wanita?->religion ?? '') === 'Konghucu')>Konghucu</option>
            </select>

            @error('wanita_religion')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Nomor HP --}}
        <div>
            <label for="wanita_no_hp" class="block font-medium text-slate-700">
                Nomor HP
            </label>

            <input
                type="text"
                name="wanita_no_hp"
                id="wanita_no_hp"
                value="{{ old('wanita_no_hp', $wanita?->no_hp ?? '') }}"
                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                placeholder="08xxxxxxxxxx"
            >

            @error('wanita_no_hp')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="wanita_email" class="block font-medium text-slate-700">
                Email
            </label>

            <input
                type="text"
                name="wanita_email"
                id="wanita_email"
                value="{{ old('wanita_email', $wanita?->email ?? '') }}"
                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                placeholder="nama@email.com"
            >

            @error('wanita_email')
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
                name="wanita_marital_status"
                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm
                       focus:border-emerald-500 focus:ring-emerald-500"
                required
            >
                <option value="">-- Pilih Status --</option>
                @foreach(\App\Support\MaritalStatus::dropdownOptions() as $val => $label)
                    <option value="{{ $val }}" @selected(in_array(old('wanita_marital_status', $wanita?->marital_status ?? ''), [$val, str_replace('_',' ',$val)], true))>{{ $label }}</option>
                @endforeach
            </select>

            @error('wanita_marital_status')
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
                name="wanita_education"
                value="{{ old('wanita_education', $wanita?->education ?? '') }}"
                placeholder="Contoh: SMA / SMK / D3 / S1"
                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm
                       focus:border-emerald-500 focus:ring-emerald-500"
            >

            @error('wanita_education')
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
                name="wanita_occupation"
                value="{{ old('wanita_occupation', $wanita?->occupation ?? '') }}"
                placeholder="Contoh: Guru / Karyawan Swasta"
                class="mt-1 block w-full rounded-md border-slate-300 shadow-sm
                       focus:border-emerald-500 focus:ring-emerald-500"
            >

            @error('wanita_occupation')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Alamat --}}
        <div class="md:col-span-2">
            <label class="block font-medium text-slate-700">
                Jalan / Blok / Dusun <span class="text-red-500">*</span>
            </label>

            <textarea
                name="wanita_address"
                rows="2"
                class="mt-1 block w-full rounded-md border-slate-300
                       shadow-sm focus:border-emerald-500 focus:ring-emerald-500"
                required
            >{{ old('wanita_address', $wanita?->address ?? '') }}</textarea>

            @error('wanita_address')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="block font-medium text-slate-700">RT</label>
            <input type="text" name="wanita_address_rt" value="{{ old('wanita_address_rt', '') }}" maxlength="3" placeholder="01" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
        </div>
        <div>
            <label class="block font-medium text-slate-700">RW</label>
            <input type="text" name="wanita_address_rw" value="{{ old('wanita_address_rw', '') }}" maxlength="3" placeholder="02" class="mt-1 block w-full rounded-md border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
        </div>

        {{-- Wilayah --}}
        <div class="md:col-span-2 bg-slate-50 p-4 rounded-lg border border-slate-200">

            <h4 class="font-semibold text-slate-700 mb-3 text-xs uppercase tracking-wider">
                Wilayah Domisili Catin Wanita
            </h4>

            @include('components.region-selector', [
                'prefix' => 'wanita',
                'provinces' => $provinces,
                'selectedProvince' => old('wanita_province_id', $wanita?->province_id ?? ''),
                'selectedRegency' => old('wanita_regency_id', $wanita?->regency_id ?? ''),
                'selectedDistrict' => old('wanita_district_id', $wanita?->district_id ?? ''),
                'selectedVillage' => old('wanita_village_id', $wanita?->village_id ?? ''),
            ])

        </div>

    </div>
</div>