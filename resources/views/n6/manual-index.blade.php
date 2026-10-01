@extends('layouts.app')

@section('title', 'N6 Manual')

@section('content')
<div class="mx-auto max-w-6xl">
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-medium text-blue-600">Modul Dokumen</p>
            <h1 class="mt-1 text-2xl font-bold text-gray-900">N6 Manual</h1>
            <p class="mt-1 text-sm text-gray-500">Buat surat keterangan kematian berdasarkan data dan berkas fisik almarhum.</p>
        </div>
        <a href="{{ route('n6.manual.create') }}" class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">+ Buat N6 Baru</a>
    </div>

    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-200 bg-gray-50 px-5 py-4">
            <h2 class="font-semibold text-gray-900">Riwayat N6</h2>
            <p class="mt-1 text-xs text-gray-500">Data anak atau pendaftaran pernikahan tidak diperlukan pada modul ini.</p>
        </div>
        <div class="divide-y divide-gray-100">
            @forelse($documents as $document)
                <div class="flex flex-col gap-4 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $document->document_number }}</p>
                        <p class="mt-1 text-sm text-gray-600">Almarhum: {{ $document->deceased_name }}</p>
                        <p class="mt-1 text-xs text-gray-500">Tanggal wafat: {{ $document->death_date?->format('d-m-Y') ?? '-' }}</p>
                    </div>
                    <a href="{{ route('n6.show', $document) }}" class="inline-flex items-center justify-center rounded-lg border border-blue-300 px-4 py-2.5 text-sm font-semibold text-blue-700 hover:bg-blue-50">Lihat N6</a>
                </div>
            @empty
                <div class="px-5 py-10 text-center text-sm text-gray-500">Belum ada dokumen N6.</div>
            @endforelse
        </div>
        @if($documents->hasPages())
            <div class="border-t border-gray-200 px-5 py-4">{{ $documents->links() }}</div>
        @endif
    </div>
</div>
@endsection
