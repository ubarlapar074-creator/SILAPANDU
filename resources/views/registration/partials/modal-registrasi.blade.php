{{-- Modal Form Pendaftaran — Reusable, hanya untuk Nikah Bedol & Nikah Kantor --}}
{{-- Tidak untuk Andon (redirect langsung ke form khusus) --}}

<div x-show="openModal"
     x-cloak
     x-transition
     @keydown.escape.window="openModal = false"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4"
     @click.away="openModal = false">

    <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto"
         @click.stop>

        {{-- Modal Header --}}
        <div class="bg-gradient-to-r from-emerald-50 to-blue-50 px-6 py-4 rounded-t-2xl border-b">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-12 h-12">
                    <svg x-show="selectedService === 'bedol'" class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-9 9 9v11a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 18V9h6v9"/>
                    </svg>
                    <svg x-show="selectedService === 'kantor'" class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-14 0h14m-2 0l2 2v-2zM5 21l-2-2v2h2z"/>
                    </svg>
                </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900"
                            x-text="selectedService === 'bedol'
                                ? 'Nikah Bedol — Luar Kantor'
                                : 'Nikah Kantor — Balai Nikah KUA'">
                        </h3>
                        <p class="text-sm text-gray-600"
                           x-text="selectedService === 'bedol'
                               ? 'Pernikahan di luar kantor KUA (rumah, gedung, masjid, dll.)'
                               : 'Pernikahan resmi di kantor KUA kecamatan.'">
                        </p>
                    </div>
                </div>
                <button type="button"
                        @click="openModal = false"
                        class="text-gray-400 hover:text-gray-600 text-2xl font-bold leading-none"
                        aria-label="Tutup">
                    &times;
                </button>
            </div>
        </div>

        <form method="POST" action="{{ route('registration.preview') }}">

        {{-- Modal Body --}}
        <div class="p-6 space-y-5">

            @csrf

            {{-- Hidden fields --}}
            <input type="hidden" name="service_type" :value="serviceType">
            <input type="hidden" name="marriage_dowry_display" :value="dowryDisplay">

            {{-- Field 0: Nama Calon Pengantin --}}
            <div class="space-y-3">
                <h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">0</span>
                    Nama Calon Pengantin
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Calon Pria</label>
                        <input type="text" name="nama_pria" required
                               placeholder="Nama calon pria"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nama Calon Wanita</label>
                        <input type="text" name="nama_wanita" required
                               placeholder="Nama calon wanita"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm">
                    </div>
                </div>
            </div>

            {{-- Field 1: Jadwal Akad --}}
            <div class="space-y-3">
                <h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">1</span>
                    Jadwal Akad Nikah
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal Akad</label>
                        <input type="date" name="tanggal_akad" x-model="tanggalAkad" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Jam Akad</label>
                        <input type="time" name="jam_akad" x-model="jamAkad" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm">
                    </div>
                </div>
            </div>

            {{-- Field 2: Mahar --}}
            <div class="space-y-3">
                <h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">2</span>
                    Mahar
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <div class="md:col-span-1">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Jenis Mahar</label>
                        <select name="jenis_mahar" x-model="jenisMahar"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm">
                            <option value="uang">Uang Tunai</option>
                            <option value="emas">Perhiasan / Emas</option>
                            <option value="lainnya">Lainnya (Alat Shalat, dll)</option>
                        </select>
                    </div>
                    <div class="md:col-span-2" x-show="jenisMahar === 'uang'" x-transition>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Nominal Uang (Rp)</label>
                        <input type="number" name="nominal_uang" x-model="nominalUang" inputmode="numeric"
                               placeholder="Contoh: 5000000"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm">
                    </div>
                    <div class="md:col-span-2" x-show="jenisMahar !== 'uang'" x-transition>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Rincian Mahar</label>
                        <input type="text" name="rincian_mahar" x-model="rincianMahar"
                               placeholder="Contoh: Emas 5 Gram Dibayar Tunai"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm">
                    </div>
                </div>
            </div>

            {{-- Field 3: Tempat & Wilayah --}}
            <div class="space-y-3">
                <h4 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">3</span>
                    Tempat & Wilayah Akad
                </h4>

                {{-- Nikah Bedol: Input alamat manual + dropdown wilayah --}}
                <div x-show="selectedService === 'bedol'" x-transition x-cloak>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Alamat Lengkap Akad</label>
                    <input type="text" name="marriage_location_address" x-model="alamatBedolan"
                           placeholder="Nama Jalan, RT/RW, Dusun, Desa"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm">
                </div>

                {{-- Dropdown wilayah (Provinsi -> Kabupaten -> Kecamatan -> Desa) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Provinsi</label>
                        <select name="province_id" x-model="provinceId" @change="updateRegencies()"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm">
                            <option value="">Pilih Provinsi</option>
                            <template x-for="p in provinces" :key="p.id">
                                <option :value="p.id" x-text="p.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Kabupaten/Kota</label>
                        <select name="regency_id" x-model="regencyId" @change="updateDistricts()"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm"
                                :disabled="!provinceId">
                            <option value="">Pilih Kabupaten/Kota</option>
                            <template x-for="r in filteredRegencies" :key="r.id">
                                <option :value="r.id" x-text="r.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Kecamatan</label>
                        <select name="district_id" x-model="districtId" @change="updateVillages()"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm"
                                :disabled="!regencyId">
                            <option value="">Pilih Kecamatan</option>
                            <template x-for="d in filteredDistricts" :key="d.id">
                                <option :value="d.id" x-text="d.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Desa/Kelurahan</label>
                        <select name="village_id" x-model="villageId"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none text-sm"
                                :disabled="!districtId">
                            <option value="">Pilih Desa/Kelurahan</option>
                            <template x-for="v in filteredVillages" :key="v.id">
                                <option :value="v.id" x-text="v.name"></option>
                            </template>
                        </select>
                    </div>
                </div>

                {{-- Nikah Kantor: KUA label otomatis --}}
                <div x-show="selectedService === 'kantor'" class="bg-blue-50 border border-blue-200 rounded-lg p-3" x-transition x-cloak>
                    <p class="text-sm text-blue-800">
                        <span class="font-medium">KUA Kecamatan:</span>
                        <span class="font-bold" x-text="kuaDisplay"></span>
                    </p>
                    <p class="text-xs text-blue-600 mt-1">
                        Akad akan dilaksanakan di kantor KUA kecamatan yang Anda pilih.
                    </p>
                </div>
            </div>
        </div>

        {{-- Modal Footer --}}
        <div class="p-4 border-t bg-gray-50 rounded-b-2xl flex justify-end gap-3">
            <button type="button"
                    @click="openModal = false"
                    class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition">
                Batal
            </button>
            <button type="submit"
                    class="px-5 py-2 text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg font-medium transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                Lanjut ke Preview
            </button>
        </div>
        </form>
    </div>
</div>
