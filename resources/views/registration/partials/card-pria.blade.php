@php
    $priaNama = $priaParty?->nama_sementara ?? 'Belum Diisi';
    $priaNik = $priaParty?->warga ? 'Nik ' . substr($priaParty->warga->nik, -4) : '—';
    $priaTtl = $priaParty?->warga ? ($priaParty->warga->tempat_lahir ?? '') . ', ' . ($priaParty->warga->tgl_lahir?->format('d M Y') ?? '') : '—';
@endphp

<div class="glass-card rounded-2xl p-6 transition-all duration-300 relative group border-l-4 border-l-emerald-500">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-4 border-b border-slate-800">
        <div>
            <h3 class="font-extrabold text-white text-base">Tahap 1: Calon Pengantin Laki-Laki</h3>
            <p class="text-xs text-slate-400">Data identitas, NIK & keperluan administrasi</p>
        </div>
        @if($priaParty)
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">Selesai & Valid</span>
        @else
            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-500/10 text-slate-400 border border-slate-500/30">Belum Diisi</span>
        @endif
    </div>

    <div class="py-4 grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
        <div>
            <p class="text-slate-400 text-[10px] font-semibold">Nama Lengkap</p>
            <p class="font-bold text-slate-100 text-sm">{{ $priaNama }}</p>
        </div>
        <div>
            <p class="text-slate-400 text-[10px] font-semibold">NIK & TTL</p>
            <p class="font-mono text-slate-300">{{ $priaNik }}</p>
            <p class="text-slate-400 text-[11px]">{{ $priaTtl }}</p>
        </div>
        <div>
            <p class="text-slate-400 text-[10px] font-semibold">Status Berkas</p>
            <p class="text-emerald-400 font-medium">{{ $priaParty ? 'Lengkap' : 'Belum diisi' }}</p>
        </div>
    </div>

    <div class="pt-3 border-t border-slate-800/80 flex justify-end">
        <a href="{{ route('registration.form.pria', $registration->id) }}" class="px-4 py-1.5 rounded-xl {{ $priaParty ? 'bg-emerald-600/20 text-emerald-300 border border-emerald-500/40' : 'bg-amber-500/20 text-amber-300 border border-amber-500/40' }} font-semibold text-xs">
            {{ $priaParty ? 'Edit Data' : 'Isi Data' }}
        </a>
    </div>
</div>