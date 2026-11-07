<form action="{{ route('registration.store-akad', $registration->id) }}" method="POST" class="glass-panel rounded-2xl p-5 border border-slate-800 space-y-4 mt-6">
    @csrf
    <input type="hidden" name="province_id" value="{{ $registration->province_id }}">
    <input type="hidden" name="regency_id" value="{{ $registration->regency_id }}">
    <input type="hidden" name="district_id" value="{{ $registration->district_id }}">
    <input type="hidden" name="village_id" value="{{ $registration->village_id }}">

    @if ($errors->any())
        <div class="rounded-xl border border-rose-500/40 bg-rose-500/10 px-4 py-3 text-xs text-rose-300">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-800 pb-3">
        <div class="flex items-center gap-2.5">
            <div>
                <h3 class="font-extrabold text-white text-sm">Jadwal Akad & Mahar</h3>
                <p class="text-xs text-slate-400">Isian ringkas &mdash; wilayah & rincian lengkap lewat tombol Edit</p>
            </div>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $adaAkad ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-slate-500/10 text-slate-400 border border-slate-500/30' }}">
            {{ $adaAkad ? 'Tersimpan' : 'Belum Diisi' }}
        </span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
            <label class="block text-[11px] font-semibold text-slate-400 mb-1.5">Tanggal Akad</label>
            <input type="date" name="tanggal_akad" required value="{{ old('tanggal_akad', $registration->tanggal_akad?->format('Y-m-d')) }}" class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-700 text-sm text-slate-100">
        </div>
        <div>
            <label class="block text-[11px] font-semibold text-slate-400 mb-1.5">Jam Akad</label>
            <input type="time" name="jam_akad" required value="{{ old('jam_akad', $registration->jam_akad ? substr($registration->jam_akad, 0, 5) : '') }}" class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-700 text-sm text-slate-100">
        </div>
        <div>
            <label class="block text-[11px] font-semibold text-slate-400 mb-1.5">Mahar</label>
            <input type="text" name="marriage_dowry_display" value="{{ old('marriage_dowry_display', $registration->marriage_dowry_display) }}" placeholder="cth. Uang Tunai: Rp 5.000.000" class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-700 text-sm text-slate-100">
        </div>
        <div>
            <label class="block text-[11px] font-semibold text-slate-400 mb-1.5">Lokasi Akad</label>
            <input type="text" name="marriage_location_address" value="{{ old('marriage_location_address', $registration->marriage_location_address) }}" placeholder="cth. Masjid Raya Al-Ikhlas" class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-700 text-sm text-slate-100">
        </div>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-2">
        <a href="{{ route('registration.form.akad', $registration->id) }}" class="text-xs font-semibold text-indigo-300 hover:text-indigo-200 underline">Edit lengkap (wilayah & rincian mahar)</a>
        <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-semibold text-xs transition-colors">Simpan Jadwal Akad & Mahar</button>
    </div>
</form>