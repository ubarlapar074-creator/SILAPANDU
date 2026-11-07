@php
    // Data pejabat dikirim dari SettingsController (tabel settings)
    $pejabat = $pejabat ?? [
        'kepala_desa' => ['nama' => '', 'nip' => '', 'ttd' => null],
        'kasi_pelayanan' => ['nama' => '', 'nip' => '', 'ttd' => null],
        'kepala_kua' => ['nama' => '', 'nip' => '', 'ttd' => null],
    ];
@endphp

<div class="bg-white rounded-lg shadow-sm p-5">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">Pejabat &amp; TTD</h3>

    <form method="POST" action="{{ route('settings.update') }}">
        @csrf
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Kepala Desa --}}
        <div class="border border-gray-200 rounded-lg p-4">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Kepala Desa</h4>
            <div class="grid grid-cols-1 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                    <input type="text" name="kepala_desa[nama]" value="{{ $pejabat['kepala_desa']['nama'] }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NIP / NIAPD</label>
                    <input type="text" name="kepala_desa[nip]" value="{{ $pejabat['kepala_desa']['nip'] }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload TTD/Stempel (Optional)</label>
                    <input type="file" name="kepala_dese[ttd]" accept="image/*"
                        class="text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
            </div>
        </div>

        {{-- Kasi Pelayanan --}}
        <div class="border border-gray-200 rounded-lg p-4">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Kasi Pelayanan / Modin</h4>
            <div class="grid grid-cols-1 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                    <input type="text" name="kasi_pelayanan[nama]" value="{{ $pejabat['kasi_pelayanan']['nama'] }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                    <input type="text" name="kasi_pelayanan[nip]" value="{{ $pejabat['kasi_pelayanan']['nip'] }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
                </div>
            </div>
        </div>

        {{-- Kepala KUA --}}
        <div class="border border-gray-200 rounded-lg p-4">
            <h4 class="text-sm font-medium text-gray-700 mb-3">Kepala KUA / Penghulu Wilayah</h4>
            <div class="grid grid-cols-1 gap-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                    <input type="text" name="kepala_kua[nama]" value="{{ $pejabat['kepala_kua']['nama'] }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                    <input type="text" name="kepala_kua[nip]" value="{{ $pejabat['kepala_kua']['nip'] }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
                </div>
            </div>
        </div>
    </div>

        <div class="mt-5 flex justify-end">
            <button type="submit"
                class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold text-sm shadow-md shadow-blue-600/20 transition">
                Simpan Pejabat
            </button>
        </div>
    </form>
</div>
