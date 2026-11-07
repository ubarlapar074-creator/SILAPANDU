@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content')
<div x-data="settingsHandler()" class="max-w-7xl mx-auto py-6 px-4">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Pengaturan Aplikasi</h1>
        <p class="text-sm text-gray-500">Kelola identitas desa, pejabat, format penomoran, dan backup database.</p>
    </div>

    {{-- Notifikasi simpan --}}
    @if (session('success'))
        <div class="mb-6 flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm rounded-xl p-4">
            <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Tab Navigation --}}
    <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex gap-6 overflow-x-auto">
            <button type="button"
                @click="activeTab = 'identitas'"
                :class="activeTab === 'identitas' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                class="pb-3 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition">
                Identitas Desa &amp; Kop Surat
            </button>
            <button type="button"
                @click="activeTab = 'pejabat'"
                :class="activeTab === 'pejabat' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                class="pb-3 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition">
                Pejabat &amp; TTD
            </button>
            <button type="button"
                @click="activeTab = 'penomoran'"
                :class="activeTab === 'penomoran' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                class="pb-3 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition">
                Format Penomoran Surat
            </button>
            <button type="button"
                @click="activeTab = 'backup'"
                :class="activeTab === 'backup' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                class="pb-3 px-1 border-b-2 font-medium text-sm whitespace-nowrap transition">
                Backup &amp; Restore Database
            </button>
        </nav>
    </div>

    {{-- Tab Content --}}
    <div class="space-y-6">
        <div x-show="activeTab === 'identitas'" x-transition>
            @include('settings.partials.tab-identitas')
        </div>
        <div x-show="activeTab === 'pejabat'" x-transition>
            @include('settings.partials.tab-pejabat')
        </div>
        <div x-show="activeTab === 'penomoran'" x-transition>
            @include('settings.partials.tab-penomoran')
        </div>
        <div x-show="activeTab === 'backup'" x-transition>
            @include('settings.partials.tab-backup')
        </div>
    </div>
</div>
@endsection
