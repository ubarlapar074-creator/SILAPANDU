@extends('layouts.app')

@push('styles')
<style>
    .glass-panel { background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.08); }
    .glass-card { background: rgba(30, 41, 59, 0.6); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.07); }
    .glass-card:hover { border-color: rgba(16, 185, 129, 0.3); background: rgba(30, 41, 59, 0.8); }
    .progress-smooth { transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1); }
</style>
@endpush

@section('title', 'Dashboard Pendaftaran')

@section('content')
<div class="dark bg-slate-900 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8 flex-grow">

        {{-- Header & Stepper --}}
        @include('registration.partials.dashboard-header')

        {{-- Peringatan N6: ayah calon wanita meninggal tapi data kematian belum lengkap --}}
        @if($n6BelumLengkap)
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 rounded-2xl border border-amber-500/40 bg-amber-500/10 px-5 py-4">
            <div class="flex items-start gap-3">
                <span class="text-amber-400 text-lg leading-none">&#9888;</span>
                <div>
                    <p class="text-sm font-bold text-amber-300">Form N6 (Surat Keterangan Kematian) wajib dilengkapi</p>
                    <p class="text-xs text-amber-200/80 mt-1">Ayah kandung calon wanita berstatus meninggal, tetapi tanggal dan/atau tempat meninggal belum diisi. Lengkapi pada Form Calon Wanita agar N6 dapat dicetak dan data dapat difinalisasi.</p>
                </div>
            </div>
            <a href="{{ route('registration.form.wanita', $registration->id) }}"
               class="shrink-0 px-4 py-2 rounded-xl bg-amber-500 text-slate-900 font-bold text-xs hover:bg-amber-400 transition">
                Lengkapi Data Wanita &rarr;
            </a>
        </div>
        @endif

        {{-- Form Ringkas Jadwal Akad (Hanya untuk Nikah Kantor/Bedol) --}}
        @if($registration->service_type !== 'andon')
            @include('registration.partials.form-akad-ringkas')
        @endif

        {{-- Grid: Cards (Left) + Action Sidebar (Right) --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-8 space-y-6">
                @include('registration.partials.card-pria')
                @include('registration.partials.card-wanita')
                @include('registration.partials.card-wali')
                @include('registration.partials.card-pusat-cetak')
            </div>
            
            <div class="lg:col-span-4 space-y-6">
                @include('registration.partials.dashboard-sidebar')
            </div>
        </div>

    </div>
</div>
@endsection