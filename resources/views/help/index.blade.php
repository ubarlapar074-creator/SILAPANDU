@extends('layouts.app')

@section('title', 'Panduan Aplikasi')

@push('styles')
<style>html{scroll-behavior:smooth} .help-toc a.active{background:#0f6b4a;color:#fff}</style>
@endpush

@section('content')
<div class="mx-auto max-w-6xl">
    <div class="mb-6">
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-emerald-700">Bantuan</p>
        <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">Panduan Penggunaan Aplikasi</h1>
        <p class="mt-1 text-sm text-slate-500">SILAPANDU — Sistem Layanan Administrasi Pernikahan Terpadu. Klik topik di kiri untuk lompat otomatis ke materi.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[260px_1fr]" x-data="{ active: 'ssot' }" @scroll.window.throttle.100ms="const ids=['ssot','alur','penomoran','cetak','cadangan']; let cur=ids[0]; for(const id of ids){ const el=document.getElementById(id); if(el && window.scrollY + 140 >= el.offsetTop) cur=id; } active=cur">

        <nav class="help-toc lg:sticky lg:top-20 h-fit rounded-xl border border-slate-200 bg-white p-3 shadow-sm">
            <p class="px-3 py-2 text-[10px] font-bold uppercase tracking-[0.16em] text-slate-500">Daftar Isi</p>
            <div class="space-y-1">
                <a href="#ssot" @click.prevent="document.getElementById('ssot').scrollIntoView({behavior:'smooth',block:'start'}); active='ssot'; history.replaceState(null,'','#ssot')" :class="active==='ssot' ? 'active' : 'text-slate-700 hover:bg-slate-50'" class="flex items-center gap-2 rounded-lg px-3 py-2.5 text-sm font-semibold transition">① Pusat Data Tunggal (SSOT)</a>
                <a href="#alur" @click.prevent="document.getElementById('alur').scrollIntoView({behavior:'smooth',block:'start'}); active='alur'; history.replaceState(null,'','#alur')" :class="active==='alur' ? 'active' : 'text-slate-700 hover:bg-slate-50'" class="flex items-center gap-2 rounded-lg px-3 py-2.5 text-sm font-semibold transition">② Alur Pendaftaran Catin</a>
                <a href="#penomoran" @click.prevent="document.getElementById('penomoran').scrollIntoView({behavior:'smooth',block:'start'}); active='penomoran'; history.replaceState(null,'','#penomoran')" :class="active==='penomoran' ? 'active' : 'text-slate-700 hover:bg-slate-50'" class="flex items-center gap-2 rounded-lg px-3 py-2.5 text-sm font-semibold transition">③ Penomoran Surat N1–N6 & F1</a>
                <a href="#cetak" @click.prevent="document.getElementById('cetak').scrollIntoView({behavior:'smooth',block:'start'}); active='cetak'; history.replaceState(null,'','#cetak')" :class="active==='cetak' ? 'active' : 'text-slate-700 hover:bg-slate-50'" class="flex items-center gap-2 rounded-lg px-3 py-2.5 text-sm font-semibold transition">④ Pusat Cetak & Validasi</a>
                <a href="#cadangan" @click.prevent="document.getElementById('cadangan').scrollIntoView({behavior:'smooth',block:'start'}); active='cadangan'; history.replaceState(null,'','#cadangan')" :class="active==='cadangan' ? 'active' : 'text-slate-700 hover:bg-slate-50'" class="flex items-center gap-2 rounded-lg px-3 py-2.5 text-sm font-semibold transition">⑤ Cadangan & Pemulihan Data</a>
            </div>
            <div class="mt-4 rounded-lg bg-emerald-50 border border-emerald-200 px-3 py-2.5 text-xs text-emerald-800">💡 Tip: Gunakan menu di kiri — halaman akan bergulir halus ke materi tanpa jeda.</div>
        </nav>

        <div class="space-y-6">

            <section id="ssot" class="scroll-mt-24 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start gap-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-sky-600 text-sm font-bold text-white">1</span>
                    <div class="min-w-0">
                        <h2 class="text-base font-bold text-slate-900">Pengenalan Pusat Data Tunggal (SSOT) untuk Penandatangan</h2>
                        <p class="mt-1 text-sm leading-6 text-slate-600">Semua data <span class="font-semibold text-slate-800">pejabat penandatangan</span> hanya diatur <span class="font-semibold">sekali</span> di menu <a href="{{ route('settings.village-profile.edit') }}" class="font-semibold text-emerald-700 hover:underline">Profil Desa & Penandatangan</a>. Setelah disimpan, data otomatis masuk ke semua cetakan tanpa perlu diketik ulang di form pendaftaran.</p>
                    </div>
                </div>
                <div class="mt-4 grid gap-3 sm:grid-cols-3">
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Kepala Desa</p>
                        <p class="mt-1 text-sm text-slate-700">Nama + NIP/NIK → tanda tangan <span class="font-medium">N1, N5, N6</span> dan baris <em>Kepala Desa</em> sesuai desa yang diatur di Profil Desa. Jika berhalangan, Kasi dapat menggantikan.</p>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Kasi Pelayanan</p>
                        <p class="mt-1 text-sm text-slate-700">Alternatif penandatangan dokumen desa. Diisi bila Kepala Desa tidak di tempat.</p>
                    </div>
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Kepala KUA + NIP</p>
                        <p class="mt-1 text-sm text-slate-700">Untuk <span class="font-medium">surat kehendak nikah (Model N2)</span> — TTD kiri berdampingan dengan Pemohon, NIP tampil di bawah nama.</p>
                    </div>
                </div>
                <div class="mt-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">↻ Ubah di Profil Desa sekali — seluruh dokumen <span class="font-semibold">N1–N6, Surat Pernyataan Status, dan N2</span> ikut ter-update saat cetak berikutnya.</div>
            </section>

            <section id="alur" class="scroll-mt-24 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start gap-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-600 text-sm font-bold text-white">2</span>
                    <div class="min-w-0">
                        <h2 class="text-base font-bold text-slate-900">Alur Pendaftaran Catin & Pengisian Data Wajib</h2>
                        <p class="mt-1 text-sm leading-6 text-slate-600">Mulai dari <a href="{{ route('registrations.create') }}" class="font-semibold text-emerald-700 hover:underline">Pendaftaran Baru</a> → lengkapi langkah berurutan. Data wajib ditandai <span class="text-red-500">*</span>. Simpan dapat dilakukan bertahap (autosave).</p>
                    </div>
                </div>
                <ol class="mt-4 list-decimal space-y-2 pl-5 text-sm leading-6 text-slate-700">
                    <li><span class="font-semibold">Data Pernikahan (Bedol/Kantor):</span> jalur (Nikah Bedol / Nikah Kantor), tanggal & jam akad, mas kawin, dan <span class="font-medium">alamat tempat akad</span> — alamat ini yang tercetak di N2. Untuk <span class="font-medium">Pembuatan Andon Nikah</span> blok ini otomatis disembunyikan (5 step: tanpa tanggal/jam/mahar/lokasi/wilayah akad) → langsung Catin Pria.</li>
                    <li><span class="font-semibold">Catin Pria & Wanita:</span> NIK 16 digit (cek duplikasi), nama, Bin/Binti, tempat & tanggal lahir, <span class="font-medium">status: Belum Kawin / Cerai Hidup / Cerai Mati</span> (cetak jadi <em>JANDA (CERAI HIDUP)</em> dst. sesuai gender), pekerjaan, pendidikan, alamat lengkap + wilayah (provinsi → desa).</li>
                    <li><span class="font-semibold">Orang tua (Ayah/Ibu kedua catin):</span> status Hidup/Meninggal — jika meninggal, nama tercetak <em>… (Alm)/(Almh)</em> dan field lain dikosongkan.</li>
                    <li><span class="font-semibold">Wali nikah:</span> Ayah kandung / wali hakim / lainnya — jika wali hakim, isian wali dikosongkan otomatis.</li>
                    <li><span class="font-semibold">Saksi (maks. 3, 2 utama):</span> NIK, nama, gender, ttl, pekerjaan, alamat, wilayah — status 3 opsi sama seperti catin.</li>
                </ol>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-lg bg-amber-50 border border-amber-200 px-4 py-3 text-xs leading-5 text-amber-800"><span class="font-semibold">NIK:</span> wajib 16 digit & pria–wanita tidak boleh sama. Ortu meninggal: NIK & alamat dikosongkan sistem.</div>
                    <div class="rounded-lg bg-sky-50 border border-sky-200 px-4 py-3 text-xs leading-5 text-sky-800"><span class="font-semibold">Alamat:</span> isi jalan/blok/dusun + RT/RW — sistem gabung otomatis (mis. “Jl. Mawar RT01/RW02”).</div>
                </div>
            </section>

            <section id="penomoran" class="scroll-mt-24 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start gap-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-500 text-sm font-bold text-white">3</span>
                    <div class="min-w-0">
                        <h2 class="text-base font-bold text-slate-900">Pengaturan dan Penomoran Surat (N1–N6 & F1)</h2>
                        <p class="mt-1 text-sm leading-6 text-slate-600">Atur sekali di <a href="{{ route('settings.document-numbers.edit') }}" class="font-semibold text-emerald-700 hover:underline">Penomoran Surat</a>. Format berlaku untuk semua N1–N6 & F1; per-pendaftaran masih bisa override bila buku agenda fisik berbeda.</p>
                    </div>
                </div>
                <div class="mt-4 rounded-lg bg-slate-900 px-4 py-3 font-mono text-xs text-slate-100"><span class="text-slate-400">[No.Urut]</span> / <span class="text-emerald-300">[Kode Klasifikasi Arsip]</span> / <span class="text-sky-300">[Kode Desa/Kecamatan]</span> / <span class="text-amber-200">[Bulan Romawi]</span> / <span class="text-slate-300">[Tahun]</span> <span class="ml-2 text-slate-500">→ 001/470/2001-001/IX/2026</span></div>
                <ul class="mt-4 list-disc space-y-1.5 pl-5 text-sm text-slate-600">
                    <li><span class="font-medium text-slate-800">Kode Klasifikasi Arsip</span> (mis. 470) dan <span class="font-medium text-slate-800">Kode Desa/Kecamatan</span> wajib diisi sebelum simpan.</li>
                    <li>Nomor urut <span class="font-medium">naik otomatis</span> lintas semua jenis surat dan <span class="font-medium">reset tiap tahun</span>.</li>
                    <li>Bulan (romawi) & tahun terisi otomatis dari tanggal cetak.</li>
                    <li>Butuh nomor khusus? Gunakan <span class="font-medium">Atur Nomor Surat</span> di halaman Detail Pendaftaran untuk override per dokumen.</li>
                </ul>
            </section>

            <section id="cetak" class="scroll-mt-24 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start gap-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-600 text-sm font-bold text-white">4</span>
                    <div class="min-w-0">
                        <h2 class="text-base font-bold text-slate-900">Pusat Cetak Dokumen & Validasi Akhir</h2>
                        <p class="mt-1 text-sm leading-6 text-slate-600">Buka <span class="font-medium">Detail Pendaftaran</span> → panel <span class="font-medium">Pusat Cetak Dokumen Administratif</span>. Semua tombol cetak ada di satu tempat.</p>
                    </div>
                </div>
                <div class="mt-4 grid gap-2 text-sm sm:grid-cols-2">
                    <div class="rounded-lg border border-slate-200 px-3 py-2.5"><span class="font-semibold">Data Pendaftaran (F1)</span><span class="text-slate-500"> — ringkasan lengkap catin & ortu untuk arsip</span></div>
                    <div class="rounded-lg border border-slate-200 px-3 py-2.5"><span class="font-semibold">N1 Pengantar Nikah</span><span class="text-slate-500"> — TTD Kepala Desa (sesuai profil desa)</span></div>
                    <div class="rounded-lg border border-slate-200 px-3 py-2.5"><span class="font-semibold">N2 Kehendak Nikah</span><span class="text-slate-500"> — TTD kiri Kepala KUA + NIP, kanan Pemohon (tidak ada di Andon)</span></div>
                    <div class="rounded-lg border border-slate-200 px-3 py-2.5"><span class="font-semibold">N4 Persetujuan</span><span class="text-slate-500"> — selalu ada</span></div>
                    <div class="rounded-lg border border-slate-200 px-3 py-2.5"><span class="font-semibold">N5</span><span class="text-slate-500"> — Bedol/Kantor kondisional &lt;21th, Andon selalu tersedia header IZIN ORTU</span></div>
                    <div class="rounded-lg border border-slate-200 px-3 py-2.5"><span class="font-semibold">Surat Pernyataan Status</span><span class="text-slate-500"> — Pria & Wanita terpisah</span></div>
                    <div class="rounded-lg border border-slate-200 px-3 py-2.5"><span class="font-semibold">Fotokopi KTP</span><span class="text-slate-500"> — kelola & cetak lampiran</span></div>
                </div>
                <div class="mt-4 rounded-lg bg-slate-50 border border-slate-200 px-4 py-3 text-sm text-slate-700">✅ <span class="font-semibold">Validasi akhir:</span> pastikan nomor surat sudah diatur, status catin benar (JANDA/DUDA berkurung), alamat akad terisi, dan NIP pejabat tidak kosong sebelum cetak. Tombol <em>Atur Nomor Surat</em> ada di atas panel cetak bila perlu koreksi cepat.</div>
            </section>

            <section id="cadangan" class="scroll-mt-24 rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start gap-3">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-teal-600 text-sm font-bold text-white">5</span>
                    <div class="min-w-0">
                        <h2 class="text-base font-bold text-slate-900">Cadangan & Pemulihan Data</h2>
                        <p class="mt-1 text-sm leading-6 text-slate-600">Jaga data tetap aman — cukup 1 klik. Arsip disimpan sebagai <span class="font-mono text-xs bg-slate-100 px-1.5 py-0.5 rounded">.simanis</span> (hakikat SQLite, hanya ganti nama agar ramah operator). Arsip lama <span class="font-mono text-xs">.sqlite</span> tetap bisa dipulihkan.</p>
                    </div>
                </div>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-wide text-emerald-700">Cadangkan (1 klik)</p>
                        <p class="mt-1 text-sm text-slate-700">Buka <a href="{{ route('backups.index') }}" class="font-semibold text-emerald-700 hover:underline">Cadangan Data</a> → <span class="font-medium">Cadangkan Data</span>. File <span class="font-mono text-xs">simanis-YYYYMMDD-HHMMSS.simanis</span> akan muncul di daftar.</p>
                        <p class="mt-2 text-xs text-slate-500">Pengingat muncul tiap 14 hari (bisa tunda 7/14/30 hari via “Nanti”) bila belum cadang.</p>
                    </div>
                    <div class="rounded-lg border border-sky-200 bg-sky-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-wide text-sky-700">Unduh Arsip</p>
                        <p class="mt-1 text-sm text-slate-700">Klik <span class="font-medium">Unduh Arsip</span> di tabel daftar → simpan ke flashdisk/komputer luar aplikasi sebagai salinan kedua.</p>
                        <p class="mt-2 text-xs text-slate-500">Tidak perlu buka file manual — cukup simpan. Jangan ubah ekstensi sendiri.</p>
                    </div>
                </div>
                <div class="mt-3 rounded-lg border border-slate-200 bg-white p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Pulihkan (Restore)</p>
                    <ol class="mt-2 list-decimal space-y-1.5 pl-5 text-sm text-slate-700">
                        <li>Di halaman Cadangan Data, bagian <span class="font-medium">Restore Data</span> → pilih file <span class="font-mono text-xs">.simanis</span> (atau <span class="font-mono text-xs">.sqlite</span> lama).</li>
                        <li>Klik <span class="font-medium">Restore</span> → sistem validasi dulu; bila rusak, data aktif <span class="font-medium">tidak tertimpa</span> (rollback aman).</li>
                        <li>Berhasil → data kembali seperti saat arsip dibuat.</li>
                    </ol>
                </div>
                <div class="mt-3 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-xs leading-5 text-amber-800"><span class="font-semibold">Apa itu .simanis?</span> Hanya nama ramah untuk file SQLite — isi tetap database utuh (semua tabel & relasi). CSV tidak cocok untuk cadangan penuh karena butuh banyak file & rawan hilang relasi; cukup pakai Arsip .simanis untuk pulih utuh.</div>
                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="{{ route('backups.index') }}" class="rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700">Buka Cadangan Data</a>
                    <a href="{{ route('dashboard') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Kembali ke Dashboard</a>
                </div>
            </section>

        </div>
    </div>
</div>
@endsection
