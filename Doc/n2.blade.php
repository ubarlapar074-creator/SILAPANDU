<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Model N2 - Permohonan Kehendak Nikah</title>
    <style>
        @page { size: A4; margin: 16mm 18mm; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; margin: 0; color: #000; }
        .header-lampiran { font-size: 10pt; text-align: center; line-height: 1.35; margin-bottom: 2px; }
        .model-n2 { text-align: right; font-weight: bold; font-size: 10pt; margin: 4px 0 14px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        table { border-collapse: collapse; }
        table.tabel-data { width: 100%; margin: 12px 0; }
        table.tabel-data td { vertical-align: top; padding: 4px 0; }
        .col-label { width: 25%; }
        .col-colon { width: 3%; text-align: center; }
        .col-value { width: 72%; }
        ol { margin-top: 5px; margin-bottom: 14px; padding-left: 20px; }
        ol li { margin-bottom: 3px; }
        .ttd-container { width: 100%; margin-top: 28px; page-break-inside: avoid; }
        .ttd-table { width: 100%; border-collapse: collapse; }
        .ttd-table td { vertical-align: top; width: 50%; }
        .closing { text-align: justify; margin-top: 12px; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body>

    @php
        $desaTtd = trim((string) ($villageProfile?->village_name ?? ''));
        $kecName = trim((string) ($villageProfile?->district_name ?? ''));
        $kuaName = trim((string) ($villageProfile?->kua_head_name ?? ''));
        $kuaNip = trim((string) ($villageProfile?->kua_head_nip ?? ''));
        $alamatAkad = $registration->marriage_location_address ?: ($villageProfile?->office_address ?: ($desaTtd !== '' ? 'Desa ' . $desaTtd : '-'));
        $hariTglJam = trim(($registration->marriage_date ? \Carbon\Carbon::parse($registration->marriage_date)->translatedFormat('l, d F Y') : '-') . ', ' . ($registration->marriage_time ? \Carbon\Carbon::parse($registration->marriage_time)->format('H:i') . ' WIB' : '-'), ', ');
    @endphp

    <div class="header-lampiran">
        <p style="margin:0;">LAMPIRAN VI</p>
        <p style="margin:0;">KEPUTUSAN DIREKTUR JENDERAL BIMBINGAN MASYARAKAT ISLAM</p>
        <p style="margin:0;">NOMOR 473 TAHUN 2020</p>
        <p style="margin:0;">TENTANG</p>
        <p style="margin:0;">PETUNJUK TEKNIS PELAKSANAAN PENCATATAN PERNIKAHAN</p>
    </div>
    <div class="model-n2">MODEL N2</div>

    <div class="text-right" style="margin-bottom: 18px;">
        {{ $desaTtd !== '' ? $desaTtd : '....................' }}, {{ now()->translatedFormat('d F Y') }}
    </div>

    <table style="width:100%; margin: 6px 0 14px; border-collapse:collapse;">
        <tr><td style="width:15%;">Perihal</td><td style="width:3%;">:</td><td style="width:82%;"><strong>Permohonan kehendak nikah</strong></td></tr>
        <tr><td colspan="3" style="padding-top:10px;">Kepada yth,<br>Kepala KUA {{ $kecName !== '' ? $kecName : '[Nama daerah Kecamatan]' }}<br>Di {{ $kecName !== '' ? $kecName : '[Nama daerah Kecamatan]' }}</td></tr>
    </table>

    <p>Dengan hormat, kami mengajukan permohonan kehendak nikah untuk atas nama:</p>

    <table class="tabel-data">
        <tr><td class="col-label">Calon Suami</td><td class="col-colon">:</td><td class="col-value">{{ $catinPria?->name ?: '..........................................................................................' }}</td></tr>
        <tr><td class="col-label">Calon Istri</td><td class="col-colon">:</td><td class="col-value">{{ $catinWanita?->name ?: '..........................................................................................' }}</td></tr>
        <tr><td class="col-label">Hari/Tanggal/Jam</td><td class="col-colon">:</td><td class="col-value">{{ $hariTglJam ?: '..........................................................................................' }}</td></tr>
        <tr><td class="col-label">Tempat akad nikah</td><td class="col-colon">:</td><td class="col-value">{{ $alamatAkad }}</td></tr>
        <tr><td class="col-label">Mas Kawin</td><td class="col-colon">:</td><td class="col-value">{{ $registration->marriage_dowry_display ?: '..........................................................................................' }}</td></tr>
        <tr><td class="col-label">Nomor Hp</td><td class="col-colon">:</td><td class="col-value">{{ $catinPria?->no_hp ?: ($catinWanita?->no_hp ?: '..........................................................................................') }}</td></tr>
        <tr><td class="col-label">Email</td><td class="col-colon">:</td><td class="col-value">{{ $catinPria?->email ?: ($catinWanita?->email ?: '..........................................................................................') }}</td></tr>
    </table>

    <p style="margin-bottom:5px;">Bersama ini kami sampaikan surat-surat yang diperlukan untuk diperiksa sebagai berikut:</p>
    <ol>
        <li>Surat pengantar nikah dari Desa/Kelurahan</li>
        <li>Persetujuan calon mempelai</li>
        <li>Fotokopi KTP</li>
        <li>Fotokopi akte kelahiran</li>
        <li>Fotokopi Kartu Keluarga</li>
        <li>Paspoto 2x3=3 lembar berlatar belakang biru</li>
        <li>Paspoto 4x6 lembar berlatar belakang biru</li>
        <li></li>
        <li></li>
    </ol>

    <p class="closing">Demikian permohonan ini kami sampaikan, kiranya dapat diperiksa, dihadiri dan dicatat sesuai dengan ketentuan peraturan perundang-undangan.</p>

    <div class="ttd-container">
        <table class="ttd-table">
            <tr>
                <td>
                    <p style="margin:0;">Diterima tanggal ..........................</p>
                    <p style="margin:0;">Yang menerima,</p>
                    <p style="margin:0;">Kepala KUA Kec. {{ $kecName !== '' ? $kecName : '[Nama Kecamatan]' }}</p>
                    <br><br><br>
                    <p style="margin:0;">{{ $kuaName !== '' ? $kuaName : 'Nama Kepala KUA' }}</p>
                    <p style="margin:0;">{{ $kuaNip !== '' ? 'NIP. ' . $kuaNip : 'NIP. ....................................................' }}</p>
                </td>
                <td class="text-center">
                    <p style="margin:0;">Wassalam,</p>
                    <p style="margin:0;">Pemohon</p>
                    <br><br><br><br>
                    <p style="margin:0;"><strong><u>{{ $catinPria?->name ?: '....................................................' }}</u></strong></p>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
