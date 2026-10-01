<div
    x-data="regionSelector('{{ $prefix }}')"
    class="grid grid-cols-1 md:grid-cols-4 gap-3"
>
    {{-- Provinsi --}}
    <div>
        <label class="block text-xs font-medium text-slate-600 mb-1">
            Provinsi
        </label>

        <select
            name="catin_{{ $prefix }}[province_id]"
            x-model="provinceId"
            @change="loadRegencies"
            class="block w-full rounded-md border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"
        >
            <option value="">Pilih Provinsi</option>

            @foreach ($provinces as $province)
                <option value="{{ $province->id }}">
                    {{ $province->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Kabupaten/Kota --}}
    <div>
        <label class="block text-xs font-medium text-slate-600 mb-1">
            Kabupaten/Kota
        </label>

        <select
            name="catin_{{ $prefix }}[regency_id]"
            x-model="regencyId"
            @change="loadDistricts"
            :disabled="!provinceId || loadingRegencies"
            class="block w-full rounded-md border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 disabled:bg-slate-100"
        >
            <option value="">
                <span x-text="loadingRegencies ? 'Memuat...' : 'Pilih Kabupaten/Kota'"></span>
            </option>

            <template x-for="item in regencies" :key="item.id">
                <option :value="item.id" x-text="item.name"></option>
            </template>
        </select>
    </div>

    {{-- Kecamatan --}}
    <div>
        <label class="block text-xs font-medium text-slate-600 mb-1">
            Kecamatan
        </label>

        <select
            name="catin_{{ $prefix }}[district_id]"
            x-model="districtId"
            @change="loadVillages"
            :disabled="!regencyId || loadingDistricts"
            class="block w-full rounded-md border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 disabled:bg-slate-100"
        >
            <option value="">Pilih Kecamatan</option>

            <template x-for="item in districts" :key="item.id">
                <option :value="item.id" x-text="item.name"></option>
            </template>
        </select>
    </div>

    {{-- Desa/Kelurahan --}}
    <div>
        <label class="block text-xs font-medium text-slate-600 mb-1">
            Desa/Kelurahan
        </label>

        <select
            name="catin_{{ $prefix }}[village_id]"
            x-model="villageId"
            :disabled="!districtId || loadingVillages"
            class="block w-full rounded-md border-slate-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 disabled:bg-slate-100"
        >
            <option value="">Pilih Desa/Kelurahan</option>

            <template x-for="item in villages" :key="item.id">
                <option :value="item.id" x-text="item.name"></option>
            </template>
        </select>
    </div>
</div>

<script>
function regionSelector(prefix) {
    return {
        prefix,

        provinceId: '',
        regencyId: '',
        districtId: '',
        villageId: '',

        regencies: [],
        districts: [],
        villages: [],

        loadingRegencies: false,
        loadingDistricts: false,
        loadingVillages: false,

        async loadRegencies() {
            this.regencyId = '';
            this.districtId = '';
            this.villageId = '';

            this.regencies = [];
            this.districts = [];
            this.villages = [];

            if (!this.provinceId) {
                return;
            }

            this.loadingRegencies = true;

            try {
                const response = await fetch(
                    `/internal/regions/regencies?province_id=${encodeURIComponent(this.provinceId)}`
                );

                if (!response.ok) {
                    throw new Error('Gagal mengambil kabupaten/kota.');
                }

                const result = await response.json();

                this.regencies = result.data ?? [];
            } catch (error) {
                /* alert removed */
            } finally {
                this.loadingRegencies = false;
            }
        },

        async loadDistricts() {
            this.districtId = '';
            this.villageId = '';

            this.districts = [];
            this.villages = [];

            if (!this.regencyId) {
                return;
            }

            this.loadingDistricts = true;

            try {
                const response = await fetch(
                    `/internal/regions/districts?regency_id=${encodeURIComponent(this.regencyId)}`
                );

                if (!response.ok) {
                    throw new Error('Gagal mengambil kecamatan.');
                }

                const result = await response.json();

                this.districts = result.data ?? [];
            } catch (error) {
                /* alert removed */
            } finally {
                this.loadingDistricts = false;
            }
        },

        async loadVillages() {
            this.villageId = '';
            this.villages = [];

            if (!this.districtId) {
                return;
            }

            this.loadingVillages = true;

            try {
                const response = await fetch(
                    `/internal/regions/villages?district_id=${encodeURIComponent(this.districtId)}`
                );

                if (!response.ok) {
                    throw new Error('Gagal mengambil desa/kelurahan.');
                }

                const result = await response.json();

                this.villages = result.data ?? [];
            } catch (error) {
                /* alert removed */
            } finally {
                this.loadingVillages = false;
            }
        }
    };
}
</script>