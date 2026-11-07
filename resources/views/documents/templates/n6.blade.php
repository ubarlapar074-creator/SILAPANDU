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
    <div class="head">
        <p>LAMPIRAN X</p>
        <p>KEPUTUSAN DIREKTUR JENDERAL BIMBINGAN MASYARAKAT ISLAM</p>
        <p>NOMOR 473 TAHUN 2020</p>
        <p>TENTANG PETUNJUK TEKNIS PELAKSANAAN PENCATATAN PERNIKAHAN</p>
        <h1>FORMULIR SURAT KETERANGAN KEMATIAN</h1>
        <h2>Model N6</h2>
    </div>

    @php
        $desaKop = trim((string) ($villageProfile?->village_name ?? '')) ?: '-';
        $desaTtd = $desaKop;
        $almarhum = $ayahWanita;
        $binAlmarhum = $almarhum?->nama_ayah_kandung ? 'bin '.$almarhum->nama_ayah_kandung : '-';
    @endphp
    <table>
        <tr><td>KANTOR DESA/KELURAHAN</td><td>: {{ $desaKop }}</td></tr>
        <tr><td>KECAMATAN</td><td>: {{ $villageProfile?->district_name ?? '-' }}</td></tr>
        <tr><td>KABUPATEN/KOTA</td><td>: {{ $villageProfile?->regency_name ?? '-' }}</td></tr>
    </table>
    <p>Nomor : {{ $registration->nomor_surat ?: '-' }}</p>

    <p>Yang bertanda tangan di bawah ini menerangkan dengan sesungguhnya bahwa:</p>
    <p><strong>A. Data almarhum</strong></p>
    <table>
        <tr><td>1. Nama lengkap dan alias</td><td>: <x-nama :nama="$almarhum?->nama_dengan_gelar" /></td></tr>
        <tr><td>2. Bin/binti</td><td>: {{ $binAlmarhum }}</td></tr>
        <tr><td>3. Nomor Induk Kependudukan</td><td>: {{ $almarhum?->nik ?: '-' }}</td></tr>
        <tr><td>4. Tempat dan Tanggal Lahir</td><td>: {{ $almarhum?->tempat_lahir ?: '-' }}, {{ $almarhum?->tgl_lahir?->format('d-m-Y') ?: '-' }}</td></tr>
        <tr><td>5. Kewarganegaraan</td><td>: -</td></tr>
        <tr><td>6. Agama</td><td>: {{ $almarhum?->agama ?: '-' }}</td></tr>
        <tr><td>7. Pekerjaan</td><td>: {{ $almarhum?->pekerjaan ?: '-' }}</td></tr>
        <tr><td>8. Alamat</td><td>: {{ \App\Support\Alamat::pendek($almarhum?->alamat) }}</td></tr>
    </table>

    <table>
        <tr><td>Telah meninggal dunia pada tanggal</td><td>: {{ $almarhum?->tgl_meninggal?->format('d-m-Y') ?: '-' }}</td></tr>
        <tr><td>Di</td><td>: {{ $almarhum?->tempat_meninggal ?: '-' }}</td></tr>
    </table>

    <p>Yang bersangkutan adalah ayah dari:</p>
    <table>
        <tr><td>Nama lengkap dan alias</td><td>: <x-nama :nama="$catinWanita?->nama_lengkap" /></td></tr>
        <tr><td>Nomor Induk Kependudukan</td><td>: {{ $catinWanita?->nik ?: '-' }}</td></tr>
        <tr><td>Tempat dan Tanggal Lahir</td><td>: {{ $catinWanita?->tempat_lahir ?: '-' }}, {{ $catinWanita?->tgl_lahir?->format('d-m-Y') ?: '-' }}</td></tr>
        <tr><td>Alamat</td><td>: {{ \App\Support\Alamat::pendek($catinWanita?->alamat) }}</td></tr>
    </table>

    <p class="closing">Demikian surat keterangan ini dibuat dengan mengingat sumpah jabatan dan untuk digunakan seperlunya.</p>

    <div class="signature">
        <div>{{ $desaTtd . ', ' }}{{ now()->format('d F Y') }}</div>
        <div>Kepala Desa{{ $desaTtd !== '-' ? ' ' . $desaTtd : '' }}</div>
        <div class="name"><x-nama :nama="$villageProfile?->village_head_name" /></div>
    </div>
</body>
</html>
