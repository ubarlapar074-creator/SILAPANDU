@extends('layouts.app')

@section('title', 'Master Data Warga')

@section('content')
<div x-data="resiList()" class="max-w-7xl mx-auto py-6 px-4">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Master Data Warga</h1>
            <p class="text-sm text-gray-500">Kelola data warga desa berdasarkan NIK.</p>
        </div>
        <div class="flex gap-2">
            <button type="button"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 16v-2h18v2m-6-6l3-3m0 0l3 3m-3-3v12" />
                </svg>
                Import Excel/CSV
            </button>
            <button type="button" @click="openModal()"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4v16m8-8H4" />
                </svg>
                + Tambah Warga Baru
            </button>
        </div>
    </div>

    {{-- Live Search --}}
    <div class="mb-4">
        <input type="text" x-model="search" placeholder="Cari NIK / Nama..."
            class="w-full md:w-80 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-lg shadow-sm overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-2 font-medium text-gray-700">NIK</th>
                    <th class="text-left px-4 py-2 font-medium text-gray-700">Nama Lengkap</th>
                    <th class="text-center px-4 py-2 font-medium text-gray-700">JK</th>
                    <th class="text-left px-4 py-2 font-medium text-gray-700">Tempat/Tgl Lahir</th>
                    <th class="text-left px-4 py-2 font-medium text-gray-700">Status Kawin</th>
                    <th class="text-left px-4 py-2 font-medium text-gray-700">RT/RW</th>
                    <th class="text-center px-4 py-2 font-medium text-gray-700">Riwayat Nikah</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($wargas as $warga)
                    <tr>
                        <td class="px-4 py-2 font-mono text-gray-800">{{ $warga->nik }}</td>
                        <td class="px-4 py-2 text-gray-800">{{ $warga->nama_lengkap }}</td>
                        <td class="px-4 py-2 text-center">{{ $warga->jenis_kelamin === 'L' ? 'Laki' : 'Perempuan' }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $warga->tempat_lahir }}, {{ $warga->tgl_lahir?->format('d-m-Y') }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $warga->status_kawin ?? '-' }}</td>
                        <td class="px-4 py-2 text-gray-500">{{ $warga->rt ?? '-' }}/{{ $warga->rw ?? '-' }}</td>
                        <td class="px-4 py-2 text-center">
                            @php
                                $hasHistory = false;
                            @endphp
                            @if ($hasHistory)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-orange-100 text-orange-800 rounded-full text-xs">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12l2 2 4-4m0 0l-4 4m4-4h-4" />
                                    </svg>
                                    Terdaftar
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-gray-400">
                            Tidak ada data warga.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($wargas->hasPages())
        <div class="mt-4">
            {{ $wargas->links() }}
        </div>
    @endif

    {{-- Modal Form: harus di dalam x-data="resiList()" agar tombol Tambah Warga bisa membukanya --}}
    @include('residents.partials.modal-form')
</div>
@endsection
