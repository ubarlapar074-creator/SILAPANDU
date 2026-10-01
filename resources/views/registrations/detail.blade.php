@extends('layouts.app')

@section('content')
@php
    $n6Exists = $registration->n6Document !== null;
    $n6Checked = (bool) ($registration->registrationDocumentChecklist?->doc_n6);
@endphp

<div
    x-data="{ n6Dialog: null }"
    @keydown.escape.window="n6Dialog = null"
    class="relative w-full py-2"
>
    
    <!-- HEADER & ACTION BAR -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900">Detail Pendaftaran Nikah</h1>
                <span class="px-3 py-1 text-xs font-semibold rounded-full 
                    {{ $registration->status === 'DRAFT' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                    {{ $registration->status }}
                </span>
            </div>
            <p class="text-sm text-gray-500 mt-1">No. Pendaftaran: <span class="font-medium text-gray-700">{{ $registration->registration_number }}</span></p>
        </div>

        <!-- Tombol Aksi Utama (Edit, Hapus, Cetak, Kembali) -->
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('dashboard') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 shadow-sm">
                &larr; Kembali
            </a>

            @if($registration->status === 'DRAFT')
                <a href="{{ route('registrations.edit', $registration->id) }}" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-sm">
                    Ubah Data (Edit)
                </a>

                <form action="{{ route('registrations.destroy', $registration->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pendaftaran ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 shadow-sm">
                        Hapus
                    </button>
                </form>
            @endif
        </div>
    </div>

    @php $hasAnyNumber = $registration->documents->isNotEmpty(); @endphp
    <!-- PANEL CETAK BERKAS (N1 - N6) -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 rounded-xl p-5 mb-8 shadow-sm">
        <div class="mb-3 flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-sm font-bold text-blue-900 uppercase tracking-wider">Pusat Cetak Dokumen Administratif</h2>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('registrations.documents.numbers.edit', $registration->id) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-amber-200 bg-white px-3 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-50">
                    ⚙️ Atur Nomor Surat
                </a>
                @if(!$hasAnyNumber)
                    <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-medium text-amber-800">Belum ada nomor — atur sebelum cetak</span>
                @endif
                <a href="{{ route('settings.document-numbers.edit') }}" class="text-xs font-medium text-slate-500 hover:text-emerald-700">Pengaturan → Penomoran Surat</a>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <!-- Contoh tombol cetak, arahkan ke route cetak masing-masing dokumen -->
            <a href="{{ route('registrations.documents.data', $registration->id) }}" class="px-3 py-2 text-xs font-medium bg-white text-blue-700 border border-blue-200 rounded-lg hover:bg-blue-600 hover:text-white transition shadow-xs">
                🖨️ Cetak Data Pendaftaran
            </a>
            <a href="{{ route('registrations.documents.n1.print', $registration->id) }}" class="px-3 py-2 text-xs font-medium bg-white text-blue-700 border border-blue-200 rounded-lg hover:bg-blue-600 hover:text-white transition shadow-xs">
                🖨️ Cetak N1 (Pengantar Nikah)
            </a>
            <a href="{{ route('registrations.documents.n2.print', $registration->id) }}" class="px-3 py-2 text-xs font-medium bg-white text-blue-700 border border-blue-200 rounded-lg hover:bg-blue-600 hover:text-white transition shadow-xs">
                🖨️ Cetak N2 (Kehendak)
            </a>
            <a href="{{ route('registrations.documents.n4.print', $registration->id) }}" class="px-3 py-2 text-xs font-medium bg-white text-blue-700 border border-blue-200 rounded-lg hover:bg-blue-600 hover:text-white transition shadow-xs">
                🖨️ Cetak N4 (Orang Tua)
            </a>
            @if($requirements['n5']['required'] ?? false)
                <a href="{{ route('registrations.documents.n5.print', $registration->id) }}" class="px-3 py-2 text-xs font-medium bg-white text-blue-700 border border-blue-200 rounded-lg hover:bg-blue-600 hover:text-white transition shadow-xs">
                    🖨️ Cetak N5 (Izin Orang Tua)
                </a>
            @endif
            @if($requirements['n6']['required'] ?? false)
                @if($n6Exists)
                    <a href="{{ route('registrations.documents.n6.print', $registration->id) }}" class="px-3 py-2 text-xs font-medium bg-white text-blue-700 border border-blue-200 rounded-lg hover:bg-blue-600 hover:text-white transition shadow-xs">
                        🖨️ Cetak N6 (Kematian/Janda/Duda)
                    </a>
                @else
                    <button
                        type="button"
                        @click="n6Dialog = @js($n6Checked ? 'checked' : 'missing')"
                        class="px-3 py-2 text-xs font-medium bg-white text-blue-700 border border-blue-200 rounded-lg hover:bg-blue-600 hover:text-white transition shadow-xs"
                    >
                        🖨️ Cetak N6 (Kematian/Janda/Duda)
                    </button>
                @endif
            @endif
            <a href="{{ route('registrations.ktp.edit', $registration) }}" class="px-3 py-2 text-xs font-medium bg-white text-emerald-700 border border-emerald-200 rounded-lg hover:bg-emerald-600 hover:text-white transition shadow-xs">
                📎 Kelola Fotokopi KTP
            </a>
            <a href="{{ route('registrations.ktp.print', $registration) }}" target="_blank" class="px-3 py-2 text-xs font-medium bg-white text-emerald-700 border border-emerald-200 rounded-lg hover:bg-emerald-600 hover:text-white transition shadow-xs">
                🖨️ Cetak Fotokopi KTP (Lampiran)
            </a>
            @if(($registration->guardian?->relationship ?? '') !== 'WALI_HAKIM')
            <a href="{{ route('registrations.documents.surat-pernyataan-wali.print', $registration->id) }}" target="_blank" class="px-3 py-2 text-xs font-medium bg-white text-amber-700 border border-amber-200 rounded-lg hover:bg-amber-600 hover:text-white transition shadow-xs">
                🖨️ Pernyataan Wali Nikah
            </a>
            @endif
            <a href="{{ route('registrations.documents.surat-pernyataan-status.print', [$registration->id, 'PRIA']) }}" target="_blank" class="px-3 py-2 text-xs font-medium bg-white text-slate-700 border border-slate-200 rounded-lg hover:bg-slate-800 hover:text-white transition shadow-xs">
                🖨️ Pernyataan Status — Pria
            </a>
            <a href="{{ route('registrations.documents.surat-pernyataan-status.print', [$registration->id, 'WANITA']) }}" target="_blank" class="px-3 py-2 text-xs font-medium bg-white text-slate-700 border border-slate-200 rounded-lg hover:bg-slate-800 hover:text-white transition shadow-xs">
                🖨️ Pernyataan Status — Wanita
            </a>
        </div>
    </div>

    <div
        x-cloak
        x-show="n6Dialog"
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4"
        role="dialog"
        aria-modal="true"
        @click.self="n6Dialog = null"
    >
        <div
            x-show="n6Dialog"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="translate-y-3 scale-95 opacity-0"
            x-transition:enter-end="translate-y-0 scale-100 opacity-100"
            class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl"
        >
            <div x-show="n6Dialog === 'missing'" class="p-6">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-2xl text-blue-700">!</div>
                <h2 class="mt-4 text-xl font-bold text-slate-900">Data N6 belum dibuat</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">Surat N6 diperlukan berdasarkan data pendaftaran, tetapi operator belum mengisi data wafat dan nomor surat.</p>
                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button type="button" @click="n6Dialog = null" class="rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Nanti dulu</button>
                    <a href="{{ route('n6.create', $registration) }}" class="rounded-lg bg-blue-600 px-4 py-2.5 text-center text-sm font-semibold text-white hover:bg-blue-700">Buat sekarang</a>
                </div>
            </div>

            <div x-show="n6Dialog === 'checked'" class="p-6">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-100 text-2xl text-emerald-700">✓</div>
                <h2 class="mt-4 text-xl font-bold text-slate-900">N6 sudah tercentang</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600">Silakan cek berkas fisik N6 terlebih dahulu. Setelah data surat dibuat dan disimpan, tombol cetak akan tersedia.</p>
                <div class="mt-6 flex justify-end">
                    <button type="button" @click="n6Dialog = null" class="rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">Saya mengerti</button>
                </div>
            </div>
        </div>
    </div>

    <!-- GRID CARD UTAMA -->
    <div class="grid min-w-0 grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        
        <!-- CARD: INFORMASI PERNIKAHAN -->
        <div class="min-w-0 bg-white rounded-xl border border-gray-200 shadow-sm p-6 lg:col-span-2">
            <h3 class="text-base font-semibold text-gray-900 border-b pb-3 mb-4 flex items-center gap-2">
                💍 Informasi Pelaksanaan Nikah
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="text-gray-500 block">Jalur Pendaftaran</span>
                    <span class="font-medium text-gray-800">{{ $registration->registration_path }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block">Tanggal & Waktu Nikah</span>
                    <span class="font-medium text-gray-800">{{ $registration->marriage_date ? \Carbon\Carbon::parse($registration->marriage_date)->translatedFormat('d F Y') : '-' }}{{ $registration->marriage_time ? ', '.\Carbon\Carbon::parse($registration->marriage_time)->format('H:i').' WIB' : '' }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block">Mas Kawin / Mahar</span>
                    <span class="font-medium text-gray-800">{{ $registration->marriage_dowry_display ?: '-' }}</span>
                </div>
                <div class="sm:col-span-3">
                    <span class="text-gray-500 block">Lokasi Alamat Nikah</span>
                    <span class="font-medium text-gray-800">{{ $registration->marriage_location_address }}</span>
                </div>
            </div>
        </div>

        <!-- CARD: CALON PENGANTIN PRIA -->
        <div class="min-w-0 bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-base font-semibold text-gray-900 border-b pb-3 mb-4 flex items-center gap-2 text-blue-600">
                👨 Calon Pengantin Pria
            </h3>
            @php $pria = $registration->catins->firstWhere('role', 'PRIA'); @endphp
            @if($pria)
                <div class="space-y-3 text-sm">
                    <div class="flex min-w-0 justify-between gap-4"><span class="shrink-0 text-gray-500">NIK:</span> <span class="min-w-0 break-words text-right font-medium">{{ $pria->nik }}</span></div>
                    <div class="flex min-w-0 justify-between gap-4"><span class="shrink-0 text-gray-500">Nama Lengkap:</span> <span class="min-w-0 break-words text-right font-medium">{{ $pria->name }}</span></div>
                    <div class="flex min-w-0 justify-between gap-4"><span class="shrink-0 text-gray-500">Bin:</span> <span class="min-w-0 break-words text-right font-medium">{{ $pria->bin_binti ?? '-' }}</span></div>
                    <div class="flex min-w-0 justify-between gap-4"><span class="shrink-0 text-gray-500">Tempat, Tgl Lahir:</span> <span class="min-w-0 break-words text-right font-medium">{{ $pria->birth_place }}, {{ $pria->birth_date?->translatedFormat('d F Y') ?? '-' }}</span></div>
                    <div class="flex min-w-0 justify-between gap-4"><span class="shrink-0 text-gray-500">Status Nikah:</span> <span class="min-w-0 break-words text-right font-medium">{{ $pria->marital_status ? \App\Support\MaritalStatus::labelForRaw($pria->marital_status, 'PRIA') : '-' }}</span></div>
                    <div class="flex min-w-0 justify-between gap-4"><span class="shrink-0 text-gray-500">No. HP:</span> <span class="min-w-0 break-words text-right font-medium">{{ $pria->no_hp ?? '-' }}</span></div>
                    <div><span class="text-gray-500 block">Alamat:</span> <span class="font-medium">{{ $pria->address }}</span></div>
                </div>
            @else
                <p class="text-sm text-gray-400 italic">Data pria tidak ditemukan.</p>
            @endif
        </div>

        <!-- CARD: CALON PENGANTIN WANITA -->
        <div class="min-w-0 bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-base font-semibold text-gray-900 border-b pb-3 mb-4 flex items-center gap-2 text-pink-600">
                👩 Calon Pengantin Wanita
            </h3>
            @php $wanita = $registration->catins->firstWhere('role', 'WANITA'); @endphp
            @if($wanita)
                <div class="space-y-3 text-sm">
                    <div class="flex min-w-0 justify-between gap-4"><span class="shrink-0 text-gray-500">NIK:</span> <span class="min-w-0 break-words text-right font-medium">{{ $wanita->nik }}</span></div>
                    <div class="flex min-w-0 justify-between gap-4"><span class="shrink-0 text-gray-500">Nama Lengkap:</span> <span class="min-w-0 break-words text-right font-medium">{{ $wanita->name }}</span></div>
                    <div class="flex min-w-0 justify-between gap-4"><span class="shrink-0 text-gray-500">Binti:</span> <span class="min-w-0 break-words text-right font-medium">{{ $wanita->bin_binti ?? '-' }}</span></div>
                    <div class="flex min-w-0 justify-between gap-4"><span class="shrink-0 text-gray-500">Tempat, Tgl Lahir:</span> <span class="min-w-0 break-words text-right font-medium">{{ $wanita->birth_place }}, {{ $wanita->birth_date?->translatedFormat('d F Y') ?? '-' }}</span></div>
                    <div class="flex min-w-0 justify-between gap-4"><span class="shrink-0 text-gray-500">Status Nikah:</span> <span class="min-w-0 break-words text-right font-medium">{{ $wanita->marital_status ? \App\Support\MaritalStatus::labelForRaw($wanita->marital_status, 'WANITA') : '-' }}</span></div>
                    <div class="flex min-w-0 justify-between gap-4"><span class="shrink-0 text-gray-500">No. HP:</span> <span class="min-w-0 break-words text-right font-medium">{{ $wanita->no_hp ?? '-' }}</span></div>
                    <div><span class="text-gray-500 block">Alamat:</span> <span class="font-medium">{{ $wanita->address }}</span></div>
                </div>
            @else
                <p class="text-sm text-gray-400 italic">Data wanita tidak ditemukan.</p>
            @endif
        </div>

        <!-- CARD: WALI NIKAH -->
        <div class="min-w-0 bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-base font-semibold text-gray-900 border-b pb-3 mb-4 flex items-center gap-2">
                📜 Data Wali Nikah
            </h3>
            @if($registration->guardian)
                @if(($registration->guardian->relationship ?? '') === 'WALI_HAKIM')
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Hubungan:</span> <span class="font-medium">-</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Nama Wali:</span> <span class="font-medium">-</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">NIK:</span> <span class="font-medium">-</span></div>
                        <div><span class="text-gray-500 block">Alamat Wali:</span> <span class="font-medium">-</span></div>
                    </div>
                @else
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Hubungan:</span> <span class="font-medium">{{ $registration->guardian->relationship }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Nama Wali:</span> <span class="font-medium">{{ $registration->guardian->name }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">NIK:</span> <span class="font-medium">{{ $registration->guardian->nik ?? '-' }}</span></div>
                        <div><span class="text-gray-500 block">Alamat Wali:</span> <span class="font-medium">{{ $registration->guardian->address ?? '-' }}</span></div>
                    </div>
                @endif
            @else
                <p class="text-sm text-gray-400 italic">Data wali belum diisi.</p>
            @endif
        </div>

        <!-- CARD: SAKSI-SAKSI -->
        <div class="min-w-0 bg-white rounded-xl border border-gray-200 shadow-sm p-6">
            <h3 class="text-base font-semibold text-gray-900 border-b pb-3 mb-4 flex items-center gap-2">
                👥 Saksi Pernikahan (2 Orang)
            </h3>
            <div class="space-y-4 text-sm">
                @forelse($registration->witnesses as $index => $witness)
                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                        <span class="font-semibold text-xs text-gray-500 block mb-1">SAKSI KE-{{ $index + 1 }}</span>
                        <div class="flex justify-between"><span class="text-gray-500">Nama:</span> <span class="font-medium">{{ $witness->name }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">NIK:</span> <span class="font-medium">{{ $witness->nik ?? '-' }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Pekerjaan:</span> <span class="font-medium">{{ $witness->occupation ?? '-' }}</span></div>
                    </div>
                @empty
                    <p class="text-sm text-gray-400 italic">Data saksi belum tersedia.</p>
                @endforelse
            </div>
        </div>

    </div>

</div>

@endsection