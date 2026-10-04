@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<div class="space-y-7">

    {{-- =========================================================
         HEADER
    ========================================================== --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-emerald-800">Pemerintah Desa</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">
                Dashboard
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Ringkasan pelayanan pendaftaran nikah.
            </p>
        </div>

        <a
            href="{{ route('registrations.create') }}"
            class="inline-flex w-fit items-center gap-2 rounded-md bg-emerald-800 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-900 focus:outline-none focus:ring-2 focus:ring-emerald-700 focus:ring-offset-2"
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


    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6 text-emerald-900">
        <h2 class="text-lg font-bold">Selamat Datang di SILAPANDU</h2>
        <p class="mt-1 text-sm text-emerald-700">Sistem Layanan Administrasi Pernikahan Terpadu — siap digunakan mandiri & cepat secara offline.</p>
    </div>

    {{-- =========================================================
         STATISTIK
    ========================================================== --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">

        {{-- Total Pendaftaran --}}
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">

            <div class="flex items-start justify-between">

                <div>
                    <p class="text-sm font-medium text-gray-500">
                        Total Pendaftaran
                    </p>

                    <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900">
                        {{ $totalRegistrations ?? 0 }}
                    </p>

                    <p class="mt-1 text-xs text-gray-500">
                        Seluruh transaksi pendaftaran
                    </p>
                </div>

                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0z"
                        />

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M4 21a8 8 0 0116 0"
                        />
                    </svg>

                </div>

            </div>

        </div>

        {{-- Informasi Sistem --}}
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">

            <div class="flex items-start justify-between">

                <div>
                    <p class="text-sm font-medium text-gray-500">
                        Sistem
                    </p>

                    <p class="mt-2 text-xl font-bold text-gray-900">
                        SILAPANDU
                    </p>

                    <p class="mt-1 text-xs text-gray-500">
                        Sistem Layanan Administrasi Pernikahan Terpadu
                    </p>
                </div>

                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414A1 1 0 0118 8.414V19a2 2 0 01-2 2z"
                        />
                    </svg>

                </div>

            </div>

        </div>

        {{-- Status --}}
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">

            <div class="flex items-start justify-between">

                <div>
                    <p class="text-sm font-medium text-gray-500">
                        Status Sistem
                    </p>

                    <p class="mt-2 text-xl font-bold text-emerald-600">
                        Aktif
                    </p>

                    <p class="mt-1 text-xs text-gray-500">
                        Siap menerima pendaftaran
                    </p>
                </div>

                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">

                    <span class="relative flex h-3 w-3">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex h-3 w-3 rounded-full bg-emerald-500"></span>
                    </span>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         PENDAFTARAN TERBARU
    ========================================================== --}}
    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">

        {{-- Header tabel --}}
        <div class="flex flex-col gap-3 border-b border-gray-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h2 class="text-base font-semibold text-gray-900">
                    Pendaftaran Terbaru
                </h2>

                <p class="mt-1 text-xs text-gray-500">
                    Lima transaksi pendaftaran terakhir.
                </p>
            </div>



        </div>


        {{-- Table --}}
        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">

                    <tr>

                        <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            No. Pendaftaran
                        </th>

                        <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Jalur
                        </th>

                        <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Status
                        </th>

                        <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Tanggal Nikah
                        </th>

                        <th class="whitespace-nowrap px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-gray-100 bg-white">

                    @forelse($recentRegistrations ?? [] as $reg)

                        <tr class="transition hover:bg-gray-50">

                            {{-- Nomor --}}
                            <td class="whitespace-nowrap px-5 py-4">

                                <div class="text-sm font-semibold text-gray-900">
                                    {{ $reg->registration_number }}
                                </div>

                            </td>


                            {{-- Jalur --}}
                            <td class="whitespace-nowrap px-5 py-4">
                                @php $pathLabel = \App\Models\Registration::pathLabel($reg->registration_path); $pathColor = match($reg->registration_path) { 'NIKAH_BEDOL','DALAM_DESA' => 'bg-blue-50 text-blue-700', 'NIKAH_KANTOR' => 'bg-emerald-50 text-emerald-700', 'PEMBUATAN_ANDON_NIKAH','ANDON' => 'bg-purple-50 text-purple-700', default => 'bg-gray-100 text-gray-600' }; @endphp
                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $pathColor }}">
                                    {{ $pathLabel }}
                                </span>
                            </td>


                            {{-- Status --}}
                            <td class="whitespace-nowrap px-5 py-4">

                                @php
                                    $status = strtoupper($reg->status ?? '');

                                    $statusClass = match ($status) {
                                        'SELESAI',
                                        'COMPLETED',
                                        'SELESAI_DIVERIFIKASI'
                                            => 'bg-emerald-50 text-emerald-700',

                                        'DITOLAK',
                                        'REJECTED'
                                            => 'bg-red-50 text-red-700',

                                        'DIPROSES',
                                        'PROCESSING',
                                        'VERIFIKASI'
                                            => 'bg-blue-50 text-blue-700',

                                        default
                                            => 'bg-yellow-50 text-yellow-700',
                                    };
                                @endphp

                                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                    {{ $reg->status ?? '-' }}
                                </span>

                            </td>


                            {{-- Tanggal Nikah --}}
                            <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-600">

                                @if ($reg->marriage_date)
                                    {{ \Carbon\Carbon::parse($reg->marriage_date)->translatedFormat('d F Y') }}
                                @else
                                    -
                                @endif

                            </td>

                            {{-- Aksi Lihat Berkas --}}
                            <td class="whitespace-nowrap px-5 py-4 text-right text-sm font-medium">
                                <a 
                                        href="{{ route('registrations.detail', $reg->id) }}"
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-100 transition"
                                >
                                    👁️ Lihat Berkas
                                </a>
                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="5" class="px-5 py-12 text-center">

                                <div class="flex flex-col items-center">

                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400">

                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-6 w-6"
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.5"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l4.414 4.414A1 1 0 0118 8.414V19a2 2 0 01-2 2z"
                                            />
                                        </svg>

                                    </div>

                                    <p class="mt-3 text-sm font-medium text-gray-700">
                                        Belum ada pendaftaran
                                    </p>

                                    <p class="mt-1 text-xs text-gray-500">
                                        Silakan buat pendaftaran nikah baru.
                                    </p>

                                    <a
                                        href="{{ route('registrations.create') }}"
                                        class="mt-4 inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700"
                                    >
                                        Buat Pendaftaran
                                    </a>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection