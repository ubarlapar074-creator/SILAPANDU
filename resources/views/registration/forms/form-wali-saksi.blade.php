@extends('layouts.app')

@section('title', 'Formulir Wali & Saksi')

@section('content')
@php
    $bapakWanita = $registration ? $registration->parents()->where('type', 'bapak_wanita')->first() : null;
    $wali = $registration ? $registration->marriageWalis()->first() : null;
    $parties = $registration
        ? $registration->parties()->whereIn('role', ['saksi_1', 'saksi_2'])->with('warga')->get()->keyBy('role')
        : collect();

    $bapakStatusHidup = $bapakWanita?->status_hidup ?? 'hidup';
    $isAyahMeninggal = ($bapakStatusHidup === 'meninggal');
    $isAyahGhoib = ($bapakStatusHidup === 'tidak_diketahui');

    $isPengecualianInit = old('wali.is_pengecualian_wali', $wali?->is_pengecualian_wali ?? false);
    $alasanHakimInit = old('wali.alasan_wali_hakim', $wali?->alasan_wali_hakim ?? '');

    $jenisWaliInit = old('jenis_wali', ($wali?->jenis_wali === 'hakim' || $isPengecualianInit) ? 'hakim' : 'nasab');
    $hubunganInit = old('wali.hubungan', $wali?->hubungan ?? ($isAyahMeninggal ? 'Kakek Kandung' : 'Ayah Kandung'));

    $initLocked = $jenisWaliInit === 'nasab'
        && $hubunganInit === 'Ayah Kandung'
        && ! $isPengecualianInit
        && ! $isAyahMeninggal
        && ! $isAyahGhoib
        && $bapakWanita !== null
        && (string) ($bapakWanita->nama ?? '') !== '';

    $waliValues = $initLocked ? [
        'nik' => $bapakWanita->nik,
        'nama' => $bapakWanita->nama,
        'tempat_lahir' => $bapakWanita->tempat_lahir,
        'ttl_lahir' => $bapakWanita->tgl_lahir?->format('Y-m-d'),
        'pekerjaan' => $bapakWanita->pekerjaan,
        'agama' => $bapakWanita->agama,
        'pendidikan' => $bapakWanita->pendidikan,
        'no_hp' => $bapakWanita->no_hp,
        'alamat' => $bapakWanita->alamat,
    ] : [
        'nik' => old('wali.nik', $wali?->nik_wali),
        'nama' => old('wali.nama', $wali?->nama_wali ?? ($isAyahGhoib ? ($bapakWanita?->nama ?? '') : '')),
        'tempat_lahir' => old('wali.tempat_lahir', $wali?->tempat_lahir),
        'ttl_lahir' => old('wali.ttl_lahir', $wali?->tgl_lahir?->format('Y-m-d')),
        'pekerjaan' => old('wali.pekerjaan', $wali?->pekerjaan),
        'agama' => old('wali.agama', $wali?->agama),
        'pendidikan' => old('wali.pendidikan', $wali?->pendidikan),
        'no_hp' => old('wali.no_hp', $wali?->no_hp),
        'alamat' => old('wali.alamat', $wali?->alamat),
    ];

    $bapakValues = [
        'nik' => $bapakWanita?->nik,
        'nama' => $bapakWanita?->nama,
        'tempat_lahir' => $bapakWanita?->tempat_lahir,
        'ttl_lahir' => $bapakWanita?->tgl_lahir?->format('Y-m-d'),
        'pekerjaan' => $bapakWanita?->pekerjaan,
        'agama' => $bapakWanita?->agama,
        'pendidikan' => $bapakWanita?->pendidikan,
        'no_hp' => $bapakWanita?->no_hp,
        'alamat' => $bapakWanita?->alamat,
        'status_hidup' => $bapakStatusHidup,
        'tgl_meninggal' => old('wali.tgl_meninggal', $wali?->tgl_meninggal?->format('Y-m-d') ?? ($bapakWanita?->tgl_meninggal?->format('Y-m-d') ?? '')),
        'tempat_meninggal' => old('wali.tempat_meninggal', $wali?->tempat_meninggal ?? ($bapakWanita?->tempat_meninggal ?? '')),
    ];

    $saksiValues = [];
    foreach ([1, 2] as $nomor) {
        $party = $parties["saksi_{$nomor}"] ?? null;
        $wargaSaksi = $party?->warga;

        $saksiValues[$nomor] = [
            'nik' => old("saksi_{$nomor}.nik", $wargaSaksi?->nik ?? ''),
            'nama' => old("saksi_{$nomor}.nama", $party?->nama_sementara ?? ''),
            'tempat_lahir' => old("saksi_{$nomor}.tempat_lahir", $wargaSaksi?->tempat_lahir ?? ''),
            'ttl_lahir' => old("saksi_{$nomor}.ttl_lahir", $wargaSaksi?->tgl_lahir?->format('Y-m-d') ?? ''),
            'pekerjaan' => old("saksi_{$nomor}.pekerjaan", $wargaSaksi?->pekerjaan ?? ''),
            'pendidikan' => old("saksi_{$nomor}.pendidikan", $wargaSaksi?->pendidikan ?? ''),
            'status' => old("saksi_{$nomor}.status", $wargaSaksi?->status_kawin ?? ''),
            'no_hp' => old("saksi_{$nomor}.no_hp", $wargaSaksi?->no_hp ?? ''),
            'alamat' => old("saksi_{$nomor}.alamat", $wargaSaksi?->alamat ?? ''),
        ];
    }
@endphp

<div class="max-w-5xl mx-auto py-6 px-4 space-y-6">

    {{-- Tombol Kembali --}}
    <div class="flex items-center justify-between mb-4">
        <a href="{{ $registration ? route('registration.dashboard', $registration->id) : url('/dashboard') }}" class="inline-flex items-center gap-2 text-sm text-emerald-600 hover:text-emerald-700 font-medium">
            &larr; Kembali ke Dashboard Pendaftaran
        </a>
        @if($registration)
            <span class="font-mono text-xs bg-slate-100 text-slate-600 border border-slate-200 px-2.5 py-1 rounded-lg">
                {{ $registration->registration_number }}
            </span>
        @endif
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

    @if (! $registration)
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 text-sm text-gray-500">
            Pendaftaran belum dipilih. Silakan mulai pendaftaran terlebih dahulu.
        </div>
    @else
        <form action="{{ route('registration.store-wali-saksi', $registration->id) }}" method="POST">
            @csrf

            {{-- CARD WALI NIKAH --}}
            <div x-data="{
                    jenisWali: {{ json_encode($jenisWaliInit) }},
                    hubungan: {{ json_encode($hubunganInit) }},
                    bapak: {{ json_encode($bapakValues) }},
                    wali: {{ json_encode($waliValues) }},
                    isPengecualian: {{ json_encode((bool) $isPengecualianInit) }},
                    alasanHakim: {{ json_encode($alasanHakimInit) }},
                    tglMeninggal: {{ json_encode($bapakValues['tgl_meninggal']) }},
                    tempatMeninggal: {{ json_encode($bapakValues['tempat_meninggal']) }},

                    get bapakStatus() {
                        return this.bapak.status_hidup || 'hidup';
                    },
                    get isMeninggal() {
                        return this.bapakStatus === 'meninggal';
                    },
                    get isGhoib() {
                        return this.bapakStatus === 'tidak_diketahui';
                    },
                    get isAyahSambung() {
                        return this.hubungan === 'Ayah Sambung';
                    },
                    get lockedByAyah() {
                        return this.jenisWali === 'nasab' 
                            && this.hubungan === 'Ayah Kandung' 
                            && this.hasBapak 
                            && !this.isPengecualian 
                            && !this.isMeninggal 
                            && !this.isGhoib;
                    },
                    get hasBapak() {
                        return !!this.bapak.nama;
                    },

                    onTogglePengecualian() {
                        if (this.isPengecualian) {
                            this.jenisWali = 'hakim';
                            this.hubungan = 'Wali Hakim';
                            if (!this.alasanHakim) {
                                this.alasanHakim = 'Ayah berbeda agama';
                            }
                        } else {
                            this.jenisWali = 'nasab';
                            this.hubungan = this.isMeninggal ? 'Kakek Kandung' : 'Ayah Kandung';
                            this.onHubunganChange();
                        }
                    },

                    onHubunganChange() {
                        if (this.isAyahSambung) {
                            // Ayah Sambung tidak berhak menjadi wali -> kosongkan form wali
                            this.wali = { nik: '', nama: '', tempat_lahir: '', ttl_lahir: '', pekerjaan: '', agama: '', pendidikan: '', no_hp: '', alamat: '' };
                        } else if (this.lockedByAyah && this.hasBapak) {
                            this.wali = { ...this.bapak };
                        } else if (!this.lockedByAyah) {
                            this.wali = { nik: '', nama: '', tempat_lahir: '', ttl_lahir: '', pekerjaan: '', agama: '', pendidikan: '', no_hp: '', alamat: '' };
                        }
                    }
                 }"
                 x-cloak
                 class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-6">

                <div class="flex items-center justify-between border-b pb-3">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">Tahap 3: Data Wali Nikah</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Penetapan wali nikah sesuai ketentuan hukum perwalian nikah.</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold"
                          :class="jenisWali === 'hakim' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200'"
                          x-text="jenisWali === 'hakim' ? 'Wali Hakim' : 'Wali Nasab'">
                    </span>
                </div>

                {{-- Status Keberadaan Ayah dari Form Wanita --}}
                <div class="p-3.5 rounded-xl border text-xs flex items-center justify-between"
                     :class="isMeninggal ? 'bg-rose-50 border-rose-200 text-rose-800' : (isGhoib ? 'bg-amber-50 border-amber-200 text-amber-800' : 'bg-slate-50 border-slate-200 text-slate-700')">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a6 6 0 00-6 6v1a1 1 0 001 1h10a1 1 0 001-1v-1a6 6 0 00-6-6z"/></svg>
                        <span>
                            Status Ayah Calon Wanita: 
                            <strong class="uppercase" x-text="bapakStatus === 'meninggal' ? 'Meninggal Dunia' : (bapakStatus === 'tidak_diketahui' ? 'Tidak Diketahui / Ghoib' : 'Masih Hidup')"></strong>
                            <template x-if="bapak.nama">
                                <span>(Nama: <span x-text="bapak.nama"></span>)</span>
                            </template>
                        </span>
                    </div>
                </div>

                {{-- BLOK 3 (3): FORM RIWAYAT MENINGGAL (N6) APABILA AYAH BERSTATUS MENINGGAL --}}
                <div x-show="isMeninggal" class="rounded-xl border border-rose-300 bg-rose-50/70 p-4 space-y-3">
                    <div class="flex items-start gap-2.5">
                        <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <div>
                            <h4 class="text-sm font-bold text-rose-900">Riwayat Meninggal Ayah Kandung (Keperluan Cetak N6)</h4>
                            <p class="text-xs text-rose-800 mt-0.5">Karena Bapak Kandung berstatus meninggal, lengkapi data riwayat kematian untuk lampiran formulir N6.</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-rose-200">
                        <div>
                            <label class="block text-xs font-semibold text-rose-900 mb-1">Tanggal Meninggal Ayah</label>
                            <input type="date" name="wali[tgl_meninggal]" x-model="tglMeninggal"
                                   class="w-full px-3 py-2 border border-rose-300 rounded-lg text-sm bg-white focus:ring-rose-500 focus:border-rose-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-rose-900 mb-1">Tempat Meninggal Ayah</label>
                            <input type="text" name="wali[tempat_meninggal]" x-model="tempatMeninggal"
                                   placeholder="Contoh: Rumah Sakit Umum / Kota Garut"
                                   class="w-full px-3 py-2 border border-rose-300 rounded-lg text-sm bg-white focus:ring-rose-500 focus:border-rose-500">
                        </div>
                    </div>
                </div>

                {{-- BLOK 3 (5): TOGGLE PENGECUALIAN HUKUM --}}
                <div class="p-4 rounded-xl border border-indigo-200 bg-indigo-50/70 space-y-3">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" x-model="isPengecualian" @change="onTogglePengecualian()"
                               class="w-4 h-4 text-indigo-600 rounded border-gray-300 focus:ring-indigo-500">
                        <span class="text-sm font-semibold text-indigo-950">
                            Jika dalam KK sebagai Ayah Kandung tetapi tidak dapat menjadi wali
                        </span>
                    </label>
                    <input type="hidden" name="wali[is_pengecualian_wali]" :value="isPengecualian ? '1' : '0'">

                    <div x-show="isPengecualian" x-transition.opacity class="pl-7 space-y-2 pt-1 border-t border-indigo-100">
                        <label class="block text-xs font-semibold text-indigo-900">Alasan Pengecualian (Otomatis Dialihkan ke Wali Hakim):</label>
                        <select name="wali[alasan_wali_hakim]" x-model="alasanHakim"
                                class="w-full sm:w-80 px-3 py-2 text-xs border border-indigo-300 rounded-lg bg-white text-indigo-950 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="Ayah berbeda agama">Ayah berbeda agama</option>
                            <option value="Ayah menolak (Adhol)">Ayah menolak (Adhol)</option>
                            <option value="Status anak luar kawin">Status anak luar kawin</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                        <p class="text-[11px] text-indigo-800 italic">
                            Sistem secara otomatis mengalihkan jenis perwalian ke <strong>Wali Hakim</strong> dan menonaktifkan form Ayah Kandung.
                        </p>
                    </div>
                </div>

                {{-- Radio Jenis Wali --}}
                <div>
                    <span class="block text-sm font-medium text-gray-700 mb-2">Jenis Wali</span>
                    <div class="flex gap-6">
                        <label class="flex items-center gap-2 cursor-pointer" :class="isPengecualian ? 'opacity-50 cursor-not-allowed' : ''">
                            <input type="radio" name="jenis_wali" value="nasab" x-model="jenisWali" :disabled="isPengecualian"
                                   @change="onHubunganChange()" required
                                   class="text-emerald-600 focus:ring-emerald-500">
                            <span class="text-sm text-gray-800">Wali Nasab</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="jenis_wali" value="hakim" x-model="jenisWali"
                                   @change="if (hubungan !== 'Wali Hakim') hubungan = 'Wali Hakim';"
                                   class="text-emerald-600 focus:ring-emerald-500">
                            <span class="text-sm text-gray-800">Wali Hakim</span>
                        </label>
                    </div>
                </div>

                {{-- ===== BLOK WALI HAKIM: hanya info box, tanpa input ===== --}}
                <div x-show="jenisWali === 'hakim'" x-transition.opacity class="rounded-xl border border-blue-200 bg-blue-50 p-4">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-sm text-blue-800 space-y-1">
                            <p class="font-medium">Ketentuan Wali Hakim</p>
                            <p>
                                Wali Hakim menggunakan format KUA. Tidak ada data wali yang perlu diinput di sini.
                                Pastikan syarat Wali Hakim (seperti surat keterangan dari KUA) sudah dilengkapi secara fisik.
                            </p>
                            <template x-if="isPengecualian">
                                <p class="text-xs font-bold text-indigo-900 pt-1">
                                    Status: Dialihkan dari Ayah Kandung berdasarkan alasan: <span x-text="alasanHakim"></span>.
                                </p>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- ===== BLOK WALI NASAB ===== --}}
                <div x-show="jenisWali === 'nasab'" x-transition.opacity class="space-y-4">

                    {{-- BLOK 3 (1): PILIHAN HUBUNGAN SESUAI HUKUM (HAPUS NENEK) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hubungan Perwalian <span class="text-red-500">*</span></label>
                        <select name="wali[hubungan]" x-model="hubungan" @change="onHubunganChange()" required
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 text-sm bg-white">
                            @if(!$isAyahMeninggal)
                                <option value="Ayah Kandung">Ayah Kandung</option>
                            @endif
                            <option value="Ayah Sambung">Ayah Sambung</option>
                            <option value="Kakek Kandung">Kakek Kandung</option>
                            <option value="Saudara Laki-laki Kandung">Saudara Laki-laki Kandung</option>
                            <option value="Paman">Paman</option>
                            <option value="Wali Hakim">Wali Hakim</option>
                        </select>
                    </div>

                    {{-- BLOK 3 (2): KASUS AYAH SAMBUNG (WARNING & TIDAK BERHAK WALI) --}}
                    <div x-show="isAyahSambung" x-cloak class="rounded-xl border border-rose-300 bg-rose-50 p-4 flex items-start gap-3">
                        <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <div>
                            <p class="text-sm font-bold text-rose-900">Peringatan Hukum Perwalian</p>
                            <p class="text-xs text-rose-800 mt-0.5">Ayah Sambung tidak berhak menjadi wali. Silakan pilih wali lain.</p>
                        </div>
                    </div>

                    {{-- Info saat Ayah Kandung terpilih & terkunci otomatis --}}
                    <div x-show="lockedByAyah"
                         class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>Data wali otomatis diambil dari data <span class="font-medium">Bapak Kandung Calon Wanita</span> dan tidak dapat diubah.</span>
                    </div>

                    {{-- BLOK 3 (4): KASUS AYAH GHOIB (STATUS TIDAK DIKETAHUI) --}}
                    <div x-show="isGhoib" class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-800">
                        Status keberadaan Bapak Calon Wanita adalah <strong>Tidak Diketahui (Ghoib)</strong>. Cukup masukkan nama wali yang ditunjuk; field administrasi lainnya dinonaktifkan.
                    </div>

                    {{-- Input Field Wali --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4" :class="isAyahSambung ? 'opacity-40 pointer-events-none' : ''">

                        <div class="md:col-span-2" x-show="isGhoib">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Wali <span class="text-red-500">*</span></label>
                            <input type="text" name="wali[nama]" x-model="wali.nama" placeholder="Ketik nama wali"
                                   class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                        </div>

                        <div x-show="!isGhoib">
                            <label class="block text-sm font-medium text-gray-700 mb-1">NIK Wali</label>
                            <input type="text" name="wali[nik]" maxlength="16" inputmode="numeric" x-model="wali.nik" :disabled="lockedByAyah"
                                :placeholder="lockedByAyah ? 'Otomatis dari data Bapak Calon Wanita' : '16 digit NIK'"
                                :class="lockedByAyah ? 'bg-gray-100 text-gray-500 border-gray-300 cursor-not-allowed' : 'bg-white border-gray-300 focus:ring-emerald-500 focus:border-emerald-500'"
                                class="w-full px-3 py-2 rounded-lg border text-sm">
                        </div>

                        <div x-show="!isGhoib">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Wali</label>
                            <input type="text" name="wali[nama]" x-model="wali.nama" :disabled="lockedByAyah"
                                :placeholder="lockedByAyah ? 'Otomatis dari data Bapak Calon Wanita' : 'Nama wali'"
                                :class="lockedByAyah ? 'bg-gray-100 text-gray-500 border-gray-300 cursor-not-allowed' : 'bg-white border-gray-300 focus:ring-emerald-500 focus:border-emerald-500'"
                                class="w-full px-3 py-2 rounded-lg border text-sm">
                        </div>

                        <div x-show="!isGhoib">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                            <input type="text" name="wali[tempat_lahir]" x-model="wali.tempat_lahir" :disabled="lockedByAyah"
                                :placeholder="lockedByAyah ? 'Otomatis dari data Bapak Calon Wanita' : 'Kota kelahiran'"
                                :class="lockedByAyah ? 'bg-gray-100 text-gray-500 border-gray-300 cursor-not-allowed' : 'bg-white border-gray-300 focus:ring-emerald-500 focus:border-emerald-500'"
                                class="w-full px-3 py-2 rounded-lg border text-sm">
                        </div>

                        <div x-show="!isGhoib">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                            <input type="date" name="wali[ttl_lahir]" x-model="wali.ttl_lahir" :disabled="lockedByAyah"
                                :class="lockedByAyah ? 'bg-gray-100 text-gray-500 border-gray-300 cursor-not-allowed' : 'bg-white border-gray-300 focus:ring-emerald-500 focus:border-emerald-500'"
                                class="w-full px-3 py-2 rounded-lg border text-sm">
                        </div>

                        <div x-show="!isGhoib">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan</label>
                            <input type="text" name="wali[pekerjaan]" x-model="wali.pekerjaan" :disabled="lockedByAyah"
                                :placeholder="lockedByAyah ? 'Otomatis dari data Bapak Calon Wanita' : 'Pekerjaan wali'"
                                :class="lockedByAyah ? 'bg-gray-100 text-gray-500 border-gray-300 cursor-not-allowed' : 'bg-white border-gray-300 focus:ring-emerald-500 focus:border-emerald-500'"
                                class="w-full px-3 py-2 rounded-lg border text-sm">
                        </div>

                        <div x-show="!isGhoib">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Agama</label>
                            <select name="wali[agama]" x-model="wali.agama" :disabled="lockedByAyah"
                                :class="lockedByAyah ? 'bg-gray-100 text-gray-500 border-gray-300 cursor-not-allowed' : 'bg-white border-gray-300 focus:ring-emerald-500 focus:border-emerald-500'"
                                class="w-full px-3 py-2 rounded-lg border text-sm">
                                <option value="">Pilih Agama</option>
                                <option>Islam</option>
                                <option>Kristen</option>
                                <option>Katolik</option>
                                <option>Hindu</option>
                                <option>Buddha</option>
                                <option>Konghucu</option>
                            </select>
                        </div>

                        <div x-show="!isGhoib">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pendidikan</label>
                            <select name="wali[pendidikan]" x-model="wali.pendidikan" :disabled="lockedByAyah"
                                :class="lockedByAyah ? 'bg-gray-100 text-gray-500 border-gray-300 cursor-not-allowed' : 'bg-white border-gray-300 focus:ring-emerald-500 focus:border-emerald-500'"
                                class="w-full px-3 py-2 rounded-lg border text-sm">
                                <option value="">Pilih Pendidikan</option>
                                <option>SD</option>
                                <option>SMP</option>
                                <option>SMA / SMK</option>
                                <option>D1</option>
                                <option>D2</option>
                                <option>D3</option>
                                <option>Sarjana (S1)</option>
                                <option>S2</option>
                                <option>S3</option>
                            </select>
                        </div>

                        <div x-show="!isGhoib">
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                            <input type="text" name="wali[no_hp]" inputmode="numeric" x-model="wali.no_hp" :disabled="lockedByAyah"
                                :placeholder="lockedByAyah ? 'Otomatis dari data Bapak Calon Wanita' : '08xxxxxxxxxx'"
                                :class="lockedByAyah ? 'bg-gray-100 text-gray-500 border-gray-300 cursor-not-allowed' : 'bg-white border-gray-300 focus:ring-emerald-500 focus:border-emerald-500'"
                                class="w-full px-3 py-2 rounded-lg border text-sm">
                        </div>

                        <div class="md:col-span-2" x-show="!isGhoib">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                            <textarea name="wali[alamat]" rows="2" x-model="wali.alamat" :disabled="lockedByAyah"
                                :placeholder="lockedByAyah ? 'Otomatis dari data Bapak Calon Wanita' : 'Alamat wali'"
                                :class="lockedByAyah ? 'bg-gray-100 text-gray-500 border-gray-300 cursor-not-allowed' : 'bg-white border-gray-300 focus:ring-emerald-500 focus:border-emerald-500'"
                                class="w-full px-3 py-2 rounded-lg border text-sm"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD SAKSI 1 & 2 --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                @foreach ([1, 2] as $nomor)
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6"
                     x-data="{
                        registrationId: {{ $registration->id }},
                        nik: {{ json_encode($saksiValues[$nomor]['nik']) }},
                        nama: {{ json_encode($saksiValues[$nomor]['nama']) }},
                        tempatLahir: {{ json_encode($saksiValues[$nomor]['tempat_lahir']) }},
                        ttlLahir: {{ json_encode($saksiValues[$nomor]['ttl_lahir']) }},
                        pekerjaan: {{ json_encode($saksiValues[$nomor]['pekerjaan']) }},
                        pendidikan: {{ json_encode($saksiValues[$nomor]['pendidikan']) }},
                        statusKawin: {{ json_encode($saksiValues[$nomor]['status']) }},
                        noHp: {{ json_encode($saksiValues[$nomor]['no_hp']) }},
                        alamat: {{ json_encode($saksiValues[$nomor]['alamat']) }},
                        statusNik: 'idle',
                        loading: false,
                        pesanError: '',
                        isRegisteredElsewhere: false,
                        tempFoundData: null,

                        get showPromptAutofill() {
                            return this.tempFoundData !== null;
                        },

                        async checkNik() {
                            this.nik = this.nik.replace(/[^0-9]/g, '');
                            this.statusNik = 'idle';
                            this.pesanError = '';
                            this.isRegisteredElsewhere = false;
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
                                    this.isRegisteredElsewhere = result.is_married_before;
                                    this.tempFoundData = result.data;
                                } else if (result.status === 'not_found') {
                                    this.statusNik = 'not_found';
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

                        normalizePendidikan(value) {
                            const map = { 'SMA/SMK': 'SMA / SMK', 'S1': 'Sarjana (S1)' };
                            return map[value] || value || '';
                        },

                        gunakanData() {
                            if (!this.tempFoundData) return;
                            const data = this.tempFoundData;
                            this.nama = data.nama_lengkap || '';
                            this.tempatLahir = data.tempat_lahir || '';
                            this.ttlLahir = data.tgl_lahir || '';
                            this.pekerjaan = data.pekerjaan || '';
                            this.pendidikan = this.normalizePendidikan(data.pendidikan);
                            this.statusKawin = data.status_kawin || '';
                            this.noHp = data.no_hp || '';
                            this.alamat = data.alamat || '';
                            this.tempFoundData = null;
                        },

                        isiManual() {
                            this.tempFoundData = null;
                        }
                     }">
                    <h2 class="text-lg font-semibold text-gray-800 border-b pb-2 mb-1">Data Saksi {{ $nomor }}</h2>
                    <p class="text-xs text-gray-400 mb-4">(Dapat diisi menyusul)</p>

                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">NIK</label>
                            <div class="relative">
                                <input type="text" name="saksi_{{ $nomor }}[nik]" inputmode="numeric" maxlength="16"
                                    x-model="nik" @input.debounce.1500ms="checkNik()" placeholder="16 digit NIK"
                                    class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                                <div x-show="loading" x-cloak class="absolute right-3 top-2 text-emerald-600">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </div>
                            </div>
                            <p x-show="pesanError" x-text="pesanError" x-cloak class="text-xs text-rose-600 mt-1"></p>
                            <p x-show="statusNik === 'not_found'" x-cloak class="text-xs text-blue-600 mt-1 font-medium">
                                Warga baru. Silakan isi data saksi secara manual.
                            </p>

                            {{-- Dialog konfirmasi autofill dari data warga --}}
                            <div x-show="showPromptAutofill" x-cloak class="mt-2 p-3 rounded-lg border border-emerald-200 bg-emerald-50 space-y-2">
                                <p class="text-xs font-semibold text-emerald-800">
                                    Data ditemukan (Nama: <span class="font-extrabold" x-text="tempFoundData ? tempFoundData.nama_lengkap : ''"></span>). Gunakan data ini?
                                </p>
                                <div class="flex items-center gap-2">
                                    <button type="button" @click="gunakanData()"
                                            class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg transition">
                                        Gunakan Data
                                    </button>
                                    <button type="button" @click="isiManual()"
                                            class="px-3 py-1.5 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 text-xs font-semibold rounded-lg transition">
                                        Isi Manual
                                    </button>
                                </div>
                            </div>

                            <div x-show="isRegisteredElsewhere" x-cloak
                                 class="mt-2 px-3 py-2 rounded-lg border border-amber-200 bg-amber-50 text-xs text-amber-800">
                                NIK ini sudah tercatat sebagai pihak pada pendaftaran lain. Pastikan bukan orang yang sama dengan pihak di pendaftaran tersebut.
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                            <input type="text" name="saksi_{{ $nomor }}[nama]" x-model="nama"
                                placeholder="Nama saksi"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                            <input type="text" name="saksi_{{ $nomor }}[tempat_lahir]" x-model="tempatLahir"
                                placeholder="Kota kelahiran"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                            <input type="date" name="saksi_{{ $nomor }}[ttl_lahir]" x-model="ttlLahir"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan</label>
                            <input type="text" name="saksi_{{ $nomor }}[pekerjaan]" x-model="pekerjaan"
                                placeholder="Pekerjaan saksi"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pendidikan</label>
                            <select name="saksi_{{ $nomor }}[pendidikan]" x-model="pendidikan"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                                <option value="">Pilih Pendidikan</option>
                                @foreach (['SD', 'SMP', 'SMA / SMK', 'D1', 'D2', 'D3', 'Sarjana (S1)', 'S2', 'S3'] as $opsi)
                                    <option>{{ $opsi }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status Pernikahan</label>
                            <input type="text" name="saksi_{{ $nomor }}[status]" x-model="statusKawin"
                                placeholder="Belum Kawin"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. HP</label>
                            <input type="text" name="saksi_{{ $nomor }}[no_hp]" inputmode="numeric" x-model="noHp"
                                placeholder="08xxxxxxxxxx"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                            <textarea name="saksi_{{ $nomor }}[alamat]" rows="2" placeholder="Alamat saksi" x-model="alamat"
                                class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 text-sm"></textarea>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- TOMBOL AKSI --}}
            <div class="flex justify-between pt-6">
                <a href="{{ route('registration.dashboard', $registration->id) }}" class="px-5 py-2.5 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-lg bg-emerald-600 text-white text-sm font-medium hover:bg-emerald-700 transition">
                    Simpan Data Wali &amp; Saksi
                </button>
            </div>
        </form>
    @endif

</div>
@endsection
