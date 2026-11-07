<div x-show="showModal" x-cloak x-transition.opacity
    @click.self="closeModal()"
    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">

    <div @click.stop class="bg-white rounded-lg shadow-xl w-full max-w-3xl mx-2 max-h-[90vh] overflow-y-auto">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800" x-text="isEditing ? 'Edit Data Warga' : 'Tambah Warga Baru'"></h3>
            <button type="button" @click="closeModal()"
                class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form @submit.prevent="submit()" class="p-4 space-y-4">
            @csrf

            {{-- Pesan validasi dari server --}}
            <div x-show="errors.length > 0" x-cloak class="rounded-lg border border-red-200 bg-red-50 p-3">
                <p class="text-sm font-semibold text-red-700">Periksa kembali data berikut:</p>
                <ul class="mt-1 list-disc list-inside text-xs text-red-600 space-y-0.5">
                    <template x-for="(error, index) in errors" :key="index">
                        <li x-text="error"></li>
                    </template>
                </ul>
            </div>

            {{-- NIK --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">NIK (16 Digit)</label>
                <input type="text" x-model="formData.nik" inputmode="numeric" maxlength="16" pattern="\d{16}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm font-mono">
                <p x-show="formData.nik && formData.nik.length !== 16"
                    class="text-xs text-red-600 mt-1">NIK harus terdiri dari 16 digit angka.</p>
            </div>

            {{-- Grid Form --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" x-model="formData.nama_lengkap" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tempat Lahir</label>
                    <input type="text" x-model="formData.tempat_lahir"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Lahir</label>
                    <input type="date" x-model="formData.tgl_lahir"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kelamin</label>
                    <select x-model="formData.jenis_kelamin" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
                        <option value="">Pilih</option>
                        <option value="L">Laki-Laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Agama</label>
                    <select x-model="formData.agama"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
                        <option value="">Pilih</option>
                        <option>Islam</option><option>Kristen</option><option>Katolik</option>
                        <option>Hindu</option><option>Buddha</option><option>Sikh</option>
                        <option>Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pekerjaan</label>
                    <input type="text" x-model="formData.pekerjaan"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                    <input type="text" x-model="formData.alamat"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">RT</label>
                    <input type="text" x-model="formData.rt" maxlength="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">RW</label>
                    <input type="text" x-model="formData.rw" maxlength="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Kawin</label>
                    <select x-model="formData.status_kawin"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
                        <option value="">Pilih</option>
                        <option>Belum Kawin</option><option>Sudah Kawin</option><option>Cerai</option>
                    </select>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" @click="closeModal()"
                    class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                    Batal
                </button>
                <button type="submit" :disabled="saving"
                    class="px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition disabled:opacity-60 disabled:cursor-not-allowed">
                    <span x-text="saving ? 'Menyimpan...' : 'Simpan'">Simpan</span>
                </button>
            </div>
        </form>
    </div>
</div>
