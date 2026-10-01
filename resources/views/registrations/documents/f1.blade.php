<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pendaftaran Nikah</title>
    <style>
        @page {
            size: 215mm 330mm;
            margin: 8pt;
        }
        @media print {
            body {
                line-height: 1.05;
            }
            .header-table {
                margin-bottom: 5mm !important;
            }
            .info-block {
                margin-bottom: 2px !important;
            }
            .info-block td,
            .data-table th,
            .data-table td,
            .wali-table th,
            .wali-table td {
                padding-top: 1px !important;
                padding-bottom: 1px !important;
            }
            .data-table th,
            .wali-table th {
                font-size: 11pt;
                line-height: 1.05;
            }
            .data-table td:first-child,
            .wali-table td:first-child {
                line-height: 1.05;
            }
            .data-table tr:last-child td,
            .wali-table tr:last-child td {
                height: 16px !important;
            }
            .data-table,
            .wali-table {
                page-break-inside: avoid;
                break-inside: avoid;
            }
        }
        body {
            font-family: 'Times New Roman', Times, serif, sans-serif;
            font-size: 12pt;
            color: #000;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        th, td {
            border: 1px solid #000;
            padding: 3px 5px;
            vertical-align: middle;
            font-size: 12pt;
            word-wrap: break-word;
        }
        .no-border td, .no-border th {
            border: none;
        }
        .header-title {
            text-align: center;
            font-weight: bold;
            font-size: 16pt;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 1px;
        }
        .desa-title {
            text-align: center;
            font-weight: bold;
            font-size: 12pt;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .photo-box {
            border: 1px solid #000;
            width: 20mm;
            height: 30mm;
            text-align: center;
            vertical-align: middle;
            font-size: 8pt;
            line-height: 1.3;
            background-color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        .photo-box-small {
            width: 20mm;
            height: 30mm;
            font-size: 7.5pt;
        }
        .section-title {
            display: none;
        }
        /* Header dengan foto kiri kanan sejajar */
        .header-table td {
            border: none;
            padding: 3px 0;
        }
        .header-table .photo-cell {
            width: 14%;
            text-align: center;
            vertical-align: middle;
        }
        .header-table .title-cell {
            width: 72%;
            text-align: center;
            vertical-align: middle;
        }
        /* Info block 2 kolom */
        .info-block td {
            border: none;
            padding: 3px 5px;
            font-size: 12pt;
        }
        .info-block .label-col {
            width: 35%;
            font-weight: bold;
        }
        .info-block .value-col {
            width: 65%;
        }
        /* Main data tables */
        .data-table th {
            background-color: #d9d9d9;
            font-size: 14pt;
            text-align: center;
            padding: 4px 3px;
            font-weight: bold;
        }
        .data-table {
            width: 92% !important;
            margin-left: auto;
            margin-right: auto;
        }
        .info-block {
            width: 92% !important;
            margin-left: auto;
            margin-right: auto;
        }
        .info-block > tbody > tr > td:first-child {
            padding-left: 0 !important;
        }
        .info-block > tbody > tr > td:last-child {
            padding-right: 0 !important;
        }
        .info-block > tbody > tr > td:last-child table td:first-child {
            width: 45% !important;
            white-space: nowrap;
        }
        .info-block > tbody > tr > td:last-child table td:last-child {
            width: 53% !important;
        }
        .data-table td {
            font-size: 12pt;
            padding: 4px 5px;
        }
        .data-table td:first-child,
        .wali-table td:first-child {
            font-size: 14pt;
            font-weight: bold;
        }
        .data-table .label-col {
            width: 18.75%;
            font-weight: bold;
            text-align: left;
        }
        .data-table .value-col {
            width: 27.08%;
        }
        .signature-cell {
            height: 28px;
        }
        /* Wali dan Saksi table */
        .wali-table th {
            background-color: #d9d9d9;
            font-size: 14pt;
            text-align: center;
            padding: 4px 3px;
            font-weight: bold;
        }
        .wali-table {
            width: 92% !important;
            margin-left: auto;
            margin-right: auto;
        }
        .wali-table td {
            font-size: 12pt;
            padding: 4px 5px;
        }
        .wali-table .label-col {
            width: 18.75%;
            font-weight: bold;
            text-align: left;
        }
        .wali-table .value-col {
            width: 27.08%;
        }
        /* Spasi antar elemen */
        .spacer-1cm {
            height: 1cm;
        }
        .spacer-1-2cm {
            height: 1.2cm;
        }
        .spacer-table {
            border: none;
            height: 1cm;
        }
        .spacer-table td {
            border: none;
            height: 1cm;
            padding: 0;
        }
        .spacer-12mm-table td {
            border: none;
            height: 0;
            padding: 0;
        }
        .spacer-12mm-table {
            display: none;
        }
        /* Batas bawah untuk tanda tangan */
        .signature-line {
            border-bottom: 1px solid #000;
            height: 25px;
        }
    </style>
</head>
<body>

    @php
        $catinPria = $registration->catins->where('role', 'PRIA')->first();
        $catinWanita = $registration->catins->where('role', 'WANITA')->first();

        $marriageDateLabel = '-';
        if ($registration->marriage_date) {
            $marriageDate = \Carbon\Carbon::parse($registration->marriage_date);
            $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            $months = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
            ];
            $marriageDateLabel = $days[$marriageDate->dayOfWeek]
                . ' / ' . $marriageDate->format('d')
                . ' ' . $months[(int) $marriageDate->format('n')]
                . ' ' . $marriageDate->format('Y');
        }

        $formatBirthDate = function($date) {
            if (!$date) return '-';

            try {
                return \Carbon\Carbon::parse($date)->format('d/m/Y');
            } catch (\Throwable $exception) {
                return (string) $date;
            }
        };

        $formatStatus = function(?string $status, string $role = 'PRIA') {
            if (!$status || trim($status) === '') return '-';
            return \App\Support\MaritalStatus::labelForRaw($status, $role);
        };
        $formatParentStatus = function(?string $status, string $parentRole = 'AYAH') {
            if (!$status || trim($status) === '') return '-';
            $r = strtoupper(trim($parentRole)) === 'IBU' ? 'WANITA' : 'PRIA';
            return \App\Support\MaritalStatus::labelForRaw($status, $r);
        };

        $ayahPria = $catinPria ? $catinPria->parents->where('role', 'AYAH')->first() : null;
        $ibuPria = $catinPria ? $catinPria->parents->where('role', 'IBU')->first() : null;

        $ayahWanita = $catinWanita ? $catinWanita->parents->where('role', 'AYAH')->first() : null;
        $ibuWanita = $catinWanita ? $catinWanita->parents->where('role', 'IBU')->first() : null;

        $fmtAlamatFull = function($m) { if (!$m) return '-'; $parts = array_filter([$m->address ? trim($m->address) : null, $m->village?->name ? 'Desa ' . $m->village->name : null, $m->district?->name ? 'Kec. ' . $m->district->name : null, $m->regency?->name ?: null]); return $parts ? implode(', ', $parts) : '-'; };
         $getParentData = function($parent) use ($formatBirthDate, $fmtAlamatFull, $formatParentStatus) {
            $data = ['name'=>'-','nik'=>'-','ttl'=>'-','occupation'=>'-','address'=>'-','marital_status'=>'-','phone'=>'-','email'=>'-'];
            if (!$parent) return $data;
            $data['name'] = $parent->formatted_name ?? $parent->display_name ?? '-';
            $status = strtoupper(trim($parent->status ?? 'HIDUP'));
            $isMeninggal = ($status === 'MENINGGAL' || $status === 'WAFAT');
            $rawMs = $parent->marital_status;
            $data['marital_status'] = $rawMs ? $formatParentStatus($rawMs, $parent->role ?? 'AYAH') : 'Kawin';
            if (!$isMeninggal) {
                $data['nik'] = $parent->nik ?? '-';
                $data['ttl'] = trim(($parent->birth_place ?? '') . ', ' . $formatBirthDate($parent->birth_date ?? null), ', ');
                $data['occupation'] = $parent->occupation ?? '-';
                $data['address'] = $fmtAlamatFull($parent);
                $data['phone'] = $parent->phone ?? '-';
                $data['email'] = $parent->email ?? '-';
            }
            return $data;
        };

        $ap = $getParentData($ayahPria);
        $ip = $getParentData($ibuPria);
        $aw = $getParentData($ayahWanita);
        $iw = $getParentData($ibuWanita);

        // Handler SSoT Wali Strict
        $guardian = $registration->guardian;
        $waliName = '-';
        $waliNik = '-';
        $waliTtl = '-';
        $waliPekerjaan = '-';
        $waliAlamat = '-';
        $waliStatus = '-';

        if ($guardian) {
            if (($guardian->relationship ?? '') === 'WALI_HAKIM') {
                $waliName = '-';
                $waliNik = '-';
                $waliTtl = '-';
                $waliPekerjaan = '-';
                $waliAlamat = '-';
                $waliStatus = '-';
            } elseif ($guardian->guardian_source === 'PARENT' && $guardian->parent) {
                $pw = $guardian->parent;
                $statusWali = strtoupper(trim($pw->status ?? 'HIDUP'));
                $isMeninggalWali = ($statusWali === 'MENINGGAL' || $statusWali === 'WAFAT');

                $waliName = $pw->formatted_name ?? $pw->display_name ?? ($pw->name ?? '-');
                if (!$isMeninggalWali) {
                    $waliNik = $pw->nik ?? '-';
                    $waliTtl = trim(($pw->birth_place ?? '') . ', ' . $formatBirthDate($pw->birth_date ?? null), ', ');
                    $waliPekerjaan = $pw->occupation ?? '-';
                    $waliAlamat = $fmtAlamatFull($pw);
                }
                $waliStatus = 'Kawin';
            } elseif ($guardian->guardian_source === 'OTHER') {
                $waliName = $guardian->name ?? '-';
                $waliNik = $guardian->nik ?? '-';
                $waliTtl = trim(($guardian->birth_place ?? '') . ', ' . $formatBirthDate($guardian->birth_date ?? null), ', ');
                $waliPekerjaan = $guardian->occupation ?? '-';
                $waliAlamat = $fmtAlamatFull($guardian);
            }
        }

        $catinAlamatPria = $fmtAlamatFull($catinPria);
        $catinAlamatWanita = $fmtAlamatFull($catinWanita);
        $waliAlamatFmt = $waliAlamat;
        $saksi1 = $registration->witnesses->get(0);
        $saksi2 = $registration->witnesses->get(1);
        $saksiAlamat1 = $fmtAlamatFull($saksi1);
        $saksiAlamat2 = $fmtAlamatFull($saksi2);
    @endphp

    <!-- ============================================================ -->
    <!-- 1. HEADER / KOP DENGAN FOTO DI SISI KIRI & KANAN SEJAJAR KOP -->
    <!-- ============================================================ -->
    <table class="no-border header-table" style="margin-bottom: 5px; width:100%;">
        <tr>
            <td class="photo-cell" style="width:13%; text-align:left; vertical-align:middle; padding-left:2px;">
                <div class="photo-box photo-box-small" style="margin:0 auto;">
                    FOTO<br>SUAMI<br>2x3
                </div>
            </td>
            <td class="title-cell" style="width:74%; text-align:center; vertical-align:middle;">
                <div class="header-title">DATA PENDAFTARAN NIKAH</div>
                @php $f1Desa = trim((string) ($villageProfile?->village_name ?? '')); @endphp
                <div class="desa-title">DESA : {{ $f1Desa !== '' ? strtoupper($f1Desa) : '[NAMA DESA BELUM DIATUR]' }}</div>
            </td>
            <td class="photo-cell" style="width:13%; text-align:right; vertical-align:middle; padding-right:2px;">
                <div class="photo-box photo-box-small" style="margin:0 auto;">
                    FOTO<br>ISTRI<br>2x3
                </div>
            </td>
        </tr>
    </table>

    <!-- ============================================================ -->
    <!-- 2. BLOK INFORMASI DI BAWAH HEADER (2 KOLOM)                  -->
    <!-- ============================================================ -->
    <table class="no-border info-block" style="margin-bottom: 5px; width:92%;">
        <tr>
            <td style="width:64.91%; vertical-align:top; padding:2px 5px 2px 0;">
                <table class="no-border" style="width:100%; font-size:9pt;">
                    <tr>
                        <td style="width:38%; font-weight:bold; padding:2px 0;">HARI/TGL AKAD</td>
                        <td style="width:2%; padding:2px 0;">:</td>
                        <td style="width:60%; padding:2px 0;">{{ $marriageDateLabel }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold; padding:2px 0;">WAKTU AKAD</td>
                        <td style="padding:2px 0;">:</td>
                        <td style="padding:2px 0;">{{ $registration->marriage_time ? \Carbon\Carbon::parse($registration->marriage_time)->format('H:i') . ' WIB' : '-' }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold; padding:2px 0;">MASKAWIN</td>
                        <td style="padding:2px 0;">:</td>
                        <td style="padding:2px 0;">{{ $registration->marriage_dowry_display ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold; padding:2px 0;">TEMPAT AKAD</td>
                        <td style="padding:2px 0;">:</td>
                        <td style="padding:2px 0;">{{ $registration->marriage_location_address ?? '-' }}</td>
                    </tr>
                </table>
            </td>
            <td style="width:35.09%; vertical-align:top; padding:2px 0; text-align:center;">
                <table class="no-border" style="width:100%; font-size:9pt;">
                    <tr>
                        <td style="width:32%; font-weight:bold; padding:2px 0;">NO. NB</td>
                        <td style="width:2%; padding:2px 0;">:</td>
                        <td style="width:66%; padding:2px 0;">-</td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold; padding:2px 0;">NO. AKTA</td>
                        <td style="padding:2px 0;">:</td>
                        <td style="padding:2px 0;">-</td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold; padding:2px 0;">NO. SERI</td>
                        <td style="padding:2px 0;">:</td>
                        <td style="padding:2px 0;">-</td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold; padding:2px 0;">PETUGAS</td>
                        <td style="padding:2px 0;">:</td>
                        <td style="padding:2px 0;">K / P¹ / P²</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- ============================================================ -->
    <!-- SPASI 1.2 cm ANTARA INFO BLOCK DAN TABEL DATA                -->
    <!-- ============================================================ -->
    <table class="no-border spacer-12mm-table" style="width:100%;">
        <tr>
            <td style="border:none; height:1.2cm; padding:0;"></td>
        </tr>
    </table>

    <!-- ============================================================ -->
    <!-- 3. TABEL DATA CALON SUAMI                                     -->
    <!-- ============================================================ -->
    <div class="section-title">A. CALON SUAMI DAN ORANG TUA</div>
    <table class="data-table" style="margin-bottom: 6px; width:92%;">
        <thead>
            <tr>
                <th style="width:18.75%; background-color:#d9d9d9; font-weight:bold; text-align:center;">DATA</th>
                <th style="width:27.08%; background-color:#d9d9d9; font-weight:bold; text-align:center;">CALON SUAMI</th>
                <th style="width:27.08%; background-color:#d9d9d9; font-weight:bold; text-align:center;">BAPAK CALON SUAMI</th>
                <th style="width:27.09%; background-color:#d9d9d9; font-weight:bold; text-align:center;">IBU CALON SUAMI</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-bold" style="padding:3px 5px;">NIK</td>
                <td style="padding:3px 5px;">{{ $catinPria->nik ?? '-' }}</td>
                <td style="padding:3px 5px;">{{ $ap['nik'] }}</td>
                <td style="padding:3px 5px;">{{ $ip['nik'] }}</td>
            </tr>
            <tr>
                <td class="text-bold" style="padding:3px 5px;">NAMA</td>
                <td style="padding:3px 5px;">{{ $catinPria->name ?? '-' }}</td>
                <td style="padding:3px 5px;">{{ $ap['name'] }}</td>
                <td style="padding:3px 5px;">{{ $ip['name'] }}</td>
            </tr>
            <tr>
                <td class="text-bold" style="padding:3px 5px;">TEMPAT/TGL</td>
                <td style="padding:3px 5px;">{{ trim(($catinPria->birth_place ?? '') . ', ' . $formatBirthDate($catinPria->birth_date ?? null), ', ') }}</td>
                <td style="padding:3px 5px;">{{ $ap['ttl'] }}</td>
                <td style="padding:3px 5px;">{{ $ip['ttl'] }}</td>
            </tr>
            <tr>
                <td class="text-bold" style="padding:3px 5px;">PEKERJAAN</td>
                <td style="padding:3px 5px;">{{ $catinPria->occupation ?? '-' }}</td>
                <td style="padding:3px 5px;">{{ $ap['occupation'] }}</td>
                <td style="padding:3px 5px;">{{ $ip['occupation'] }}</td>
            </tr>
            <tr>
                <td class="text-bold" style="padding:3px 5px;">PENDIDIKAN</td>
                <td style="padding:3px 5px;">-</td>
                <td style="padding:3px 5px;">-</td>
                <td style="padding:3px 5px;">-</td>
            </tr>
            <tr>
                <td class="text-bold" style="padding:3px 5px;">STATUS</td>
                <td style="padding:3px 5px;">{{ $formatStatus($catinPria->marital_status ?? null, 'PRIA') }}</td>
                <td style="padding:3px 5px;">{{ $ap['marital_status'] }}</td>
                <td style="padding:3px 5px;">{{ $ip['marital_status'] }}</td>
            </tr>
            <tr>
                <td class="text-bold" style="padding:3px 5px;">NO. HP</td>
                <td style="padding:3px 5px;">-</td>
                <td style="padding:3px 5px;">-</td>
                <td style="padding:3px 5px;">-</td>
            </tr>
            <tr>
                <td class="text-bold" style="padding:3px 5px;">E-MAIL</td>
                <td style="padding:3px 5px;">-</td>
                <td style="padding:3px 5px;">-</td>
                <td style="padding:3px 5px;">-</td>
            </tr>
            <tr>
                <td class="text-bold" style="padding:3px 5px;">ALAMAT</td>
                <td style="padding:3px 5px;">{{ $catinAlamatPria }}</td>
                <td style="padding:3px 5px;">{{ $ap['address'] }}</td>
                <td style="padding:3px 5px;">{{ $ip['address'] }}</td>
            </tr>
            <tr>
                <td class="text-bold" style="padding:3px 5px;">TANDA TANGAN</td>
                <td style="height:28px; padding:3px 5px;"></td>
                <td style="height:28px; padding:3px 5px;"></td>
                <td style="height:28px; padding:3px 5px;"></td>
            </tr>
        </tbody>
    </table>

    <!-- ============================================================ -->
    <!-- 4. TABEL DATA CALON ISTRI                                     -->
    <!-- ============================================================ -->
    <div class="section-title">B. CALON ISTRI DAN ORANG TUA</div>
    <table class="data-table" style="margin-bottom: 6px; width:92%;">
        <thead>
            <tr>
                <th style="width:18.75%; background-color:#d9d9d9; font-weight:bold; text-align:center;">DATA</th>
                <th style="width:27.08%; background-color:#d9d9d9; font-weight:bold; text-align:center;">CALON ISTRI</th>
                <th style="width:27.08%; background-color:#d9d9d9; font-weight:bold; text-align:center;">BAPAK CALON ISTRI</th>
                <th style="width:27.09%; background-color:#d9d9d9; font-weight:bold; text-align:center;">IBU CALON ISTRI</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-bold" style="padding:3px 5px;">NIK</td>
                <td style="padding:3px 5px;">{{ $catinWanita->nik ?? '-' }}</td>
                <td style="padding:3px 5px;">{{ $aw['nik'] }}</td>
                <td style="padding:3px 5px;">{{ $iw['nik'] }}</td>
            </tr>
            <tr>
                <td class="text-bold" style="padding:3px 5px;">NAMA</td>
                <td style="padding:3px 5px;">{{ $catinWanita->name ?? '-' }}</td>
                <td style="padding:3px 5px;">{{ $aw['name'] }}</td>
                <td style="padding:3px 5px;">{{ $iw['name'] }}</td>
            </tr>
            <tr>
                <td class="text-bold" style="padding:3px 5px;">TEMPAT/TGL</td>
                <td style="padding:3px 5px;">{{ trim(($catinWanita->birth_place ?? '') . ', ' . $formatBirthDate($catinWanita->birth_date ?? null), ', ') }}</td>
                <td style="padding:3px 5px;">{{ $aw['ttl'] }}</td>
                <td style="padding:3px 5px;">{{ $iw['ttl'] }}</td>
            </tr>
            <tr>
                <td class="text-bold" style="padding:3px 5px;">PEKERJAAN</td>
                <td style="padding:3px 5px;">{{ $catinWanita->occupation ?? '-' }}</td>
                <td style="padding:3px 5px;">{{ $aw['occupation'] }}</td>
                <td style="padding:3px 5px;">{{ $iw['occupation'] }}</td>
            </tr>
            <tr>
                <td class="text-bold" style="padding:3px 5px;">PENDIDIKAN</td>
                <td style="padding:3px 5px;">-</td>
                <td style="padding:3px 5px;">-</td>
                <td style="padding:3px 5px;">-</td>
            </tr>
            <tr>
                <td class="text-bold" style="padding:3px 5px;">STATUS</td>
                <td style="padding:3px 5px;">{{ $formatStatus($catinWanita->marital_status ?? null, 'WANITA') }}</td>
                <td style="padding:3px 5px;">{{ $aw['marital_status'] }}</td>
                <td style="padding:3px 5px;">{{ $iw['marital_status'] }}</td>
            </tr>
            <tr>
                <td class="text-bold" style="padding:3px 5px;">NO. HP</td>
                <td style="padding:3px 5px;">-</td>
                <td style="padding:3px 5px;">-</td>
                <td style="padding:3px 5px;">-</td>
            </tr>
            <tr>
                <td class="text-bold" style="padding:3px 5px;">E-MAIL</td>
                <td style="padding:3px 5px;">-</td>
                <td style="padding:3px 5px;">-</td>
                <td style="padding:3px 5px;">-</td>
            </tr>
            <tr>
                <td class="text-bold" style="padding:3px 5px;">ALAMAT</td>
                <td style="padding:3px 5px;">{{ $catinAlamatWanita }}</td>
                <td style="padding:3px 5px;">{{ $aw['address'] }}</td>
                <td style="padding:3px 5px;">{{ $iw['address'] }}</td>
            </tr>
            <tr>
                <td class="text-bold" style="padding:3px 5px;">TANDA TANGAN</td>
                <td style="height:28px; padding:3px 5px;"></td>
                <td style="height:28px; padding:3px 5px;"></td>
                <td style="height:28px; padding:3px 5px;"></td>
            </tr>
        </tbody>
    </table>

    <!-- ============================================================ -->
    <!-- 5. TABEL WALI DAN SAKSI                                       -->
    <!-- ============================================================ -->
    <div class="section-title">C. WALI NIKAH DAN SAKSI</div>
    <table class="wali-table" style="width:92%;">
        <thead>
            <tr>
                <th style="width:18.75%; background-color:#d9d9d9; font-weight:bold; text-align:center;">DATA</th>
                <th style="width:27.08%; background-color:#d9d9d9; font-weight:bold; text-align:center;">WALI</th>
                <th style="width:27.08%; background-color:#d9d9d9; font-weight:bold; text-align:center;">SAKSI 1</th>
                <th style="width:27.09%; background-color:#d9d9d9; font-weight:bold; text-align:center;">SAKSI 2</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-bold" style="padding:3px 5px;">NIK</td>
                <td style="padding:3px 5px;">{{ $waliNik }}</td>
                <td style="padding:3px 5px;">{{ $saksi1->nik ?? '-' }}</td>
                <td style="padding:3px 5px;">{{ $saksi2->nik ?? '-' }}</td>
            </tr>
            <tr>
                <td class="text-bold" style="padding:3px 5px;">NAMA</td>
                <td style="padding:3px 5px;">{{ $waliName }}</td>
                <td style="padding:3px 5px;">{{ $saksi1->name ?? '-' }}</td>
                <td style="padding:3px 5px;">{{ $saksi2->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="text-bold" style="padding:3px 5px;">TEMPAT/TGL</td>
                <td style="padding:3px 5px;">{{ $waliTtl }}</td>
                <td style="padding:3px 5px;">{{ trim(($saksi1->birth_place ?? '') . ', ' . $formatBirthDate($saksi1->birth_date ?? null), ', ') }}</td>
                <td style="padding:3px 5px;">{{ trim(($saksi2->birth_place ?? '') . ', ' . $formatBirthDate($saksi2->birth_date ?? null), ', ') }}</td>
            </tr>
            <tr>
                <td class="text-bold" style="padding:3px 5px;">PEKERJAAN</td>
                <td style="padding:3px 5px;">{{ $waliPekerjaan ?: '-' }}</td>
                <td style="padding:3px 5px;">{{ $saksi1->occupation ?? '-' }}</td>
                <td style="padding:3px 5px;">{{ $saksi2->occupation ?? '-' }}</td>
            </tr>
            <tr>
                <td class="text-bold" style="padding:3px 5px;">PENDIDIKAN</td>
                <td style="padding:3px 5px;">-</td>
                <td style="padding:3px 5px;">-</td>
                <td style="padding:3px 5px;">-</td>
            </tr>
            <tr>
                <td class="text-bold" style="padding:3px 5px;">STATUS</td>
                <td style="padding:3px 5px;">{{ $waliStatus }}</td>
                <td style="padding:3px 5px;">{{ $saksi1 ? $formatStatus($saksi1->marital_status ?? null, $saksi1->gender === 'P' ? 'WANITA' : 'PRIA') : '-' }}</td>
                <td style="padding:3px 5px;">{{ $saksi2 ? $formatStatus($saksi2->marital_status ?? null, $saksi2->gender === 'P' ? 'WANITA' : 'PRIA') : '-' }}</td>
            </tr>
            <tr>
                <td class="text-bold" style="padding:3px 5px;">NO. HP</td>
                <td style="padding:3px 5px;">-</td>
                <td style="padding:3px 5px;">-</td>
                <td style="padding:3px 5px;">-</td>
            </tr>
            <tr>
                <td class="text-bold" style="padding:3px 5px;">ALAMAT</td>
                <td style="padding:3px 5px;">{{ $waliAlamatFmt }}</td>
                <td style="padding:3px 5px;">{{ $saksiAlamat1 }}</td>
                <td style="padding:3px 5px;">{{ $saksiAlamat2 }}</td>
            </tr>
            <tr>
                <td class="text-bold" style="padding:3px 5px;">TANDA TANGAN</td>
                <td style="height:28px; padding:3px 5px;"></td>
                <td style="height:28px; padding:3px 5px;"></td>
                <td style="height:28px; padding:3px 5px;"></td>
            </tr>
        </tbody>
    </table>

</body>
</html>