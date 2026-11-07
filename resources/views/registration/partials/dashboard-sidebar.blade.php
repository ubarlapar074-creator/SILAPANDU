<div class="glass-panel rounded-2xl p-5 border border-slate-800 space-y-4 shadow-xl">
    <h3 class="text-sm font-bold text-white border-b border-slate-800 pb-3">Panel Aksi & Penerbitan</h3>

    {{-- Ringkasan nomor surat pengantar --}}
    <div class="rounded-xl border border-slate-800 bg-slate-900/60 px-3 py-3">
        <span class="block text-[10px] uppercase tracking-wider font-bold text-slate-500">Nomor Surat</span>
        @if ($registration->nomor_surat)
            <p class="font-mono text-xs font-bold text-emerald-400 mt-1 break-all">{{ $registration->nomor_surat }}</p>
            <p class="text-[10px] text-slate-500 mt-1">Tercetak pada dokumen N1&ndash;N6.</p>
        @else
            <p class="text-xs font-semibold text-amber-400 mt-1">Menunggu generate otomatis</p>
            <p class="text-[10px] text-slate-500 mt-1">Nomor surat mengikuti pengaturan penomoran.</p>
        @endif
    </div>

    <div class="space-y-2.5">
        <a href="{{ route('registration.review', $registration->id) }}" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold text-xs flex justify-between">Review &amp; Simpan Final &rarr;</a>
        <a href="{{ route('documents.index', ['registration_id' => $registration->id]) }}" class="w-full py-2.5 px-4 rounded-xl bg-slate-800 text-slate-300 border border-slate-700 font-semibold text-xs flex justify-between">Cetak Formulir N1 – N6 &rarr;</a>
    </div>
</div>

<div class="glass-panel rounded-2xl p-5 border border-slate-800 space-y-3">
    <div class="flex justify-between items-center">
        <h4 class="text-xs font-bold text-slate-300">Checklist Syarat Administratif</h4>
        <span class="text-[10px] font-mono text-emerald-400 bg-emerald-950 px-2 py-0.5 rounded border border-emerald-500/30">
            {{ count(array_filter(array_column($cekKelengkapan, 'ok'))) }}/4 Kelengkapan
        </span>
    </div>

    <div class="space-y-2 text-xs">
        @foreach($cekKelengkapan as $item)
        <label class="flex items-center space-x-2.5">
            <input type="checkbox" {{ $item['ok'] ? 'checked' : '' }} disabled class="rounded bg-slate-900 border-slate-700 text-emerald-500 w-4 h-4">
            <span class="text-slate-300">{{ $item['label'] }}</span>
        </label>
        @endforeach
    </div>
</div>