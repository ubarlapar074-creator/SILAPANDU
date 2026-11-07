@php
    $dbPath = config('database.connections.sqlite.database');
    $exists = file_exists($dbPath);
    $size = $exists ? filesize($dbPath) : 0;
    $sizeHuman = $size > 0 ? round($size / 1024, 2) . ' KB' : '0 KB';
    $modified = $exists ? date('d M Y H:i:s', filemtime($dbPath)) : '-';
@endphp

<div x-data="settingsHandler()" class="bg-white rounded-lg shadow-sm p-5">

    <h3 class="text-lg font-semibold text-gray-800 mb-4">Backup &amp; Restore Database</h3>

    {{-- Status Database --}}
    <div class="border border-gray-200 rounded-lg p-4 mb-4 bg-gray-50">
        <div class="flex flex-wrap gap-4 text-sm">
            <div>
                <span class="font-medium text-gray-500">File DB:</span>
                <span class="text-gray-800 font-mono">{{ basename($dbPath) }}</span>
            </div>
            <div>
                <span class="font-medium text-gray-500">Ukuran:</span>
                <span class="text-gray-800">{{ $sizeHuman }}</span>
            </div>
            <div>
                <span class="font-medium text-gray-500">Tgl Modifikasi:</span>
                <span class="text-gray-800">{{ $modified }}</span>
            </div>
        </div>
    </div>

    {{-- Download Backup --}}
    <div class="border border-gray-200 rounded-lg p-4 mb-4">
        <h4 class="text-sm font-medium text-gray-700 mb-3">Download Backup</h4>
        <button type="button" @click="downloadBackup()"
            class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4m0 12l-4 4m0 0l4-4"/>
            </svg>
            <span>Download Backup Database (1-Click)</span>
        </button>
    </div>

    {{-- Restore --}}
    <div class="border border-gray-200 rounded-lg p-4">
        <h4 class="text-sm font-medium text-gray-700 mb-3">Restore Database</h4>
        <div class="flex items-center gap-3">
            <input type="file" x-ref="restoreFile" accept=".sqlite,.sql"
                @change="restoreFile = $event.target.files[0]"
                class="text-sm text-gray-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
            <button type="button" @click="showRestoreModal = true"
                :disabled="!restoreFile"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h5M4 4l6.184 6.184a9 9 0 00.765.765 1 1 0 001.415-.7V4m-3 9h8m-8 0l2-2m-2 2l-2 2m4-2l-2-2"/>
                </svg>
                Restore
            </button>
        </div>
    </div>

    {{-- Restore Confirmation Modal --}}
    <div x-show="showRestoreModal" x-transition
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
        <div @click.outside="showRestoreModal = false"
            class="bg-white rounded-lg shadow-xl w-96 p-5">
            <h4 class="text-lg font-semibold text-gray-800 mb-3">Konfirmasi Restore</h4>
            <p class="text-sm text-gray-600 mb-4">
                Database saat ini akan diganti dengan file yang dipilih.
                Data lama akan hilang (backup otomatis tersedia).
                Ketik <span class="font-mono font-bold">RESTORE</span> untuk konfirmasi.
            </p>
            <input type="text" x-model="restoreConfirmation"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg mb-4 focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm">
            <div class="flex justify-end gap-2">
                <button type="button" @click="showRestoreModal = false"
                    class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Batal
                </button>
                <button type="button" @click="confirmRestore()"
                    :disabled="restoreConfirmation !== 'RESTORE'"
                    class="px-4 py-2 text-white bg-red-600 rounded-lg hover:bg-red-700 disabled:opacity-50">
                    Restore
                </button>
            </div>
        </div>
    </div>
</div>
