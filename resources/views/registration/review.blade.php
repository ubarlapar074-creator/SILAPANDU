@extends('layouts.app')

@section('title', 'Review Formulir Pendaftaran Nikah')

@section('content')
<div class="max-w-5xl mx-auto py-6 px-4 space-y-6">

    {{-- Breadcrumb / Header navigasi --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('registration.dashboard', $registration->id) }}" class="inline-flex items-center gap-2 text-sm text-emerald-600 hover:text-emerald-700 font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Dashboard Pendaftaran
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('documents.index', ['registration_id' => $registration->id]) }}" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold flex items-center gap-1.5 shadow-sm shadow-emerald-600/20 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Buka Pusat Cetak Dokumen
            </a>
            <span class="font-mono text-xs bg-slate-100 text-slate-700 border border-slate-200 px-3 py-1 rounded-lg font-semibold">
                {{ $registration->nomor_surat ?? $registration->nomor_surat_sementara ?? 'Belum ada nomor surat' }}
            </span>
        </div>
    </div>

    {{-- Banner Instruksi Mencegah Typo --}}
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-start gap-3">
        <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <div class="text-xs text-amber-800 space-y-1">
            <p class="font-bold text-amber-900 text-sm">Halaman Review Pendaftaran (Pemeriksaan Ulang)</p>
            <p>Silakan teliti seluruh data mempelai, orang tua, wali, saksi, dan jadwal akad nikah. Jika ada kesalahan ketik (typo), klik tombol <strong>Edit</strong> di sebelah kanan setiap bagian data sebelum Anda menekan <strong>Konfirmasi &amp; Kunci Data</strong>.</p>
        </div>
    </div>

    {{-- Container Kartu Review (Format Lembar Formulir / Struk Cetak) --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden divide-y divide-gray-200">
        
        {{-- Header Dokumen --}}
        <div class="p-6 bg-slate-50 border-b border-gray-200 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">Kementerian Agama RI &middot; KUA Kecamatan</span>
                <h1 class="text-xl font-extrabold text-gray-900 mt-0.5">Lembar Verifikasi Pendaftaran Nikah</h1>
                <p class="text-xs text-gray-500 mt-1">Layanan: <strong class="text-gray-800">{{ $registration->service_label }}</strong> &middot; Status: <span class="uppercase font-semibold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded text-[11px]">{{ $registration->status }}</span></p>
            </div>
            <div class="text-right sm:text-right">
                <p class="text-xs text-gray-500 font-medium">Tanggal Dibuat</p>
                <p class="text-xs font-mono font-bold text-gray-800">{{ $registration->created_at?->format('d/m/Y H:i') }} WIB</p>
            </div>
        </div>

        {{-- BAGIAN 1: CALON PRIA & ORANG TUA --}}
        <div class="p-6 space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold flex items-center justify-center">1</span>
                    <h2 class="text-base font-bold text-gray-800">Calon Pengantin Laki-laki &amp; Orang Tua</h2>
                </div>
                <a href="{{ route('registration.form.pria', $registration->id) }}"
                   class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    Edit Data Pria
                </a>
            </div>

            @if($priaParty || $pria)
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                    <div>
                        <p class="text-gray-500 font-medium">Nama Lengkap</p>
                        <p class="font-bold text-gray-900 text-sm mt-0.5">{{ $pria?->nama_lengkap ?? $priaParty?->nama_sementara ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 font-medium">NIK</p>
                        <p class="font-mono font-semibold text-gray-800 mt-0.5">{{ $pria?->nik ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 font-medium">Tempat, Tanggal Lahir</p>
                        <p class="text-gray-800 mt-0.5">{{ $pria?->tempat_lahir ?? '-' }}, {{ $pria?->tgl_lahir?->format('d/m/Y') ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 font-medium">Pekerjaan</p>
                        <p class="text-gray-800 mt-0.5">{{ $pria?->pekerjaan ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 font-medium">Pendidikan</p>
                        <p class="text-gray-800 mt-0.5">{{ $pria?->pendidikan ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 font-medium">Status Pernikahan</p>
                        <p class="text-gray-800 mt-0.5 uppercase">{{ str_replace('_', ' ', $pria?->status_kawin ?? 'belum_kawin') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 font-medium">No. HP / Telepon</p>
                        <p class="text-gray-800 mt-0.5">{{ $pria?->no_hp ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 font-medium">Email</p>
                        <p class="text-gray-800 mt-0.5">{{ $pria?->email ?? '-' }}</p>
                    </div>
                    <div class="sm:col-span-2 md:col-span-4">
                        <p class="text-gray-500 font-medium">Alamat Lengkap</p>
                        <p class="text-gray-800 mt-0.5">{{ $pria?->alamat ?? '-' }}</p>
                    </div>
                </div>

                {{-- Data Orang Tua Calon Pria --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-3 mt-3 border-t border-gray-100 bg-gray-50/70 p-3 rounded-xl text-xs">
                    <div>
                        <span class="font-bold text-gray-700 block mb-1">Data Bapak Calon Pria</span>
                        <p><span class="text-gray-500">Nama:</span> <strong class="text-gray-900">{{ $bapakPria?->nama ?? '-' }}</strong></p>
                        <p><span class="text-gray-500">Status Hidup:</span> <span class="font-semibold uppercase text-xs {{ ($bapakPria?->status_hidup ?? 'hidup') === 'hidup' ? 'text-emerald-700' : 'text-slate-500' }}">{{ $bapakPria?->status_hidup ?? '-' }}</span></p>
                        <p><span class="text-gray-500">Status Perkawinan:</span> {{ $bapakPria?->status_kawin_ortu ?? '-' }}</p>
                        <p><span class="text-gray-500">NIK:</span> {{ $bapakPria?->nik ?? '-' }}</p>
                        <p><span class="text-gray-500">Pekerjaan:</span> {{ $bapakPria?->pekerjaan ?? '-' }}</p>
                        <p><span class="text-gray-500">Alamat:</span> {{ $bapakPria?->alamat ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="font-bold text-gray-700 block mb-1">Data Ibu Calon Pria</span>
                        <p><span class="text-gray-500">Nama:</span> <strong class="text-gray-900">{{ $ibuPria?->nama ?? '-' }}</strong></p>
                        <p><span class="text-gray-500">Status Hidup:</span> <span class="font-semibold uppercase text-xs {{ ($ibuPria?->status_hidup ?? 'hidup') === 'hidup' ? 'text-emerald-700' : 'text-slate-500' }}">{{ $ibuPria?->status_hidup ?? '-' }}</span></p>
                        <p><span class="text-gray-500">Status Perkawinan:</span> {{ $ibuPria?->status_kawin_ortu ?? '-' }}</p>
                        <p><span class="text-gray-500">NIK:</span> {{ $ibuPria?->nik ?? '-' }}</p>
                        <p><span class="text-gray-500">Pekerjaan:</span> {{ $ibuPria?->pekerjaan ?? '-' }}</p>
                        <p><span class="text-gray-500">Alamat:</span> {{ $ibuPria?->alamat ?? '-' }}</p>
                    </div>
                </div>
            @else
                <p class="text-xs text-gray-400 italic">Data calon pria belum diisi.</p>
            @endif
        </div>

        {{-- BAGIAN 2: CALON WANITA & ORANG TUA --}}
        <div class="p-6 space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-amber-100 text-amber-700 text-xs font-bold flex items-center justify-center">2</span>
                    <h2 class="text-base font-bold text-gray-800">Calon Pengantin Wanita &amp; Orang Tua</h2>
                </div>
                <a href="{{ route('registration.form.wanita', $registration->id) }}"
                   class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    Edit Data Wanita
                </a>
            </div>

            @if($wanitaParty || $wanita)
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                    <div>
                        <p class="text-gray-500 font-medium">Nama Lengkap</p>
                        <p class="font-bold text-gray-900 text-sm mt-0.5">{{ $wanita?->nama_lengkap ?? $wanitaParty?->nama_sementara ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 font-medium">NIK</p>
                        <p class="font-mono font-semibold text-gray-800 mt-0.5">{{ $wanita?->nik ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 font-medium">Tempat, Tanggal Lahir</p>
                        <p class="text-gray-800 mt-0.5">{{ $wanita?->tempat_lahir ?? '-' }}, {{ $wanita?->tgl_lahir?->format('d/m/Y') ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 font-medium">Pekerjaan</p>
                        <p class="text-gray-800 mt-0.5">{{ $wanita?->pekerjaan ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 font-medium">Pendidikan</p>
                        <p class="text-gray-800 mt-0.5">{{ $wanita?->pendidikan ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 font-medium">Status Pernikahan</p>
                        <p class="text-gray-800 mt-0.5 uppercase">{{ str_replace('_', ' ', $wanita?->status_kawin ?? 'belum_kawin') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 font-medium">No. HP / Telepon</p>
                        <p class="text-gray-800 mt-0.5">{{ $wanita?->no_hp ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 font-medium">Email</p>
                        <p class="text-gray-800 mt-0.5">{{ $wanita?->email ?? '-' }}</p>
                    </div>
                    <div class="sm:col-span-2 md:col-span-4">
                        <p class="text-gray-500 font-medium">Alamat Lengkap</p>
                        <p class="text-gray-800 mt-0.5">{{ $wanita?->alamat ?? '-' }}</p>
                    </div>
                </div>

                {{-- Data Orang Tua Calon Wanita --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-3 mt-3 border-t border-gray-100 bg-gray-50/70 p-3 rounded-xl text-xs">
                    <div>
                        <span class="font-bold text-gray-700 block mb-1">Data Bapak Calon Wanita</span>
                        <p><span class="text-gray-500">Nama:</span> <strong class="text-gray-900">{{ $bapakWanita?->nama ?? '-' }}</strong></p>
                        <p><span class="text-gray-500">Status Hidup:</span> <span class="font-semibold uppercase text-xs {{ ($bapakWanita?->status_hidup ?? 'hidup') === 'hidup' ? 'text-emerald-700' : 'text-slate-500' }}">{{ $bapakWanita?->status_hidup ?? '-' }}</span></p>
                        <p><span class="text-gray-500">Status Perkawinan:</span> {{ $bapakWanita?->status_kawin_ortu ?? '-' }}</p>
                        @if($butuhN6 || $bapakWanita?->tgl_meninggal || $wali?->tgl_meninggal)
                            @php
                                $tglMeninggal = $bapakWanita?->tgl_meninggal ?? $wali?->tgl_meninggal;
                                $tempatMeninggal = $bapakWanita?->tempat_meninggal ?? $wali?->tempat_meninggal;
                            @endphp
                            <div class="mt-2 p-2.5 rounded-lg border {{ $butuhN6 ? 'bg-rose-50 border-rose-200' : 'bg-gray-50 border-gray-200' }}">
                                <p class="font-bold {{ $butuhN6 ? 'text-rose-700' : 'text-gray-600' }}">Data Kematian Ayah (Form N6)</p>
                                <p><span class="text-gray-500">Tanggal Meninggal:</span> {{ $tglMeninggal?->format('d/m/Y') ?? '-' }}</p>
                                <p><span class="text-gray-500">Tempat Meninggal:</span> {{ $tempatMeninggal ?: '-' }}</p>
                                <p><span class="text-gray-500">Bin:</span> {{ $bapakWanita?->nama_ayah_kandung ?: '-' }}</p>
                                @if($butuhN6 && (! $tglMeninggal || ! $tempatMeninggal))
                                    <p class="text-rose-600 font-semibold mt-1">Data kematian belum lengkap. Lengkapi tanggal dan tempat meninggal pada Form Wanita agar N6 dapat dicetak.</p>
                                @endif
                            </div>
                        @endif
                        <p><span class="text-gray-500">NIK:</span> {{ $bapakWanita?->nik ?? '-' }}</p>
                        <p><span class="text-gray-500">Pekerjaan:</span> {{ $bapakWanita?->pekerjaan ?? '-' }}</p>
                        <p><span class="text-gray-500">Alamat:</span> {{ $bapakWanita?->alamat ?? '-' }}</p>
                    </div>
                    <div>
                        <span class="font-bold text-gray-700 block mb-1">Data Ibu Calon Wanita</span>
                        <p><span class="text-gray-500">Nama:</span> <strong class="text-gray-900">{{ $ibuWanita?->nama ?? '-' }}</strong></p>
                        <p><span class="text-gray-500">Status Hidup:</span> <span class="font-semibold uppercase text-xs {{ ($ibuWanita?->status_hidup ?? 'hidup') === 'hidup' ? 'text-emerald-700' : 'text-slate-500' }}">{{ $ibuWanita?->status_hidup ?? '-' }}</span></p>
                        <p><span class="text-gray-500">Status Perkawinan:</span> {{ $ibuWanita?->status_kawin_ortu ?? '-' }}</p>
                        <p><span class="text-gray-500">NIK:</span> {{ $ibuWanita?->nik ?? '-' }}</p>
                        <p><span class="text-gray-500">Pekerjaan:</span> {{ $ibuWanita?->pekerjaan ?? '-' }}</p>
                        <p><span class="text-gray-500">Alamat:</span> {{ $ibuWanita?->alamat ?? '-' }}</p>
                    </div>
                </div>
            @else
                <p class="text-xs text-gray-400 italic">Data calon wanita belum diisi.</p>
            @endif
        </div>

        {{-- BAGIAN 3: WALI NIKAH --}}
        <div class="p-6 space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold flex items-center justify-center">3</span>
                    <h2 class="text-base font-bold text-gray-800">Data Wali Nikah</h2>
                </div>
                <a href="{{ route('registration.form.wali-saksi', $registration->id) }}"
                   class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    Edit Data Wali
                </a>
            </div>

            @if($wali)
                @if($wali->is_pengecualian_wali || $wali->alasan_wali_hakim)
                    <div class="p-3 bg-indigo-50 border border-indigo-200 rounded-xl text-xs text-indigo-900 flex items-center gap-2">
                        <svg class="w-4 h-4 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>
                            <strong>Pengecualian Hukum:</strong> Perwalian dialihkan ke Wali Hakim dengan alasan: <span class="font-bold underline">{{ $wali->alasan_wali_hakim }}</span>.
                        </span>
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div>
                        <p class="text-gray-500 font-medium">Jenis / Peran Wali</p>
                        <p class="font-bold text-gray-900 mt-0.5">
                            {{ $wali->jenis_wali === 'hakim' ? 'Wali Hakim' : 'Wali Nasab' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500 font-medium">Nama Wali</p>
                        <p class="font-bold text-gray-900 mt-0.5">
                            {{ $wali->nama_wali ?? ($wali->jenis_wali === 'hakim' ? 'Kepala KUA / Penghulu (Wali Hakim)' : '-') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500 font-medium">NIK Wali</p>
                        <p class="font-mono text-gray-800 mt-0.5">{{ $wali->nik_wali ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 font-medium">Hubungan dengan Catin Wanita</p>
                        <p class="text-gray-800 mt-0.5">{{ $wali->hubungan ?? ($wali->jenis_wali === 'hakim' ? 'Wali Hakim' : '-') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 font-medium">Tempat, Tanggal Lahir</p>
                        <p class="text-gray-800 mt-0.5">{{ $wali->tempat_lahir ?? '-' }}, {{ $wali->tgl_lahir ? date('d/m/Y', strtotime($wali->tgl_lahir)) : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 font-medium">Alamat Wali</p>
                        <p class="text-gray-800 mt-0.5">{{ $wali->alamat ?? '-' }}</p>
                    </div>
                </div>

                @if($wali->tgl_meninggal || $wali->tempat_meninggal)
                    <div class="pt-2 border-t border-gray-100 text-xs text-rose-800 bg-rose-50/60 p-3 rounded-lg">
                        <strong class="text-rose-900">Catatan Riwayat Kematian Ayah (N6):</strong> Meninggal pada tanggal {{ $wali->tgl_meninggal?->format('d/m/Y') ?? '-' }} di {{ $wali->tempat_meninggal ?? '-' }}.
                    </div>
                @endif
            @else
                <p class="text-xs text-gray-400 italic">Data wali nikah belum diisi atau menggunakan wali hakim resmi KUA.</p>
            @endif
        </div>

        {{-- BAGIAN 4: SAKSI NIKAH --}}
        <div class="p-6 space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 text-xs font-bold flex items-center justify-center">4</span>
                    <h2 class="text-base font-bold text-gray-800">Data Saksi-Saksi Nikah</h2>
                </div>
                <a href="{{ route('registration.form.wali-saksi', $registration->id) }}"
                   class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    Edit Data Saksi
                </a>
            </div>

            @if($saksi && $saksi->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    @foreach($saksi as $index => $s)
                        <div class="p-3 bg-gray-50 rounded-xl space-y-1">
                            <span class="font-bold text-gray-800 block">Saksi {{ $index + 1 }}</span>
                            <p><span class="text-gray-500">Nama:</span> <strong class="text-gray-900">{{ $s->warga?->nama_lengkap ?? $s->nama_sementara ?? '-' }}</strong></p>
                            <p><span class="text-gray-500">NIK:</span> {{ $s->warga?->nik ?? '-' }}</p>
                            <p><span class="text-gray-500">Pekerjaan:</span> {{ $s->warga?->pekerjaan ?? '-' }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-gray-400 italic">Data 2 orang saksi belum diisi.</p>
            @endif
        </div>

        {{-- BAGIAN 5: JADWAL AKAD & MAHAR (Hanya untuk Nikah Kantor / Bedol) --}}
        @if($registration->service_type !== 'andon')
        <div class="p-6 space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-purple-100 text-purple-700 text-xs font-bold flex items-center justify-center">5</span>
                    <h2 class="text-base font-bold text-gray-800">Jadwal Pelaksanaan Akad &amp; Mahar</h2>
                </div>
                <a href="{{ route('registration.form.akad', $registration->id) }}"
                   class="inline-flex items-center gap-1 text-xs font-semibold px-3 py-1.5 rounded-lg bg-purple-50 text-purple-700 hover:bg-purple-100 border border-purple-200 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                    Edit Jadwal &amp; Mahar
                </a>
            </div>

            @if($registration->tanggal_akad)
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div>
                        <p class="text-gray-500 font-medium">Tanggal Akad</p>
                        <p class="font-bold text-gray-900 text-sm mt-0.5">{{ $registration->tanggal_akad->translatedFormat('l, d F Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 font-medium">Jam Akad</p>
                        <p class="font-mono font-bold text-gray-800 text-sm mt-0.5">{{ $registration->jam_akad ? substr($registration->jam_akad, 0, 5) : '-' }} WIB</p>
                    </div>
                    <div>
                        <p class="text-gray-500 font-medium">Rincian Mahar</p>
                        <p class="font-semibold text-emerald-800 mt-0.5">{{ $registration->marriage_dowry_display ?? '-' }}</p>
                    </div>
                    <div class="sm:col-span-3">
                        <p class="text-gray-500 font-medium">Lokasi &amp; Wilayah Akad</p>
                        <p class="text-gray-800 mt-0.5">
                            {{ $registration->marriage_location_address ?? 'Lokasi KUA' }}
                            @if(!empty($wilayah))
                                &mdash; Desa: {{ $wilayah['desa'] ?? '-' }}, Kec. {{ $wilayah['kecamatan'] ?? '-' }}, {{ $wilayah['kabupaten'] ?? '-' }}, Prov. {{ $wilayah['provinsi'] ?? '-' }}
                            @endif
                        </p>
                    </div>
                </div>
            @else
                <p class="text-xs text-gray-400 italic">Jadwal akad dan rincian mahar belum diisi.</p>
            @endif
        </div>
        @endif

        {{-- ACTION FOOTER: SIMPAN PENDAFTARAN --}}
        <div class="p-6 bg-slate-50 flex flex-col sm:flex-row items-center justify-between gap-4">
            <a href="{{ route('registration.dashboard', $registration->id) }}"
               class="px-5 py-2.5 rounded-xl border border-gray-300 text-sm font-semibold text-gray-700 bg-white hover:bg-gray-100 transition shadow-sm">
                &larr; Kembali ke Dashboard
            </a>

            <div class="flex flex-col sm:flex-row items-center gap-3">
                @error('n6')
                    <p class="text-xs font-semibold text-rose-700 bg-rose-50 border border-rose-200 rounded-lg px-3 py-2">{{ $message }}</p>
                @enderror

                <a href="{{ route('registration.dashboard', $registration->id) }}"
                   class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-900 font-semibold text-sm shadow-sm transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Data
                </a>

                <form action="{{ route('registration.finalize', $registration->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin seluruh data pendaftaran sudah benar dan ingin mengunci data pendaftaran ini?');">
                    @csrf
                    <button type="submit" {{ in_array($registration->status, ['valid', 'selesai']) || $n6BelumLengkap ? 'disabled' : '' }}
                            title="{{ $n6BelumLengkap ? 'Lengkapi tanggal & tempat meninggal ayah calon wanita untuk mencetak N6.' : '' }}"
                            class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 disabled:opacity-60 disabled:cursor-not-allowed text-white font-bold text-sm shadow-md shadow-emerald-600/20 transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        {{ in_array($registration->status, ['valid', 'selesai']) ? 'Data Terkunci' : ($n6BelumLengkap ? 'Lengkapi Data N6' : 'Konfirmasi & Kunci Data') }}
                    </button>
                    @if($n6BelumLengkap)
                        <p class="text-xs text-rose-600 font-semibold">Finalisasi diblokir: lengkapi tanggal &amp; tempat meninggal ayah calon wanita pada Form Wanita.</p>
                    @endif
                </form>
            </div>
        </div>

    </div>

</div>
@endsection
