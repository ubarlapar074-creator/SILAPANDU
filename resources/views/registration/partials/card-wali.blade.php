<div class="glass-card rounded-2xl p-6 transition-all duration-300 relative group border-l-4 border-l-slate-700">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-4 border-b border-slate-800">
        <div>
            <h3 class="font-extrabold text-white text-base">Tahap 3: Wali Nikah &amp; Saksi</h3>
            <p class="text-xs text-slate-400">Wali nasab/hakim, Saksi 1, Saksi 2 dalam satu formulir</p>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $wali ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-slate-500/10 text-slate-400 border border-slate-500/30' }}">
            {{ $wali ? 'Tersimpan' : 'Belum Diisi' }}
        </span>
    </div>

    <div class="py-4 grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
        <div>
            <p class="text-slate-400 text-[10px] font-semibold">Wali Nikah</p>
            <p class="font-bold text-slate-100 text-sm">{{ $wali?->nama_wali ?? 'Belum diisi' }}</p>
        </div>
        <div>
            <p class="text-slate-400 text-[10px] font-semibold">Saksi 1</p>
            <p class="font-bold text-slate-100 text-sm">{{ $saksiParties['saksi_1']?->nama_sementara ?? 'Belum diisi' }}</p>
        </div>
        <div>
            <p class="text-slate-400 text-[10px] font-semibold">Saksi 2</p>
            <p class="font-bold text-slate-100 text-sm">{{ $saksiParties['saksi_2']?->nama_sementara ?? 'Belum diisi' }}</p>
        </div>
    </div>

    <div class="pt-3 border-t border-slate-800/80 flex justify-end">
        <a href="{{ route('registration.form.wali-saksi', $registration->id) }}" class="px-4 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs">
            {{ $wali ? 'Edit Data Wali & Saksi' : 'Isi Data Wali & Saksi' }}
        </a>
    </div>
</div>