@extends('layouts.app')

@section('title', 'Preview Pendaftaran')

@section('content')
<div class="max-w-3xl mx-auto py-8 px-4">

    {{-- Header --}}
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Preview Pendaftaran</h1>
        <p class="text-sm text-gray-600 max-w-2xl mx-auto">
            Periksa kembali ringkasan data di bawah ini sebelum disimpan sebagai draft pendaftaran.
        </p>
    </div>

    @php
        $alamatParts = array_filter([
            $data['marriage_location_address'] ?? null,
            $regionNames['village'] ?? null,
            $regionNames['district'] ?? null,
            $regionNames['regency'] ?? null,
            $regionNames['province'] ?? null,
        ]);
        $alamatFull = $alamatParts ? implode(', ', $alamatParts) : '-';
    @endphp

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-800">Ringkasan Data</h2>
        </div>

        <div class="divide-y divide-gray-100">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 px-6 py-4">
                <span class="text-sm font-medium text-gray-500">Nama Calon Pria</span>
                <span class="sm:col-span-2 text-sm text-gray-800">{{ $data['nama_pria'] ?? '-' }}</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 px-6 py-4">
                <span class="text-sm font-medium text-gray-500">Nama Calon Wanita</span>
                <span class="sm:col-span-2 text-sm text-gray-800">{{ $data['nama_wanita'] ?? '-' }}</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 px-6 py-4">
                <span class="text-sm font-medium text-gray-500">Jenis Layanan</span>
                <span class="sm:col-span-2 text-sm text-gray-800">{{ $serviceLabel }}</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 px-6 py-4">
                <span class="text-sm font-medium text-gray-500">Jadwal Akad</span>
                <span class="sm:col-span-2 text-sm text-gray-800">
                    {{ $tanggalDisplay }}{{ ! empty($data['jam_akad']) ? ', ' . $data['jam_akad'] : '' }}
                </span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 px-6 py-4">
                <span class="text-sm font-medium text-gray-500">Mahar</span>
                <span class="sm:col-span-2 text-sm text-gray-800">{{ $dowryDisplay }}</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 px-6 py-4">
                <span class="text-sm font-medium text-gray-500">Tempat & Alamat Akad</span>
                <span class="sm:col-span-2 text-sm text-gray-800">{{ $alamatFull }}</span>
            </div>
        </div>
    </div>

    {{-- Aksi --}}
    <div class="mt-8 flex flex-col sm:flex-row sm:justify-end gap-3">
        <a href="{{ url()->previous() ?: route('dashboard') }}"
           class="px-5 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition text-center">
            Kembali Edit
        </a>

        <form method="POST" action="{{ route('registration.store') }}" class="inline">
            @csrf
            <input type="hidden" name="service_type" value="{{ $data['service_type'] ?? '' }}">
            <input type="hidden" name="nama_pria" value="{{ $data['nama_pria'] ?? '' }}">
            <input type="hidden" name="nama_wanita" value="{{ $data['nama_wanita'] ?? '' }}">
            <input type="hidden" name="tanggal_akad" value="{{ $data['tanggal_akad'] ?? '' }}">
            <input type="hidden" name="jam_akad" value="{{ $data['jam_akad'] ?? '' }}">
            <input type="hidden" name="jenis_mahar" value="{{ $data['jenis_mahar'] ?? '' }}">
            <input type="hidden" name="nominal_uang" value="{{ $data['nominal_uang'] ?? '' }}">
            <input type="hidden" name="rincian_mahar" value="{{ $data['rincian_mahar'] ?? '' }}">
            <input type="hidden" name="marriage_dowry_display" value="{{ $data['marriage_dowry_display'] ?? $dowryDisplay }}">
            <input type="hidden" name="marriage_location_address" value="{{ $data['marriage_location_address'] ?? '' }}">
            <input type="hidden" name="province_id" value="{{ $data['province_id'] ?? '' }}">
            <input type="hidden" name="regency_id" value="{{ $data['regency_id'] ?? '' }}">
            <input type="hidden" name="district_id" value="{{ $data['district_id'] ?? '' }}">
            <input type="hidden" name="village_id" value="{{ $data['village_id'] ?? '' }}">

            <button type="submit"
                    class="px-5 py-2 text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg font-medium transition w-full sm:w-auto">
                Konfirmasi &amp; Simpan
            </button>
        </form>
    </div>

</div>
@endsection
