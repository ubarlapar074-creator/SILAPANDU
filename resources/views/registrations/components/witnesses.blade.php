<div class="space-y-6 text-sm" x-data="{ count: {{ is_array(old('witnesses')) ? count(old('witnesses')) : 0 }} }">
    <div class="flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50 p-4">
        <div>
            <h4 class="font-bold text-slate-800">Saksi Nikah <span class="font-normal text-slate-500">(Opsional)</span></h4>
            <p class="mt-0.5 text-xs text-slate-500">Boleh kosong saat simpan awal; lengkapi mendekati hari H.</p>
        </div>

        <button type="button" @click="if (count < 3) count++" x-show="count < 3" class="rounded-md bg-emerald-600 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-emerald-700">
            + Tambah Saksi
        </button>
    </div>
    <div x-show="count === 0" class="rounded-lg border border-dashed border-slate-300 bg-white p-4 text-center text-xs text-slate-500">Belum ada saksi — klik Tambah Saksi jika diperlukan.</div>

    @for ($index = 0; $index < 3; $index++)
        <div
            x-show="count >= {{ $index + 1 }}" x-cloak
            x-data="{ gender: '{{ old("witnesses.$index.gender", 'L') }}' }"
            class="space-y-4 rounded-lg border border-slate-200 bg-white p-4"
        >
            <div class="flex items-center justify-between">
                <h5 class="text-xs font-bold uppercase tracking-wider text-emerald-700">
                    Saksi Nikah {{ $index + 1 }}{{ $index === 2 ? ' (Cadangan)' : '' }}
                </h5>
                <button type="button" @click="count = {{ $index }}" class="text-xs text-red-500 hover:underline">Hapus</button>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="block font-medium text-slate-700">Jenis Kelamin</label>
                    <select name="witnesses[{{ $index }}][gender]" x-model="gender" :disabled="count < {{ $index + 1 }}" class="mt-1 block w-full rounded-md border-slate-300 text-sm">
                        <option value="">-- Pilih --</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>

                <div>
                    <label class="block font-medium text-slate-700">NIK</label>
                    <input type="text" name="witnesses[{{ $index }}][nik]" value="{{ old("witnesses.$index.nik") }}" maxlength="16" inputmode="numeric" :disabled="count < {{ $index + 1 }}" class="mt-1 block w-full rounded-md border-slate-300 text-sm" placeholder="16 digit NIK">
                </div>

                <div>
                    <label class="block font-medium text-slate-700">Nama Lengkap</label>
                    <input type="text" name="witnesses[{{ $index }}][name]" value="{{ old("witnesses.$index.name") }}" :disabled="count < {{ $index + 1 }}" class="mt-1 block w-full rounded-md border-slate-300 text-sm" placeholder="Nama lengkap saksi">
                </div>

                <div>
                    <label class="block font-medium text-slate-700">Tempat Lahir</label>
                    <input type="text" name="witnesses[{{ $index }}][birth_place]" value="{{ old("witnesses.$index.birth_place") }}" :disabled="count < {{ $index + 1 }}" class="mt-1 block w-full rounded-md border-slate-300 text-sm">
                </div>

                <div>
                    <label class="block font-medium text-slate-700">Tanggal Lahir</label>
                    <input type="date" name="witnesses[{{ $index }}][birth_date]" value="{{ old("witnesses.$index.birth_date") }}" :disabled="count < {{ $index + 1 }}" class="mt-1 block w-full rounded-md border-slate-300 text-sm">
                </div>

                <div>
                    <label class="block font-medium text-slate-700">Kewarganegaraan</label>
                    <input type="text" name="witnesses[{{ $index }}][nationality]" value="{{ old("witnesses.$index.nationality", 'WNI') }}" @if ($index === 2) :disabled="count < 3" @endif class="mt-1 block w-full rounded-md border-slate-300 text-sm" placeholder="WNI / WNA">
                </div>

                <div>
                    <label class="block font-medium text-slate-700">Agama</label>
                    <select name="witnesses[{{ $index }}][religion]" @if ($index === 2) :disabled="count < 3" @endif class="mt-1 block w-full rounded-md border-slate-300 text-sm">
                        <option value="">-- Pilih Agama --</option>
                        @foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $religion)
                            <option value="{{ $religion }}" @selected(old("witnesses.$index.religion") === $religion)>{{ $religion }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-medium text-slate-700">Nomor HP</label>
                    <input type="text" name="witnesses[{{ $index }}][phone]" value="{{ old("witnesses.$index.phone") }}" @if ($index === 2) :disabled="count < 3" @endif class="mt-1 block w-full rounded-md border-slate-300 text-sm" placeholder="08xxxxxxxxxx">
                </div>

                <div>
                    <label class="block font-medium text-slate-700">Status Perkawinan</label>
                    <select name="witnesses[{{ $index }}][marital_status]" :disabled="count < {{ $index + 1 }}" class="mt-1 block w-full rounded-md border-slate-300 text-sm">
                        <option value="">-- Pilih Status --</option>
                        @foreach (\App\Support\MaritalStatus::dropdownOptions() as $value => $label)
                            <option value="{{ $value }}" @selected(in_array(old("witnesses.$index.marital_status"), [$value, str_replace('_',' ',$value)], true))>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-medium text-slate-700">Pendidikan</label>
                    <input type="text" name="witnesses[{{ $index }}][education]" value="{{ old("witnesses.$index.education") }}" @if ($index === 2) :disabled="count < 3" @endif class="mt-1 block w-full rounded-md border-slate-300 text-sm">
                </div>

                <div>
                    <label class="block font-medium text-slate-700">Pekerjaan</label>
                    <input type="text" name="witnesses[{{ $index }}][occupation]" value="{{ old("witnesses.$index.occupation") }}" @if ($index === 2) :disabled="count < 3" @endif class="mt-1 block w-full rounded-md border-slate-300 text-sm">
                </div>

                <div class="md:col-span-2">
                    <label class="block font-medium text-slate-700">Jalan / Blok / Dusun</label>
                    <textarea name="witnesses[{{ $index }}][address]" rows="2" :disabled="count < {{ $index + 1 }}" class="mt-1 block w-full rounded-md border-slate-300 text-sm">{{ old("witnesses.$index.address") }}</textarea>
                </div>
                <div>
                    <label class="block font-medium text-slate-700">RT</label>
                    <input type="text" name="witnesses[{{ $index }}][address_rt]" value="{{ old("witnesses.$index.address_rt", '') }}" maxlength="3" placeholder="01" :disabled="count < {{ $index + 1 }}" class="mt-1 block w-full rounded-md border-slate-300 text-sm">
                </div>
                <div>
                    <label class="block font-medium text-slate-700">RW</label>
                    <input type="text" name="witnesses[{{ $index }}][address_rw]" value="{{ old("witnesses.$index.address_rw", '') }}" maxlength="3" placeholder="02" :disabled="count < {{ $index + 1 }}" class="mt-1 block w-full rounded-md border-slate-300 text-sm">
                </div>

                <div class="rounded-lg border border-slate-200 bg-slate-50 p-3 md:col-span-2">
                    <h6 class="mb-3 text-xs font-bold uppercase tracking-wider text-slate-700">Wilayah Domisili Saksi</h6>
                    @include('components.region-selector', [
                        'prefix' => 'witnesses[' . $index . ']',
                        'idPrefix' => 'witnesses_' . $index,
                        'namePrefix' => 'witnesses[' . $index . ']',
                        'provinces' => $provinces,
                        'selectedProvince' => old("witnesses.$index.province_id", ''),
                        'selectedRegency' => old("witnesses.$index.regency_id", ''),
                        'selectedDistrict' => old("witnesses.$index.district_id", ''),
                        'selectedVillage' => old("witnesses.$index.village_id", ''),
                        'disabledExpression' => 'count < ' . ($index + 1),
                    ])
                </div>
            </div>
        </div>
    @endfor
</div>