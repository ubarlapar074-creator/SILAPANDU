<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Keterangan Kematian - Model N6</title>
    <style>
        @page { size: A4; margin: 16mm 19mm 18mm; }
        body { color: #111; font-family: "Times New Roman", serif; font-size: 12pt; line-height: 1.25; margin: 0; }
        table { border-collapse: collapse; width: 100%; table-layout: fixed; }
        td { padding: 2px 0; vertical-align: top; }
        td:first-child { width: 38%; padding-right: 8px; }
        .head { text-align: center; }
        .head p { margin: 0; font-size: 10pt; }
        .head h1 { font-size: 15pt; margin: 16px 0 1px; }
        .head h2 { font-size: 13pt; margin: 0 0 12px; }
        .closing { margin-top: 12px; text-align: justify; }
        .signature { margin: 24px 0 0 62%; text-align: center; }
        .signature .name { margin-top: 72px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="head">
        <p>LAMPIRAN X</p>
        <p>KEPUTUSAN DIREKTUR JENDERAL BIMBINGAN MASYARAKAT ISLAM</p>
        <p>NOMOR 473 TAHUN 2020</p>
        <p>TENTANG PETUNJUK TEKNIS PELAKSANAAN PENCATATAN PERNIKAHAN</p>
        <h1>FORMULIR SURAT KETERANGAN KEMATIAN</h1>
        <h2>Model N6</h2>
    </div>

    @php $desaKop = trim((string) ($villageProfile?->village_name ?? '')); $desaKop = $desaKop !== '' ? $desaKop : '[nama desa belum diatur]'; $fmtAlamat = function($m) { if (!$m) return '-'; $parts = array_filter([$m->address ? trim($m->address) : null, $m->village?->name ? 'Desa ' . $m->village->name : null, $m->district?->name ? 'Kec. ' . $m->district->name : null, $m->regency?->name ?: null]); return $parts ? implode(', ', $parts) : '-'; }; $desaTtd = trim((string) ($villageProfile?->village_name ?? '')); @endphp
    <table>
        <tr><td>KANTOR DESA/KELURAHAN</td><td>: {{ $desaKop }}</td></tr>
        <tr><td>KECAMATAN</td><td>: {{ $villageProfile?->district_name ?? '-' }}</td></tr>
        <tr><td>KABUPATEN/KOTA</td><td>: {{ $villageProfile?->regency_name ?? '-' }}</td></tr>
    </table>
    <p>Nomor : {{ $n6->document_number }}</p>

    <p>Yang bertanda tangan di bawah ini menerangkan dengan sesungguhnya bahwa:</p>
    <p><strong>A. Data almarhum</strong></p>
    <table>
        <tr><td>1. Nama lengkap dan alias</td><td>: {{ $n6->deceased_name }}</td></tr>
        <tr><td>2. Bin/binti</td><td>: {{ $n6->deceased_bin_binti }}</td></tr>
        <tr><td>3. Nomor Induk Kependudukan</td><td>: {{ $n6->deceased_nik ?: '-' }}</td></tr>
        <tr><td>4. Tempat dan Tanggal Lahir</td><td>: {{ $n6->deceased_birth_place }}, {{ $n6->deceased_birth_date?->format('d-m-Y') ?: '-' }}</td></tr>
        <tr><td>5. Kewarganegaraan</td><td>: {{ $n6->deceased_nationality ?: '-' }}</td></tr>
        <tr><td>6. Agama</td><td>: {{ $n6->deceased_religion ?: '-' }}</td></tr>
        <tr><td>7. Pekerjaan</td><td>: {{ $n6->deceased_occupation ?: '-' }}</td></tr>
        <tr><td>8. Alamat</td><td>: {{ $n6->deceased_address }}</td></tr>
    </table>

    <table>
        <tr><td>Telah meninggal dunia pada tanggal</td><td>: {{ $n6->death_date?->format('d-m-Y') }}</td></tr>
        <tr><td>Di</td><td>: {{ $n6->death_place ?: '-' }}</td></tr>
    </table>

    <p>Yang bersangkutan adalah ayah dari:</p>
    <table>
        <tr><td>Nama lengkap dan alias</td><td>: {{ $catinWanita?->name ?: '-' }}</td></tr>
        <tr><td>Nomor Induk Kependudukan</td><td>: {{ $catinWanita?->nik ?: '-' }}</td></tr>
        <tr><td>Tempat dan Tanggal Lahir</td><td>: {{ $catinWanita?->birth_place ?: '-' }}, {{ $catinWanita?->birth_date?->format('d-m-Y') ?: '-' }}</td></tr>
        <tr><td>Alamat</td><td>: {{ $fmtAlamat($catinWanita) }}</td></tr>
    </table>

    <p class="closing">Demikian surat keterangan ini dibuat dengan mengingat sumpah jabatan dan untuk digunakan seperlunya.</p>

    <div class="signature">
        <div>{{ $desaTtd !== '' ? $desaTtd . ', ' : '[nama desa belum diatur], ' }}{{ $n6->issue_date?->format('d F Y') ?: now()->format('d F Y') }}</div>
        <div>{{ $n6->signed_by_position ?: 'Kepala Desa' . ($desaTtd !== '' ? ' ' . $desaTtd : '') }}</div>
        <div class="name">{{ $n6->signed_by ?: ($villageProfile?->village_head_name ?: '........................') }}</div>
    </div>
</body>
</html>
