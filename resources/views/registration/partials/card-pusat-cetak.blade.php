<div class="glass-card rounded-2xl p-6 transition-all duration-300 relative group border-l-4 border-l-teal-500">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-4 border-b border-slate-800">
        <div>
            <h3 class="font-extrabold text-white text-base">Tahap 4: Pusat Cetak &amp; Review</h3>
            <p class="text-xs text-slate-400">Ringkasan status & penerbitan dokumen N1&ndash;N6</p>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $siapCetak ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/10 text-amber-400 border border-amber-500/30' }}">
            {{ $siapCetak ? 'Siap Cetak' : 'Belum Lengkap' }}
        </span>
    </div>

    <div class="py-4 grid grid-cols-2 sm:grid-cols-4 gap-3">
        @foreach ($cekKelengkapan as $item)
            <div class="rounded-xl border border-slate-800 bg-slate-900/60 px-3 py-3 text-xs">
                <span class="flex items-center gap-1.5 {{ $item['ok'] ? 'text-emerald-400' : 'text-slate-500' }} font-semibold">
                    {{ $item['label'] }}
                </span>
                <span class="block mt-1 text-[10px] {{ $item['ok'] ? 'text-emerald-300' : 'text-slate-500' }}">
                    {{ $item['ok'] ? 'Lengkap' : 'Belum diisi' }}
                </span>
            </div>
        @endforeach
    </div>

    <div class="pt-3 border-t border-slate-800/80 flex justify-end gap-2">
        {{-- <a href="{{ route('documents.index', ['registration_id' => $registration->id]) }}" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-200 border border-slate-700 font-semibold text-xs">Pusat Dokumen</a> --}}
        <a href="{{ route('registration.review', $registration->id) }}" class="px-5 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold text-sm shadow-lg">Pratinjau &amp; Cetak Dokumen (N1-N6)</a>
    </div>
</div>