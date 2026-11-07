<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Pernyataan Status - {{ $catin?->name ?: ($catinRole === 'PRIA' ? 'Catin Pria' : 'Catin Wanita') }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            margin: 2cm;
            color: #000;
        }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        h3 { margin-bottom: 20px; font-size: 14pt; }
        table.identitas {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table.identitas td {
            vertical-align: top;
            padding: 3px 0;
        }
        .col-label { width: 28%; }
        .col-colon { width: 2%; text-align: center; }
        .col-value { width: 70%; }
        .paragraf {
            text-align: justify;
            text-indent: 40px;
            margin-bottom: 15px;
        }
        .ttd-container {
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .ttd-table { width: 100%; border-collapse: collapse; }
        .ttd-table td { vertical-align: top; }
        .box-materai {
            width: 130px;
            height: 70px;
            border: 1px dashed #333;
            text-align: center;
            vertical-align: middle;
            line-height: 70px;
            font-size: 10pt;
            margin: 10px auto;
            color: #555;
        }
        @media print {
            body { margin: 1.5cm; }
            @page { margin: 1.5cm; }
        }
    </style>
</head>
<body>
    @php
        use App\Support\MaritalStatus;
        $statusRaw = $catin?->marital_status ?? 'BELUM_KAWIN';
        $statusLabel = MaritalStatus::labelForStatement($statusRaw, $catinRole);
        $kades = $villageProfile?->village_head_name;
        $kasi = $villageProfile?->service_officer_name;
        $ttl = trim(($catin?->birth_place ?: '-') . ', ' . ($catin?->birth_date?->format('d-m-Y') ?: '-'));
        $desaTtd = trim((string) ($villageProfile?->village_name ?? ''));
        $addrParts = array_filter([
            $catin?->address ? trim($catin->address) : null,
            $catin?->village?->name ? 'Desa ' . $catin->village->name : null,
            $catin?->district?->name ? 'Kec. ' . $catin->district->name : null,
            $catin?->regency?->name ?: null,
        ]);
        $alamatFull = $addrParts ? implode(', ', $addrParts) : '-';
    @endphp

    <div class="text-center">
        <h3 class="uppercase"><strong>SURAT PERNYATAAN STATUS</strong></h3>
    </div>

    <p>Yang bertanda tangan di bawah ini :</p>

    <table class="identitas">
        <tr><td class="col-label">Nama</td><td class="col-colon">:</td><td class="col-value uppercase"><strong>{{ $catin?->name ?: '-' }}</strong></td></tr>
        <tr><td class="col-label">Bin / Binti</td><td class="col-colon">:</td><td class="col-value">{{ $catin?->bin_binti ?: '-' }}</td></tr>
        <tr><td class="col-label">Nomor Induk Kependudukan</td><td class="col-colon">:</td><td class="col-value">{{ $catin?->nik ?: '-' }}</td></tr>
        <tr><td class="col-label">Tempat dan tanggal lahir</td><td class="col-colon">:</td><td class="col-value">{{ $ttl }}</td></tr>
        <tr><td class="col-label">Kewarganegaraan</td><td class="col-colon">:</td><td class="col-value">{{ $catin?->nationality ?: '-' }}</td></tr>
        <tr><td class="col-label">Agama</td><td class="col-colon">:</td><td class="col-value">{{ $catin?->religion ?: '-' }}</td></tr>
        <tr><td class="col-label">Pekerjaan</td><td class="col-colon">:</td><td class="col-value">{{ $catin?->occupation ?: '-' }}</td></tr>
        <tr><td class="col-label">Alamat</td><td class="col-colon">:</td><td class="col-value">{{ $alamatFull }}</td></tr>
    </table>

    <div class="paragraf">
        Dengan ini saya menyatakan dengan sebenarnya bahwa hingga saat ini saya berstatus <strong>{{ $statusLabel }}</strong> dan tidak sedang terkait oleh pernikahan/perkawinan dengan siapapun baik secara siri maupun negara serta tidak dalam masa pendidikan yang melarang saya untuk melangsungkan pernikahan. Pernyataan ini saya buat untuk kelengkapan persyaratan pendaftaran Nikah.
    </div>

    <div class="paragraf">
        Demikian Surat Pernyataan ini saya buat dengan sebenarnya dalam keadaan sehat jasmani serta tidak dipengaruhi pihak manapun, apabila dikemudian hari ternyata pernyataan saya ini tidak benar, maka saya bersedia dituntut dengan ketentuan perundang-undangan yang berlaku.
    </div>

    <div class="ttd-container">
        <table class="ttd-table">
            <tr>
                <td style="width: 50%;">
                    <p style="margin-bottom: 10px;">Saksi-saksi:</p>
                    <table style="width: 100%; margin-bottom: 15px;">
                        <tr><td style="width: 10%;">1.</td><td style="width: 35%;">Kepala Desa</td><td style="width: 55%;">: _____________________</td></tr>
                        <tr><td></td><td colspan="2" style="padding-top: 5px;"><strong class="uppercase">{{ $kades ?: '........................' }}</strong></td></tr>
                    </table>
                    <table style="width: 100%;">
                        <tr><td style="width: 10%;">2.</td><td style="width: 35%;">Kasi Pelayanan</td><td style="width: 55%;">: _____________________</td></tr>
                        <tr><td></td><td colspan="2" style="padding-top: 5px;"><strong class="uppercase">{{ $kasi ?: '........................' }}</strong></td></tr>
                    </table>
                </td>
                <td style="width: 50%; text-align: center;">
                    <p>{{ $desaTtd !== '' ? $desaTtd . ', ' : '[nama desa belum diatur], ' }}{{ now()->translatedFormat('d F Y') }}</p>
                    <p style="margin: 0;">Yang Menyatakan,</p>
                    <div class="box-materai">Materai Rp10.000</div>
                    <p style="margin-top: 5px; margin-bottom: 0;" class="uppercase"><strong><u>{{ $catin?->name ?: '........................' }}</u></strong></p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
