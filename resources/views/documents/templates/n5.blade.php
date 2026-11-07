<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ ($registration->registration_path ?? '') === 'PEMBUATAN_ANDON_NIKAH' ? 'IZIN ORTU - Model N5' : 'Surat Izin Orang Tua - Model N5' }}</title>
    <style>
        @page { size: A4; margin: 18mm 20mm; }
        body { color: #111; font-family: "Times New Roman", serif; font-size: 12pt; line-height: 1.3; margin: 0; }
        table { border-collapse: collapse; width: 100%; table-layout: fixed; }
        td { padding: 2px 0; vertical-align: top; }
        td:first-child { width: 38%; padding-right: 8px; }
        .head { text-align: center; }
        .head h1 { font-size: 15pt; margin: 22px 0 16px; }
        .section { margin: 10px 0 3px; font-weight: bold; }
        .closing { margin-top: 18px; text-align: justify; }
        .signatures { display: table; width: 100%; table-layout: fixed; margin-top: 34px; text-align: center; }
        .signatures > div { display: table-cell; width: 50%; }
        .signatures .name { margin-top: 72px; font-weight: bold; }
            .nama-pdf {
            font-weight: 700;
            text-transform: uppercase;
        }

        .nama-gelar {
            font-weight: 700;
            text-transform: none;
        }

</style>
</head>
<body>
     @php $isAndonN5 = ($registration->registration_path ?? '') === 'PEMBUATAN_ANDON_NIKAH'; @endphp
    @php
        $desaKop = trim((string) ($villageProfile?->village_name ?? '')) ?: '-';
    @endphp
    <table>
        <tr><td>KANTOR DESA/KELURAHAN</td><td>: {{ $desaKop }}</td></tr>
        <tr><td>KECAMATAN</td><td>: {{ $villageProfile?->district_name ?? '-' }}</td></tr>
        <tr><td>KABUPATEN/KOTA</td><td>: {{ $villageProfile?->regency_name ?? '-' }}</td></tr>
    </table>
    <div class="head">
        <h1>{{ $isAndonN5 ? 'IZIN ORTU' : 'SURAT IZIN ORANG TUA' }}<br>Model N5</h1>
    </div>

    <p>Nomor : {{ $registration->nomor_surat ?: '-' }}</p>

    @if($isAndonN5)
    @php
        $fmtAlamat = fn($m) => \App\Support\Alamat::pendek($m?->address);
        $fmtParentName = function($p) { if (!$p) return '-'; return $p->nama_dengan_gelar ?: '-'; };
        // Label "Bin" (ayah) / "Binti" (ibu) sesuai tipe orang tua.
        $parentBinLabel = function($p) {
            if (!$p) return 'Bin';
            return str_starts_with(strtolower((string) ($p->type ?? '')), 'ibu') ? 'Binti' : 'Bin';
        };
        // Nilai bin/binti diisi dari nama ayah kandung masing-masing orang tua.
        $parentBinValue = function($p) { if (!$p || !$p->nama_ayah_kandung) return '-'; return $p->nama_ayah_kandung; };
        $namaAyahPria = ($ayahPria ?? null)?->nama_dengan_gelar;
        $namaAyahWanita = ($ayahWanita ?? null)?->nama_dengan_gelar;
    @endphp
    <p>Yang bertanda tangan di bawah ini:</p>
    <p class="section">A. Calon Suami</p>
    <table>
        <tr><td>1. Nama lengkap dan alias</td><td>: <x-nama :nama="$catinPria?->name" /></td></tr>
        <tr><td>2. Nama Ayah</td><td>: {{ $namaAyahPria ?: '-' }}</td></tr>
        <tr><td>3. Nomor Induk Kependudukan</td><td>: {{ $catinPria?->nik ?: '-' }}</td></tr>
        <tr><td>4. Tempat dan Tanggal Lahir</td><td>: {{ $catinPria?->birth_place ?: '-' }}, {{ $catinPria?->birth_date?->format('d-m-Y') ?: '-' }}</td></tr>
        <tr><td>5. Kewarganegaraan</td><td>: {{ $catinPria?->nationality ?: '-' }}</td></tr>
        <tr><td>6. Agama</td><td>: {{ $catinPria?->religion ?: '-' }}</td></tr>
        <tr><td>7. Pekerjaan</td><td>: {{ $catinPria?->occupation ?: '-' }}</td></tr>
        <tr><td>8. Alamat</td><td>: {{ $fmtAlamat($catinPria) }}</td></tr>
    </table>
    <p class="section">B. Calon Istri</p>
    <table>
        <tr><td>1. Nama lengkap dan alias</td><td>: <x-nama :nama="$catinWanita?->name" /></td></tr>
        <tr><td>2. Nama Ayah</td><td>: {{ $namaAyahWanita ?: '-' }}</td></tr>
        <tr><td>3. Nomor Induk Kependudukan</td><td>: {{ $catinWanita?->nik ?: '-' }}</td></tr>
        <tr><td>4. Tempat dan Tanggal Lahir</td><td>: {{ $catinWanita?->birth_place ?: '-' }}, {{ $catinWanita?->birth_date?->format('d-m-Y') ?: '-' }}</td></tr>
        <tr><td>5. Kewarganegaraan</td><td>: {{ $catinWanita?->nationality ?: '-' }}</td></tr>
        <tr><td>6. Agama</td><td>: {{ $catinWanita?->religion ?: '-' }}</td></tr>
        <tr><td>7. Pekerjaan</td><td>: {{ $catinWanita?->occupation ?: '-' }}</td></tr>
        <tr><td>8. Alamat</td><td>: {{ $fmtAlamat($catinWanita) }}</td></tr>
    </table>
    <p class="section">C. Orang Tua/Wali</p>
    <table>
        <tr><td>Ayah Catin Pria</td><td>: <x-nama :nama="$fmtParentName($ayahPria)" /> {{ $ayahPria?->nik ? '('.$ayahPria->nik.')' : '' }}</td></tr>
        <tr><td>{{ $parentBinLabel($ayahPria) }}</td><td>: {{ $parentBinValue($ayahPria) }}</td></tr>
        <tr><td>Status Perkawinan Ayah</td><td>: {{ $ayahPria?->status_kawin_ortu ?: '-' }}</td></tr>
        <tr><td>Ibu Catin Pria</td><td>: <x-nama :nama="$fmtParentName($ibuPria)" /> {{ $ibuPria?->nik ? '('.$ibuPria->nik.')' : '' }}</td></tr>
        {{-- Binti ibu diambil dari nama ayah kandung ibu itu sendiri, bukan ayah calon. --}}
        <tr><td>{{ $parentBinLabel($ibuPria) }}</td><td>: {{ $ibuPria?->nama_ayah_kandung ?: '-' }}</td></tr>
        <tr><td>Status Perkawinan Ibu</td><td>: {{ $ibuPria?->status_kawin_ortu ?: '-' }}</td></tr>
        <tr><td>Ayah Catin Wanita</td><td>: <x-nama :nama="$fmtParentName($ayahWanita)" /> {{ $ayahWanita?->nik ? '('.$ayahWanita->nik.')' : '' }}</td></tr>
        <tr><td>{{ $parentBinLabel($ayahWanita) }}</td><td>: {{ $parentBinValue($ayahWanita) }}</td></tr>
        <tr><td>Status Perkawinan Ayah</td><td>: {{ $ayahWanita?->status_kawin_ortu ?: '-' }}</td></tr>
        <tr><td>Ibu Catin Wanita</td><td>: <x-nama :nama="$fmtParentName($ibuWanita)" /> {{ $ibuWanita?->nik ? '('.$ibuWanita->nik.')' : '' }}</td></tr>
        {{-- Binti ibu diambil dari nama ayah kandung ibu itu sendiri, bukan ayah calon. --}}
        <tr><td>{{ $parentBinLabel($ibuWanita) }}</td><td>: {{ $ibuWanita?->nama_ayah_kandung ?: '-' }}</td></tr>
        <tr><td>Status Perkawinan Ibu</td><td>: {{ $ibuWanita?->status_kawin_ortu ?: '-' }}</td></tr>
    </table>
    <p class="closing">Menyatakan dengan sesungguhnya bahwa atas dasar suka rela, dengan kesadaran sendiri, tanpa ada paksaan dari siapapun juga, setuju untuk melangsungkan pernikahan. Izin orang tua/wali telah diberikan.</p>
    <p>Demikian surat izin ini dibuat untuk digunakan seperlunya.</p>
    <div class="signatures">
        <div>Calon Suami<div class="name"><x-nama :nama="$catinPria?->name" /></div></div>
        <div>Calon Istri<div class="name"><x-nama :nama="$catinWanita?->name" /></div></div>
    </div>
@else
@php
        $fmtAlamat = fn($m) => \App\Support\Alamat::pendek($m?->address);
        $fmtParentName = function($p) { if (!$p) return '-'; return $p->nama_dengan_gelar ?: '-'; };
        // Label "Bin" (ayah) / "Binti" (ibu) sesuai tipe orang tua.
        $parentBinLabel = function($p) {
            if (!$p) return 'Bin';
            return str_starts_with(strtolower((string) ($p->type ?? '')), 'ibu') ? 'Binti' : 'Bin';
        };
        // Nilai bin/binti diisi dari nama ayah kandung orang tua (ayah/bapak).
        $parentBinValue = function($p) { if (!$p || !$p->nama_ayah_kandung) return '-'; return $p->nama_ayah_kandung; };
        $namaAyahPria = ($ayahPria ?? null)?->nama_dengan_gelar;
        $namaAyahWanita = ($ayahWanita ?? null)?->nama_dengan_gelar;

        // N5 adalah Surat Izin Orang Tua untuk calon pengantin yang "di desa ini".
        // Hanya satu sisi orang tua yang ditampilkan, sesuai $catinRole.
        $subject = $catin;
        $pasangan = $catinRole === 'PRIA' ? $catinWanita : $catinPria;
        $subjectNamaAyah = $catinRole === 'PRIA' ? $namaAyahPria : $namaAyahWanita;
        $pasanganNamaAyah = $catinRole === 'PRIA' ? $namaAyahWanita : $namaAyahPria;
        $subjectSigner = $catinRole === 'PRIA' ? $n5SignerPria : $n5SignerWanita;
        $subjectLabel = $catinRole === 'PRIA' ? 'Catin Pria' : 'Catin Wanita';
    @endphp
    <p>Yang bertanda tangan di bawah ini:</p>
    <p class="section">A. Orang Tua {{ $subjectLabel }}</p>
    <table>
        <tr><td>1. Nama lengkap dan alias</td><td>: <x-nama :nama="$fmtParentName($subjectSigner)" /></td></tr>
        <tr><td>2. {{ $parentBinLabel($subjectSigner) }}</td><td>: {{ $parentBinValue($subjectSigner) }}</td></tr>
        <tr><td>3. Nomor Induk Kependudukan</td><td>: {{ $subjectSigner?->nik ?: '-' }}</td></tr>
        <tr><td>4. Alamat</td><td>: {{ $fmtAlamat($subjectSigner) }}</td></tr>
        <tr><td>5. Status Perkawinan</td><td>: {{ $subjectSigner?->status_kawin_ortu ?: '-' }}</td></tr>
    </table>
    <p>Adalah ayah dan ibu kandung/wali/pengampu dari:</p>
    <table>
        <tr><td>1. Nama lengkap dan alias</td><td>: <x-nama :nama="$subject?->name" /></td></tr>
        <tr><td>2. Nama Ayah</td><td>: {{ $subjectNamaAyah ?: '-' }}</td></tr>
        <tr><td>3. Nomor Induk Kependudukan</td><td>: {{ $subject?->nik ?: '-' }}</td></tr>
        <tr><td>4. Tempat dan Tanggal Lahir</td><td>: {{ $subject?->birth_place ?: '-' }}, {{ $subject?->birth_date?->format('d-m-Y') ?: '-' }}</td></tr>
        <tr><td>5. Alamat</td><td>: {{ $fmtAlamat($subject) }}</td></tr>
    </table>
    <p>Memberikan izin kepada anak kami untuk melakukan pernikahan dengan:</p>
    <table>
        <tr><td>1. Nama lengkap dan alias</td><td>: <x-nama :nama="$pasangan?->name" /></td></tr>
        <tr><td>2. Nama Ayah</td><td>: {{ $pasanganNamaAyah ?: '-' }}</td></tr>
        <tr><td>3. Nomor Induk Kependudukan</td><td>: {{ $pasangan?->nik ?: '-' }}</td></tr>
        <tr><td>4. Alamat</td><td>: {{ $fmtAlamat($pasangan) }}</td></tr>
    </table>
    <p class="closing">Demikian surat izin ini dibuat dengan kesadaran tanpa ada paksaan dari siapapun dan untuk digunakan seperlunya.</p>
    <div class="signatures">
        <div>Orang Tua {{ $subjectLabel }}<div class="name"><x-nama :nama="$fmtParentName($subjectSigner)" /></div></div>
    </div>
    @endif
</body>
</html>
