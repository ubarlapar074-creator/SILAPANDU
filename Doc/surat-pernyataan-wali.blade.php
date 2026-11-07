<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pernyataan Wali Nikah</title>
    <style>
        @page { size: A4; margin: 16mm 19mm 18mm; }
        body { color: #111; font-family: "Times New Roman", serif; font-size: 12pt; line-height: 1.35; margin: 0; }
        table { border-collapse: collapse; width: 100%; table-layout: fixed; }
        td { padding: 2px 0; vertical-align: top; }
        td:first-child { width: 38%; padding-right: 8px; }
        .head { text-align: center; }
        .head h1 { font-size: 14pt; margin: 18px 0 14px; }
        .number { margin: 8px 0 10px; }
        .section { margin: 10px 0 4px; font-weight: bold; }
        .closing { margin-top: 14px; text-align: justify; }
        .signatures { display: table; width: 100%; table-layout: fixed; margin-top: 28px; }
        .signatures > div { display: table-cell; width: 50%; text-align: center; vertical-align: top; }
        .signatures .name { margin-top: 72px; font-weight: bold; text-transform: uppercase; }
        .box-materai { width: 120px; height: 64px; border: 1px dashed #444; margin: 10px auto; line-height: 64px; font-size: 9pt; color: #555; }
        .intro { margin: 6px 0 8px; }
    </style>
</head>
<body>
    @php
        $desaTtd = trim((string) ($villageProfile?->village_name ?? ''));
        $desaKop = $desaTtd !== '' ? $desaTtd : ($registration->marriage_location_address ?: '[nama desa belum diatur]');
        $fmtAlamat = function($m) { if (!$m) return '-'; $parts = array_filter([$m->address ? trim($m->address) : null, $m->village?->name ? 'Desa ' . $m->village->name : null, $m->district?->name ? 'Kec. ' . $m->district->name : null, $m->regency?->name ?: null]); return $parts ? implode(', ', $parts) : '-'; };
        $fmtTtl = function($place, $date) { $d = $date ? \Carbon\Carbon::parse($date)->format('d-m-Y') : '-'; return trim(($place ?: '-') . ', ' . $d, ', '); };
        $waliSrc = ($guardian?->guardian_source === 'PARENT' && $guardianParent) ? $guardianParent : $guardian;
        $waliNama = $waliSrc?->name ?? $guardian?->name ?? '-';
        if ($guardian?->relationship === 'AYAH_KANDUNG' && $guardianParent) $waliNama = $guardianParent->formatted_name ?? $guardianParent->display_name ?? $guardianParent->name ?? '-';
        elseif ($guardian?->relationship !== 'WALI_HAKIM' && $guardian?->name) $waliNama = $guardian->name;
        elseif ($guardian?->relationship === 'WALI_HAKIM') $waliNama = 'Wali Hakim (KUA)';
        $waliBin = $waliSrc?->bin_binti ?? $guardian?->bin_binti ?? '-';
        $waliTtl = $waliSrc ? $fmtTtl($waliSrc->birth_place ?? null, $waliSrc->birth_date ?? null) : '-';
        $waliAgama = $waliSrc?->religion ?? $catinWanita?->religion ?? '-';
        $waliPekerjaan = $waliSrc?->occupation ?? $guardian?->occupation ?? '-';
        $waliAlamat = $waliSrc ? $fmtAlamat($waliSrc) : ($guardian?->address ?: '-');
        if ($guardian?->relationship === 'WALI_HAKIM') { $waliBin = '-'; $waliTtl = '-'; $waliPekerjaan = '-'; $waliAlamat = 'Kantor KUA Setempat'; }
        $wanitaNama = $catinWanita?->name ?: '-';
        $wanitaBinti = $catinWanita?->bin_binti ?: '-';
        $wanitaTtl = $fmtTtl($catinWanita?->birth_place ?? null, $catinWanita?->birth_date ?? null);
        $wanitaAgama = $catinWanita?->religion ?: '-';
        $wanitaPekerjaan = $catinWanita?->occupation ?: '-';
        $wanitaAlamat = $catinWanita ? $fmtAlamat($catinWanita) : '-';
        $hubunganLabel = \App\Support\WaliHelper::label($guardian?->relationship ?? '');
        $ayahStatus = $ayahWanita?->status ?? null;
        $sebabWali = \App\Support\WaliHelper::resolveSebab($guardian?->sebab_wali ?? null, $guardian?->relationship ?? '', $ayahStatus);
        $nomorSurat = $documents['N1']?->document_number ?? $documents['N2']?->document_number ?? 'diisi pada pengaturan nomor surat';
    @endphp

    <div class="head">
        <h1>SURAT PERNYATAAN WALI NIKAH</h1>
    </div>

    <table>
        <tr><td>KANTOR DESA/KELURAHAN</td><td>: {{ $desaKop }}</td></tr>
        <tr><td>KECAMATAN</td><td>: {{ $villageProfile?->district_name ?? '-' }}</td></tr>
        <tr><td>KABUPATEN/KOTA</td><td>: {{ $villageProfile?->regency_name ?? '-' }}</td></tr>
    </table>

    <p class="intro">Yang bertanda tangan di bawah ini :</p>

    <p class="section">I. Wali Nikah</p>
    <table>
        <tr><td>Nama lengkap dan alias</td><td>: {{ $waliNama }}</td></tr>
        <tr><td>Bin</td><td>: {{ $waliBin }}</td></tr>
        <tr><td>Tempat dan tanggal lahir</td><td>: {{ $waliTtl }}</td></tr>
        <tr><td>Agama</td><td>: {{ $waliAgama }}</td></tr>
        <tr><td>Pekerjaan</td><td>: {{ $waliPekerjaan }}</td></tr>
        <tr><td>Alamat</td><td>: {{ $waliAlamat }}</td></tr>
    </table>

    <p>Menyatakan dengan sesungguhnya bahwa saya adalah Wali Nikah/Hakim dari seorang perempuan :</p>

    <p class="section">II. Calon Pengantin Wanita</p>
    <table>
        <tr><td>Nama</td><td>: {{ $wanitaNama }}</td></tr>
        <tr><td>Binti</td><td>: {{ $wanitaBinti }}</td></tr>
        <tr><td>Tempat dan tanggal lahir</td><td>: {{ $wanitaTtl }}</td></tr>
        <tr><td>Agama</td><td>: {{ $wanitaAgama }}</td></tr>
        <tr><td>Pekerjaan</td><td>: {{ $wanitaPekerjaan }}</td></tr>
        <tr><td>Alamat</td><td>: {{ $wanitaAlamat }}</td></tr>
    </table>

    <table>
        <tr><td>Hubungan wali</td><td>: {{ $hubunganLabel }}</td></tr>
        <tr><td>Sebab wali</td><td>: {{ $sebabWali }}</td></tr>
    </table>

    <p class="closing">Demikian Surat Pernyataan Wali Nikah ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>

    <p style="margin: 12px 0 10px;">Nomor Surat Desa : {{ $nomorSurat }}</p>

    <div class="signatures">
        <div>
            Mengetahui,<br>Kepala Desa{{ $desaTtd !== '' ? ' ' . $desaTtd : '' }}
            <div class="name">{{ $villageProfile?->village_head_name ?: '........................' }}</div>
        </div>
        <div>
            {{ $desaTtd !== '' ? $desaTtd : '[nama desa belum diatur]' }}, {{ now()->translatedFormat('d F Y') }}<br>Yang Menyatakan,
            <div class="box-materai">Materai Rp10.000</div>
            <div class="name">{{ $waliNama !== '-' ? $waliNama : '........................' }}</div>
        </div>
    </div>
</body>
</html>
