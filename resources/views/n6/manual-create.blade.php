@extends('layouts.app')

@section('title', 'Buat N6 Manual')

@section('content')
<div class="mx-auto max-w-5xl">
    <div class="mb-6">
        <p class="text-sm font-medium text-blue-600">Modul Dokumen Mandiri</p>
        <h1 class="mt-1 text-2xl font-bold text-gray-900">Buat Surat N6</h1>
        <p class="mt-1 text-sm text-gray-500">Isi berdasarkan data dan berkas fisik almarhum. Modul ini tidak terkait pendaftaran pernikahan.</p>
    </div>

    <form action="{{ route('n6.store') }}" method="POST" class="space-y-6">
        @csrf
        <input type="hidden" name="registration_id" value="">

        <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="border-b border-gray-200 pb-3 text-base font-semibold text-gray-900">Data Almarhum/Almarhumah</h2>
            <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
                @foreach([
                    'deceased_name' => 'Nama Lengkap',
                    'deceased_bin_binti' => 'Bin/Binti',
                    'deceased_nik' => 'NIK',
                    'deceased_birth_place' => 'Tempat Lahir',
                    'deceased_birth_date' => 'Tanggal Lahir',
                    'deceased_nationality' => 'Kewarganegaraan',
                    'deceased_religion' => 'Agama',
                    'deceased_occupation' => 'Pekerjaan',
                ] as $field => $label)
                    <div>
                        <label for="{{ $field }}" class="block text-sm font-medium text-gray-700">{{ $label }} @if(in_array($field, ['deceased_name', 'deceased_bin_binti', 'deceased_nik', 'deceased_birth_place', 'deceased_birth_date']))<span class="text-red-500">*</span>@endif</label>
                        <input id="{{ $field }}" type="{{ $field === 'deceased_birth_date' ? 'date' : 'text' }}" name="{{ $field }}" value="{{ old($field) }}" @required(in_array($field, ['deceased_name', 'deceased_bin_binti', 'deceased_nik', 'deceased_birth_place', 'deceased_birth_date'])) class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                        @error($field) <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                @endforeach
                <div class="md:col-span-2">
                    <label for="deceased_address" class="block text-sm font-medium text-gray-700">Alamat <span class="text-red-500">*</span></label>
                    <textarea id="deceased_address" name="deceased_address" rows="3" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">{{ old('deceased_address') }}</textarea>
                    @error('deceased_address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="border-b border-gray-200 pb-3 text-base font-semibold text-gray-900">Informasi Kematian</h2>
            <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="death_date" class="block text-sm font-medium text-gray-700">Tanggal Wafat <span class="text-red-500">*</span></label>
                    <input id="death_date" type="text" name="death_date" value="{{ old('death_date') }}" placeholder="dd/mm/yyyy" inputmode="numeric" pattern="\d{2}/\d{2}/\d{4}" data-date-dmy required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                </div>
                <div>
                    <label for="death_place" class="block text-sm font-medium text-gray-700">Tempat Wafat</label>
                    <input id="death_place" type="text" name="death_place" value="{{ old('death_place') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                </div>
            </div>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="border-b border-gray-200 pb-3 text-base font-semibold text-gray-900">Informasi Surat</h2>
            <div class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="document_number" class="block text-sm font-medium text-gray-700">Nomor Surat <span class="text-red-500">*</span></label>
                    <input id="document_number" type="text" name="document_number" value="{{ old('document_number') }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                </div>
                <div>
                    <label for="issue_date" class="block text-sm font-medium text-gray-700">Tanggal Surat <span class="text-red-500">*</span></label>
                    <input id="issue_date" type="text" name="issue_date" value="{{ old('issue_date', now()->format('d/m/Y')) }}" placeholder="dd/mm/yyyy" inputmode="numeric" pattern="\d{2}/\d{2}/\d{4}" data-date-dmy required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                </div>
                <div>
                    <label for="signed_by" class="block text-sm font-medium text-gray-700">Ditandatangani Oleh <span class="text-red-500">*</span></label>
                    <input id="signed_by" type="text" name="signed_by" value="{{ old('signed_by') }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                </div>
                <div>
                    <label for="signed_by_position" class="block text-sm font-medium text-gray-700">Jabatan <span class="text-red-500">*</span></label>
                    <input id="signed_by_position" type="text" name="signed_by_position" value="{{ old('signed_by_position') }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                </div>
            </div>
        </section>

        <div class="flex justify-end gap-3">
            <a href="{{ route('n6.manual.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700">Batal</a>
            <button type="submit" class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Simpan N6</button>
        </div>
    </form>
</div>
@endsection
