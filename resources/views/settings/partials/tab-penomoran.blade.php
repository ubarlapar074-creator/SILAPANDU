@php
    $format = $penomoran['format'] ?? \App\Services\RegistrationService::DEFAULT_FORMAT_NOMOR;
    $contoh = $penomoran['contoh'] ?? $format;
@endphp

<div class="bg-white rounded-lg shadow-sm p-5">
    <h3 class="text-lg font-semibold text-gray-800 mb-1">Format Penomoran Surat</h3>
    <p class="text-sm text-gray-500 mb-4">
        Nomor surat disusun dari format di bawah ini. Tulis kode desa/kecamatan Anda langsung pada format,
        lalu letakkan token di posisi nomor urut, bulan, dan tahun.
    </p>

    <form method="POST" action="{{ route('settings.update') }}">
        @csrf
        <div class="mb-4">
            <label for="format_nomor_surat" class="block text-sm font-medium text-gray-700 mb-1">Format Nomor Surat</label>
            <input type="text" id="format_nomor_surat" name="format_nomor_surat"
                value="{{ old('format_nomor_surat', $format) }}" placeholder="475/DS/{NO}/{BLN}/{THN}"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm font-mono">
            @error('format_nomor_surat')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
            <p class="mt-2 text-xs text-gray-500">
                Token: <code class="font-mono bg-gray-100 px-1 rounded">{NO}</code> nomor urut,
                <code class="font-mono bg-gray-100 px-1 rounded">{BLN}</code> bulan Romawi,
                <code class="font-mono bg-gray-100 px-1 rounded">{THN}</code> tahun surat.
                Teks lain dicetak apa adanya. Jika dikosongkan, dipakai format
                <code class="font-mono bg-gray-100 px-1 rounded">{{ \App\Services\RegistrationService::DEFAULT_FORMAT_NOMOR }}</code>.
            </p>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Contoh Nomor Berikutnya</label>
            <div class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-600">
                Output: <span class="font-mono font-medium">{{ $contoh }}</span>
            </div>
            <p class="mt-1 text-xs text-gray-500">
                Nomor yang sudah tersimpan akan disusun ulang mengikuti format ini saat pengaturan disimpan.
            </p>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium text-sm rounded-lg transition">
                Simpan Penomoran
            </button>
        </div>
    </form>
</div>
