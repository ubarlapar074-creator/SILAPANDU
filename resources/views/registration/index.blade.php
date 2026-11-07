@extends('layouts.app')

@section('title', 'Riwayat & Data Nikah')
@section('breadcrumb', 'Riwayat & Data Nikah')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 space-y-6">

    {{-- Header Halaman --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Riwayat &amp; Data Nikah</h1>
            <p class="text-sm text-gray-500">Arsip seluruh pendaftaran nikah: cari, filter, lalu buka kembali formulirnya.</p>
        </div>
        <a href="{{ route('dashboard') }}"
           class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg flex items-center justify-center gap-2 transition">
            <i class="fa-solid fa-plus"></i>
            <span>Pendaftaran Baru</span>
        </a>
    </div>

    {{-- Pencarian & Filter (Server-side) --}}
    <form method="GET" action="{{ route('registration.index') }}"
          class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col md:flex-row md:items-end gap-3">
        <div class="flex-1 min-w-0">
            <label for="filter-q" class="block text-xs font-medium text-gray-500 mb-1">Cari</label>
            <input type="text" id="filter-q" name="q" value="{{ $search }}"
                   placeholder="No. registrasi / no. surat / nama pemohon..."
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none">
        </div>

        <div class="w-full md:w-48">
            <label for="filter-type" class="block text-xs font-medium text-gray-500 mb-1">Jenis Layanan</label>
            <select id="filter-type" name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                <option value="">Semua Layanan</option>
                <option value="kantor" @selected($activeType === 'kantor')>Nikah Kantor</option>
                <option value="bedol" @selected($activeType === 'bedol')>Nikah Bedolan</option>
                <option value="andon" @selected($activeType === 'andon')>Andon Nikah</option>
            </select>
        </div>

        <div class="w-full md:w-48">
            <label for="filter-status" class="block text-xs font-medium text-gray-500 mb-1">Status Berkas</label>
            <select id="filter-status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                <option value="">Semua Status</option>
                <option value="lengkap" @selected($activeStatus === 'lengkap')>Lengkap</option>
                <option value="belum" @selected($activeStatus === 'belum')>Belum Lengkap</option>
            </select>
        </div>

        <div class="flex items-center gap-2">
            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition">
                Terapkan
            </button>
            <a href="{{ route('registration.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition">
                Reset
            </a>
        </div>
    </form>

    {{-- Rekap Singkat --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <p class="text-xs text-gray-500 uppercase font-medium">Total Arsip</p>
            <p class="text-xl font-bold text-gray-800 mt-1">{{ $registrations->total() }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <p class="text-xs text-gray-500 uppercase font-medium">Nikah Kantor</p>
            <p class="text-xl font-bold text-emerald-600 mt-1">{{ $counts['kantor'] ?? 0 }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <p class="text-xs text-gray-500 uppercase font-medium">Nikah Bedolan</p>
            <p class="text-xl font-bold text-amber-600 mt-1">{{ $counts['bedol'] ?? 0 }}</p>
        </div>
        <div class="bg-white border border-gray-200 rounded-xl p-4">
            <p class="text-xs text-gray-500 uppercase font-medium">Andon Nikah</p>
            <p class="text-xl font-bold text-cyan-600 mt-1">{{ $counts['andon'] ?? 0 }}</p>
        </div>
    </div>

    {{-- Tabel Arsip --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold">
                        <th class="p-4">Nomor Surat</th>
                        <th class="p-4">Pasangan Pengantin</th>
                        <th class="p-4">Layanan</th>
                        <th class="p-4">Tgl Akad</th>
                        <th class="p-4 text-center">Status Berkas</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse ($registrations as $reg)
                        @php
                            $priaParty = $reg->parties->where('role', 'calon_pria')->first();
                            $wanitaParty = $reg->parties->where('role', 'calon_wanita')->first();
                            $priaName = $priaParty?->nama_sementara ?? $priaParty?->warga?->nama_lengkap ?? 'Calon Pria';
                            $wanitaName = $wanitaParty?->nama_sementara ?? $wanitaParty?->warga?->nama_lengkap ?? 'Calon Wanita';
                            $isLengkap = ($reg->step >= 4) || in_array($reg->status, ['lengkap', 'valid', 'selesai'], true);
                            $serviceBadges = [
                                'kantor' => ['Nikah Kantor', 'bg-emerald-50 text-emerald-700 border border-emerald-200'],
                                'bedol' => ['Nikah Bedolan', 'bg-amber-50 text-amber-700 border border-amber-200'],
                                'andon' => ['Andon Nikah', 'bg-cyan-50 text-cyan-700 border border-cyan-200'],
                            ];
                            $badge = $serviceBadges[$reg->service_type] ?? ['Tidak diketahui', 'bg-gray-100 text-gray-600 border border-gray-200'];
                        @endphp
                        <tr class="hover:bg-gray-50/70">
                            <td class="p-4 font-mono font-medium text-emerald-700">
                                {{ $reg->nomor_surat ?? $reg->registration_number }}
                                @if ($reg->nomor_surat)
                                    <div class="text-[11px] font-sans text-gray-400">No. Reg: {{ $reg->registration_number }}</div>
                                @endif
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-gray-800">{{ $priaName }}</div>
                                <div class="text-xs text-gray-500">&amp; {{ $wanitaName }}</div>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $badge[1] }}">{{ $badge[0] }}</span>
                            </td>
                            <td class="p-4 text-gray-600">
                                @if ($reg->tanggal_akad)
                                    {{ $reg->tanggal_akad->translatedFormat('d M Y') }}
                                    @if ($reg->jam_akad)
                                        <div class="text-xs text-gray-400">{{ substr($reg->jam_akad, 0, 5) }} WIB</div>
                                    @endif
                                @else
                                    <span class="text-gray-400 italic">Belum dijadwalkan</span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                @if ($isLengkap)
                                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs rounded-full font-semibold">Lengkap</span>
                                @else
                                    <span class="px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200 text-xs rounded-full font-semibold">Belum Lengkap</span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('registration.show', $reg->id) }}"
                                       class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs rounded-md flex items-center gap-1 transition">
                                        <i class="fa-solid fa-eye"></i> Detail
                                    </a>
                                    <a href="{{ route('registration.dashboard', $reg->id) }}"
                                       class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 text-xs rounded-md flex items-center gap-1 transition">
                                        <i class="fa-solid fa-pen-to-square"></i> Edit
                                    </a>
                                    <a href="{{ route('registration.review', $reg->id) }}"
                                       class="px-2.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs rounded-md flex items-center gap-1 transition">
                                        <i class="fa-solid fa-print"></i> Review &amp; Cetak
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-10 text-center text-gray-400">
                                <i class="fa-solid fa-box-archive text-3xl mb-2 block text-gray-300"></i>
                                @if ($search !== '' || $activeType !== null || $activeStatus !== null)
                                    Tidak ada pendaftaran yang cocok dengan pencarian/filter Anda.
                                @else
                                    Belum ada riwayat pendaftaran nikah.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if ($registrations->hasPages())
        <div class="mt-2">
            {{ $registrations->links() }}
        </div>
    @endif

</div>
@endsection
