@extends('layouts.app')

@section('title', 'Profil Desa')

@section('content')
<div class="mx-auto max-w-5xl">
    <div class="mb-6">
        <p class="text-sm font-medium text-emerald-600">Pengaturan</p>
        <h1 class="mt-1 text-2xl font-bold text-gray-900">Profil Desa</h1>
        <p class="mt-1 text-sm text-gray-500">Data ini akan digunakan pada kop surat dan tanda tangan dokumen.</p>
    </div>

    <form action="{{ route('settings.village-profile.update') }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold text-gray-900">Identitas Desa</h2>
            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                @foreach([
                    'village_name' => 'Nama Desa/Kelurahan',
                    'district_name' => 'Kecamatan',
                    'regency_name' => 'Kabupaten/Kota',
                    'office_address' => 'Alamat Kantor Desa',
                ] as $field => $label)
                    <div class="{{ $field === 'office_address' ? 'md:col-span-2' : '' }}">
                        <label for="{{ $field }}" class="block text-sm font-medium text-gray-700">{{ $label }}</label>
                        <input id="{{ $field }}" type="text" name="{{ $field }}" value="{{ old($field, $profile?->{$field}) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                        @error($field) <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                @endforeach
            </div>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold text-gray-900">Pejabat Penandatangan</h2>
            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="village_head_name" class="block text-sm font-medium text-gray-700">Nama Kepala Desa</label>
                    <input id="village_head_name" type="text" name="village_head_name" value="{{ old('village_head_name', $profile?->village_head_name) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                </div>
                <div>
                    <label for="village_head_id" class="block text-sm font-medium text-gray-700">NIP/NIK Kepala Desa</label>
                    <input id="village_head_id" type="text" name="village_head_id" value="{{ old('village_head_id', $profile?->village_head_id) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                </div>
                <div>
                    <label for="service_officer_name" class="block text-sm font-medium text-gray-700">Nama Kasi Pelayanan</label>
                    <input id="service_officer_name" type="text" name="service_officer_name" value="{{ old('service_officer_name', $profile?->service_officer_name) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                </div>
                <div>
                    <label for="service_officer_id" class="block text-sm font-medium text-gray-700">NIP/NIK Kasi Pelayanan</label>
                    <input id="service_officer_id" type="text" name="service_officer_id" value="{{ old('service_officer_id', $profile?->service_officer_id) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                </div>
            </div>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-base font-semibold text-gray-900">Kepala KUA <span class="text-xs font-normal text-slate-400">— untuk TTD kiri surat kehendak nikah (N2)</span></h2>
            <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label for="kua_head_name" class="block text-sm font-medium text-gray-700">Nama Kepala KUA</label>
                    <input id="kua_head_name" type="text" name="kua_head_name" value="{{ old('kua_head_name', $profile?->kua_head_name) }}" placeholder="Contoh: H. Ahmad Fauzi, S.Ag" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                    @error('kua_head_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="kua_head_nip" class="block text-sm font-medium text-gray-700">NIP Kepala KUA</label>
                    <input id="kua_head_nip" type="text" name="kua_head_nip" value="{{ old('kua_head_nip', $profile?->kua_head_nip) }}" placeholder="18 digit NIP" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                    @error('kua_head_nip') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
            <p class="mt-2 text-xs text-slate-500">Jika kosong, TTD kiri N2 akan tampil tanpa nama/NIP (garis kosong).</p>
        </section>

        <div class="flex items-center justify-between">
            <a href="{{ route('settings.document-numbers.edit') }}" class="text-sm font-medium text-emerald-700 hover:underline">Kelola penomoran → Penomoran Surat</a>
            <button type="submit" class="rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">Simpan Profil Desa</button>
        </div>
    </form>
</div>
@endsection
