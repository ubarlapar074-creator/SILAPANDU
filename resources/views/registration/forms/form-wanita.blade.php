@extends('layouts.app')

@section('title', 'Formulir Calon Wanita')

@section('content')
@php
    $wanitaParty = isset($registration) ? $registration->parties()->where('role', 'calon_wanita')->with('warga')->first() : null;
    $wanita = $wanitaParty?->warga;
    $bapak = isset($registration) ? $registration->parents()->where('type', 'bapak_wanita')->first() : null;
    $ibu = isset($registration) ? $registration->parents()->where('type', 'ibu_wanita')->first() : null;
    $provincesList = $provinces ?? \Illuminate\Support\Facades\DB::table('provinces')->orderBy('name')->get(['id', 'name']);
@endphp

<div class="min-h-screen bg-slate-950 py-6 px-4 md:px-8 font-sans text-slate-200" 
     x-data="{
        activeTab: 'wanita',
        nik: @js(old('wanita.nik', $wanita?->nik ?? '')),
        registrationId: @js(isset($registration) ? $registration->id : null),
        loading: false,
        statusNik: @js($wanita ? 'found' : 'idle'),
        pesanError: '',
        isMarriedBefore: false,
        statusPernikahanSebelumnya: @js(old('wanita.status_sebelumnya', '')),
        verifikasiDokumen: @js((bool) old('wanita.verifikasi_dokumen', false)),
        tempFoundData: null,
        showPromptAutofill: false,
        isReadonly: false,
        
        dataWarga: {
            nama_lengkap: @js(old('wanita.nama_lengkap', $wanita?->nama_lengkap ?? '')),
            tempat_lahir: @js(old('wanita.tempat_lahir', $wanita?->tempat_lahir ?? '')),
            ttl_lahir: @js(old('wanita.ttl_lahir', $wanita?->tgl_lahir?->format('Y-m-d') ?? '')),
            pekerjaan: @js(old('wanita.pekerjaan', $wanita?->pekerjaan ?? '')),
            alamat: @js(old('wanita.alamat', $wanita?->alamat ?? '')),
            rt: @js(old('wanita.rt', $wanita?->rt ?? '')),
            rw: @js(old('wanita.rw', $wanita?->rw ?? '')),
            desa_asal: @js(old('wanita.desa_asal', $wanitaParty?->desa_asal ?? '')),
            no_hp: @js(old('wanita.no_hp', $wanita?->no_hp ?? '')),
            email: @js(old('wanita.email', $wanita?->email ?? '')),
            pendidikan: @js(old('wanita.pendidikan', $wanita?->pendidikan ?? '')),
            agama: @js(old('wanita.agama', $wanita?->agama ?? 'Islam')),
            status_kawin: @js(old('wanita.status_kawin', $wanita?->status_kawin ?? 'belum_kawin')),
            nama_ayah: @js(old('wanita.nama_ayah', $wanita?->nama_ayah ?? '')),
            nama_ibu: @js(old('wanita.nama_ibu', $wanita?->nama_ibu ?? ''))
        },
        
        bapakNama: @js(old('bapak.nama', $bapak?->nama ?? ($wanita?->nama_ayah ?? ''))),
        bapakAlamat: @js(old('bapak.alamat', $bapak?->alamat ?? '')),
        
        ibuNama: @js(old('ibu.nama', $ibu?->nama ?? ($wanita?->nama_ibu ?? ''))),
        ibuAlamat: @js(old('ibu.alamat', $ibu?->alamat ?? '')),

        provinces: @js($provincesList),
        regencies: [],
        districts: [],
        villages: [],
        provinceId: '',
        regencyId: '',
        districtId: '',
        villageId: '',
        alamatJalan: '',

        async fetchRegions(table, parentId = null) {
            const url = '{{ url('/registration/api/region') }}/' + table + (parentId ? '/' + parentId : '');
            try {
                const res = await fetch(url);
                return res.ok ? await res.json() : [];
            } catch (e) { return []; }
        },
        async loadProvinces() {
            if (this.provinces.length === 0) this.provinces = await this.fetchRegions('provinces');
        },
        async updateRegencies() {
            this.regencyId = ''; this.districtId = ''; this.villageId = '';
            this.regencies = this.provinceId ? await this.fetchRegions('regencies', this.provinceId) : [];
            this.districts = []; this.villages = [];
            this.composeAlamat();
        },
        async updateDistricts() {
            this.districtId = ''; this.villageId = '';
            this.districts = this.regencyId ? await this.fetchRegions('districts', this.regencyId) : [];
            this.villages = [];
            this.composeAlamat();
        },
        async updateVillages() {
            this.villageId = '';
            this.villages = this.districtId ? await this.fetchRegions('villages', this.districtId) : [];
            this.composeAlamat();
        },
        composeAlamat() {
            let parts = [];
            if (this.alamatJalan && this.alamatJalan.trim()) parts.push(this.alamatJalan.trim());
            const villageObj = this.villages.find(v => String(v.id) === String(this.villageId));
            if (villageObj) parts.push('Desa/Kel. ' + villageObj.name);
            const districtObj = this.districts.find(d => String(d.id) === String(this.districtId));
            if (districtObj) parts.push('Kec. ' + districtObj.name);
            const regencyObj = this.regencies.find(r => String(r.id) === String(this.regencyId));
            if (regencyObj) parts.push('Kab. ' + regencyObj.name);
            const provinceObj = this.provinces.find(p => String(p.id) === String(this.provinceId));
            if (provinceObj) parts.push('Prov. ' + provinceObj.name);
            this.dataWarga.alamat = parts.join(', ');
        },
        async initAlamatFromText(alamat) {
            if (!alamat) return;
            const provMatch = alamat.match(/Prov\.\s*([^,]+)/i);
            const desaMatch = alamat.match(/Desa\/Kel\.\s*([^,]+)/i);
            const kecMatch = alamat.match(/Kec\.\s*([^,]+)/i);
            const parts = alamat.split(/Desa\/Kel\./i);
            if (parts.length > 1) this.alamatJalan = parts[0].replace(/,\s*$/, '').trim();
            else this.alamatJalan = alamat;
            
            if (provMatch && this.provinces.length > 0) {
                const provName = provMatch[1].trim().toLowerCase();
                const foundProv = this.provinces.find(p => p.name.toLowerCase() === provName || p.name.toLowerCase().includes(provName));
                if (foundProv) {
                    this.provinceId = foundProv.id;
                    this.regencies = await this.fetchRegions('regencies', this.provinceId);
                    const foundReg = this.regencies.find(r => alamat.toLowerCase().includes(r.name.toLowerCase()));
                    if (foundReg) {
                        this.regencyId = foundReg.id;
                        this.districts = await this.fetchRegions('districts', this.regencyId);
                        if (kecMatch) {
                            const kecName = kecMatch[1].trim().toLowerCase();
                            const foundDist = this.districts.find(d => d.name.toLowerCase() === kecName || d.name.toLowerCase().includes(kecName));
                            if (foundDist) {
                                this.districtId = foundDist.id;
                                this.villages = await this.fetchRegions('villages', this.districtId);
                                if (desaMatch) {
                                    const desaName = desaMatch[1].trim().toLowerCase();
                                    const foundVill = this.villages.find(v => v.name.toLowerCase() === desaName || v.name.toLowerCase().includes(desaName));
                                    if (foundVill) this.villageId = foundVill.id;
                                }
                            }
                        }
                    }
                }
            }
        },
        async checkNik() {
            this.nik = this.nik.replace(/[^0-9]/g, '');
            this.statusNik = 'idle';
            this.pesanError = '';
            this.isMarriedBefore = false;
            this.statusPernikahanSebelumnya = '';
            this.verifikasiDokumen = false;
            this.showPromptAutofill = false;
            this.tempFoundData = null;

            if (this.nik.length === 0) return;
            if (this.nik.length < 16) {
                this.pesanError = 'NIK harus tepat 16 digit.';
                return;
            }
            this.loading = true;
            this.statusNik = 'loading';
            try {
                const regId = this.registrationId ? `?registration_id=${this.registrationId}` : '';
                const response = await fetch(`/api/check-nik/${this.nik}${regId}`);
                const result = await response.json();
                if (result.status === 'found') {
                    this.statusNik = 'found';
                    this.isMarriedBefore = result.is_married_before;
                    this.tempFoundData = result.data;
                    this.showPromptAutofill = true;
                } else if (result.status === 'not_found') {
                    this.statusNik = 'not_found';
                    this.isReadonly = false;
                    this.showPromptAutofill = false;
                } else {
                    this.statusNik = 'error';
                    this.pesanError = result.message || 'Terjadi kesalahan.';
                }
            } catch (error) {
                this.statusNik = 'error';
                this.pesanError = 'Terjadi kesalahan jaringan.';
            } finally {
                this.loading = false;
            }
        },
        gunakanData() {
            if (!this.tempFoundData) return;
            this.dataWarga = { ...this.dataWarga, ...this.tempFoundData };
            if (this.tempFoundData.nama_ayah) this.bapakNama = this.tempFoundData.nama_ayah;
            if (this.tempFoundData.nama_ibu) this.ibuNama = this.tempFoundData.nama_ibu;
            if (this.dataWarga.alamat) this.initAlamatFromText(this.dataWarga.alamat);
            this.isReadonly = true;
            this.showPromptAutofill = false;
        },
        isiManual() {
            this.dataWarga = { nama_lengkap: '', tempat_lahir: '', ttl_lahir: '', pekerjaan: '', alamat: '', desa_asal: '', no_hp: '', email: '', pendidikan: '', agama: 'Islam', status_kawin: 'belum_kawin', nama_ayah: '', nama_ibu: '' };
            this.bapakNama = ''; this.ibuNama = '';
            this.alamatJalan = '';
            this.provinceId = ''; this.regencyId = ''; this.districtId = ''; this.villageId = '';
            this.regencies = []; this.districts = []; this.villages = [];
            this.isReadonly = false;
            this.showPromptAutofill = false;
        }
     }"
     x-init="
        loadProvinces();
        if (dataWarga.alamat) { initAlamatFromText(dataWarga.alamat); }
        $watch('dataWarga.nama_ayah', val => { if(val) bapakNama = val; });
        $watch('dataWarga.nama_ibu', val => { if(val) ibuNama = val; });
     ">

    <div class="max-w-5xl mx-auto bg-slate-900 rounded-2xl shadow-2xl border border-slate-800 overflow-hidden">
        
        <!-- HEADER FORM -->
        <div class="p-6 border-b border-slate-800 bg-slate-900/50">
            <a href="{{ isset($registration) ? route('registration.dashboard', $registration->id) : route('dashboard') }}" class="inline-flex items-center text-sm text-slate-400 hover:text-emerald-400 mb-3 transition">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard Pendaftaran
            </a>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-white">Formulir Calon Pengantin Wanita</h1>
                    <p class="text-sm text-slate-400">Lengkapi data calon pengantin wanita, ayah, dan ibu sesuai dokumen kependudukan.</p>
                </div>
                @if(isset($registration))
                <div class="flex items-center gap-2 bg-emerald-950/50 text-emerald-400 px-3 py-1.5 rounded-lg border border-emerald-500/30 text-sm font-medium">
                    <i class="fas fa-shield-halved"></i> {{ $registration->registration_number }}
                </div>
                @endif
            </div>
        </div>

        <!-- TAB NAVIGATION -->
        <div class="border-b border-slate-800 bg-slate-950/50 px-6 pt-3">
            <nav class="flex space-x-8" aria-label="Tabs">
                <button type="button" @click="activeTab = 'wanita'" :class="activeTab === 'wanita' ? 'border-b-2 border-emerald-500 text-emerald-400' : 'text-slate-500 hover:text-slate-300 border-b-2 border-transparent'" class="pb-4 text-sm flex items-center gap-2 transition-colors">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold" :class="activeTab === 'wanita' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-800 text-slate-400'">1</span>
                    Data Calon Wanita
                </button>
                <button type="button" @click="activeTab = 'bapak'" :class="activeTab === 'bapak' ? 'border-b-2 border-emerald-500 text-emerald-400' : 'text-slate-500 hover:text-slate-300 border-b-2 border-transparent'" class="pb-4 text-sm flex items-center gap-2 transition-colors">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold" :class="activeTab === 'bapak' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-800 text-slate-400'">2</span>
                    Data Bapak
                </button>
                <button type="button" @click="activeTab = 'ibu'" :class="activeTab === 'ibu' ? 'border-b-2 border-emerald-500 text-emerald-400' : 'text-slate-500 hover:text-slate-300 border-b-2 border-transparent'" class="pb-4 text-sm flex items-center gap-2 transition-colors">
                    <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold" :class="activeTab === 'ibu' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-slate-800 text-slate-400'">3</span>
                    Data Ibu
                </button>
            </nav>
        </div>

        <!-- FORM CONTENT -->
        <form action="{{ route('registration.store-wanita', $registration->id) }}" method="POST" class="p-6 md:p-8 space-y-6">
            @csrf

            @if ($errors->any())
                <div class="rounded-xl border border-rose-500/30 bg-rose-950/30 p-4 text-xs text-rose-400 space-y-1">
                    <p class="font-bold">Mohon perbaiki kesalahan berikut:</p>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- TAB 1: DATA CALON WANITA -->
            <div x-show="activeTab === 'wanita'" class="space-y-6" x-transition.opacity>
                
                <div class="md:col-span-2 flex justify-end">
                    <template x-if="isReadonly">
                        <button type="button" @click="isReadonly = false" class="text-xs px-2.5 py-1 bg-amber-500/10 text-amber-400 border border-amber-500/30 rounded-md hover:bg-amber-500/20 transition">
                            Buka Kunci (Edit Manual)
                        </button>
                    </template>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-300 mb-1">NIK (Nomor Induk Kependudukan) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="text" name="wanita[nik]" x-model="nik" @input.debounce.1500ms="checkNik()" maxlength="16" inputmode="numeric" required placeholder="Masukkan 16 digit NIK" 
                                   class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none text-sm text-white placeholder-slate-600">
                            <div x-show="loading" class="absolute right-3 top-3 text-emerald-400">
                                <i class="fas fa-spinner fa-spin"></i>
                            </div>
                        </div>
                        <p x-show="pesanError" x-text="pesanError" class="text-xs text-rose-400 mt-1" x-cloak></p>
                        <p x-show="statusNik === 'not_found'" class="text-xs text-blue-400 mt-1 font-medium" x-cloak>ℹ️ Warga Baru. Silakan isi data secara manual.</p>

                        <!-- DIALOG KONFIRMASI AUTOFILL -->
                        <div x-show="showPromptAutofill && tempFoundData" x-cloak class="mt-3 p-4 rounded-xl bg-emerald-950/30 border border-emerald-500/30 shadow-sm space-y-3">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-sm font-semibold text-emerald-300">
                                    Data ditemukan (Nama: <span class="font-extrabold" x-text="tempFoundData ? tempFoundData.nama_lengkap : ''"></span>). Gunakan data ini?
                                </p>
                            </div>
                            <div class="flex items-center gap-2 pt-1">
                                <button type="button" @click="gunakanData()" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold rounded-lg shadow-sm transition">Gunakan Data</button>
                                <button type="button" @click="isiManual()" class="px-3.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 text-xs font-semibold rounded-lg shadow-sm transition">Isi Manual</button>
                            </div>
                        </div>

                        <!-- DETEKSI PERNIKAHAN KEDUA -->
                        <div x-show="isMarriedBefore || ['duda', 'janda', 'cerai'].includes(dataWarga.status_kawin)" x-cloak class="mt-3 bg-amber-950/30 border border-amber-500/30 p-4 rounded-xl space-y-3">
                            <div class="flex gap-2.5 items-start">
                                <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <div>
                                    <p class="text-sm text-amber-400 font-bold">Peringatan: Deteksi Pernikahan Sebelumnya</p>
                                    <p class="text-xs text-amber-500/80 mt-0.5">Calon pengantin tercatat pernah menikah sebelumnya. Petugas wajib memverifikasi dokumen fisik asli.</p>
                                </div>
                            </div>
                            <div x-show="isMarriedBefore" x-cloak class="mt-3 p-4 bg-amber-50 border border-amber-300 rounded-lg space-y-3">
                                <p class="text-sm font-bold text-amber-800">⚠️ NIK ini sudah pernah terdaftar. Pilih status pernikahan sebelumnya:</p>
                                <div class="flex flex-wrap gap-4 mt-2">
                                    <label class="inline-flex items-center gap-1.5 text-sm font-medium text-amber-900 cursor-pointer">
                                        <input type="radio" name="wanita[status_sebelumnya]" value="cerai_mati" x-model="statusPernikahanSebelumnya" class="w-4 h-4 text-emerald-600 border-amber-400 focus:ring-emerald-600">
                                        <span>Cerai Mati</span>
                                    </label>
                                    <label class="inline-flex items-center gap-1.5 text-sm font-medium text-amber-900 cursor-pointer">
                                        <input type="radio" name="wanita[status_sebelumnya]" value="cerai_hidup" x-model="statusPernikahanSebelumnya" class="w-4 h-4 text-emerald-600 border-amber-400 focus:ring-emerald-600">
                                        <span>Cerai Hidup</span>
                                    </label>
                                    <label class="inline-flex items-center gap-1.5 text-sm font-medium text-amber-900 cursor-pointer">
                                        <input type="radio" name="wanita[status_sebelumnya]" value="poligami" x-model="statusPernikahanSebelumnya" class="w-4 h-4 text-emerald-600 border-amber-400 focus:ring-emerald-600">
                                        <span>Poligami</span>
                                    </label>
                                </div>
                            </div>

                            <div class="pt-2 border-t border-amber-500/20">
                                <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-amber-400">
                                    <input type="checkbox" name="wanita[verifikasi_dokumen]" value="1" x-model="verifikasiDokumen"
                                           :required="['duda', 'janda', 'cerai'].includes(dataWarga.status_kawin) || isMarriedBefore"
                                           class="w-4 h-4 text-emerald-500 bg-slate-900 border-slate-700 rounded focus:ring-emerald-600 focus:ring-2">
                                    <span>Saya telah memverifikasi Akta Cerai / Akta Kematian (Fisik). <span class="text-rose-500">*</span></span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                        <input type="text" name="wanita[nama_lengkap]" x-model="dataWarga.nama_lengkap" :readonly="isReadonly" required
                               :class="isReadonly ? 'bg-slate-800 text-slate-400' : 'bg-slate-950 text-white'"
                               class="w-full px-3 py-2.5 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm placeholder-slate-600 transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">Tempat Lahir <span class="text-rose-500">*</span></label>
                        <input type="text" name="wanita[tempat_lahir]" x-model="dataWarga.tempat_lahir" :readonly="isReadonly" required
                               :class="isReadonly ? 'bg-slate-800 text-slate-400' : 'bg-slate-950 text-white'"
                               class="w-full px-3 py-2.5 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm placeholder-slate-600 transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">Tanggal Lahir <span class="text-rose-500">*</span></label>
                        <input type="date" name="wanita[ttl_lahir]" x-model="dataWarga.ttl_lahir" :readonly="isReadonly" required
                               :class="isReadonly ? 'bg-slate-800 text-slate-400' : 'bg-slate-950 text-white'"
                               class="w-full px-3 py-2.5 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">Pekerjaan <span class="text-rose-500">*</span></label>
                        <input type="text" name="wanita[pekerjaan]" x-model="dataWarga.pekerjaan" :readonly="isReadonly" required
                               :class="isReadonly ? 'bg-slate-800 text-slate-400' : 'bg-slate-950 text-white'"
                               class="w-full px-3 py-2.5 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm placeholder-slate-600 transition-colors">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">Agama <span class="text-rose-500">*</span></label>
                        <!-- HIDDEN INPUT UNTUK MENCEGAH ERROR SAAT DISABLE -->
                        <input type="hidden" name="wanita[agama]" :value="dataWarga.agama">
                        <select x-model="dataWarga.agama" :disabled="isReadonly" required
                                :class="isReadonly ? 'bg-slate-800 text-slate-400' : 'bg-slate-950 text-white'"
                                class="w-full px-3 py-2.5 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm transition-colors">
                            <option value="Islam">Islam</option>
                            <option value="Kristen">Kristen</option>
                            <option value="Katolik">Katolik</option>
                            <option value="Hindu">Hindu</option>
                            <option value="Buddha">Buddha</option>
                            <option value="Konghucu">Konghucu</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">No. HP</label>
                        <input type="text" name="wanita[no_hp]" x-model="dataWarga.no_hp" :readonly="isReadonly"
                               :class="isReadonly ? 'bg-slate-800 text-slate-400' : 'bg-slate-950 text-white'"
                               class="w-full px-3 py-2.5 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm placeholder-slate-600 transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">Email</label>
                        <input type="text" name="wanita[email]" x-model="dataWarga.email" :readonly="isReadonly"
                               :class="isReadonly ? 'bg-slate-800 text-slate-400' : 'bg-slate-950 text-white'"
                               class="w-full px-3 py-2.5 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm placeholder-slate-600 transition-colors">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">Pendidikan</label>
                        <!-- HIDDEN INPUT UNTUK PENDIDIKAN -->
                        <input type="hidden" name="wanita[pendidikan]" :value="dataWarga.pendidikan">
                        <select x-model="dataWarga.pendidikan" :disabled="isReadonly"
                                :class="isReadonly ? 'bg-slate-800 text-slate-400' : 'bg-slate-950 text-white'"
                                class="w-full px-3 py-2.5 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm transition-colors">
                            <option value="">Pilih Pendidikan</option>
                            <option value="SD">SD</option>
                            <option value="SMP">SMP</option>
                            <option value="SMA/SMK">SMA/SMK</option>
                            <option value="D3">D3</option>
                            <option value="S1">S1</option>
                            <option value="S2">S2</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">Status Perkawinan <span class="text-rose-500">*</span></label>
                        <!-- HIDDEN INPUT UNTUK STATUS KAWIN -->
                        <input type="hidden" name="wanita[status_kawin]" :value="dataWarga.status_kawin">
                        <select x-model="dataWarga.status_kawin" :disabled="isReadonly" required
                                :class="isReadonly ? 'bg-slate-800 text-slate-400' : 'bg-slate-950 text-white'"
                                class="w-full px-3 py-2.5 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm transition-colors">
                            <option value="belum_kawin">Belum Menikah (<i>Perawan</i>)</option>
                            <option value="janda">Janda (Cerai Mati / Cerai Hidup)</option>
                        </select>
                    </div>
                    
                    <!-- ALAMAT & WILAYAH -->
                    <div class="md:col-span-2 space-y-3">
                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-1">Nama Jalan</label>
                            <input type="text" x-model="alamatJalan" @input="composeAlamat()" :readonly="isReadonly" placeholder="Contoh: Jl. Merdeka No. 12 / Jl. Dr. Setiabudi / Jl. Imam Bonjol"
                                   class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none text-sm text-white placeholder-slate-600 transition-colors">
                            <p class="text-[11px] text-slate-500 mt-1">Nama jalan akan otomatis disertakan pada Alamat Lengkap.</p>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold text-slate-400 mb-1.5">Pilihan Wilayah Administratif</span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2.5 bg-slate-950/50 p-3 rounded-xl border border-slate-800">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-500 mb-1">Provinsi</label>
                                    <select x-model="provinceId" @change="updateRegencies()" :disabled="isReadonly"
                                            class="w-full px-2.5 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white disabled:bg-slate-800 disabled:text-slate-500">
                                        <option value="">Pilih Provinsi</option>
                                        <template x-for="p in provinces" :key="p.id">
                                            <option :value="p.id" x-text="p.name" :selected="String(p.id) === String(provinceId)"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-500 mb-1">Kabupaten</label>
                                    <select x-model="regencyId" @change="updateDistricts()" :disabled="!provinceId || isReadonly"
                                            class="w-full px-2.5 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white disabled:bg-slate-800 disabled:text-slate-500">
                                        <option value="">Pilih Kab/Kota</option>
                                        <template x-for="r in regencies" :key="r.id">
                                            <option :value="r.id" x-text="r.name" :selected="String(r.id) === String(regencyId)"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-500 mb-1">Kecamatan</label>
                                    <select x-model="districtId" @change="updateVillages()" :disabled="!regencyId || isReadonly"
                                            class="w-full px-2.5 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white disabled:bg-slate-800 disabled:text-slate-500">
                                        <option value="">Pilih Kecamatan</option>
                                        <template x-for="d in districts" :key="d.id">
                                            <option :value="d.id" x-text="d.name" :selected="String(d.id) === String(districtId)"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-500 mb-1">Desa/Kel.</label>
                                    <select x-model="villageId" @change="composeAlamat()" :disabled="!districtId || isReadonly"
                                            class="w-full px-2.5 py-1.5 bg-slate-900 border border-slate-700 rounded-lg text-xs text-white disabled:bg-slate-800 disabled:text-slate-500">
                                        <option value="">Pilih Desa</option>
                                        <template x-for="v in villages" :key="v.id">
                                            <option :value="v.id" x-text="v.name" :selected="String(v.id) === String(villageId)"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-300 mb-1">Alamat Lengkap</label>
                            <textarea name="wanita[alamat]" x-model="dataWarga.alamat" readonly rows="2"
                                      :class="isReadonly ? 'bg-slate-800 text-slate-400' : 'bg-slate-950 text-slate-300'"
                                      class="w-full px-3 py-2.5 border border-slate-700 rounded-lg bg-slate-950 outline-none text-sm placeholder-slate-600 transition-colors cursor-not-allowed"></textarea>
                            <p class="text-[11px] text-slate-500 mt-1">Otomatis dari Nama Jalan dan Wilayah di atas.</p>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-500 mb-1">RT</label>
                                <input type="text" name="wanita[rt]" x-model="dataWarga.rt" maxlength="3" :readonly="isReadonly"
                                       :class="isReadonly ? 'bg-slate-800 text-slate-400' : 'bg-slate-950 text-white'"
                                       class="w-full px-3 py-2.5 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm placeholder-slate-600 transition-colors"
                                       placeholder="001">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-500 mb-1">RW</label>
                                <input type="text" name="wanita[rw]" x-model="dataWarga.rw" maxlength="3" :readonly="isReadonly"
                                       :class="isReadonly ? 'bg-slate-800 text-slate-400' : 'bg-slate-950 text-white'"
                                       class="w-full px-3 py-2.5 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm placeholder-slate-600 transition-colors"
                                       placeholder="002">
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">Desa Asal <span class="text-xs text-slate-400 font-normal">(nama desa/kelurahan sesuai KTP/akte calon wanita)</span></label>
                        <input type="text" name="wanita[desa_asal]" x-model="dataWarga.desa_asal" :readonly="isReadonly"
                               :class="isReadonly ? 'bg-slate-800 text-slate-400' : 'bg-slate-950 text-white'"
                               class="w-full px-3 py-2.5 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm placeholder-slate-600 transition-colors"
                               placeholder="Contoh: Silapandu">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">Nama Ayah <span class="text-xs text-emerald-500 font-normal">(Otomatis mengisi Data Bapak)</span></label>
                        <input type="text" name="wanita[nama_ayah]" x-model="dataWarga.nama_ayah" @input="bapakNama = dataWarga.nama_ayah" :readonly="isReadonly"
                               :class="isReadonly ? 'bg-slate-800 text-slate-400' : 'bg-slate-950 text-white'"
                               class="w-full px-3 py-2.5 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm placeholder-slate-600 transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">Nama Ibu <span class="text-xs text-emerald-500 font-normal">(Otomatis mengisi Data Ibu)</span></label>
                        <input type="text" name="wanita[nama_ibu]" x-model="dataWarga.nama_ibu" @input="ibuNama = dataWarga.nama_ibu" :readonly="isReadonly"
                               :class="isReadonly ? 'bg-slate-800 text-slate-400' : 'bg-slate-950 text-white'"
                               class="w-full px-3 py-2.5 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm placeholder-slate-600 transition-colors">
                    </div>
                </div>
            </div>

            <!-- TAB 2: DATA BAPAK -->
            <div x-data="{ statusHidup: @js(old('bapak.status_hidup', $bapak?->status_hidup ?? 'hidup')) }" x-show="activeTab === 'bapak'" class="space-y-6" x-transition.opacity x-cloak>
                <div class="p-4 bg-slate-950/50 border border-slate-800 rounded-lg flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <span class="text-sm font-medium text-amber-400"><i class="fas fa-info-circle mr-2"></i> Status Keberadaan Ayah Kandung:</span>
                    <div class="flex gap-6">
                        <label class="inline-flex items-center text-sm font-medium text-slate-300 cursor-pointer">
                            <input type="radio" name="bapak[status_hidup]" x-model="statusHidup" value="hidup" class="w-4 h-4 text-emerald-500 bg-slate-900 border-slate-700 focus:ring-emerald-600 focus:ring-2">
                            <span class="ml-2">Masih Hidup</span>
                        </label>
                        <label class="inline-flex items-center text-sm font-medium text-slate-300 cursor-pointer">
                            <input type="radio" name="bapak[status_hidup]" x-model="statusHidup" value="meninggal" class="w-4 h-4 text-emerald-500 bg-slate-900 border-slate-700 focus:ring-emerald-600 focus:ring-2">
                            <span class="ml-2">Meninggal Dunia</span>
                        </label>
                        <label class="inline-flex items-center text-sm font-medium text-slate-300 cursor-pointer">
                            <input type="radio" name="bapak[status_hidup]" x-model="statusHidup" value="tidak_diketahui" class="w-4 h-4 text-emerald-500 bg-slate-900 border-slate-700 focus:ring-emerald-600 focus:ring-2">
                            <span class="ml-2">Tidak Diketahui</span>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">Nama Lengkap Bapak <span class="text-rose-500">*</span></label>
                        <input type="text" name="bapak[nama]" x-model="bapakNama" placeholder="Nama Bapak" required class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm text-white placeholder-slate-600">
                        <p x-show="statusHidup !== 'hidup'" class="text-xs text-amber-400 mt-1">Nama tetap wajib diisi.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">Nama Ayah Kandung dari Bapak</label>
                        <input type="text" name="bapak[nama_ayah_kandung]" value="{{ old('bapak.nama_ayah_kandung', $bapak?->nama_ayah_kandung ?? '') }}" placeholder="Nama ayah dari bapak" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm text-white placeholder-slate-600">
                        <p class="text-xs text-slate-500 mt-1">Dipakai untuk penulisan bin/binti pada dokumen.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">Status Perkawinan Bapak</label>
                        <select name="bapak[status_kawin_ortu]" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm text-white transition-colors">
                            <option value="">Pilih Status Perkawinan</option>
                            @foreach(['Kawin', 'Cerai Mati', 'Cerai Hidup'] as $statusKawinOrtu)
                                <option value="{{ $statusKawinOrtu }}" {{ old('bapak.status_kawin_ortu', $bapak?->status_kawin_ortu) === $statusKawinOrtu ? 'selected' : '' }}>{{ $statusKawinOrtu }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Data bapak selain nama disembunyikan bila bapak sudah meninggal --}}
                <div x-show="statusHidup === 'hidup'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">NIK Bapak</label>
                        <input type="text" name="bapak[nik]" value="{{ old('bapak.nik', $bapak?->nik ?? '') }}" :disabled="statusHidup !== 'hidup'" maxlength="16" inputmode="numeric" :class="statusHidup === 'hidup' ? 'bg-white' : 'bg-gray-100 text-gray-500 cursor-not-allowed'" class="w-full px-3 py-2.5 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm text-slate-900 placeholder-slate-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">Tempat Lahir Bapak</label>
                        <input type="text" name="bapak[tempat_lahir]" value="{{ old('bapak.tempat_lahir', $bapak?->tempat_lahir ?? '') }}" :disabled="statusHidup !== 'hidup'" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm text-white placeholder-slate-600 disabled:bg-slate-800 disabled:text-slate-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">Tanggal Lahir Bapak</label>
                        <input type="date" name="bapak[ttl_lahir]" value="{{ old('bapak.ttl_lahir', $bapak?->tgl_lahir?->format('Y-m-d') ?? '') }}" :disabled="statusHidup !== 'hidup'" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm text-white disabled:bg-slate-800 disabled:text-slate-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">Agama Bapak</label>
                        <!-- HIDDEN INPUT AGAMA BAPAK -->
                        <input type="hidden" name="bapak[agama]" :value="statusHidup === 'hidup' ? document.querySelector('#bapak_agama_select_w').value : 'Islam'">
                        <select id="bapak_agama_select_w" name="bapak[agama]" :disabled="statusHidup !== 'hidup'" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm text-white disabled:bg-slate-800 disabled:text-slate-500 transition-colors">
                            <option value="Islam" {{ old('bapak.agama', $bapak?->agama) == 'Islam' ? 'selected' : '' }}>Islam</option>
                            <option value="Kristen" {{ old('bapak.agama', $bapak?->agama) == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                            <option value="Katolik" {{ old('bapak.agama', $bapak?->agama) == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                            <option value="Hindu" {{ old('bapak.agama', $bapak?->agama) == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                            <option value="Buddha" {{ old('bapak.agama', $bapak?->agama) == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                            <option value="Konghucu" {{ old('bapak.agama', $bapak?->agama) == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">Pekerjaan Bapak</label>
                        <input type="text" name="bapak[pekerjaan]" value="{{ old('bapak.pekerjaan', $bapak?->pekerjaan ?? '') }}" :disabled="statusHidup !== 'hidup'" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm text-white placeholder-slate-600 disabled:bg-slate-800 disabled:text-slate-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">Pendidikan Bapak</label>
                        <!-- HIDDEN INPUT PENDIDIKAN BAPAK -->
                        <input type="hidden" name="bapak[pendidikan]" :value="statusHidup === 'hidup' ? document.querySelector('#bapak_pendidikan_select_w').value : ''">
                        <select id="bapak_pendidikan_select_w" name="bapak[pendidikan]" :disabled="statusHidup !== 'hidup'" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm text-white disabled:bg-slate-800 disabled:text-slate-500 transition-colors">
                            <option value="">Pilih Pendidikan</option>
                            @foreach(['SD', 'SMP', 'SMA/SMK', 'D3', 'S1', 'S2', 'S3'] as $edu)
                                <option value="{{ $edu }}" {{ old('bapak.pendidikan', $bapak?->pendidikan) === $edu ? 'selected' : '' }}>{{ $edu }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">Status Hubungan</label>
                        <!-- HIDDEN INPUT STATUS HUBUNGAN -->
                        <input type="hidden" name="bapak[status_hubungan]" :value="statusHidup === 'hidup' ? document.querySelector('#bapak_hubungan_select_w').value : 'kandung'">
                        <select id="bapak_hubungan_select_w" name="bapak[status_hubungan]" :disabled="statusHidup !== 'hidup'" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm text-white disabled:bg-slate-800 disabled:text-slate-500 transition-colors">
                            <option value="kandung" {{ old('bapak.status_hubungan', $bapak?->status_hubungan ?? 'kandung') === 'kandung' ? 'selected' : '' }}>Kandung</option>
                            <option value="sambung" {{ old('bapak.status_hubungan', $bapak?->status_hubungan) === 'sambung' ? 'selected' : '' }}>Sambung</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">No. HP Bapak</label>
                        <input type="text" name="bapak[no_hp]" value="{{ old('bapak.no_hp', $bapak?->no_hp ?? '') }}" :disabled="statusHidup !== 'hidup'" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm text-white placeholder-slate-600 disabled:bg-slate-800 disabled:text-slate-500 transition-colors">
                    </div>
                    <div class="md:col-span-2">
                        <div class="flex justify-between items-end mb-1">
                            <label class="block text-sm font-semibold text-slate-300">Alamat Bapak</label>
                            <button type="button" @click="bapakAlamat = dataWarga.alamat" :disabled="statusHidup !== 'hidup'" class="text-xs text-emerald-500 hover:text-emerald-400 disabled:opacity-50">Salin Alamat dari Calon</button>
                        </div>
                        <textarea name="bapak[alamat]" x-model="bapakAlamat" :disabled="statusHidup !== 'hidup'" rows="2" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm text-white placeholder-slate-600 disabled:bg-slate-800 disabled:text-slate-500 transition-colors"></textarea>
                    </div>
                </div>

                {{-- Data kematian bapak (untuk Form N6) --}}
                <div x-show="statusHidup === 'meninggal'" class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4 bg-amber-500/5 border border-amber-500/40 rounded-lg">
                    <div class="md:col-span-2 text-sm text-amber-400"><i class="fas fa-info-circle mr-2"></i> Data kematian ayah diperlukan untuk Form N6.</div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">Tanggal Meninggal Bapak</label>
                        <input type="date" name="bapak[tgl_meninggal]" value="{{ old('bapak.tgl_meninggal', $bapak?->tgl_meninggal?->format('Y-m-d') ?? '') }}" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">Tempat Meninggal Bapak</label>
                        <input type="text" name="bapak[tempat_meninggal]" value="{{ old('bapak.tempat_meninggal', $bapak?->tempat_meninggal ?? '') }}" placeholder="Kota/Kabupaten tempat meninggal" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm text-white placeholder-slate-600">
                    </div>
                </div>
            </div>

            <!-- TAB 3: DATA IBU -->
            <div x-data="{ statusHidup: @js(old('ibu.status_hidup', $ibu?->status_hidup ?? 'hidup')) }" x-show="activeTab === 'ibu'" class="space-y-6" x-transition.opacity x-cloak>
                <div class="p-4 bg-slate-950/50 border border-slate-800 rounded-lg flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                    <span class="text-sm font-medium text-amber-400"><i class="fas fa-info-circle mr-2"></i> Status Keberadaan Ibu Kandung:</span>
                    <div class="flex gap-6">
                        <label class="inline-flex items-center text-sm font-medium text-slate-300 cursor-pointer">
                            <input type="radio" name="ibu[status_hidup]" x-model="statusHidup" value="hidup" class="w-4 h-4 text-emerald-500 bg-slate-900 border-slate-700 focus:ring-emerald-600 focus:ring-2">
                            <span class="ml-2">Masih Hidup</span>
                        </label>
                        <label class="inline-flex items-center text-sm font-medium text-slate-300 cursor-pointer">
                            <input type="radio" name="ibu[status_hidup]" x-model="statusHidup" value="meninggal" class="w-4 h-4 text-emerald-500 bg-slate-900 border-slate-700 focus:ring-emerald-600 focus:ring-2">
                            <span class="ml-2">Meninggal Dunia</span>
                        </label>
                        <label class="inline-flex items-center text-sm font-medium text-slate-300 cursor-pointer">
                            <input type="radio" name="ibu[status_hidup]" x-model="statusHidup" value="tidak_diketahui" class="w-4 h-4 text-emerald-500 bg-slate-900 border-slate-700 focus:ring-emerald-600 focus:ring-2">
                            <span class="ml-2">Tidak Diketahui</span>
                        </label>
                    </div>
                </div>

                {{-- Status perkawinan orang tua tetap dapat diubah walau sudah meninggal --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">Status Perkawinan Ibu</label>
                        <select name="ibu[status_kawin_ortu]" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm text-white transition-colors">
                            <option value="">Pilih Status Perkawinan</option>
                            @foreach(['Kawin', 'Cerai Mati', 'Cerai Hidup'] as $statusKawinOrtu)
                                <option value="{{ $statusKawinOrtu }}" {{ old('ibu.status_kawin_ortu', $ibu?->status_kawin_ortu) === $statusKawinOrtu ? 'selected' : '' }}>{{ $statusKawinOrtu }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6" :class="statusHidup !== 'hidup' ? 'opacity-60' : ''">
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">NIK Ibu</label>
                        <input type="text" name="ibu[nik]" value="{{ old('ibu.nik', $ibu?->nik ?? '') }}" :disabled="statusHidup !== 'hidup'" maxlength="16" inputmode="numeric" :class="statusHidup === 'hidup' ? 'bg-white' : 'bg-gray-100 text-gray-500 cursor-not-allowed'" class="w-full px-3 py-2.5 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm text-slate-900 placeholder-slate-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">Nama Lengkap Ibu <span class="text-rose-500">*</span></label>
                        <input type="text" name="ibu[nama]" x-model="ibuNama" placeholder="Nama Ibu" required class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm text-white placeholder-slate-600">
                        <p x-show="statusHidup !== 'hidup'" class="text-xs text-amber-400 mt-1">Nama tetap wajib diisi.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">Nama Ayah dari Ibu (Untuk Binti)</label>
                        <input type="text" name="ibu[nama_ayah_kandung]" value="{{ old('ibu.nama_ayah_kandung', $ibu?->nama_ayah_kandung ?? '') }}" placeholder="Nama ayah dari ibu" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm text-white placeholder-slate-600">
                        <p class="text-xs text-slate-500 mt-1">Dicetak sebagai binti pada dokumen.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">Tempat Lahir Ibu</label>
                        <input type="text" name="ibu[tempat_lahir]" value="{{ old('ibu.tempat_lahir', $ibu?->tempat_lahir ?? '') }}" :disabled="statusHidup !== 'hidup'" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm text-white placeholder-slate-600 disabled:bg-slate-800 disabled:text-slate-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">Tanggal Lahir Ibu</label>
                        <input type="date" name="ibu[ttl_lahir]" value="{{ old('ibu.ttl_lahir', $ibu?->tgl_lahir?->format('Y-m-d') ?? '') }}" :disabled="statusHidup !== 'hidup'" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm text-white disabled:bg-slate-800 disabled:text-slate-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">Agama Ibu</label>
                        <!-- HIDDEN INPUT AGAMA IBU -->
                        <input type="hidden" name="ibu[agama]" :value="statusHidup === 'hidup' ? document.querySelector('#ibu_agama_select_w').value : 'Islam'">
                        <select id="ibu_agama_select_w" name="ibu[agama]" :disabled="statusHidup !== 'hidup'" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm text-white disabled:bg-slate-800 disabled:text-slate-500 transition-colors">
                            <option value="Islam" {{ old('ibu.agama', $ibu?->agama) == 'Islam' ? 'selected' : '' }}>Islam</option>
                            <option value="Kristen" {{ old('ibu.agama', $ibu?->agama) == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                            <option value="Katolik" {{ old('ibu.agama', $ibu?->agama) == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                            <option value="Hindu" {{ old('ibu.agama', $ibu?->agama) == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                            <option value="Buddha" {{ old('ibu.agama', $ibu?->agama) == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                            <option value="Konghucu" {{ old('ibu.agama', $ibu?->agama) == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">Pekerjaan Ibu</label>
                        <input type="text" name="ibu[pekerjaan]" value="{{ old('ibu.pekerjaan', $ibu?->pekerjaan ?? '') }}" :disabled="statusHidup !== 'hidup'" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm text-white placeholder-slate-600 disabled:bg-slate-800 disabled:text-slate-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">Pendidikan Ibu</label>
                        <!-- HIDDEN INPUT PENDIDIKAN IBU -->
                        <input type="hidden" name="ibu[pendidikan]" :value="statusHidup === 'hidup' ? document.querySelector('#ibu_pendidikan_select_w').value : ''">
                        <select id="ibu_pendidikan_select_w" name="ibu[pendidikan]" :disabled="statusHidup !== 'hidup'" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm text-white disabled:bg-slate-800 disabled:text-slate-500 transition-colors">
                            <option value="">Pilih Pendidikan</option>
                            @foreach(['SD', 'SMP', 'SMA/SMK', 'D3', 'S1', 'S2', 'S3'] as $edu)
                                <option value="{{ $edu }}" {{ old('ibu.pendidikan', $ibu?->pendidikan) === $edu ? 'selected' : '' }}>{{ $edu }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">Status Hubungan</label>
                        <!-- HIDDEN INPUT STATUS HUBUNGAN IBU -->
                        <input type="hidden" name="ibu[status_hubungan]" :value="statusHidup === 'hidup' ? document.querySelector('#ibu_hubungan_select_w').value : 'kandung'">
                        <select id="ibu_hubungan_select_w" name="ibu[status_hubungan]" :disabled="statusHidup !== 'hidup'" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm text-white disabled:bg-slate-800 disabled:text-slate-500 transition-colors">
                            <option value="kandung" {{ old('ibu.status_hubungan', $ibu?->status_hubungan ?? 'kandung') === 'kandung' ? 'selected' : '' }}>Kandung</option>
                            <option value="sambung" {{ old('ibu.status_hubungan', $ibu?->status_hubungan) === 'sambung' ? 'selected' : '' }}>Sambung</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-1">No. HP Ibu</label>
                        <input type="text" name="ibu[no_hp]" value="{{ old('ibu.no_hp', $ibu?->no_hp ?? '') }}" :disabled="statusHidup !== 'hidup'" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm text-white placeholder-slate-600 disabled:bg-slate-800 disabled:text-slate-500 transition-colors">
                    </div>
                    <div class="md:col-span-2">
                        <div class="flex justify-between items-end mb-1">
                            <label class="block text-sm font-semibold text-slate-300">Alamat Ibu</label>
                            <button type="button" @click="ibuAlamat = dataWarga.alamat" :disabled="statusHidup !== 'hidup'" class="text-xs text-emerald-500 hover:text-emerald-400 disabled:opacity-50">Salin Alamat dari Calon</button>
                        </div>
                        <textarea name="ibu[alamat]" x-model="ibuAlamat" :disabled="statusHidup !== 'hidup'" rows="2" class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-lg focus:ring-2 focus:ring-emerald-500 outline-none text-sm text-white placeholder-slate-600 disabled:bg-slate-800 disabled:text-slate-500 transition-colors"></textarea>
                    </div>
                </div>
            </div>

            <!-- FOOTER BUTTONS -->
            <div class="pt-6 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4">
                <a href="{{ isset($registration) ? route('registration.dashboard', $registration->id) : route('dashboard') }}" class="w-full sm:w-auto px-5 py-2.5 border border-slate-700 text-slate-300 rounded-lg font-medium text-sm hover:bg-slate-800 transition text-center">
                    Batal
                </a>

                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <button type="button" @click="if(activeTab === 'wanita') activeTab = 'bapak'; else if(activeTab === 'bapak') activeTab = 'ibu'" 
                            x-show="activeTab !== 'ibu'"
                            class="w-full sm:w-auto px-5 py-2.5 bg-slate-800 text-white rounded-lg font-medium text-sm hover:bg-slate-700 transition flex items-center justify-center gap-2">
                        Selanjutnya <i class="fas fa-arrow-right text-xs"></i>
                    </button>
                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-emerald-600 text-white rounded-lg font-semibold text-sm hover:bg-emerald-500 shadow-lg shadow-emerald-600/20 transition flex items-center justify-center gap-2">
                        <i class="fas fa-check"></i> Simpan Data Wanita
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection