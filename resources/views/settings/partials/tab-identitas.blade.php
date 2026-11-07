@php
    // Data identitas dikirim dari SettingsController (tabel settings)
    $identitas = $identitas ?? [
        'logo' => null,
        'kop_surat' => null,
        'nama_desa' => '',
        'kecamatan' => '',
        'kabupaten' => '',
        'alamat_kantor' => '',
    ];
@endphp

<div class="bg-white rounded-lg shadow-sm p-5">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Identitas Desa &amp; Kop Surat</h3>

    {{-- Logo Desa --}}
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Logo Desa</label>
        <div class="flex items-center gap-4">
            @if ($identitas['logo'])
                <img src="{{ asset('storage/' . $identitas['logo']) }}" alt="Logo"
                    class="w-16 h-16 object-cover rounded-lg border border-gray-200">
            @else
                <div class="w-16 h-16 bg-gray-100 border border-gray-200 rounded-lg flex items-center justify-center text-gray-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4-4 4 4 8-8" />
                    </svg>
                </div>
            @endif
            <input type="file" name="logo" accept="image/*"
                class="text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
        </div>
    </div>

    {{-- Kop Surat --}}
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">Kop Surat (Preview)</label>
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-3 bg-gray-50">
            <input type="file" name="kop_surat" accept="image/*"
                class="text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
        </div>
    </div>

    {{-- Form Identitas --}}
    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Desa</label>
            <input type="text" name="nama_desa" value="{{ $identitas['nama_desa'] }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kecamatan</label>
            <input type="text" name="kecamatan" value="{{ $identitas['kecamatan'] }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kabupaten</label>
            <input type="text" name="kabupaten" value="{{ $identitas['kabupaten'] }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Kantor</label>
            <input type="text" name="alamat_kantor" value="{{ $identitas['alamat_kantor'] }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
        </div>
        <div class="md:col-span-2 flex justify-end">
            <button type="submit"
                class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-sm shadow-md shadow-blue-600/20 transition">
                Simpan Identitas Desa
            </button>
        </div>
    </form>
</div>
