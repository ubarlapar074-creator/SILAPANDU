@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Edit Data Pendaftaran Nikah</h1>
        <p class="text-sm text-gray-500">No. Pendaftaran: {{ $registration->registration_number }}</p>
    </div>

    <form action="{{ route('registrations.update', $registration->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Panggil file partial form yang berisi seluruh input -->
        @include('registrations._form')

        <div class="flex items-center justify-end gap-4 mt-6">
            <a href="{{ route('registrations.detail', $registration->id) }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                Batal
            </a>
            <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-sm">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection