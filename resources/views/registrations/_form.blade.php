@php
    $dowryType = old('marriage_dowry_type', $registration?->marriage_dowry_type ?? ($registration?->marriage_dowry ?? '' ? 'LAINNYA' : ''));
    $dowryMoney = old('marriage_dowry_money', $registration?->marriage_dowry_money ?? '');
    $dowryGold = old('marriage_dowry_gold', $registration?->marriage_dowry_gold ?? '');
    $dowryOther = old('marriage_dowry_other', $registration?->marriage_dowry_other ?? ((!$registration?->marriage_dowry_type || $registration?->marriage_dowry_type === 'LAINNYA') ? ($registration?->marriage_dowry ?? '') : ''));
@endphp

<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
    <div>
        <label for="registration_path" class="block text-sm font-medium text-gray-700">Jalur Pendaftaran</label>
        <select id="registration_path" name="registration_path" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            <option value="NIKAH_BEDOL" @selected(old('registration_path', $registration?->registration_path) === 'NIKAH_BEDOL')>Nikah Bedol</option>
            <option value="NIKAH_KANTOR" @selected(old('registration_path', $registration?->registration_path) === 'NIKAH_KANTOR')>Nikah Kantor</option>
            <option value="PEMBUATAN_ANDON_NIKAH" @selected(old('registration_path', $registration?->registration_path) === 'PEMBUATAN_ANDON_NIKAH')>Pembuatan Andon Nikah</option>
        </select>
        @error('registration_path') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="marriage_date" class="block text-sm font-medium text-gray-700">Tanggal Nikah</label>
        <input id="marriage_date" type="text" name="marriage_date" value="{{ old('marriage_date', $registration?->marriage_date ? \Carbon\Carbon::parse($registration?->marriage_date)->format('d/m/Y') : '') }}" placeholder="dd/mm/yyyy" inputmode="numeric" pattern="\d{2}/\d{2}/\d{4}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        @error('marriage_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>
    <div>
        <label for="marriage_time" class="block text-sm font-medium text-gray-700">Waktu Akad</label>
        <input id="marriage_time" type="time" name="marriage_time" value="{{ old('marriage_time', $registration?->marriage_time ? substr($registration?->marriage_time, 0, 5) : '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        @error('marriage_time') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>
    <div class="md:col-span-2" x-data="{ dowryType: '{{ $dowryType }}' }">
        <label for="marriage_dowry_type" class="block text-sm font-medium text-gray-700">Mahar / Mas Kawin</label>
        <select id="marriage_dowry_type" name="marriage_dowry_type" x-model="dowryType" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            <option value="">Pilih jenis mahar</option>
            <option value="UANG_EMAS">Uang + Emas</option>
            <option value="UANG">Uang saja</option>
            <option value="EMAS">Emas saja</option>
            <option value="LAINNYA">Lainnya</option>
        </select>
        <input type="hidden" name="marriage_dowry" value="{{ old('marriage_dowry', $registration?->marriage_dowry ?? '') }}">
        @error('marriage_dowry_type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        <div x-show="dowryType === 'UANG_EMAS' || dowryType === 'UANG'" x-cloak class="mt-3">
            <label for="marriage_dowry_money" class="block text-xs font-medium text-gray-600">Nominal uang</label>
            <input id="marriage_dowry_money" type="text" name="marriage_dowry_money" value="{{ $dowryMoney }}" placeholder="Contoh: Rp 1.000.000" :required="dowryType === 'UANG_EMAS' || dowryType === 'UANG'" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        </div>
        <div x-show="dowryType === 'UANG_EMAS' || dowryType === 'EMAS'" x-cloak class="mt-3">
            <label for="marriage_dowry_gold" class="block text-xs font-medium text-gray-600">Berat emas</label>
            <input id="marriage_dowry_gold" type="text" name="marriage_dowry_gold" value="{{ $dowryGold }}" placeholder="Contoh: 3,5 gram" :required="dowryType === 'UANG_EMAS' || dowryType === 'EMAS'" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        </div>
        <div x-show="dowryType === 'LAINNYA'" x-cloak class="mt-3">
            <label for="marriage_dowry_other" class="block text-xs font-medium text-gray-600">Keterangan mahar</label>
            <input id="marriage_dowry_other" type="text" name="marriage_dowry_other" value="{{ $dowryOther }}" placeholder="Contoh: Seperangkat alat salat" :required="dowryType === 'LAINNYA'" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        </div>
    </div>
    <div class="md:col-span-2">
        <label for="marriage_location_address" class="block text-sm font-medium text-gray-700">Tempat Akad</label>
        <textarea id="marriage_location_address" name="marriage_location_address" rows="2" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('marriage_location_address', $registration?->marriage_location_address ?? '') }}</textarea>
        @error('marriage_location_address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
    </div>
</div>