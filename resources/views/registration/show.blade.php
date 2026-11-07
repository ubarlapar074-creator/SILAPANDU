@extends('layouts.app')

@section('title', 'Detail Registrasi')

@section('content')
@php
    // Data ringkas untuk kartu & pemeriksaan riwayat pernikahan di halaman detail
    $priaParty = $registration->parties()->where('role', 'calon_pria')->with('warga')->first();
    $wanitaParty = $registration->parties()->where('role', 'calon_wanita')->with('warga')->first();
    $wali = $registration->marriageWalis()->first();
    $saksiParties = $registration->parties()->whereIn('role', ['saksi_1', 'saksi_2'])->with('warga')->get()->keyBy('role');
    $niksDicheck = [$priaParty?->warga?->nik, $wanitaParty?->warga?->nik];
    $niksDicheck = array_values(array_filter($niksDicheck));
@endphp
<div class="max-w-7xl mx-auto py-6 px-4">
    <div class="space-y-6" x-data="wargaLookup()" x-init="cekRiwayat()">

        {{-- Peringatan bila ada NIK yang tercatat menikah pada pendaftaran lain --}}
        <div x-show="riwayatLain.length" x-cloak
             class="rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm text-amber-900">
            Perhatian: NIK berikut tercatat sebagai pihak pernikahan pada pendaftaran lain:
            <span class="font-mono font-semibold" x-text="riwayatLain.join(', ')"></span>
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}"
                   class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <nav class="text-sm text-gray-500 mb-1" aria-label="breadcrumb">
                        <span>Dashboard</span>
                        <span class="mx-2 text-gray-300">/</span>
                        <span>Pendaftaran Nikah</span>
                        <span class="mx-2 text-gray-300">/</span>
                        <span class="text-gray-800 font-medium">Detail Formulir</span>
                    </nav>
                    <h1 class="text-2xl font-bold text-gray-800">Formulir Pendaftaran Nikah</h1>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2-10H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V8a2 2 0 00-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">No. Registrasi</p>
                        <p class="font-mono text-lg font-semibold text-gray-800">REG/001/2026</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Tanggal Akad:</span>
                        <span class="font-medium text-gray-700">21 Oktober 2026</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Jenis Layanan:</span>
                        <span class="font-medium text-gray-700">Nikah Bedol</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Status:</span>
                        <span class="px-2.5 py-1 text-xs font-semibold bg-amber-100 text-amber-800 rounded-full">Draft</span>
                    </div>
                </div>
            </div>
        </div>

        <nav class="mb-6" aria-label="Progress registrasi">
            <ol class="flex items-center justify-between">
                <li class="relative">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center text-sm font-bold">1</div>
                        <p class="mt-1 text-xs text-center text-gray-700 font-medium">Pengantin Laki-laki</p>
                        <p class="text-xs text-gray-500 text-center">& Orang Tua</p>
                    </div>
                </li>
                <li class="flex-1 h-2 -mx-1 bg-gray-200 rounded"></li>
                <li class="relative">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center text-sm font-bold">2</div>
                        <p class="mt-1 text-xs text-center text-gray-700 font-medium">Pengantin Wanita</p>
                        <p class="text-xs text-gray-500 text-center">& Orang Tua</p>
                    </div>
                </li>
                <li class="flex-1 h-2 -mx-1 bg-gray-200 rounded"></li>
                <li class="relative">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center text-sm font-bold">3</div>
                        <p class="mt-1 text-xs text-center text-gray-700 font-medium">Wali Nikah</p>
                    </div>
                </li>
                <li class="flex-1 h-2 -mx-1 bg-gray-200 rounded"></li>
                <li class="relative">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full bg-emerald-500 text-white flex items-center justify-center text-sm font-bold">4</div>
                        <p class="mt-1 text-xs text-center text-gray-700 font-medium">Saksi</p>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            @include('registration.partials.card-pria')
            @include('registration.partials.card-wanita')
            @include('registration.partials.card-wali')
            @include('registration.partials.card-saksi')
        </div>

        <div class="mt-8 flex justify-between items-center pt-6 border-t border-gray-200">
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm text-gray-600 hover:text-gray-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Dashboard
            </a>
            <div class="flex gap-3">
                <button type="button"
                    class="inline-flex items-center gap-2 px-5 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition">
                    Simpan Draft
                </button>
                <button type="button"
                    class="inline-flex items-center gap-2 px-5 py-2 text-white bg-blue-600 hover:bg-blue-700 rounded-lg font-medium transition">
                    Validasi & Cetak Surat (N1-N6)
                </button>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    // Lookup NIK dari halaman detail: selalu kirimkan registration_id agar
    // pendaftaran yang sedang dilihat tidak dihitung sebagai riwayat pernikahan.
    function wargaLookup() {
        return {
            registrationId: @js($registration->id),
            niks: @js($niksDicheck),
            riwayatLain: [],
            async cekRiwayat() {
                for (const nik of this.niks) {
                    const regId = this.registrationId ? `?registration_id=${this.registrationId}` : '';
                    try {
                        const res = await fetch(`/api/check-nik/${nik}${regId}`);
                        if (!res.ok) continue;
                        const hasil = await res.json();
                        if (hasil.is_married_before) this.riwayatLain.push(nik);
                    } catch (e) {
                        // lookup gagal: abaikan, halaman tetap bisa dibuka
                    }
                }
            }
        };
    }
</script>
@endpush
