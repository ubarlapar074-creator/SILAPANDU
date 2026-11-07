@extends('layouts.app')

@section('title', 'Dashboard Utama')
@section('breadcrumb', 'Dashboard Utama')

@section('content')
@php
    $activeType = request('type');
    $activeType = in_array($activeType, ['kantor', 'bedol', 'andon'], true) ? $activeType : 'all';

    $total = (int) ($stats['total'] ?? 0);
    $percent = static fn (int $value): int => $total > 0 ? (int) round($value / $total * 100) : 0;

    $serviceBadges = [
        'kantor' => ['label' => 'Nikah Kantor', 'class' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20'],
        'bedol' => ['label' => 'Nikah Bedolan', 'class' => 'bg-amber-500/10 text-amber-400 border border-amber-500/20'],
        'andon' => ['label' => 'Andon Nikah', 'class' => 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20'],
    ];
@endphp

<div id="view-dashboard" class="p-6 space-y-6">

    @if (session('success'))
        <div class="rounded-2xl border border-emerald-500/30 bg-emerald-950/50 px-4 py-3 text-sm text-emerald-300 flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Hero Welcome Banner --}}
    <div class="relative rounded-3xl bg-gradient-to-r from-slate-900 via-slate-900 to-emerald-950 p-6 sm:p-8 border border-slate-800 shadow-2xl overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 space-y-2 max-w-2xl">
            <span class="px-3 py-1 rounded-full text-[11px] font-extrabold uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                SIMKAH &amp; Sistem Desa Terintegrasi
            </span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-white">Selamat Datang di Aplikasi SILAPANDU</h2>
            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                Pusat kendali layanan pencatatan pernikahan, penerbitan surat rekomendasi (Andon Nikah), serta pengelolaan formulir N1–N6 Desa.
            </p>
        </div>
    </div>

    {{-- KPI Metric Overview Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Total Pendaftaran Bulan Ini --}}
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-slate-400 text-xs font-semibold">Total Pendaftaran ({{ now()->translatedFormat('M') }})</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center justify-center">
                    <i class="fa-solid fa-receipt"></i>
                </div>
            </div>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-extrabold text-white">{{ $stats['total'] ?? 0 }}</span>
                <span class="text-xs font-bold text-emerald-400 flex items-center gap-1">
                    <i class="fa-solid fa-arrow-trend-up"></i> Bulan Ini
                </span>
            </div>
            <p class="text-[11px] text-slate-500">Seluruh pendaftaran masuk bulan berjalan</p>
        </div>

        {{-- Nikah Kantor --}}
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-slate-400 text-xs font-semibold">Nikah Kantor KUA</span>
                <div class="w-9 h-9 rounded-xl bg-blue-500/10 border border-blue-500/30 text-blue-400 flex items-center justify-center">
                    <i class="fa-solid fa-building"></i>
                </div>
            </div>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-extrabold text-white">{{ $stats['kantor'] ?? 0 }}</span>
                <span class="text-xs font-semibold text-slate-400">{{ $percent((int) ($stats['kantor'] ?? 0)) }}% dari total</span>
            </div>
            <p class="text-[11px] text-slate-500">Kelengkapan Dokumen</p>
        </div>

        {{-- Nikah Bedolan --}}
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-slate-400 text-xs font-semibold">Nikah Bedolan (Luar)</span>
                <div class="w-9 h-9 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-400 flex items-center justify-center">
                    <i class="fa-solid fa-house-user"></i>
                </div>
            </div>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-extrabold text-white">{{ $stats['bedol'] ?? 0 }}</span>
                <span class="text-xs font-semibold text-amber-400">{{ $percent((int) ($stats['bedol'] ?? 0)) }}% dari total</span>
            </div>
            <p class="text-[11px] text-slate-500">Di Kediaman Pemohon / Gedung</p>
        </div>

        {{-- Andon Nikah / Numpang Nikah --}}
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-slate-400 text-xs font-semibold">Rekomendasi Andon</span>
                <div class="w-9 h-9 rounded-xl bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 flex items-center justify-center">
                    <i class="fa-solid fa-paper-plane"></i>
                </div>
            </div>
            <div class="flex items-baseline justify-between">
                <span class="text-2xl font-extrabold text-white">{{ $stats['andon'] ?? 0 }}</span>
                <span class="text-xs font-semibold text-cyan-400">{{ $percent((int) ($stats['andon'] ?? 0)) }}% dari total</span>
            </div>
            <p class="text-[11px] text-slate-500">Surat Rekomendasi Luar Desa</p>
        </div>

    </div>

    {{-- Quick Action Cards (Entry Shortcuts) --}}
    <div class="space-y-3">
        <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider">Akses Cepat Pendaftaran Baru</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            <form method="POST" action="{{ route('registration.start') }}" class="h-full">
                @csrf
                <input type="hidden" name="service_type" value="kantor">
                <button type="submit" class="group w-full h-full text-left p-5 rounded-2xl bg-slate-900/80 hover:bg-slate-800/90 border border-slate-800 hover:border-emerald-500/50 cursor-pointer transition-all space-y-3 block">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-building-circle-check"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-white text-sm">1. Nikah Kantor</h4>
                        <p class="text-xs text-slate-400 mt-1">Akad dilaksanakan di Balai KUA pada jam kerja.</p>
                    </div>
                    <span class="text-xs text-emerald-400 font-bold inline-flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                        Buat Permohonan <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </span>
                </button>
            </form>

            <form method="POST" action="{{ route('registration.start') }}" class="h-full">
                @csrf
                <input type="hidden" name="service_type" value="bedol">
                <button type="submit" class="group w-full h-full text-left p-5 rounded-2xl bg-slate-900/80 hover:bg-slate-800/90 border border-slate-800 hover:border-amber-500/50 cursor-pointer transition-all space-y-3 block">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-house-chimney-window"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-white text-sm">2. Nikah Bedolan</h4>
                        <p class="text-xs text-slate-400 mt-1">Akad diluar Balai KUA (Rumah, Masjid, Gedung).</p>
                    </div>
                    <span class="text-xs text-amber-400 font-bold inline-flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                        Buat Permohonan <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </span>
                </button>
            </form>

            <form method="POST" action="{{ route('registration.start') }}" class="h-full">
                @csrf
                <input type="hidden" name="service_type" value="andon">
                <button type="submit" class="group w-full h-full text-left p-5 rounded-2xl bg-slate-900/80 hover:bg-slate-800/90 border border-slate-800 hover:border-cyan-500/50 cursor-pointer transition-all space-y-3 block">
                    <div class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-lg group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-stamp"></i>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-white text-sm">3. Andon / Rekomendasi</h4>
                        <p class="text-xs text-slate-400 mt-1">Surat Numpang Nikah untuk warga keluar wilayah.</p>
                    </div>
                    <span class="text-xs text-cyan-400 font-bold inline-flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                        Buat Surat <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </span>
                </button>
            </form>

        </div>
    </div>

    {{-- Recent Registrations Table --}}
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 space-y-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="font-extrabold text-white text-base">Pendaftaran Nikah Terbaru</h3>
                <p class="text-xs text-slate-400">Daftar permohonan masuk yang perlu diperiksa atau dicetak formulirnya.</p>
            </div>

            {{-- Table Filters (Server-side via ?type=) --}}
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard') }}"
                   @class([
                       'px-3 py-1.5 rounded-xl bg-slate-800 text-xs font-semibold hover:bg-slate-700',
                       'text-white ring-1 ring-emerald-500/50' => $activeType === 'all',
                       'text-slate-200' => $activeType !== 'all',
                   ])>Semua</a>
                <a href="{{ route('dashboard', ['type' => 'kantor']) }}"
                   @class([
                       'px-3 py-1.5 rounded-xl bg-slate-800 text-xs font-semibold hover:bg-slate-700',
                       'text-emerald-400 ring-1 ring-emerald-500/50' => $activeType === 'kantor',
                       'text-slate-200' => $activeType !== 'kantor',
                   ])>Nikah Kantor</a>
                <a href="{{ route('dashboard', ['type' => 'bedol']) }}"
                   @class([
                       'px-3 py-1.5 rounded-xl bg-slate-800 text-xs font-semibold hover:bg-slate-700',
                       'text-amber-400 ring-1 ring-amber-500/50' => $activeType === 'bedol',
                       'text-slate-200' => $activeType !== 'bedol',
                   ])>Bedolan</a>
                <a href="{{ route('dashboard', ['type' => 'andon']) }}"
                   @class([
                       'px-3 py-1.5 rounded-xl bg-slate-800 text-xs font-semibold hover:bg-slate-700',
                       'text-cyan-400 ring-1 ring-cyan-500/50' => $activeType === 'andon',
                       'text-slate-200' => $activeType !== 'andon',
                   ])>Andon</a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 text-slate-400 font-semibold border-b border-slate-800">
                    <tr>
                        <th class="p-3.5">Nomor Surat</th>
                        <th class="p-3.5">Nama Pemohon (L/P)</th>
                        <th class="p-3.5">Layanan</th>
                        <th class="p-3.5">Tgl Akad / Rencana</th>
                        <th class="p-3.5">Status Berkas</th>
                        <th class="p-3.5 rounded-r-xl text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-medium">
                    @forelse ($registrations as $reg)
                        @php
                            $priaParty = $reg->parties->where('role', 'calon_pria')->first();
                            $wanitaParty = $reg->parties->where('role', 'calon_wanita')->first();
                            $priaName = $priaParty?->nama_sementara ?? $priaParty?->warga?->nama_lengkap ?? 'Calon Pria';
                            $wanitaName = $wanitaParty?->nama_sementara ?? $wanitaParty?->warga?->nama_lengkap ?? 'Calon Wanita';
                            $isLengkap = ($reg->step >= 4) || in_array($reg->status, ['lengkap', 'valid', 'selesai'], true);
                            $badge = $serviceBadges[$reg->service_type] ?? ['label' => 'Tidak diketahui', 'class' => 'bg-slate-800 text-slate-300 border border-slate-700'];
                        @endphp
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="p-3.5 font-mono text-emerald-400">{{ $reg->nomor_surat ?? $reg->registration_number }}</td>
                            <td class="p-3.5">
                                <div class="font-bold text-white">{{ $priaName }}</div>
                                <div class="text-[10px] text-slate-400">&amp; {{ $wanitaName }}</div>
                            </td>
                            <td class="p-3.5">
                                <span class="px-2.5 py-1 rounded-full font-bold {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                            </td>
                            <td class="p-3.5">
                                @if ($reg->tanggal_akad)
                                    {{ $reg->tanggal_akad->translatedFormat('d M Y') }}
                                @else
                                    <span class="text-slate-500 italic">Belum dijadwalkan</span>
                                @endif
                            </td>
                            <td class="p-3.5">
                                @if ($isLengkap)
                                    <span class="px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 font-bold">Lengkap</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-400 font-bold">Belum Lengkap</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-right space-x-2">
                                <a href="{{ route('registration.dashboard', $reg->id) }}" title="Buka Dashboard Registrasi"
                                   class="p-2 inline-block rounded-lg bg-emerald-600/20 text-emerald-400 hover:bg-emerald-600/30">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <a href="{{ route('registration.review', $reg->id) }}" title="Review &amp; Cetak Surat"
                                   class="p-2 inline-block rounded-lg bg-slate-800 text-slate-300 hover:bg-slate-700">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                                <form method="POST" action="{{ route('registration.destroy', $reg->id) }}" class="inline-block"
                                      onsubmit="return confirm('Hapus pendaftaran {{ $reg->nomor_surat ?? $reg->registration_number }}? Seluruh data calon pengantin, wali, saksi, dan jadwal akad ikut terhapus dan tidak bisa dikembalikan.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" title="Hapus Pendaftaran"
                                            class="p-2 rounded-lg bg-red-600/15 text-red-400 hover:bg-red-600/30">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-slate-500">
                                <i class="fa-solid fa-box-archive text-2xl mb-2 block text-slate-600"></i>
                                @if ($activeType !== 'all')
                                    Belum ada pendaftaran dengan tipe "{{ $serviceBadges[$activeType]['label'] ?? strtoupper($activeType) }}".
                                @else
                                    Belum ada pendaftaran nikah yang tercatat.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
