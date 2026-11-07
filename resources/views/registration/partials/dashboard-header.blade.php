@php
    $priaNama = $priaParty?->nama_sementara ?? 'Belum Diisi';
    $wanitaNama = $wanitaParty?->nama_sementara ?? $wanitaParty?->warga?->nama_lengkap ?? 'Belum Diisi';
    $isLengkap = $progressPercent >= 100;
@endphp

<div class="glass-panel rounded-3xl p-6 sm:p-8 relative overflow-hidden shadow-2xl border border-slate-800">
    <div class="relative z-10 flex flex-col lg:flex-row lg:items-end justify-between gap-6">
        <div class="space-y-3">
            <div class="flex flex-wrap items-center gap-2 text-xs text-slate-400">
                <a href="{{ route('dashboard') }}" class="hover:text-emerald-400 transition-colors flex items-center gap-1">Dashboard</a>
                <span class="text-slate-600">/</span>
                <span class="text-slate-200 font-semibold">Pendaftaran Nikah</span>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Dashboard Pendaftaran Nikah</h2>
                <span class="px-3 py-1 rounded-full text-xs font-bold {{ $isLengkap ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/10 text-amber-400 border border-amber-500/30' }} flex items-center gap-1.5">
                    {{ $isLengkap ? 'Lengkap' : 'Belum Lengkap' }}
                </span>
            </div>

            <p class="text-xs sm:text-sm text-slate-400 flex flex-wrap items-center gap-y-1 gap-x-4 mb-2">
                <span class="font-mono bg-slate-900/80 px-2.5 py-1 rounded-lg border border-slate-700/80 text-emerald-400 font-semibold flex items-center gap-1">
                    {{ $registration->nomor_surat ?? $registration->registration_number }}
                </span>
                <span class="flex items-center text-slate-300 gap-1">{{ $registration->service_label }}</span>
                <span class="flex items-center text-slate-300 gap-1">
                    <span class="text-slate-300">/</span>
                    {{ $registration->service_label ?: 'Jenis pelayanan' }}
                </span>
            </p>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 min-w-[260px] flex flex-col justify-between space-y-3 shadow-inner mt-4 lg:mt-0">
            <div class="flex justify-between items-center text-xs">
                <span class="text-slate-400 font-semibold">Kelengkapan Pendaftaran</span>
                <span class="text-xl font-extrabold text-emerald-400">{{ $progressPercent }}%</span>
            </div>
            <div class="w-full bg-slate-800 h-3 rounded-full overflow-hidden p-0.5 border border-slate-700/50">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-300 h-full rounded-full progress-smooth" style="width: {{ $progressPercent }}%"></div>
            </div>
            <div class="flex justify-between text-[11px] text-slate-400">
                <span>{{ $progressText }}</span>
                <span class="text-emerald-400 font-medium">{{ $isLengkap ? 'Lengkap' : 'Belum Lengkap' }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Stepper --}}
<div class="glass-panel rounded-2xl p-5 overflow-x-auto border border-slate-800 mt-6">
    <div class="min-w-[600px] flex items-center justify-between relative">
        <div class="absolute top-1/2 left-8 right-8 -translate-y-1/2 h-0.5 bg-slate-800 -z-0"></div>
        <div class="absolute top-1/2 left-8 -translate-y-1/2 h-0.5 bg-gradient-to-r from-emerald-500 to-teal-400 -z-0 transition-all" style="width: {{ $progressPercent - 5 }}%"></div>

        @php
            $steps = [
                ['no' => 1, 'label' => 'Pria & Orang Tua', 'sub' => $priaParty ? 'Terisi' : 'Belum Diisi', 'complete' => (bool) $priaParty],
                ['no' => 2, 'label' => 'Wanita & Orang Tua', 'sub' => $wanitaParty ? 'Terisi' : 'Belum Diisi', 'complete' => (bool) $wanitaParty],
                ['no' => 3, 'label' => 'Wali & Saksi', 'sub' => $wali ? 'Tersimpan' : 'Belum Diisi', 'complete' => (bool) $wali],
                ['no' => 4, 'label' => 'Pusat Cetak', 'sub' => $siapCetak ? 'Siap Cetak' : 'Menunggu Data', 'complete' => $siapCetak],
            ];
        @endphp

        @foreach ($steps as $step)
            <div class="relative z-10 flex flex-col items-center">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm {{ $step['complete'] ? 'bg-emerald-500 text-slate-950' : 'bg-slate-800 text-slate-400 border border-slate-700' }} ring-4 ring-slate-900">
                    {{ $step['complete'] ? '✓' : $step['no'] }}
                </div>
                <span class="text-[11px] font-bold mt-2 {{ $step['complete'] ? 'text-emerald-400' : 'text-slate-400' }}">{{ $step['label'] }}</span>
                <span class="text-[10px] {{ $step['complete'] ? 'text-emerald-300' : 'text-slate-500' }}">{{ $step['sub'] }}</span>
            </div>
            @if (!$loop->last)
                <div class="flex-1 h-0.5 bg-slate-800 -z-0"></div>
            @endif
        @endforeach
    </div>
</div>