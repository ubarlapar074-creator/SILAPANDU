@extends('layouts.app')

@section('title', 'Edit N6')

@section('content')

<div class="mb-6">
    <div class="mb-2 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('dashboard') }}" class="hover:text-emerald-600">Dashboard</a>
        <span>/</span>
        <a href="{{ $n6->registration ? route('registrations.detail', $n6->registration) : route('n6.manual.index') }}" class="hover:text-emerald-600">{{ $n6->registration ? 'Pendaftaran' : 'N6 Manual' }}</a>
        <span>/</span>
        <a href="{{ route('n6.show', $n6) }}" class="hover:text-emerald-600">Detail N6</a>
        <span>/</span>
        <span class="text-gray-700">Edit N6</span>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">
        Edit Surat Keterangan Kematian (N6)
    </h1>
    <p class="mt-1 text-sm text-gray-500">
        Nomor Surat: <strong>{{ $n6->document_number }}</strong>
    </p>
</div>

<div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
    <form action="{{ route('n6.update', $n6) }}" method="POST" class="p-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

            {{-- =============================================
                 BAGIAN A - DATA ALMARHUM
            ============================================== --}}
            <div class="md:col-span-2">
                <h3 class="text-lg font-bold text-gray-900 border-b border-gray-200 pb-2 mb-4">
                    A. Data Almarhum / Almarhumah
                </h3>
            </div>

            {{-- Nama --}}
            <div>
                <label class="block font-medium text-gray-700">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="deceased_name"
                    value="{{ old('deceased_name', $n6->deceased_name) }}"
                    required
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                >
                @error('deceased_name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Bin/Binti --}}
            <div>
                <label class="block font-medium text-gray-700">
                    Bin / Binti <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="deceased_bin_binti"
                    value="{{ old('deceased_bin_binti', $n6->deceased_bin_binti) }}"
                    required
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                >
                @error('deceased_bin_binti')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- NIK --}}
            <div>
                <label class="block font-medium text-gray-700">
                    NIK <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="deceased_nik"
                    value="{{ old('deceased_nik', $n6->deceased_nik) }}"
                    maxlength="16"
                    inputmode="numeric"
                    required
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                >
                @error('deceased_nik')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tempat Lahir --}}
            <div>
                <label class="block font-medium text-gray-700">
                    Tempat Lahir <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="deceased_birth_place"
                    value="{{ old('deceased_birth_place', $n6->deceased_birth_place) }}"
                    required
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                >
                @error('deceased_birth_place')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tanggal Lahir --}}
            <div>
                <label class="block font-medium text-gray-700">
                    Tanggal Lahir <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="deceased_birth_date"
                    value="{{ old('deceased_birth_date', $n6->deceased_birth_date->format('d/m/Y')) }}"
                    placeholder="dd/mm/yyyy"
                    inputmode="numeric"
                    pattern="\d{2}/\d{2}/\d{4}"
                    data-date-dmy
                    required
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                >
                @error('deceased_birth_date')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Kewarganegaraan --}}
            <div>
                <label class="block font-medium text-gray-700">
                    Kewarganegaraan
                </label>
                <input
                    type="text"
                    name="deceased_nationality"
                    value="{{ old('deceased_nationality', $n6->deceased_nationality ?? 'WNI') }}"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                >
                @error('deceased_nationality')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Agama --}}
            <div>
                <label class="block font-medium text-gray-700">
                    Agama
                </label>
                <select
                    name="deceased_religion"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                >
                    <option value="">Pilih Agama</option>
                    <option value="Islam" @selected(old('deceased_religion', $n6->deceased_religion) === 'Islam')>Islam</option>
                    <option value="Kristen" @selected(old('deceased_religion', $n6->deceased_religion) === 'Kristen')>Kristen</option>
                    <option value="Katolik" @selected(old('deceased_religion', $n6->deceased_religion) === 'Katolik')>Katolik</option>
                    <option value="Hindu" @selected(old('deceased_religion', $n6->deceased_religion) === 'Hindu')>Hindu</option>
                    <option value="Buddha" @selected(old('deceased_religion', $n6->deceased_religion) === 'Buddha')>Buddha</option>
                    <option value="Konghucu" @selected(old('deceased_religion', $n6->deceased_religion) === 'Konghucu')>Konghucu</option>
                </select>
                @error('deceased_religion')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Pekerjaan --}}
            <div>
                <label class="block font-medium text-gray-700">
                    Pekerjaan
                </label>
                <input
                    type="text"
                    name="deceased_occupation"
                    value="{{ old('deceased_occupation', $n6->deceased_occupation) }}"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                >
                @error('deceased_occupation')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Alamat --}}
            <div class="md:col-span-2">
                <label class="block font-medium text-gray-700">
                    Alamat <span class="text-red-500">*</span>
                </label>
                <textarea
                    name="deceased_address"
                    rows="2"
                    required
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                >{{ old('deceased_address', $n6->deceased_address) }}</textarea>
                @error('deceased_address')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- =============================================
                 BAGIAN C - INFORMASI KEMATIAN
            ============================================== --}}
            <div class="md:col-span-2 mt-4">
                <h3 class="text-lg font-bold text-gray-900 border-b border-gray-200 pb-2 mb-4">
                    C. Informasi Kematian
                </h3>
            </div>

            {{-- Tanggal Meninggal --}}
            <div>
                <label class="block font-medium text-gray-700">
                    Tanggal Meninggal <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="death_date"
                    value="{{ old('death_date', $n6->death_date->format('d/m/Y')) }}"
                    placeholder="dd/mm/yyyy"
                    inputmode="numeric"
                    pattern="\d{2}/\d{2}/\d{4}"
                    data-date-dmy
                    required
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                >
                @error('death_date')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tempat Meninggal --}}
            <div>
                <label class="block font-medium text-gray-700">
                    Tempat Meninggal
                </label>
                <input
                    type="text"
                    name="death_place"
                    value="{{ old('death_place', $n6->death_place) }}"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                >
                @error('death_place')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- =============================================
                 BAGIAN D - INFORMASI SURAT
            ============================================== --}}
            <div class="md:col-span-2 mt-4">
                <h3 class="text-lg font-bold text-gray-900 border-b border-gray-200 pb-2 mb-4">
                    D. Informasi Surat
                </h3>
            </div>

            {{-- Nomor Surat --}}
            <div>
                <label class="block font-medium text-gray-700">
                    Nomor Surat <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="document_number"
                    value="{{ old('document_number', $n6->document_number) }}"
                    required
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                >
                @error('document_number')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tanggal Surat --}}
            <div>
                <label class="block font-medium text-gray-700">
                    Tanggal Surat <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="issue_date"
                    value="{{ old('issue_date', $n6->issue_date->format('d/m/Y')) }}"
                    placeholder="dd/mm/yyyy"
                    inputmode="numeric"
                    pattern="\d{2}/\d{2}/\d{4}"
                    data-date-dmy
                    required
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                >
                @error('issue_date')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Penanda Tangan --}}
            <div>
                <label class="block font-medium text-gray-700">
                    Nama Penanda Tangan <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="signed_by"
                    value="{{ old('signed_by', $n6->signed_by) }}"
                    required
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                >
                @error('signed_by')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Jabatan Penanda Tangan --}}
            <div>
                <label class="block font-medium text-gray-700">
                    Jabatan Penanda Tangan <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="signed_by_position"
                    value="{{ old('signed_by_position', $n6->signed_by_position) }}"
                    required
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                >
                @error('signed_by_position')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status --}}
            <div class="md:col-span-2 mt-4">
                <label class="block font-medium text-gray-700">
                    Status
                </label>
                <select
                    name="status"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500"
                >
                    <option value="DRAFT" @selected(old('status', $n6->status) === 'DRAFT')>Draft</option>
                    <option value="PUBLISHED" @selected(old('status', $n6->status) === 'PUBLISHED')>Published</option>
                    <option value="ARCHIVED" @selected(old('status', $n6->status) === 'ARCHIVED')>Archived</option>
                </select>
                @error('status')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

        </div>

        {{-- Tombol --}}
        <div class="mt-6 flex items-center justify-between border-t border-gray-200 pt-6">
            <a
                href="{{ route('n6.show', $n6) }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
            >
                Kembali
            </a>

            <button
                type="submit"
                class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                Update N6
            </button>
        </div>

    </form>
</div>

@endsection