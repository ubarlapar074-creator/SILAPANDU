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
        $desaTtd = trim((string) ($villageProfile?->village_name ?? ''));
        $desaKop = $desaTtd !== '' ? $desaTtd : '-';
        $fmtAlamat = fn($m) => \App\Support\Alamat::pendek($m?->address);
        $isHakim = $guardian?->jenis_wali === 'hakim';
        $waliNama = ! $guardian ? '-' : ($isHakim ? 'Wali Hakim (KUA)' : ($guardian->name ?: '-'));
        // Bin (pria) / Binti (wanita) diisi dari ayah kandung wali.
        $waliBin = '-';
        if (! $guardian || $isHakim) {
            $waliBin = '-';
        } elseif (($guardian->hubungan ?? '') === 'Ayah Kandung') {
            // Wali adalah ayah kandung wanita, ambil dari data bapak wanita.
            $waliBin = $ayahWanita?->nama_ayah_kandung ?: '-';
        } elseif ($guardian->warga_id) {
            $wargaWali = $guardian->warga;
            if ($wargaWali && $wargaWali->nama_ayah) {
                $waliBin = ($wargaWali->jenis_kelamin === 'P' ? 'binti ' : 'bin ') . $wargaWali->nama_ayah;
            }
        }
        $waliTtl = (! $guardian || $isHakim) ? '-' : trim(($guardian->birth_place ?: '-') . ', ' . ($guardian->birth_date?->format('d-m-Y') ?: '-'), ', ');
        $waliAgama = (! $guardian || $isHakim) ? '-' : ($guardian->agama ?: ($catinWanita?->religion ?: '-'));
        $waliPekerjaan = (! $guardian || $isHakim) ? '-' : ($guardian->occupation ?: '-');
        $waliAlamat = (! $guardian || $isHakim) ? '-' : \App\Support\Alamat::pendek($guardian->address);
        $wanitaNama = $catinWanita?->name ?: '-';
        $namaAyahWanita = $ayahWanita?->nama_dengan_gelar;
        $wanitaAyahKandung = $ayahWanita?->nama_ayah_kandung ?: '-';
        $wanitaTtl = $catinWanita ? trim(($catinWanita->birth_place ?: '-') . ', ' . ($catinWanita->birth_date?->format('d-m-Y') ?: '-'), ', ') : '-';
        $wanitaAgama = $catinWanita?->religion ?: '-';
        $wanitaPekerjaan = $catinWanita?->occupation ?: '-';
        $wanitaAlamat = $catinWanita ? $fmtAlamat($catinWanita) : '-';
        $hubunganLabel = $guardian?->hubungan ?: '-';
        $sebabWali = $isHakim
            ? ($guardian?->alasan_wali_hakim ?: 'Tidak ada wali nasab (wali hakim)')
            : ($guardian?->hubungan ?: '-');
        $nomorSurat = $registration->nomor_surat ?: '-';
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
        <tr><td>Nama lengkap dan alias</td><td>: <x-nama :nama="$waliNama" /></td></tr>
        <tr><td>Bin</td><td>: {{ $waliBin }}</td></tr>
        <tr><td>Tempat dan tanggal lahir</td><td>: {{ $waliTtl }}</td></tr>
        <tr><td>Agama</td><td>: {{ $waliAgama }}</td></tr>
        <tr><td>Pekerjaan</td><td>: {{ $waliPekerjaan }}</td></tr>
        <tr><td>Alamat</td><td>: {{ $waliAlamat }}</td></tr>
    </table>

    <p>Menyatakan dengan sesungguhnya bahwa saya adalah Wali Nikah/Hakim dari seorang perempuan :</p>

    <p class="section">II. Calon Pengantin Wanita</p>
    <table>
        <tr><td>Nama</td><td>: <x-nama :nama="$wanitaNama" /></td></tr>
        <tr><td>Binti</td><td>: {{ $namaAyahWanita ?: '-' }}</td></tr>
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
            <div class="name"><x-nama :nama="$villageProfile?->village_head_name" /></div>
        </div>
        <div>
            {{ $desaTtd !== '' ? $desaTtd : '-' }}, {{ now()->translatedFormat('d F Y') }}<br>Yang Menyatakan,
            <div class="box-materai">Materai Rp10.000</div>
            <div class="name"><x-nama :nama="$waliNama" /></div>
        </div>
    </div>
</body>
</html>
