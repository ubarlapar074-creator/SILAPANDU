@extends('layouts.app')

@section('title', 'Jadwal Akad & Mahar')

@section('content')
@php
    $priaParty = $registration->parties()->where('role', 'calon_pria')->with('warga')->first();
    $wanitaParty = $registration->parties()->where('role', 'calon_wanita')->with('warga')->first();
    $priaNama = $priaParty?->nama_sementara ?? $priaParty?->warga?->nama_lengkap ?? 'Belum Diisi';
    $wanitaNama = $wanitaParty?->nama_sementara ?? $wanitaParty?->warga?->nama_lengkap ?? 'Belum Diisi';

    $provincesData = $provinces ?? \Illuminate\Support\Facades\DB::table('provinces')->orderBy('name')->get(['id', 'name']);
    $regenciesData = $regencies ?? ($registration->province_id ? \Illuminate\Support\Facades\DB::table('regencies')->where('province_id', $registration->province_id)->orderBy('name')->get(['id', 'name']) : collect());
    $districtsData = $districts ?? ($registration->regency_id ? \Illuminate\Support\Facades\DB::table('districts')->where('regency_id', $registration->regency_id)->orderBy('name')->get(['id', 'name']) : collect());
    $villagesData = $villages ?? ($registration->district_id ? \Illuminate\Support\Facades\DB::table('villages')->where('district_id', $registration->district_id)->orderBy('name')->get(['id', 'name']) : collect());
@endphp

<div class="max-w-5xl mx-auto py-6 px-4 space-y-6"
     x-data="{
         provinces: @js($provincesData),
         regencies: @js($regenciesData),
         districts: @js($districtsData),
         villages: @js($villagesData),
         provinceId: '{{ old('province_id', $registration->province_id ?? '') }}',
         regencyId: '{{ old('regency_id', $registration->regency_id ?? '') }}',
         districtId: '{{ old('district_id', $registration->district_id ?? '') }}',
         villageId: '{{ old('village_id', $registration->village_id ?? '') }}',

         async fetchRegions(table, parentId = null) {
             const url = '{{ url('/registration/api/region') }}/' + table + (parentId ? '/' + parentId : '');
             try {
                 const res = await fetch(url);
                 return res.ok ? await res.json() : [];
             } catch (e) {
                 return [];
             }
         },
         async loadProvinces() {
             if (this.provinces.length === 0) {
                 this.provinces = await this.fetchRegions('provinces');
             }
         },
         async updateRegencies() {
             this.regencyId = ''; this.districtId = ''; this.villageId = '';
             this.regencies = this.provinceId ? await this.fetchRegions('regencies', this.provinceId) : [];
             this.districts = []; this.villages = [];
         },
         async updateDistricts() {
             this.districtId = ''; this.villageId = '';
             this.districts = this.regencyId ? await this.fetchRegions('districts', this.regencyId) : [];
             this.villages = [];
         },
         async updateVillages() {
             this.villageId = '';
             this.villages = this.districtId ? await this.fetchRegions('villages', this.districtId) : [];
         },
         async init() {
             await this.loadProvinces();
             if (this.provinceId && this.regencies.length === 0) {
                 this.regencies = await this.fetchRegions('regencies', this.provinceId);
             }
             if (this.regencyId && this.districts.length === 0) {
                 this.districts = await this.fetchRegions('districts', this.regencyId);
             }
             if (this.districtId && this.villages.length === 0) {
                 this.villages = await this.fetchRegions('villages', this.districtId);
             }
         }
     }"
     x-init="init()">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('registration.dashboard', $registration->id) }}" class="inline-flex items-center gap-2 text-sm text-emerald-600 hover:text-emerald-700 font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Dashboard Pendaftaran
        </a>
        <span class="font-mono text-xs bg-slate-100 text-slate-600 border border-slate-200 px-2.5 py-1 rounded-lg">
            {{ $registration->registration_number }}
        </span>
    </div>

    @if ($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-xs text-rose-800 space-y-1">
            <p class="font-bold">Mohon perbaiki kesalahan berikut:</p>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('registration.store-akad', $registration->id) }}" method="POST">
        @csrf

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <div class="flex items-start justify-between gap-4 border-b pb-4 mb-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Tahap 5: Jadwal Akad & Mahar</h2>
                    <p class="text-xs text-gray-500 mt-1">Lengkapi tanggal &amp; waktu pelaksanaan akad serta rincian mahar.</p>
                </div>
                <span class="shrink-0 px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                    Kerangka Formulir
                </span>
            </div>

            {{-- Info Mempelai (read-only teks) --}}
            <div class="bg-emerald-50 border border-emerald-200 rounded-lg px-4 py-3 mb-6 flex items-center gap-2 text-sm text-emerald-800">
                <svg class="w-4 h-4 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                <p><span class="font-semibold">Mempelai:</span> Pernikahan antara: {{ $priaNama }} & {{ $wanitaNama }}</p>
            </div>

            {{-- Field 1: Jadwal Akad --}}
            <div class="space-y-3 mb-6">
                <h4 class="text-sm font-semibold text-gray-700">Jadwal Akad Nikah</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label for="tanggal_akad" class="block text-xs font-medium text-gray-600 mb-1">Tanggal Akad <span class="text-red-500">*</span></label>
                        <input type="date" id="tanggal_akad" name="tanggal_akad" required
                               value="{{ old('tanggal_akad', $registration->tanggal_akad?->format('Y-m-d') ?? '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm">
                    </div>
                    <div>
                        <label for="jam_akad" class="block text-xs font-medium text-gray-600 mb-1">Jam Akad <span class="text-red-500">*</span></label>
                        <input type="time" id="jam_akad" name="jam_akad" required
                               value="{{ old('jam_akad', $registration->jam_akad ? substr($registration->jam_akad, 0, 5) : '') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm">
                    </div>
                </div>
            </div>

            {{-- Field 2: Mahar --}}
            <div class="space-y-3 mb-6">
                <h4 class="text-sm font-semibold text-gray-700">Mahar</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="md:col-span-1">
                        <label for="jenis_mahar" class="block text-xs font-medium text-gray-600 mb-1">Jenis Mahar</label>
                        <select id="jenis_mahar" name="jenis_mahar"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm">
                            <option value="uang" {{ old('jenis_mahar') === 'uang' ? 'selected' : '' }}>Uang Tunai</option>
                            <option value="emas" {{ old('jenis_mahar') === 'emas' ? 'selected' : '' }}>Perhiasan / Emas</option>
                            <option value="lainnya" {{ old('jenis_mahar') === 'lainnya' ? 'selected' : '' }}>Lainnya (Alat Shalat, dll)</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label for="nominal_uang" class="block text-xs font-medium text-gray-600 mb-1">Nominal Uang (Rp)</label>
                        <input type="number" id="nominal_uang" name="nominal_uang" inputmode="numeric"
                               value="{{ old('nominal_uang', '') }}"
                               placeholder="Contoh: 5000000"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm">
                    </div>
                    <div class="md:col-span-3">
                        <label for="rincian_mahar" class="block text-xs font-medium text-gray-600 mb-1">Rincian Mahar (untuk selain uang / teks lengkap)</label>
                        <input type="text" id="rincian_mahar" name="rincian_mahar"
                               value="{{ old('rincian_mahar', $registration->marriage_dowry_display ?? '') }}"
                               placeholder="Contoh: Seperangkat alat sholat &amp; emas 5 gram dibayar tunai"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm">
                    </div>
                </div>
            </div>

            {{-- BLOK 2: Field 3: Wilayah & Lokasi Akad (Dropdown Wilayah Terpilih Otomatis) --}}
            <div class="space-y-3 mb-6">
                <h4 class="text-sm font-semibold text-gray-700">Wilayah &amp; Lokasi Akad</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                    <div>
                        <label for="province_id" class="block text-xs font-medium text-gray-600 mb-1">Provinsi</label>
                        <select id="province_id" name="province_id" x-model="provinceId" @change="updateRegencies()"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm bg-white">
                            <option value="">Pilih Provinsi</option>
                            <template x-for="p in provinces" :key="p.id">
                                <option :value="p.id" x-text="p.name" :selected="String(p.id) === String(provinceId)"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label for="regency_id" class="block text-xs font-medium text-gray-600 mb-1">Kabupaten/Kota</label>
                        <select id="regency_id" name="regency_id" x-model="regencyId" @change="updateDistricts()"
                                :disabled="!provinceId"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm bg-white disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed">
                            <option value="">Pilih Kabupaten/Kota</option>
                            <template x-for="r in regencies" :key="r.id">
                                <option :value="r.id" x-text="r.name" :selected="String(r.id) === String(regencyId)"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label for="district_id" class="block text-xs font-medium text-gray-600 mb-1">Kecamatan</label>
                        <select id="district_id" name="district_id" x-model="districtId" @change="updateVillages()"
                                :disabled="!regencyId"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm bg-white disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed">
                            <option value="">Pilih Kecamatan</option>
                            <template x-for="d in districts" :key="d.id">
                                <option :value="d.id" x-text="d.name" :selected="String(d.id) === String(districtId)"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label for="village_id" class="block text-xs font-medium text-gray-600 mb-1">Desa/Kelurahan</label>
                        <select id="village_id" name="village_id" x-model="villageId"
                                :disabled="!districtId"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm bg-white disabled:bg-gray-100 disabled:text-gray-500 disabled:cursor-not-allowed">
                            <option value="">Pilih Desa/Kelurahan</option>
                            <template x-for="v in villages" :key="v.id">
                                <option :value="v.id" x-text="v.name" :selected="String(v.id) === String(villageId)"></option>
                            </template>
                        </select>
                    </div>
                </div>
                <div>
                    <label for="marriage_location_address" class="block text-xs font-medium text-gray-600 mb-1">Alamat Lengkap Lokasi Akad (Gedung/Masjid/Rumah)</label>
                    <textarea id="marriage_location_address" name="marriage_location_address" rows="2"
                              placeholder="Contoh: Jl. Masjid Agung No. 12, RT 01/RW 02"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm">{{ old('marriage_location_address', $registration->marriage_location_address ?? '') }}</textarea>
                </div>
            </div>

            {{-- Footer Tombol --}}
            <div class="pt-4 border-t flex flex-wrap items-center justify-between gap-3">
                <a href="{{ route('registration.dashboard', $registration->id) }}"
                   class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium text-sm transition">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2 text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg font-medium text-sm transition flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Simpan Jadwal &amp; Mahar
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
