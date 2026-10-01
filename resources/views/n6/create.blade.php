@extends('layouts.app')

@section('title', 'Pendaftaran Nikah Baru')

@section('content')


{{-- =========================================================
     SUCCESS STATE
========================================================== --}}
@if(session('registration_success'))

    <div class="mb-6">
        <div class="mb-2 flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('dashboard') }}" class="hover:text-emerald-600">Dashboard</a>
            <span>/</span>
            <span class="text-gray-700">Pendaftaran Berhasil</span>
        </div>
    </div>

    <div class="overflow-hidden rounded-xl border border-emerald-200 bg-white shadow-sm">
        <div class="border-b border-emerald-100 bg-emerald-50 px-6 py-8 text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-600 text-white shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <h1 class="mt-5 text-2xl font-bold text-gray-900">
                Pendaftaran Kedua Calon Pengantin Berhasil Dibuat!
            </h1>
            <p class="mx-auto mt-2 max-w-xl text-sm text-gray-600">
                Data pendaftaran kedua calon pengantin telah berhasil disimpan sebagai satu transaksi.
            </p>
        </div>

        <div class="px-6 py-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">No. Pendaftaran</p>
                    <p class="mt-1 text-lg font-bold text-emerald-700">
                        {{ session('registration_success.registration_number') }}
                    </p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Tanggal Nikah</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">
                        {{ session('registration_success.marriage_date') }}
                    </p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Catin Pria</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">
                        {{ session('registration_success.catin_pria') }}
                    </p>
                </div>
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Catin Wanita</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900">
                        {{ session('registration_success.catin_wanita') }}
                    </p>
                </div>
            </div>

            <div class="mt-8 flex flex-col gap-3 border-t border-gray-200 pt-6 sm:flex-row sm:justify-center">
                <a href="{{ session('registration_success.detail_url') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                    Lihat Detail
                </a>
                <a href="{{ session('registration_success.print_url') }}" target="_blank" class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">
                    Cetak Berkas
                </a>
                <a href="{{ route('registrations.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-emerald-300 bg-white px-5 py-2.5 text-sm font-semibold text-emerald-700 shadow-sm transition hover:bg-emerald-50">
                    Pendaftaran Baru
                </a>
            </div>
        </div>
    </div>

@else

{{-- =========================================================
     HEADER
========================================================== --}}
<div class="mb-6">
    <div class="mb-2 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('dashboard') }}" class="hover:text-emerald-600">Dashboard</a>
        <span>/</span>
        <span class="text-gray-700">Pendaftaran Baru</span>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">
        Formulir Pendaftaran Nikah
    </h1>
    <p class="mt-1 text-sm text-gray-500">
        Lengkapi data secara bertahap. Data akan disimpan sebagai satu transaksi pendaftaran.
    </p>
</div>

{{-- =========================================================
     CARD + WIZARD STATE (TOTAL STEP 7)
========================================================== --}}
<div
    x-data="{
        step: {{ old('_step', 1) }},
        totalSteps: 7,

        fatherStatusPria: '{{ old('pria_father_status', 'HIDUP') }}',
        motherStatusPria: '{{ old('pria_mother_status', 'HIDUP') }}',
        fatherStatusWanita: '{{ old('wanita_father_status', 'HIDUP') }}',
        motherStatusWanita: '{{ old('wanita_mother_status', 'HIDUP') }}',

        nextStep() {
            if (this.step < this.totalSteps) {
                this.step++;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },

        prevStep() {
            if (this.step > 1) {
                this.step--;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },

        goToStep(step) {
            if (step >= 1 && step <= this.totalSteps) {
                this.step = step;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }
    }"
    class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
>

    {{-- =====================================================
         STEP INDICATOR
    ====================================================== --}}
    <div class="border-b border-gray-200 bg-gray-50 px-4 py-4 sm:px-6">
        @include('registrations.components.steps')
    </div>

    {{-- =====================================================
         FORM UTAMA
    ====================================================== --}}
    <form action="{{ route('registrations.store') }}" method="POST" class="p-5 sm:p-6 lg:p-8">
        @csrf

        <input type="hidden" name="_step" x-bind:value="step">
        <input type="hidden" name="n6_exists" value="0">
        <input type="hidden" name="registration_id" value="0">

        {{-- STEP 1 — INFORMASI PENDAFTARAN & LOKASI AKAD --}}
        <section x-show="step === 1" x-cloak x-transition.opacity>
            <div class="mb-6">
                <h2 class="text-lg font-bold text-gray-900">Informasi Pendaftaran & Lokasi Akad</h2>
                <p class="mt-1 text-sm text-gray-500">Tentukan jalur, rencana tanggal, mahar, serta lokasi pelaksanaan pernikahan.</p>
            </div>
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label for="registration_path" class="block text-sm font-medium text-gray-700">Jalur Pendaftaran <span class="text-red-500">*</span></label>
                    <select id="registration_path" name="registration_path" required class="mt-1.5 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        <option value="">Pilih Jalur Pendaftaran</option>
                        <option value="DALAM_DESA" @selected(old('registration_path') === 'DALAM_DESA')>Dalam Desa</option>
                        <option value="ANDON" @selected(old('registration_path') === 'ANDON')>Andon Nikah</option>
                    </select>
                    @error('registration_path') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="marriage_date" class="block text-sm font-medium text-gray-700">Rencana Tanggal Nikah <span class="text-red-500">*</span></label>
                    <input id="marriage_date" type="text" name="marriage_date" value="{{ old('marriage_date') }}" placeholder="dd/mm/yyyy" inputmode="numeric" pattern="\d{2}/\d{2}/\d{4}" data-date-dmy required class="mt-1.5 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    @error('marriage_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="marriage_dowry" class="block text-sm font-medium text-gray-700">Mahar / Mas Kawin <span class="text-red-500">*</span></label>
                    <input id="marriage_dowry" type="text" name="marriage_dowry" value="{{ old('marriage_dowry') }}" placeholder="Contoh: Emas 10 gram / Uang Rp 1.000.000" required class="mt-1.5 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                    @error('marriage_dowry') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2">
                    <label for="marriage_location_address" class="block text-sm font-medium text-gray-700">Alamat Detail Tempat Akad <span class="text-red-500">*</span></label>
                    <textarea id="marriage_location_address" name="marriage_location_address" rows="2" placeholder="Contoh: Masjid Al-Ikhlas Desa Sukamaju" required class="mt-1.5 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">{{ old('marriage_location_address') }}</textarea>
                    @error('marriage_location_address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="md:col-span-2 rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <h3 class="mb-3 text-xs font-bold uppercase tracking-wider text-gray-700">Wilayah Administratif Tempat Akad Nikah</h3>
                    @include('components.region-selector', ['prefix' => 'marriage', 'provinces' => $provinces])
                </div>
            </div>
        </section>

        {{-- STEP 2 — CATIN PRIA --}}
        <section x-show="step === 2" x-cloak x-transition.opacity>
            @include('registrations.components.catin-pria')
        </section>

        {{-- STEP 3 — ORANG TUA PRIA --}}
        <section x-show="step === 3" x-cloak x-transition.opacity>
            @include('registrations.components.parents-pria')
        </section>

        {{-- STEP 4 — CATIN WANITA --}}
        <section x-show="step === 4" x-cloak x-transition.opacity>
            @include('registrations.components.catin-wanita')
        </section>

        {{-- STEP 5 — ORANG TUA WANITA --}}
        <section x-show="step === 5" x-cloak x-transition.opacity>
            @include('registrations.components.parents-wanita')
        </section>

        {{-- STEP 6 — WALI & SAKSI --}}
        <section x-show="step === 6" x-cloak x-transition.opacity>
            <div class="space-y-8">
                @include('registrations.components.guardian')
                @include('registrations.components.witnesses')
            </div>
        </section>

        {{-- STEP 7 — DOKUMEN (LANGKAH TERAKHIR) --}}
        <section x-show="step === 7" x-cloak x-transition.opacity>
            @include('registrations.components.documents')
        </section>

        {{-- =================================================
             NAVIGASI WIZARD
        ================================================== --}}
        <div class="mt-8 flex items-center justify-between border-t border-gray-200 pt-6">
            <div>
                <button
                    type="button"
                    x-show="step > 1"
                    x-cloak
                    @click="prevStep()"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    Kembali
                </button>
            </div>

            <div>
                <button
                    type="button"
                    x-show="step < totalSteps"
                    x-cloak
                    @click="nextStep()"
                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                >
                    Selanjutnya
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </button>

                <button
                    type="submit"
                    x-show="step === totalSteps"
                    x-cloak
                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Simpan Pendaftaran
                </button>
            </div>
        </div>

    </form>
</div>

@endif

@endsection


@push('styles')
<style>
    [x-cloak] {
        display: none !important;
    }
</style>
@endpush