@extends('layouts.app')

@section('title', 'Nomor Surat')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Nomor Surat — Pendaftaran {{ $registration->registration_number }}</h1>
        <p class="mt-1 text-sm text-gray-500">
            Saran dari <a href="{{ route('settings.document-numbers.edit') }}" class="font-medium text-emerald-700 hover:underline">Pengaturan → Penomoran Surat</a>. Tetap bisa override manual untuk sinkron buku fisik (loncat/selip).
        </p>
        @php $fallbackName = $docDefaults['signed_by'] ?? null; @endphp
        <p class="mt-1 text-xs text-gray-400">Format global: <span class="font-mono">[Urut]/[Klasifikasi]/[Desa-Kec]/[Bulan]/[Tahun]</span> — berlaku sama untuk N1–N6. Penandatangan fallback ke <a href="{{ route('settings.village-profile.edit') }}" class="underline">Profil Desa & Penandatangan</a>@if($fallbackName) — <span class="font-medium">{{ $fallbackName }}</span>@endif.</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('registrations.documents.numbers.update', $registration) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        @foreach($documentTypes as $documentType)
            @php($document = $documents->get($documentType))
            @php($suggestion = $suggestions[$documentType] ?? '')
            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                <div class="mb-4 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="font-semibold text-gray-900">Dokumen {{ $documentType }}</h2>
                        <p class="text-xs text-gray-500">
                            @switch($documentType)
                                @case('N1') Pengantar Nikah @break
                                @case('N2') Permohonan Kehendak Nikah @break
                                @case('N4') Persetujuan Calon Pengantin @break
                                @case('N5') Surat Izin Orang Tua @break
                                @case('N6') Surat Keterangan Kematian @break
                            @endswitch
                        </p>
                    </div>
                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700">Saran + Override</span>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label for="number-{{ $documentType }}" class="block text-sm font-medium text-gray-700">Nomor surat <span class="text-red-500">*</span></label>
                        <div class="mt-1 flex gap-2">
                            <input id="number-{{ $documentType }}" type="text" name="documents[{{ $documentType }}][number]" value="{{ old("documents.$documentType.number", $document?->document_number ?: $suggestion) }}" required placeholder="{{ $suggestion }}" class="block w-full rounded-lg border-gray-300 shadow-sm">
                            <button type="button" onclick="document.getElementById('number-{{ $documentType }}').value='{{ $suggestion }}'" class="shrink-0 rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 hover:bg-gray-50">Pakai saran</button>
                        </div>
                        <p class="mt-1 text-xs text-gray-400">Saran: <span class="font-mono">{{ $suggestion }}</span> — dapat diketik ulang agar sesuai buku fisik.</p>
                        @error("documents.$documentType.number") <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="date-{{ $documentType }}" class="block text-sm font-medium text-gray-700">Tanggal surat</label>
                        <input id="date-{{ $documentType }}" type="text" name="documents[{{ $documentType }}][issue_date]" value="{{ old("documents.$documentType.issue_date", $document?->issue_date?->format('d/m/Y')) }}" placeholder="dd/mm/yyyy" inputmode="numeric" pattern="\d{2}/\d{2}/\d{4}" data-date-dmy class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label for="signed-by-{{ $documentType }}" class="block text-sm font-medium text-gray-700">Penandatangan</label>
                        <input id="signed-by-{{ $documentType }}" type="text" name="documents[{{ $documentType }}][signed_by]" value="{{ old("documents.$documentType.signed_by", $document?->signed_by ?? $docDefaults['signed_by'] ?? '') }}" placeholder="{{ $docDefaults['signed_by'] ?? 'Default Pengaturan' }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label for="position-{{ $documentType }}" class="block text-sm font-medium text-gray-700">Jabatan</label>
                        <input id="position-{{ $documentType }}" type="text" name="documents[{{ $documentType }}][signed_by_position]" value="{{ old("documents.$documentType.signed_by_position", $document?->signed_by_position ?? $docDefaults['position'] ?? '') }}" placeholder="{{ $docDefaults['position'] ?? 'Jabatan default' }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm">
                    </div>
                </div>
            </div>
        @endforeach

        <div class="flex justify-end gap-3">
            <a href="{{ route('registrations.detail', $registration) }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700">Batal</a>
            <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Simpan Nomor Surat</button>
        </div>
    </form>
</div>
@endsection
