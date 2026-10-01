@extends('layouts.app')

@section('title', 'Penomoran Surat')

@section('content')
@php
    $klas = old('kode_klasifikasi_arsip', $settings->kode_klasifikasi_arsip ?? $settings->prefix_n1 ?? '470');
    $desa = old('kode_desa_kecamatan', $settings->kode_desa_kecamatan ?? '');
    $hasDesa = trim($desa) !== '';
    $roman = ['', 'I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'][(int) date('n')] ?? date('n');
    $year = date('Y');
    $preview = $hasDesa ? sprintf('001/%s/%s/%s/%s', $klas, trim($desa), $roman, $year) : sprintf('001/%s/%s/%s', $klas, $roman, $year);
@endphp
<div class="mx-auto max-w-5xl">
    <div class="mb-6">
        <p class="text-sm font-medium text-emerald-600">Konfigurasi</p>
        <h1 class="mt-1 text-2xl font-bold text-gray-900">Penomoran Surat</h1>
        <p class="mt-1 text-sm text-gray-500">Atur Format Surat global. Berlaku untuk semua N1–N6. Data penandatangan dari <a href="{{ route('settings.village-profile.edit') }}" class="font-medium text-emerald-700 hover:underline">Profil Desa & Penandatangan</a>. Per-pendaftaran tetap bisa override untuk sinkron buku fisik.</p>
    </div>

    <form action="{{ route('settings.document-numbers.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm" x-data="{ klas: @js($klas), desa: @js($desa), roman: @js($roman), year: @js($year), get preview(){ const d = this.desa.trim(); return d ? `001/${this.klas || '470'}/${d}/${this.roman}/${this.year}` : `001/${this.klas || '470'}/${this.roman}/${this.year}` } }">
            <h2 class="text-base font-semibold text-gray-900">Format Surat</h2>
            <p class="mt-1 text-xs text-gray-500">Format: <span class="font-mono">[Nomor Urut] / [Kode Klasifikasi Arsip] / [Kode Desa/Kecamatan] / [Bulan] / [Tahun]</span> — contoh <span class="font-mono">001/470/IX/2026</span> atau <span class="font-mono">001/470/2001-001/IX/2026</span></p>
            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="kode_klasifikasi_arsip" class="block text-sm font-medium text-gray-700">Kode Klasifikasi Arsip <span class="text-red-500">*</span></label>
                    <input id="kode_klasifikasi_arsip" type="text" name="kode_klasifikasi_arsip" x-model="klas" value="{{ $klas }}" placeholder="470" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm font-mono">
                    @error('kode_klasifikasi_arsip') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    <p class="mt-1 text-xs text-gray-400">Kode arsip desa (mis. 470).</p>
                </div>
                <div>
                    <label for="kode_desa_kecamatan" class="block text-sm font-medium text-gray-700">Kode Desa/Kecamatan <span class="text-red-500">*</span></label>
                    <input id="kode_desa_kecamatan" type="text" name="kode_desa_kecamatan" x-model="desa" value="{{ $desa }}" placeholder="2001-001" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm font-mono">
                    @error('kode_desa_kecamatan') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    <p class="mt-1 text-xs text-gray-400">Wajib diisi sebelum simpan — sinkron dengan buku agenda fisik.</p>
                </div>
            </div>
            <div class="mt-4 rounded-lg bg-gray-50 border border-gray-200 px-4 py-3">
                <p class="text-xs font-medium text-gray-500">Preview Format Surat</p>
                <p class="mt-1 font-mono text-sm font-semibold text-gray-900" x-text="preview">{{ $preview }}</p>
                <p class="mt-1 text-xs text-gray-400">Nomor urut global untuk semua N1–N6, direset tiap tahun. Bulan <span x-text="roman"></span>, Tahun <span x-text="year"></span>.</p>
            </div>
        </section>

        <section class="rounded-xl border border-amber-200 bg-amber-50 p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Penandatangan</h2>
                    <p class="mt-1 text-xs text-gray-600">SSOT ada di Profil Desa & Penandatangan. Halaman ini hanya menampilkan — ubah di sana.</p>
                </div>
                <a href="{{ route('settings.village-profile.edit') }}" class="shrink-0 rounded-lg bg-white border border-amber-300 px-4 py-2 text-sm font-semibold text-amber-800 hover:bg-amber-100">Edit di Profil Desa &rarr;</a>
            </div>
            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="rounded-lg bg-white border border-gray-200 p-4">
                    <p class="text-xs font-medium text-gray-500">Kepala Desa</p>
                    <p class="mt-1 font-semibold text-gray-900">{{ $profile?->village_head_name ?: 'Belum diatur' }}</p>
                    <p class="text-xs text-gray-500">{{ $profile?->village_head_id ? 'NIP/NIK: '.$profile->village_head_id : 'NIP belum diisi' }}</p>
                </div>
                <div class="rounded-lg bg-white border border-gray-200 p-4">
                    <p class="text-xs font-medium text-gray-500">Kasi Pelayanan</p>
                    <p class="mt-1 font-semibold text-gray-900">{{ $profile?->service_officer_name ?: 'Belum diatur' }}</p>
                    <p class="text-xs text-gray-500">{{ $profile?->service_officer_id ? 'NIP/NIK: '.$profile->service_officer_id : 'NIP belum diisi' }}</p>
                </div>
            </div>
            @if(!$profile?->village_head_name && !$profile?->service_officer_name)
                <p class="mt-3 text-xs text-amber-800">Belum ada penandatangan — isi di <a href="{{ route('settings.village-profile.edit') }}" class="font-semibold underline">Profil Desa & Penandatangan</a>.</p>
            @endif
        </section>

        <div class="flex justify-end">
            <button type="submit" class="rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">Simpan Penomoran</button>
        </div>
    </form>
</div>
@endsection
