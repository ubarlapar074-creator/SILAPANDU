@extends('layouts.app')

@section('title', 'Detail N6')

@section('content')

<div class="mb-6">
    <div class="mb-2 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('dashboard') }}" class="hover:text-emerald-600">Dashboard</a>
        <span>/</span>
                @if($n6->registration)
                    <a href="{{ route('registrations.detail', $n6->registration) }}" class="hover:text-emerald-600">Pendaftaran</a>
                    <span>/</span>
                @else
                    <a href="{{ route('n6.manual.index') }}" class="hover:text-emerald-600">N6 Manual</a>
                    <span>/</span>
                @endif
        <span class="text-gray-700">Detail N6</span>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">
        Detail Surat Keterangan Kematian (N6)
    </h1>
    <p class="mt-1 text-sm text-gray-500">
        Nomor Surat: <strong>{{ $n6->document_number }}</strong>
    </p>
</div>

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    <div class="p-6">

        {{-- Status --}}
        <div class="mb-6 rounded-lg border p-4
            @if($n6->status === 'PUBLISHED') border-emerald-200 bg-emerald-50
            @elseif($n6->status === 'ARCHIVED') border-gray-200 bg-gray-50
            @else border-amber-200 bg-amber-50 @endif
        ">
            <span class="font-semibold">Status:</span>
            <span class="
                @if($n6->status === 'PUBLISHED') text-emerald-700
                @elseif($n6->status === 'ARCHIVED') text-gray-600
                @else text-amber-700 @endif
            ">
                {{ $n6->status }}
            </span>
            <span class="ml-4 text-sm text-gray-500">
                Dibuat oleh: {{ $n6->createdBy->name ?? '-' }}
            </span>
            <span class="ml-4 text-sm text-gray-500">
                Tanggal: {{ $n6->created_at->format('d-m-Y H:i') }}
            </span>
        </div>

        {{-- Data Pendaftaran --}}
        <div class="mb-6 rounded-lg border border-gray-200 bg-gray-50 p-4">
            <div class="grid grid-cols-2 gap-4 text-sm">
                @if($n6->registration)
                    <div>
                        <span class="text-gray-500">No. Pendaftaran:</span>
                        <span class="font-semibold">{{ $n6->registration->registration_number }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Catin Wanita:</span>
                        <span class="font-semibold">{{ $n6->catin_wanita->name ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">NIK Catin Wanita:</span>
                        <span class="font-semibold">{{ $n6->catin_wanita->nik ?? '-' }}</span>
                    </div>
                @else
                    <div>
                        <span class="text-gray-500">Jenis Dokumen:</span>
                        <span class="font-semibold">N6 Mandiri</span>
                    </div>
                @endif
                <div>
                    <span class="text-gray-500">Tanggal Surat:</span>
                    <span class="font-semibold">{{ $n6->issue_date->format('d-m-Y') }}</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

            {{-- Bagian A --}}
            <div class="md:col-span-2">
                <h3 class="text-lg font-bold text-gray-900 border-b border-gray-200 pb-2 mb-4">
                    A. Data Almarhum / Almarhumah
                </h3>
            </div>

            <div><span class="text-gray-500">Nama:</span> <span class="font-semibold">{{ $n6->deceased_name }}</span></div>
            <div><span class="text-gray-500">Bin/Binti:</span> <span class="font-semibold">{{ $n6->deceased_bin_binti }}</span></div>
            <div><span class="text-gray-500">NIK:</span> <span class="font-semibold">{{ $n6->deceased_nik }}</span></div>
            <div><span class="text-gray-500">Tempat Lahir:</span> <span class="font-semibold">{{ $n6->deceased_birth_place }}</span></div>
            <div><span class="text-gray-500">Tanggal Lahir:</span> <span class="font-semibold">{{ $n6->deceased_birth_date->format('d-m-Y') }}</span></div>
            <div><span class="text-gray-500">Kewarganegaraan:</span> <span class="font-semibold">{{ $n6->deceased_nationality ?? '-' }}</span></div>
            <div><span class="text-gray-500">Agama:</span> <span class="font-semibold">{{ $n6->deceased_religion ?? '-' }}</span></div>
            <div><span class="text-gray-500">Pekerjaan:</span> <span class="font-semibold">{{ $n6->deceased_occupation ?? '-' }}</span></div>
            <div class="md:col-span-2"><span class="text-gray-500">Alamat:</span> <span class="font-semibold">{{ $n6->deceased_address }}</span></div>

            {{-- Bagian C --}}
            <div class="md:col-span-2 mt-4">
                <h3 class="text-lg font-bold text-gray-900 border-b border-gray-200 pb-2 mb-4">
                    C. Informasi Kematian
                </h3>
            </div>

            <div><span class="text-gray-500">Tanggal Meninggal:</span> <span class="font-semibold">{{ $n6->death_date->format('d-m-Y') }}</span></div>
            <div><span class="text-gray-500">Tempat Meninggal:</span> <span class="font-semibold">{{ $n6->death_place ?? '-' }}</span></div>

            {{-- Bagian D --}}
            <div class="md:col-span-2 mt-4">
                <h3 class="text-lg font-bold text-gray-900 border-b border-gray-200 pb-2 mb-4">
                    D. Informasi Surat
                </h3>
            </div>

            <div><span class="text-gray-500">Nomor Surat:</span> <span class="font-semibold">{{ $n6->document_number }}</span></div>
            <div><span class="text-gray-500">Tanggal Surat:</span> <span class="font-semibold">{{ $n6->issue_date->format('d-m-Y') }}</span></div>
            <div><span class="text-gray-500">Penanda Tangan:</span> <span class="font-semibold">{{ $n6->signed_by }}</span></div>
            <div><span class="text-gray-500">Jabatan:</span> <span class="font-semibold">{{ $n6->signed_by_position }}</span></div>

        </div>

        {{-- Tombol --}}
        <div class="mt-6 flex items-center justify-between border-t border-gray-200 pt-6">
            <a
                href="{{ $n6->registration ? route('registrations.detail', $n6->registration) : route('n6.manual.index') }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
            >
                Kembali
            </a>

            <div class="flex gap-2">
                <a
                    href="{{ route('n6.print', $n6) }}"
                    target="_blank"
                    class="inline-flex items-center gap-2 rounded-lg border border-blue-300 bg-white px-4 py-2.5 text-sm font-semibold text-blue-700 transition hover:bg-blue-50"
                >
                    Cetak N6
                </a>
                <a
                    href="{{ route('n6.edit', $n6) }}"
                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700"
                >
                    Edit N6
                </a>

                <form action="{{ route('n6.destroy', $n6) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button
                        type="submit"
                        onclick="return confirm('Yakin ingin menghapus N6 ini?')"
                        class="inline-flex items-center gap-2 rounded-lg border border-red-300 bg-white px-4 py-2.5 text-sm font-semibold text-red-700 transition hover:bg-red-50"
                    >
                        Hapus
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

@endsection