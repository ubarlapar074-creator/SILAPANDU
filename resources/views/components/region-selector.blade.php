@php
$disabledExpression = $disabledExpression ?? 'false';
$selectedProvince = $selectedProvince ?? '';
$selectedRegency = $selectedRegency ?? '';
$selectedDistrict = $selectedDistrict ?? '';
$selectedVillage = $selectedVillage ?? '';
$idPrefix = $idPrefix ?? preg_replace('/[^A-Za-z0-9_-]/', '_', $prefix ?? 'region');
$namePrefix = $namePrefix ?? ($prefix ?? 'region');
@endphp

<div
    x-data="regionSelector({
        province: '{{ $selectedProvince }}',
        regency: '{{ $selectedRegency }}',
        district: '{{ $selectedDistrict }}',
        village: '{{ $selectedVillage }}'
    })"
    x-init="init()"
    class="grid grid-cols-1 gap-4 md:grid-cols-2"
>

{{-- =========================================================
     PROVINSI
========================================================== --}}
<div>

    <label
        for="{{ $idPrefix }}_province_id"
        class="block text-sm font-medium text-slate-700"
    >
        Provinsi
        <span
            x-show="!({{ $disabledExpression }})"
            class="text-red-500"
        >*</span>
    </label>

    <select
        id="{{ $idPrefix }}_province_id"
        name="{{ str_contains($namePrefix, '[') ? $namePrefix.'[province_id]' : $namePrefix.'_province_id' }}"
        x-model="province"
        @change="getRegencies()"
        :disabled="{{ $disabledExpression }}"
        :required="!({{ $disabledExpression }})"
        class="mt-1 block w-full rounded-md border-slate-300 shadow-sm
               focus:border-emerald-500 focus:ring-emerald-500
               disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400"
    >

        <option value="">
            Pilih Provinsi
        </option>

        @foreach ($provinces as $item)
            <option value="{{ $item->id }}">
                {{ $item->name }}
            </option>
        @endforeach

    </select>

</div>

{{-- =========================================================
     KABUPATEN / KOTA
========================================================== --}}
<div>

    <label
        for="{{ $idPrefix }}_regency_id"
        class="block text-sm font-medium text-slate-700"
    >
        Kabupaten/Kota
        <span
            x-show="!({{ $disabledExpression }})"
            class="text-red-500"
        >*</span>
    </label>

    <select
        id="{{ $idPrefix }}_regency_id"
        name="{{ str_contains($namePrefix, '[') ? $namePrefix.'[regency_id]' : $namePrefix.'_regency_id' }}"
        x-model="regency"
        @change="getDistricts()"
        :disabled="{{ $disabledExpression }} || !province || loadingRegency"
        :required="!({{ $disabledExpression }})"
        class="mt-1 block w-full rounded-md border-slate-300 shadow-sm
               focus:border-emerald-500 focus:ring-emerald-500
               disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400"
    >

        <option value="">
            Pilih Kabupaten/Kota
        </option>

        <template x-for="item in regencies" :key="item.id">
            <option
                :value="item.id"
                x-text="item.name"
            ></option>
        </template>

    </select>

    <p
        x-show="loadingRegency && !({{ $disabledExpression }})"
        class="mt-1 text-xs text-slate-500"
    >
        Memuat kabupaten/kota...
    </p>

</div>

{{-- =========================================================
     KECAMATAN
========================================================== --}}
<div>

    <label
        for="{{ $idPrefix }}_district_id"
        class="block text-sm font-medium text-slate-700"
    >
        Kecamatan
        <span
            x-show="!({{ $disabledExpression }})"
            class="text-red-500"
        >*</span>
    </label>

    <select
        id="{{ $idPrefix }}_district_id"
        name="{{ str_contains($namePrefix, '[') ? $namePrefix.'[district_id]' : $namePrefix.'_district_id' }}"
        x-model="district"
        @change="getVillages()"
        :disabled="{{ $disabledExpression }} || !regency || loadingDistrict"
        :required="!({{ $disabledExpression }})"
        class="mt-1 block w-full rounded-md border-slate-300 shadow-sm
               focus:border-emerald-500 focus:ring-emerald-500
               disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400"
    >

        <option value="">
            Pilih Kecamatan
        </option>

        <template x-for="item in districts" :key="item.id">
            <option
                :value="item.id"
                x-text="item.name"
            ></option>
        </template>

    </select>

    <p
        x-show="loadingDistrict && !({{ $disabledExpression }})"
        class="mt-1 text-xs text-slate-500"
    >
        Memuat kecamatan...
    </p>

</div>

{{-- =========================================================
     DESA / KELURAHAN
========================================================== --}}
<div>

    <label
        for="{{ $idPrefix }}_village_id"
        class="block text-sm font-medium text-slate-700"
    >
        Desa/Kelurahan
        <span
            x-show="!({{ $disabledExpression }})"
            class="text-red-500"
        >*</span>
    </label>

    <select
        id="{{ $idPrefix }}_village_id"
        name="{{ str_contains($namePrefix, '[') ? $namePrefix.'[village_id]' : $namePrefix.'_village_id' }}"
        x-model="village"
        :disabled="{{ $disabledExpression }} || !district || loadingVillage"
        :required="!({{ $disabledExpression }})"
        class="mt-1 block w-full rounded-md border-slate-300 shadow-sm
               focus:border-emerald-500 focus:ring-emerald-500
               disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400"
    >

        <option value="">
            Pilih Desa/Kelurahan
        </option>

        <template x-for="item in villages" :key="item.id">
            <option
                :value="item.id"
                x-text="item.name"
            ></option>
        </template>

    </select>

    <p
        x-show="loadingVillage && !({{ $disabledExpression }})"
        class="mt-1 text-xs text-slate-500"
    >
        Memuat desa/kelurahan...
    </p>

</div>

{{-- =========================================================
     ERROR
========================================================== --}}
<div
    x-show="error && !({{ $disabledExpression }})"
    x-text="error"
    class="text-sm text-red-600 md:col-span-2"
></div>

</div>

<script>
function regionSelector(initial = {}) {
    return {
        province: initial.province || '',
        regency: initial.regency || '',
        district: initial.district || '',
        village: initial.village || '',

        regencies: [],
        districts: [],
        villages: [],

        loadingRegency: false,
        loadingDistrict: false,
        loadingVillage: false,

        error: '',

        async init() {
            if (!this.province) {
                return;
            }

            await this.getRegencies(true);

            if (this.regency) {
                await this.getDistricts(true);
            }

            if (this.district) {
                await this.getVillages(true);
            }
        },

        async getRegencies(preserveSelection = false) {
            const keepRegency = preserveSelection ? this.regency : '';
            const keepDistrict = preserveSelection ? this.district : '';
            const keepVillage = preserveSelection ? this.village : '';
            if (!preserveSelection) {
                this.regency = '';
                this.district = '';
                this.village = '';
            }

            this.regencies = [];
            this.districts = [];
            this.villages = [];

            if (!this.province) {
                return;
            }

            this.loadingRegency = true;
            this.error = '';

            try {

                const response = await fetch(
                    '/internal/regions/regencies?province_id=' +
                    encodeURIComponent(this.province),
                    {
                        headers: {
                            'Accept': 'application/json'
                        }
                    }
                );
                if (!response.ok) {
                    throw new Error(
                        'HTTP ' + response.status
                    );
                }

                const json = await response.json();
                this.regencies = json.data || [];
                if (preserveSelection && keepRegency) {
                    const exists = this.regencies.some(r => String(r.id) === String(keepRegency));
                    if (!exists) {
                        this.regency = '';
                        this.district = '';
                        this.village = '';
                    } else {
                        this.regency = keepRegency;
                        this.district = keepDistrict;
                        this.village = keepVillage;
                    }
                }

            } catch (e) {
                this.error =
                    'Gagal memuat Kabupaten/Kota.';
                if (preserveSelection) {
                    this.regency = keepRegency;
                    this.district = keepDistrict;
                    this.village = keepVillage;
                }

            } finally {

                this.loadingRegency = false;

            }
        },

        async getDistricts(preserveSelection = false) {
            const keepDistrict = preserveSelection ? this.district : '';
            const keepVillage = preserveSelection ? this.village : '';
            if (!preserveSelection) {
                this.district = '';
                this.village = '';
            }

            this.districts = [];
            this.villages = [];

            if (!this.regency) {
                return;
            }

            this.loadingDistrict = true;
            this.error = '';

            try {

                const response = await fetch(
                    '/internal/regions/districts?regency_id=' +
                    encodeURIComponent(this.regency),
                    {
                        headers: {
                            'Accept': 'application/json'
                        }
                    }
                );

                if (!response.ok) {
                    throw new Error(
                        'HTTP ' + response.status
                    );
                }

                const json = await response.json();
                this.districts = json.data || [];
                if (preserveSelection && keepDistrict) {
                    const exists = this.districts.some(d => String(d.id) === String(keepDistrict));
                    if (!exists) {
                        this.district = '';
                        this.village = '';
                    } else {
                        this.district = keepDistrict;
                        this.village = keepVillage;
                    }
                }

            } catch (e) {
                this.error =
                    'Gagal memuat Kecamatan.';
                if (preserveSelection) {
                    this.district = keepDistrict;
                    this.village = keepVillage;
                }

            } finally {

                this.loadingDistrict = false;

            }
        },

        async getVillages(preserveSelection = false) {
            const keepVillage = preserveSelection ? this.village : '';
            if (!preserveSelection) {
                this.village = '';
            }
            this.villages = [];

            if (!this.district) {
                return;
            }

            this.loadingVillage = true;
            this.error = '';

            try {

                const response = await fetch(
                    '/internal/regions/villages?district_id=' +
                    encodeURIComponent(this.district),
                    {
                        headers: {
                            'Accept': 'application/json'
                        }
                    }
                );

                if (!response.ok) {
                    throw new Error(
                        'HTTP ' + response.status
                    );
                }

                const json = await response.json();
                this.villages = json.data || [];
                if (preserveSelection && keepVillage) {
                    const exists = this.villages.some(v => String(v.id) === String(keepVillage));
                    if (!exists) this.village = '';
                    else this.village = keepVillage;
                }

            } catch (e) {
                this.error =
                    'Gagal memuat Desa/Kelurahan.';
                if (preserveSelection) this.village = keepVillage;

            } finally {

                this.loadingVillage = false;

            }
        }
    };
}
</script>
