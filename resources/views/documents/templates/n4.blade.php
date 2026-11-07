<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Persetujuan Calon Pengantin - Model N4</title>
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
    @php
        $desaKop = trim((string) ($villageProfile?->village_name ?? '')) ?: '-';
    @endphp
    <table>
        <tr><td>KANTOR DESA/KELURAHAN</td><td>: {{ $desaKop }}</td></tr>
        <tr><td>KECAMATAN</td><td>: {{ $villageProfile?->district_name ?? '-' }}</td></tr>
        <tr><td>KABUPATEN/KOTA</td><td>: {{ $villageProfile?->regency_name ?? '-' }}</td></tr>
    </table>

    <div class="head">
        <h1>FORMULIR PERSETUJUAN CALON PENGANTIN<br>Model N4</h1>
    </div>

    <p>Nomor : {{ $registration->nomor_surat ?: '-' }}</p>

    @php
        $fmtAlamat = fn($m) => \App\Support\Alamat::pendek($m?->address);
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

    <p class="closing">Menyatakan dengan sesungguhnya bahwa atas dasar suka rela, dengan kesadaran sendiri, tanpa ada paksaan dari siapapun juga, setuju untuk melangsungkan pernikahan.</p>
    <p>Demikian surat persetujuan ini dibuat untuk digunakan seperlunya.</p>

    <div class="signatures">
        <div>Calon Suami<div class="name"><x-nama :nama="$catinPria?->name" /></div></div>
        <div>Calon Istri<div class="name"><x-nama :nama="$catinWanita?->name" /></div></div>
    </div>
</body>
</html>
