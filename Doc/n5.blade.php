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
    </style>
</head>
<body>
     @php $isAndonN5 = ($registration->registration_path ?? '') === 'PEMBUATAN_ANDON_NIKAH'; @endphp
    <div class="head">
        <h1>{{ $isAndonN5 ? 'IZIN ORTU<br>Model N5' : 'SURAT IZIN ORANG TUA<br>Model N5' }}</h1>
    </div>

    <p>Nomor : {{ $documents['N5']?->document_number ?? 'diisi pada pengaturan nomor surat' }}</p>

    @if($isAndonN5)
    @php $fmtAlamat = function($m) { if (!$m) return '-'; $parts = array_filter([$m->address ? trim($m->address) : null, $m->village?->name ? 'Desa ' . $m->village->name : null, $m->district?->name ? 'Kec. ' . $m->district->name : null, $m->regency?->name ?: null]); return $parts ? implode(', ', $parts) : '-'; }; $fmtParentName = function($p) { if (!$p) return '-'; return $p->formatted_name ?? $p->display_name ?? ($p->name ?? '-'); }; @endphp
    <p>Yang bertanda tangan di bawah ini:</p>
    <p class="section">A. Calon Suami</p>
    <table>
        <tr><td>1. Nama lengkap dan alias</td><td>: {{ $catinPria?->name ?: '-' }}</td></tr>
        <tr><td>2. Bin</td><td>: {{ $catinPria?->bin_binti ?: '-' }}</td></tr>
        <tr><td>3. Nomor Induk Kependudukan</td><td>: {{ $catinPria?->nik ?: '-' }}</td></tr>
        <tr><td>4. Tempat dan Tanggal Lahir</td><td>: {{ $catinPria?->birth_place ?: '-' }}, {{ $catinPria?->birth_date?->format('d-m-Y') ?: '-' }}</td></tr>
        <tr><td>5. Kewarganegaraan</td><td>: {{ $catinPria?->nationality ?: '-' }}</td></tr>
        <tr><td>6. Agama</td><td>: {{ $catinPria?->religion ?: '-' }}</td></tr>
        <tr><td>7. Pekerjaan</td><td>: {{ $catinPria?->occupation ?: '-' }}</td></tr>
        <tr><td>8. Alamat</td><td>: {{ $fmtAlamat($catinPria) }}</td></tr>
    </table>
    <p class="section">B. Calon Istri</p>
    <table>
        <tr><td>1. Nama lengkap dan alias</td><td>: {{ $catinWanita?->name ?: '-' }}</td></tr>
        <tr><td>2. Binti</td><td>: {{ $catinWanita?->bin_binti ?: '-' }}</td></tr>
        <tr><td>3. Nomor Induk Kependudukan</td><td>: {{ $catinWanita?->nik ?: '-' }}</td></tr>
        <tr><td>4. Tempat dan Tanggal Lahir</td><td>: {{ $catinWanita?->birth_place ?: '-' }}, {{ $catinWanita?->birth_date?->format('d-m-Y') ?: '-' }}</td></tr>
        <tr><td>5. Kewarganegaraan</td><td>: {{ $catinWanita?->nationality ?: '-' }}</td></tr>
        <tr><td>6. Agama</td><td>: {{ $catinWanita?->religion ?: '-' }}</td></tr>
        <tr><td>7. Pekerjaan</td><td>: {{ $catinWanita?->occupation ?: '-' }}</td></tr>
        <tr><td>8. Alamat</td><td>: {{ $fmtAlamat($catinWanita) }}</td></tr>
    </table>
    <p class="section">C. Orang Tua/Wali</p>
    <table>
        <tr><td>Ayah Catin Pria</td><td>: {{ $fmtParentName($ayahPria) }} {{ $ayahPria?->nik ? '('.$ayahPria->nik.')' : '' }}</td></tr>
        <tr><td>Ibu Catin Pria</td><td>: {{ $fmtParentName($ibuPria) }} {{ $ibuPria?->nik ? '('.$ibuPria->nik.')' : '' }}</td></tr>
        <tr><td>Ayah Catin Wanita</td><td>: {{ $fmtParentName($ayahWanita) }} {{ $ayahWanita?->nik ? '('.$ayahWanita->nik.')' : '' }}</td></tr>
        <tr><td>Ibu Catin Wanita</td><td>: {{ $fmtParentName($ibuWanita) }} {{ $ibuWanita?->nik ? '('.$ibuWanita->nik.')' : '' }}</td></tr>
    </table>
    <p class="closing">Menyatakan dengan sesungguhnya bahwa atas dasar suka rela, dengan kesadaran sendiri, tanpa ada paksaan dari siapapun juga, setuju untuk melangsungkan pernikahan. Izin orang tua/wali telah diberikan.</p>
    <p>Demikian surat izin ini dibuat untuk digunakan seperlunya.</p>
    <div class="signatures">
        <div>Calon Suami<div class="name">{{ $catinPria?->name ?: '-' }}</div></div>
        <div>Calon Istri<div class="name">{{ $catinWanita?->name ?: '-' }}</div></div>
    </div>
    @else
    @php $fmtAlamat = function($m) { if (!$m) return '-'; $parts = array_filter([$m->address ? trim($m->address) : null, $m->village?->name ? 'Desa ' . $m->village->name : null, $m->district?->name ? 'Kec. ' . $m->district->name : null, $m->regency?->name ?: null]); return $parts ? implode(', ', $parts) : '-'; }; $fmtParentName = function($p) { if (!$p) return '-'; return $p->formatted_name ?? $p->display_name ?? ($p->name ?? '-'); }; @endphp
    <p>Yang bertanda tangan di bawah ini:</p>
    <p class="section">A. Orang Tua Catin Pria</p>
    <table>
        <tr><td>1. Nama lengkap dan alias</td><td>: {{ $fmtParentName($ayahPria) }}</td></tr>
        <tr><td>2. Bin</td><td>: {{ $catinPria?->bin_binti ?: '-' }}</td></tr>
        <tr><td>3. Nomor Induk Kependudukan</td><td>: {{ $ayahPria?->nik ?: '-' }}</td></tr>
        <tr><td>4. Alamat</td><td>: {{ $fmtAlamat($ayahPria) }}</td></tr>
    </table>
    <p class="section">B. Orang Tua Catin Wanita</p>
    <table>
        <tr><td>1. Nama lengkap dan alias</td><td>: {{ $fmtParentName($ayahWanita) }}</td></tr>
        <tr><td>2. Binti</td><td>: {{ $catinWanita?->bin_binti ?: '-' }}</td></tr>
        <tr><td>3. Nomor Induk Kependudukan</td><td>: {{ $ayahWanita?->nik ?: '-' }}</td></tr>
        <tr><td>4. Alamat</td><td>: {{ $fmtAlamat($ayahWanita) }}</td></tr>
    </table>
    <p>Adalah ayah dan ibu kandung/wali/pengampu dari:</p>
    <table>
        <tr><td>Nama lengkap dan alias</td><td>: {{ $catinWanita?->name ?: '-' }}</td></tr>
        <tr><td>Bin/binti</td><td>: {{ $catinWanita?->bin_binti ?: '-' }}</td></tr>
        <tr><td>Nomor Induk Kependudukan</td><td>: {{ $catinWanita?->nik ?: '-' }}</td></tr>
        <tr><td>Tempat dan Tanggal Lahir</td><td>: {{ $catinWanita?->birth_place ?: '-' }}, {{ $catinWanita?->birth_date?->format('d-m-Y') ?: '-' }}</td></tr>
        <tr><td>Alamat</td><td>: {{ $fmtAlamat($catinWanita) }}</td></tr>
    </table>
    <p>Memberikan izin kepada anak kami untuk melakukan pernikahan dengan:</p>
    <table>
        <tr><td>Nama lengkap dan alias</td><td>: {{ $catinPria?->name ?: '-' }}</td></tr>
        <tr><td>Bin/binti</td><td>: {{ $catinPria?->bin_binti ?: '-' }}</td></tr>
        <tr><td>Nomor Induk Kependudukan</td><td>: {{ $catinPria?->nik ?: '-' }}</td></tr>
        <tr><td>Alamat</td><td>: {{ $fmtAlamat($catinPria) }}</td></tr>
    </table>
    <p class="closing">Demikian surat izin ini dibuat dengan kesadaran tanpa ada paksaan dari siapapun dan untuk digunakan seperlunya.</p>
    <div class="signatures">
        <div>Orang Tua Catin Pria<div class="name">{{ $fmtParentName($ayahPria) }}</div></div>
        <div>Orang Tua Catin Wanita<div class="name">{{ $fmtParentName($ayahWanita) }}</div></div>
    </div>
    @endif
</body>
</html>
