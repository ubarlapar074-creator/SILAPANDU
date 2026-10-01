@extends('layouts.app')

@section('title', 'Pendaftaran Berhasil')

@section('content')

<div class="mx-auto max-w-5xl space-y-6">

    {{-- =========================================================
         SUCCESS HEADER
    ========================================================== --}}
    <div class="overflow-hidden rounded-2xl border border-emerald-100 bg-white shadow-sm">

        <div class="bg-emerald-50 px-6 py-10 text-center sm:px-10">

            {{-- ICON SUCCESS --}}
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-emerald-100">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-10 w-10 text-emerald-600"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M5 13l4 4L19 7"
                    />
                </svg>

            </div>

            {{-- TITLE --}}
            <h1 class="mt-5 text-2xl font-bold text-gray-900 sm:text-3xl">
                Pendaftaran Nikah Berhasil
            </h1>

            <p class="mx-auto mt-2 max-w-2xl text-sm leading-6 text-gray-600">
                Data kedua calon pengantin telah berhasil disimpan
                sebagai satu transaksi pendaftaran.
            </p>

        </div>

        {{-- =====================================================
             DETAIL PENDAFTARAN
        ====================================================== --}}
        <div class="p-6 sm:p-8">

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

                {{-- NOMOR PENDAFTARAN --}}
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">

                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                        No. Pendaftaran
                    </p>

                    <p class="mt-1 text-lg font-bold text-emerald-700">
                        {{ $registration->registration_number ?? '-' }}
                    </p>

                </div>

                {{-- TANGGAL NIKAH --}}
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">

                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                        Tanggal Nikah
                    </p>

                    <p class="mt-1 text-lg font-semibold text-gray-900">

                        @if (!empty($registration->marriage_date))

                            {{ \Carbon\Carbon::parse($registration->marriage_date)->translatedFormat('d F Y') }}

                        @else

                            Belum ditentukan

                        @endif

                    </p>

                </div>

                {{-- STATUS --}}
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">

                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                        Status
                    </p>

                    <p class="mt-1 text-lg font-semibold text-emerald-700">
                        Pendaftaran Tersimpan
                    </p>

                </div>

            </div>

            {{-- =================================================
                 DATA KEDUA CATIN
            ================================================== --}}
            <div class="mt-8">

                <h2 class="text-base font-bold text-gray-900">
                    Data Calon Pengantin
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Pendaftaran ini mencakup dua calon pengantin.
                </p>

                <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">

                    {{-- CATIN PRIA --}}
                    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">

                        <div class="flex items-center gap-3">

                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-50 text-sm font-bold text-blue-600">
                                P
                            </div>

                            <div>

                                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                                    Catin Pria
                                </p>

                                <p class="font-bold text-gray-900">
                                    {{ $pria?->name ?? '-' }}
                                </p>

                            </div>

                        </div>

                        <div class="mt-4 space-y-2 text-sm">

                            <div class="flex justify-between gap-4">

                                <span class="text-gray-500">
                                    NIK
                                </span>

                                <span class="font-medium text-gray-800">
                                    {{ $pria?->nik ?? '-' }}
                                </span>

                            </div>

                            <div class="flex justify-between gap-4">

                                <span class="text-gray-500">
                                    No. HP
                                </span>

                                <span class="font-medium text-gray-800">
                                    {{ $pria?->no_hp ?? '-' }}
                                </span>

                            </div>

                            <div class="flex justify-between gap-4">

                                <span class="text-gray-500">
                                    Email
                                </span>

                                <span class="font-medium text-gray-800">
                                    {{ $pria?->email ?? '-' }}
                                </span>

                            </div>

                        </div>

                    </div>

                    {{-- CATIN WANITA --}}
                    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">

                        <div class="flex items-center gap-3">

                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-pink-50 text-sm font-bold text-pink-600">
                                W
                            </div>

                            <div>

                                <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                                    Catin Wanita
                                </p>

                                <p class="font-bold text-gray-900">
                                    {{ $wanita?->name ?? '-' }}
                                </p>

                            </div>

                        </div>

                        <div class="mt-4 space-y-2 text-sm">

                            <div class="flex justify-between gap-4">

                                <span class="text-gray-500">
                                    NIK
                                </span>

                                <span class="font-medium text-gray-800">
                                    {{ $wanita?->nik ?? '-' }}
                                </span>

                            </div>

                            <div class="flex justify-between gap-4">

                                <span class="text-gray-500">
                                    No. HP
                                </span>

                                <span class="font-medium text-gray-800">
                                    {{ $wanita?->no_hp ?? '-' }}
                                </span>

                            </div>

                            <div class="flex justify-between gap-4">

                                <span class="text-gray-500">
                                    Email
                                </span>

                                <span class="font-medium text-gray-800">
                                    {{ $wanita?->email ?? '-' }}
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            {{-- =================================================
                 REQUIREMENT HASIL EVALUASI
            ================================================== --}}
            <div class="mt-8">

                <div class="flex items-center justify-between">

                    <div>

                        <h2 class="text-base font-bold text-gray-900">
                            Hasil Pemeriksaan Persyaratan
                        </h2>

                        <p class="mt-1 text-sm text-gray-500">
                            Hasil ini ditentukan otomatis berdasarkan data
                            yang telah tersimpan.
                        </p>

                    </div>

                </div>

                <div class="mt-4 space-y-4">

                    {{-- =================================================
                         N5
                    ================================================== --}}
                    @if (!empty($requirements['n5']['required']))

                        <div class="rounded-xl border border-amber-200 bg-amber-50 p-5">

                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

                                <div class="flex gap-3">

                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100 font-bold text-amber-700">
                                        !
                                    </div>

                                    <div>

                                        <h3 class="font-bold text-amber-900">
                                            N5 Diperlukan
                                        </h3>

                                        <p class="mt-1 text-sm text-amber-700">
                                            Terdapat calon pengantin yang berusia
                                            di bawah 21 tahun pada tanggal pernikahan.
                                        </p>

                                        @if (!empty($requirements['n5']['reasons']))

                                            <div class="mt-3 space-y-2">

                                                @foreach ($requirements['n5']['reasons'] as $reason)

                                                    <div class="rounded-lg border border-amber-100 bg-white p-3">

                                                        <p class="text-sm font-semibold text-gray-800">
                                                            Catin {{ $reason['role'] ?? '-' }}
                                                        </p>

                                                        <p class="mt-1 text-xs text-gray-500">
                                                            {{ $reason['name'] ?? '-' }}
                                                            — usia {{ $reason['age'] ?? '-' }} tahun
                                                        </p>

                                                    </div>

                                                @endforeach

                                            </div>

                                        @endif

                                    </div>

                                </div>

                                <button
                                    type="button"
                                    onclick="/* alert removed */"
                                    class="shrink-0 rounded-lg bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-amber-700"
                                >
                                    Buat N5
                                </button>

                            </div>

                        </div>

                    @endif

                    {{-- =================================================
                         N6
                    ================================================== --}}
                    @if (!empty($requirements['n6']['required']))

                        <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">

                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

                                <div class="flex gap-3">

                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 font-bold text-blue-700">
                                        !
                                    </div>

                                    <div class="flex-1">

                                        <h3 class="font-bold text-blue-900">
                                            N6 Diperlukan
                                        </h3>

                                        <p class="mt-1 text-sm text-blue-700">
                                            {{ $requirements['n6']['reason'] ?? 'Diperlukan dokumen N6.' }}
                                        </p>

                                        <div class="mt-4 rounded-lg border border-blue-100 bg-white p-4">

                                            <div class="space-y-2 text-sm">

                                                <div class="flex justify-between gap-4">

                                                    <span class="text-gray-500">
                                                        Ayah Catin Wanita
                                                    </span>

                                                    <span class="font-semibold text-gray-800">
                                                        {{ $requirements['n6']['father']['name'] ?? '-' }}
                                                    </span>

                                                </div>

                                                <div class="flex justify-between gap-4">

                                                    <span class="text-gray-500">
                                                        Status Ayah
                                                    </span>

                                                    <span class="font-semibold text-gray-800">
                                                        {{ $requirements['n6']['father']['status'] ?? '-' }}
                                                    </span>

                                                </div>

                                                <div class="flex justify-between gap-4">

                                                    <span class="text-gray-500">
                                                        Wali
                                                    </span>

                                                    <span class="font-semibold text-gray-800">
                                                        {{ $requirements['n6']['guardian']['relationship'] ?? '-' }}
                                                    </span>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                                <a
                                    href="{{ route('n6.create', $registration) }}"
                                    class="shrink-0 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                                >
                                    Buat N6
                                </a>

                            </div>

                        </div>

                    @endif

                    {{-- =================================================
                         SURAT KEMATIAN
                    ================================================== --}}
                    @if (!empty($requirements['death']['required']))

                        <div class="rounded-xl border border-red-200 bg-red-50 p-5">

                            <div class="flex gap-3">

                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100 font-bold text-red-700">
                                    !
                                </div>

                                <div class="flex-1">

                                    <h3 class="font-bold text-red-900">
                                        Surat Kematian Diperlukan
                                    </h3>

                                    <p class="mt-1 text-sm text-red-700">
                                        Terdapat orang tua yang tercatat
                                        berstatus meninggal.
                                    </p>

                                    @if (!empty($requirements['death']['parents']))

                                        <div class="mt-4 space-y-2">

                                            @foreach ($requirements['death']['parents'] as $parent)

                                                <div class="rounded-lg border border-red-100 bg-white p-3">

                                                    <p class="text-sm font-semibold text-gray-800">
                                                        {{ $parent['parent_role'] ?? '-' }}
                                                        Catin {{ $parent['catin_role'] ?? '-' }}
                                                    </p>

                                                    <p class="mt-1 text-xs text-gray-500">
                                                        {{ $parent['parent_name'] ?? '-' }}
                                                    </p>

                                                </div>

                                            @endforeach

                                        </div>

                                    @endif

                                </div>

                            </div>

                        </div>

                    @endif

                    {{-- =================================================
                         SEMUA REQUIREMENT NORMAL
                    ================================================== --}}
                    @if (
                        empty($requirements['n5']['required']) &&
                        empty($requirements['n6']['required']) &&
                        empty($requirements['death']['required'])
                    )

                        <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5">

                            <div class="flex items-center gap-3">

                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-100 font-bold text-emerald-700">
                                    ✓
                                </div>

                                <div>

                                    <h3 class="font-bold text-emerald-900">
                                        Tidak Ada Persyaratan Kondisional
                                    </h3>

                                    <p class="mt-1 text-sm text-emerald-700">
                                        Berdasarkan data yang tersimpan,
                                        N5, N6, dan surat kematian tidak diperlukan.
                                    </p>

                                </div>

                            </div>

                        </div>

                    @endif

                </div>

            </div>

            {{-- =================================================
                 ACTIONS
            ================================================== --}}
            <div class="mt-8 flex flex-col gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:justify-end">

                {{-- DETAIL --}}
                <a
                    href="{{ route('registrations.detail', $registration) }}"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                        />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                        />
                    </svg>

                    Lihat Detail

                </a>

                {{-- CETAK --}}
                <a
                    href="{{ route('registrations.documents.data', $registration) }}"
                    target="_blank"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-emerald-600 bg-white px-5 py-2.5 text-sm font-semibold text-emerald-700 transition hover:bg-emerald-50"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6 9V2h12v7"
                        />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2"
                        />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6 14h12v8H6z"
                        />

                    </svg>

                    Cetak Berkas

                </a>

                {{-- PENDAFTARAN BARU --}}
                <a
                    href="{{ route('registrations.create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700"
                >

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 4v16m8-8H4"
                        />
                    </svg>

                    Pendaftaran Baru

                </a>

            </div>

        </div>

    </div>

</div>

@endsection