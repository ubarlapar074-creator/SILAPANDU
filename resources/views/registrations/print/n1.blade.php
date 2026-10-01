<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Formulir Pengantar Nikah - Model N1</title>
    <style>
        @page { size: A4; margin: 16mm 19mm 18mm; }
        body { color: #111; font-family: "Times New Roman", serif; font-size: 12pt; line-height: 1.25; margin: 0; }
        table { border-collapse: collapse; width: 100%; table-layout: fixed; }
        .identity { margin: 0 0 8px; }
        .identity td { padding: 1px 0; vertical-align: top; }
        .identity td:first-child { width: 36%; padding-right: 8px; }
        .header { text-align: center; }
        .header p { margin: 0; }
        .header .lampiran { font-size: 10pt; }
        .header h1 { font-size: 15pt; margin: 16px 0 1px; }
        .header h2 { font-size: 13pt; margin: 0 0 12px; }
        .number { margin: 6px 0 12px; }
        .intro, .closing { text-align: justify; }
        .closing { margin-top: 12px; }
        .signature { margin-top: 22px; margin-left: 62%; text-align: center; }
        .signature .name { margin-top: 72px; font-weight: bold; text-transform: uppercase; }
        .muted { color: #444; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="header">
        <p class="lampiran">LAMPIRAN V</p>
        <p class="lampiran">KEPUTUSAN DIREKTUR JENDERAL BIMBINGAN MASYARAKAT ISLAM</p>
        <p class="lampiran">NOMOR 473 TAHUN 2020</p>
        <p class="lampiran">TENTANG PETUNJUK TEKNIS PELAKSANAAN PENCATATAN PERNIKAHAN</p>
        <h1>FORMULIR PENGANTAR NIKAH</h1>
        <h2>Model N1</h2>
    </div>

    @php
        $desaTtd = trim((string) ($villageProfile?->village_name ?? ''));
        $desaKop = $desaTtd !== '' ? $desaTtd : ($registration->marriage_location_address ?: '[nama desa belum diatur]');
        $fmtAlamat = function($m) { if (!$m) return '-'; $parts = array_filter([$m->address ? trim($m->address) : null, $m->village?->name ? 'Desa ' . $m->village->name : null, $m->district?->name ? 'Kec. ' . $m->district->name : null, $m->regency?->name ?: null]); return $parts ? implode(', ', $parts) : '-'; };
        $fmtParentName = function($p) { if (!$p) return '-'; return $p->formatted_name ?? $p->display_name ?? ($p->name ?? '-'); };
    @endphp
    <table class="identity">
        <tr><td>KANTOR DESA/KELURAHAN</td><td>: {{ $desaKop }}</td></tr>
        <tr><td>KECAMATAN</td><td>: {{ $villageProfile?->district_name ?? '-' }}</td></tr>
        <tr><td>KABUPATEN/KOTA</td><td>: {{ $villageProfile?->regency_name ?? '-' }}</td></tr>
    </table>

    <p class="number">Nomor : {{ $documents['N1']?->document_number ?? 'diisi pada pengaturan nomor surat' }}</p>

    <p class="intro">Yang bertanda tangan di bawah ini menjelaskan dengan sesungguhnya bahwa:</p>

    <table class="identity">
        <tr><td>1. Nama</td><td>: {{ $catinPria?->name ?: '-' }}</td></tr>
        <tr><td>2. Nomor Induk Kependudukan (NIK)</td><td>: {{ $catinPria?->nik ?: '-' }}</td></tr>
        <tr><td>3. Jenis Kelamin</td><td>: Laki-laki</td></tr>
        <tr><td>4. Tempat dan Tanggal Lahir</td><td>: {{ $catinPria?->birth_place ?: '-' }}, {{ $catinPria?->birth_date?->format('d-m-Y') ?: '-' }}</td></tr>
        <tr><td>5. Kewarganegaraan</td><td>: {{ $catinPria?->nationality ?: '-' }}</td></tr>
        <tr><td>6. Agama</td><td>: {{ $catinPria?->religion ?: '-' }}</td></tr>
        <tr><td>7. Pekerjaan</td><td>: {{ $catinPria?->occupation ?: '-' }}</td></tr>
        <tr><td>8. Alamat</td><td>: {{ $fmtAlamat($catinPria) }}</td></tr>
        @php $n1StatusLabel = $catinPria?->marital_status ? \App\Support\MaritalStatus::labelForRaw($catinPria->marital_status, 'PRIA') : '-'; $n1AyahStatus = $ayahPria?->marital_status ? (function($s){ $c=\App\Support\MaritalStatus::normalize($s); if($c==='CERAI_HIDUP') return 'Duda (Cerai Hidup)'; if($c==='CERAI_MATI') return 'Duda (Cerai Mati)'; if($c==='KAWIN') return 'Kawin'; if($c==='BELUM_KAWIN') return 'Belum Kawin'; return \App\Support\MaritalStatus::labelForRaw($s,'PRIA'); })($ayahPria->marital_status) : '-'; $n1IbuStatus = $ibuPria?->marital_status ? (function($s){ $c=\App\Support\MaritalStatus::normalize($s); if($c==='CERAI_HIDUP') return 'Janda (Cerai Hidup)'; if($c==='CERAI_MATI') return 'Janda (Cerai Mati)'; return \App\Support\MaritalStatus::labelForRaw($s,'WANITA'); })($ibuPria->marital_status) : '-'; @endphp
        <tr><td>9. Status perkawinan</td><td>: {{ $n1StatusLabel }}</td></tr>
    </table>

    <p>Adalah benar anak dari pernikahan seorang pria:</p>
    <table class="identity">
        <tr><td>Nama lengkap dan alias</td><td>: {{ $fmtParentName($ayahPria) }}</td></tr>
        <tr><td>Nomor Induk Kependudukan (NIK)</td><td>: {{ $ayahPria?->nik ?: '-' }}</td></tr>
        <tr><td>Tempat dan Tanggal Lahir</td><td>: {{ $ayahPria?->birth_place ?: '-' }}, {{ $ayahPria?->birth_date?->format('d-m-Y') ?: '-' }}</td></tr>
        <tr><td>Agama</td><td>: -</td></tr>
        <tr><td>Pekerjaan</td><td>: {{ $ayahPria?->occupation ?: '-' }}</td></tr>
        <tr><td>Alamat</td><td>: {{ $fmtAlamat($ayahPria) }}</td></tr>
    </table>

    <p>Dengan seorang wanita:</p>
    <table class="identity">
        <tr><td>Nama lengkap dan alias</td><td>: {{ $fmtParentName($ibuPria) }}</td></tr>
        <tr><td>Nomor Induk Kependudukan (NIK)</td><td>: {{ $ibuPria?->nik ?: '-' }}</td></tr>
        <tr><td>Tempat dan Tanggal Lahir</td><td>: {{ $ibuPria?->birth_place ?: '-' }}, {{ $ibuPria?->birth_date?->format('d-m-Y') ?: '-' }}</td></tr>
        <tr><td>Agama</td><td>: -</td></tr>
        <tr><td>Pekerjaan</td><td>: {{ $ibuPria?->occupation ?: '-' }}</td></tr>
        <tr><td>Alamat</td><td>: {{ $ibuPria?->address ?: '-' }}</td></tr>
    </table>

    <p class="closing">Demikian surat pengantar ini dibuat dengan mengingat sumpah jabatan dan untuk dipergunakan sebagaimana mestinya.</p>

    <div class="signature">
        <div>{{ $desaTtd !== '' ? $desaTtd . ', ' : '[nama desa belum diatur], ' }}{{ now()->format('d F Y') }}</div>
        <div>Kepala Desa{{ $desaTtd !== '' ? ' ' . $desaTtd : '' }}</div>
        <div class="name">{{ $villageProfile?->village_head_name ?: '........................' }}</div>
    </div>
</body>
</html>
