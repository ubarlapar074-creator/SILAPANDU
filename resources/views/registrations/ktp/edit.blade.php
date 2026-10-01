@extends('layouts.app')
@section('title', 'Fotokopi KTP — '.$registration->registration_number)
@section('content')
<div class="mx-auto max-w-5xl">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-slate-900">Formulir Fotokopi KTP</h1>
            <p class="mt-1 text-sm text-slate-500">No. {{ $registration->registration_number }} — Upload 8 foto KTP (jpg/png/webp, maks 4MB)</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('registrations.detail', $registration) }}" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Kembali</a>
            <a href="{{ route('registrations.ktp.print', $registration) }}" target="_blank" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black">Cetak Grid</a>
        </div>
    </div>
    @if(session('success'))<div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>@endif
    <form action="{{ route('registrations.ktp.update', $registration) }}" method="POST" enctype="multipart/form-data" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            @foreach($slots as $slot => $label)
            @php $copy = $copies->get($slot); @endphp
            <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                <label class="block text-sm font-semibold text-slate-700">{{ $label }} <span class="text-xs font-normal text-slate-400">({{ $slot }})</span></label>
                <div class="mt-3 flex gap-4">
                    <div class="h-28 w-44 shrink-0 overflow-hidden rounded border border-slate-300 bg-white flex items-center justify-center">
                        @if($copy)
                            <img src="{{ $copy->url() }}" alt="{{ $label }}" class="h-full w-full object-contain">
                        @else
                            <span class="text-xs text-slate-400">Belum ada foto</span>
                        @endif
                    </div>
                    <div class="flex-1">
                        <input type="file" name="ktp[{{ $slot }}]" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm file:mr-3 file:rounded-md file:border-0 file:bg-emerald-600 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-emerald-700">
                        @error('ktp.'.$slot)<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        @if($copy)
                            <button type="submit" form="del-{{ $slot }}" class="mt-2 text-xs text-red-600 hover:underline" onclick="return confirm('Hapus foto {{ $label }}?')">Hapus</button>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-6 flex justify-end">
            <button type="submit" class="rounded-lg bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white shadow hover:bg-emerald-700">Simpan Fotokopi KTP</button>
        </div>
    </form>
    @foreach($slots as $slot => $label)
        @if($copies->has($slot))
        <form id="del-{{ $slot }}" action="{{ route('registrations.ktp.destroy', [$registration, $slot]) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
        @endif
    @endforeach
</div>
@endsection
