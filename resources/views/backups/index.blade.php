@extends('layouts.app')

@section('title', 'Cadangan Data')

@section('content')
<div class="mx-auto max-w-5xl">
    <div class="mb-6">
        <p class="text-sm font-medium text-emerald-600">Pemeliharaan</p>
        <h1 class="mt-1 text-2xl font-bold text-gray-900">Cadangan Data</h1>
        <p class="mt-1 text-sm text-gray-500">Buat <span class="font-semibold">Arsip Cadangan Data</span> — file utuh berisi semua pendaftaran & pengaturan, siap dipulihkan kapan pun. Disimpan di <span class="font-mono">storage/app/backups</span> sebagai file <span class="font-mono">.simanis</span> (berbasis SQLite). Restore memvalidasi dulu — data aktif tetap aman jika file rusak.</p>
    </div>

    @if($shouldWarn)
        <div class="mb-6 rounded-xl border border-amber-300 bg-amber-50 p-4">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-sm font-semibold text-amber-900">Belum ada cadangan selama 14 hari</p>
                    <p class="mt-1 text-xs text-amber-800">
                        @if($setting->last_backup_at)
                            Terakhir: {{ $setting->last_backup_at->format('d/m/Y H:i') }} ({{ $setting->last_backup_at->diffForHumans() }}).
                        @else
                            Belum pernah melakukan cadangan. Segera cadangkan data untuk keamanan.
                        @endif
                        Lakukan cadangan sekarang agar data aman.
                    </p>
                </div>
                <div class="flex shrink-0 flex-col gap-2 sm:flex-row">
                    <form action="{{ route('backups.store') }}" method="POST">@csrf<button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Cadangkan Sekarang</button></form>
                    <form action="{{ route('backups.dismiss') }}" method="POST" class="flex items-center gap-2">@csrf
                        <select name="snooze_days" class="rounded-lg border border-amber-300 bg-white px-2 py-2 text-xs font-medium text-amber-800">
                            <option value="14" selected>14 hari</option>
                            <option value="7">7 hari</option>
                            <option value="30">30 hari</option>
                        </select>
                        <button type="submit" class="rounded-lg border border-amber-300 bg-white px-4 py-2 text-sm font-semibold text-amber-800 hover:bg-amber-100">Nanti</button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-2">
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <h2 class="text-sm font-semibold text-gray-900">Status Cadangan</h2>
            <p class="mt-2 text-sm text-gray-600">Terakhir berhasil: <span class="font-semibold">{{ $setting->last_backup_at ? $setting->last_backup_at->format('d/m/Y H:i') : 'Belum pernah' }}</span></p>
            <p class="mt-1 text-xs text-gray-500">Backup gagal tidak mengubah status. "Nanti" menunda pengingat sesuai pilihan (default 14 hari) tanpa memblokir pelayanan.</p>
            <form action="{{ route('backups.store') }}" method="POST" class="mt-4">@csrf<button type="submit" class="rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">Cadangkan Data</button></form>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <h2 class="text-sm font-semibold text-gray-900">Restore Data</h2>
            <p class="mt-1 text-xs text-gray-500">Pilih <span class="font-semibold">Arsip .simanis</span> (arsip lama <span class="font-mono">.sqlite</span> tetap bisa). File divalidasi dulu — data aktif tetap aman jika rusak.</p>
            <form action="{{ route('backups.restore') }}" method="POST" enctype="multipart/form-data" class="mt-3 flex items-center gap-3">
                @csrf
                <input type="file" name="backup_file" accept=".simanis,.sqlite,.db" required class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm">
                <button type="submit" class="shrink-0 rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-900">Restore</button>
            </form>
            @error('backup_file') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="rounded-xl border border-gray-200 bg-white p-5">
        <h2 class="text-sm font-semibold text-gray-900">Daftar Cadangan</h2>
            <p class="mt-1 text-xs text-gray-500">File tidak dihapus otomatis. <span class="font-medium">Unduh Arsip</span> untuk simpan di flashdisk/luar aplikasi. Arsip lama <span class="font-mono">.sqlite</span> tetap tampil.</p>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="text-xs uppercase text-gray-500"><tr><th class="py-2">Nama File</th><th class="py-2">Ukuran</th><th class="py-2">Tanggal</th><th class="py-2"></th></tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($backups as $b)
                        <tr>
                            <td class="py-2 font-mono text-xs" title="Arsip SILAPANDU — hakikat SQLite">{{ $b['name'] }}</td>
                            <td class="py-2 text-xs">{{ number_format($b['size']/1024,1) }} KB</td>
                            <td class="py-2 text-xs">{{ date('d/m/Y H:i', $b['modified']) }}</td>
                            <td class="py-2 text-right"><a href="{{ route('backups.download', $b['name']) }}" class="rounded border border-gray-300 bg-white px-3 py-1 text-xs font-medium hover:bg-gray-50">Unduh Arsip</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-6 text-center text-sm text-gray-400">Belum ada file cadangan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-6 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-xs text-slate-600">
        <p class="font-semibold">Instalasi baru:</p>
        <p class="mt-1">Jika belum pernah cadangan, pengingat 14 hari langsung aktif (dianggap perlu cadangan). Lakukan "Cadangkan Data" untuk menghilangkan warning sampai 14 hari berikutnya.</p>
    </div>
</div>
@endsection
