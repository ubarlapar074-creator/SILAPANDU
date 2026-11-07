@php
    $wanitaNama = $wanitaParty?->nama_sementara ?? $wanitaParty?->warga?->nama_lengkap ?? 'Belum Diisi';
    $wanitaNik = $wanitaParty?->warga ? 'Nik •••• ••• •' . substr($wanitaParty->warga->nik, -4) : '—';
    $wanitaTtl = $wanitaParty?->warga ? ($wanitaParty->warga->tempat_lahir ?? '') . ', ' . ($wanitaParty->warga->tgl_lahir?->format('d M Y') ?? '') : '—';
@endphp

<div class="glass-card rounded-2xl p-6 transition-all duration-300 relative group border-l-4 border-l-amber-500">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-4 border-b border-slate-800">
        <div>
            <h3 class="font-extrabold text-white text-base">Tahap 2: Calon Pengantin Wanita</h3>
            <p class="text-xs text-slate-400">Data identitas, foto & kepastian status wali</p>
        </div>
        @if($wanitaParty)
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">Selesai & Valid</span>
        @else
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/30">Belum Diisi</span>
        @endif
    </div>

    <div class="py-4 grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
        <div>
            <p class="text-slate-400 text-[10px] font-semibold">Nama Lengkap</p>
            <p class="font-bold text-slate-100 text-sm">{{ $wanitaNama }}</p>
        </div>
        <div>
            <p class="text-slate-400 text-[10px] font-semibold">NIK & TTL</p>
            <p class="font-mono text-slate-300">{{ $wanitaNik }}</p>
            <p class="text-slate-400 text-[11px]">{{ $wanitaTtl }}</p>
        </div>
        <div>
            <p class="text-slate-400 text-[10px] font-semibold">Kelengkapan Syarat</p>
            @if($wanitaParty)
                <p class="text-emerald-400 font-medium">Lengkap</p>
            @else
                <p class="text-amber-400 font-medium">Perlu dilengkapi</p>
            @endif
        </div>
    </div>

    <div class="pt-3 border-t border-slate-800/80 flex justify-end">
        <a href="{{ route('registration.form.wanita', $registration->id) }}" class="px-4 py-1.5 rounded-xl {{ $wanitaParty ? 'bg-emerald-600/20 text-emerald-300 border border-emerald-500/40' : 'bg-amber-500/20 text-amber-300 border border-amber-500/40' }} font-semibold text-xs">
            {{ $wanitaParty ? 'Edit Data' : 'Isi & Lengkapi Data' }}
        </a>
    </div>
</div>