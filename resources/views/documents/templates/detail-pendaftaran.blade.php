<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pendaftaran Nikah</title>
    <style>
        @page { size: 215mm 330mm; margin: 8pt; }
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; color: #000; line-height: 1.3; margin: 0; padding: 0; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { border: 1px solid #000; padding: 3px 5px; vertical-align: middle; font-size: 12pt; word-wrap: break-word; }
        .no-border td, .no-border th { border: none; }
        
        .header-title { text-align: center; font-weight: bold; font-size: 16pt; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 1px; }
        .desa-title { text-align: center; font-weight: bold; font-size: 12pt; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px; }
        
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        
        .photo-box {
            border: 1px solid #000; width: 20mm; height: 30mm; text-align: center; font-size: 8pt; line-height: 1.3;
            background-color: #fff; display: flex; align-items: center; justify-content: center; flex-direction: column;
        }
        .header-table td { border: none; padding: 3px 0; }
        .header-table .photo-cell { width: 14%; text-align: center; vertical-align: middle; }
        .header-table .title-cell { width: 72%; text-align: center; vertical-align: middle; }
        
        .info-block td { border: none; padding: 3px 5px; font-size: 12pt; }
        .info-block .label-col { width: 35%; font-weight: bold; }
        .info-block .value-col { width: 65%; }
        
        .data-table th { background-color: #d9d9d9; font-size: 14pt; text-align: center; padding: 4px 3px; font-weight: bold; }
        .data-table { width: 92% !important; margin-left: auto; margin-right: auto; margin-bottom: 6px; }
        .data-table td { font-size: 12pt; padding: 4px 5px; }
        .data-table td:first-child { font-size: 14pt; font-weight: bold; }
        
        .wali-table th { background-color: #d9d9d9; font-size: 14pt; text-align: center; padding: 4px 3px; font-weight: bold; }
        .wali-table { width: 92% !important; margin-left: auto; margin-right: auto; }
        .wali-table td { font-size: 12pt; padding: 4px 5px; }
        .wali-table td:first-child { font-size: 14pt; font-weight: bold; }
        
        .info-block { width: 92% !important; margin-left: auto; margin-right: auto; margin-bottom: 5px; }
        .signature-cell { height: 28px; }
    </style>
</head>
<body>

    @php
        $formatTtl = function ($person): string {
            if (! $person) {
                return '-';
            }

            $ttl = trim(($person->tempat_lahir ?: '-') . ', ' . ($person->tgl_lahir?->format('d-m-Y') ?: '-'), ', ');

            return $ttl ?: '-';
        };
        $formatPartyTtl = function ($party) use ($formatTtl): string {
            if (! $party) {
                return '-';
            }

            return $formatTtl($party->warga ?? $party);
        };
        $formatParentName = function ($parent): string {
            return $parent?->nama_dengan_gelar ?: '-';
        };
        $formatParentDeceasedName = function ($parent): string {
            if (! $parent?->nama) {
                return '-';
            }

            $gelar = \App\Support\Nama::gelar(str_starts_with(strtolower((string) $parent->type), 'ibu'));

            return strtoupper(trim($parent->nama))." ( {$gelar} )";
        };
        $formatPartyName = function ($party): string {
            if (! $party) {
                return '-';
            }

            // Saksi bisa saja berstatus meninggal: beri gelar ALM/ALMH.
            return \App\Support\Nama::denganGelar($party->name, $party->status_hidup, $party->gender);
        };
        $formatWaliName = function ($guardian): string {
            if (! $guardian) {
                return '-';
            }

            // Tanggal meninggal terisi pada wali berarti wali sudah meninggal.
            $status = $guardian->tgl_meninggal ? 'meninggal' : 'hidup';

            return \App\Support\Nama::denganGelar($guardian->name ?: '-', $status, $guardian->warga?->jenis_kelamin);
        };
        $isWaliHakim = ($guardian?->jenis_wali ?? null) === 'hakim';
        $ayahPriaMeninggal = \App\Support\Nama::meninggal($ayahPria?->status_hidup);
        $ayahWanitaMeninggal = \App\Support\Nama::meninggal($ayahWanita?->status_hidup);
    @endphp

    <!-- KOP SURAT & FOTO -->
    <table class="no-border header-table" style="margin-bottom: 5px; width:100%;">
        <tr>
            <td class="photo-cell" style="text-align:left; padding-left:2px;">
                <div class="photo-box">FOTO<br>SUAMI<br>2x3</div>
            </td>
            <td class="title-cell">
                <div class="header-title">DATA PENDAFTARAN NIKAH</div>
                <div class="desa-title">DESA : {{ $villageProfile?->village_name ?: '-' }}</div>
            </td>
            <td class="photo-cell" style="text-align:right; padding-right:2px;">
                <div class="photo-box">FOTO<br>ISTRI<br>2x3</div>
            </td>
        </tr>
    </table>

    <!-- INFO BLOCK -->
    <table class="no-border info-block">
        <tr>
            <td style="width:64.91%; vertical-align:top; padding-right:5px;">
                <table class="no-border" style="width:100%; font-size:9pt;">
                    <tr><td style="width:38%; font-weight:bold;">HARI/TGL AKAD</td><td style="width:2%;">:</td><td style="width:60%;">{{ $registration->tanggal_akad?->translatedFormat('l / d F Y') ?: '-' }}</td></tr>
                    <tr><td style="font-weight:bold;">WAKTU AKAD</td><td>:</td><td>{{ $registration->jam_akad ? \Carbon\Carbon::parse($registration->jam_akad)->format('H:i') . ' WIB' : '-' }}</td></tr>
                    <tr><td style="font-weight:bold;">MASKAWIN</td><td>:</td><td>{{ $registration->marriage_dowry_display ?: '-' }}</td></tr>
                    <tr><td style="font-weight:bold;">TEMPAT AKAD</td><td>:</td><td>{{ $registration->marriage_location_address ?: '-' }}</td></tr>
                </table>
            </td>
            <td style="width:35.09%; vertical-align:top; padding-left:5px;">
                <table class="no-border" style="width:100%; font-size:9pt;">
                    <tr><td style="width:32%; font-weight:bold;">NO. NB</td><td style="width:2%;">:</td><td style="width:66%;">-</td></tr>
                    <tr><td style="font-weight:bold;">NO. AKTA</td><td>:</td><td>-</td></tr>
                    <tr><td style="font-weight:bold;">NO. SERI</td><td>:</td><td>-</td></tr>
                    <tr><td style="font-weight:bold;">PETUGAS</td><td>:</td><td>K / P¹ / P²</td></tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- DATA CALON SUAMI -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:18.75%;">DATA</th>
                <th style="width:27.08%;">CALON SUAMI</th>
                <th style="width:27.08%;">BAPAK CALON SUAMI</th>
                <th style="width:27.09%;">IBU CALON SUAMI</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>NIK</td><td>{{ $catinPria?->nik ?: '-' }}</td><td>{{ $ayahPriaMeninggal ? '-' : ($ayahPria?->nik ?: '-') }}</td><td>{{ $ibuPria?->nik ?: '-' }}</td></tr>
            <tr><td>NAMA</td><td>{{ $catinPria?->nama_lengkap ?: ($catinPria?->name ?: '-') }}</td><td>{!! $ayahPriaMeninggal ? '<strong style="text-transform: uppercase;">' . e($formatParentDeceasedName($ayahPria)) . '</strong>' : e($formatParentName($ayahPria)) !!}</td><td>{{ $formatParentName($ibuPria) }}</td></tr>
            <tr><td>TEMPAT/TGL</td><td>{{ $formatTtl($catinPria) }}</td><td>{{ $ayahPriaMeninggal ? '-' : $formatTtl($ayahPria) }}</td><td>{{ $formatTtl($ibuPria) }}</td></tr>
            <tr><td>PEKERJAAN</td><td>{{ $catinPria?->pekerjaan ?: '-' }}</td><td>{{ $ayahPriaMeninggal ? '-' : ($ayahPria?->pekerjaan ?: '-') }}</td><td>{{ $ibuPria?->pekerjaan ?: '-' }}</td></tr>
            <tr><td>PENDIDIKAN</td><td>-</td><td>-</td><td>-</td></tr>
            <tr><td>STATUS</td><td>{!! strtolower((string) ($catinPria?->status_kawin ?? '')) === 'belum_kawin' ? 'Belum Menikah (<i>Perjaka</i>)' : e($catinPria?->status_kawin ?: '-') !!}</td><td>{{ $ayahPriaMeninggal ? '-' : ($ayahPria?->status_hidup ?: '-') }}</td><td>{{ $ibuPria?->status_hidup ?: '-' }}</td></tr>
            <tr><td>NO. HP</td><td>-</td><td>-</td><td>-</td></tr>
            <tr><td>E-MAIL</td><td>-</td><td>-</td><td>-</td></tr>
            <tr><td>ALAMAT</td><td>{{ \App\Support\Alamat::pendek($catinPria?->alamat) }}</td><td>{{ $ayahPriaMeninggal ? '-' : \App\Support\Alamat::pendek($ayahPria?->alamat) }}</td><td>{{ \App\Support\Alamat::pendek($ibuPria?->alamat) }}</td></tr>
            <tr><td>TANDA TANGAN</td><td class="signature-cell"></td><td class="signature-cell"></td><td class="signature-cell"></td></tr>
        </tbody>
    </table>

    <!-- DATA CALON ISTRI -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:18.75%;">DATA</th>
                <th style="width:27.08%;">CALON ISTRI</th>
                <th style="width:27.08%;">BAPAK CALON ISTRI</th>
                <th style="width:27.09%;">IBU CALON ISTRI</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>NIK</td><td>{{ $catinWanita?->nik ?: '-' }}</td><td>{{ $ayahWanitaMeninggal ? '-' : ($ayahWanita?->nik ?: '-') }}</td><td>{{ $ibuWanita?->nik ?: '-' }}</td></tr>
            <tr><td>NAMA</td><td>{{ $catinWanita?->nama_lengkap ?: ($catinWanita?->name ?: '-') }}</td><td>{!! $ayahWanitaMeninggal ? '<strong style="text-transform: uppercase;">' . e($formatParentDeceasedName($ayahWanita)) . '</strong>' : e($formatParentName($ayahWanita)) !!}</td><td>{{ $formatParentName($ibuWanita) }}</td></tr>
            <tr><td>TEMPAT/TGL</td><td>{{ $formatTtl($catinWanita) }}</td><td>{{ $ayahWanitaMeninggal ? '-' : $formatTtl($ayahWanita) }}</td><td>{{ $formatTtl($ibuWanita) }}</td></tr>
            <tr><td>PEKERJAAN</td><td>{{ $catinWanita?->pekerjaan ?: '-' }}</td><td>{{ $ayahWanitaMeninggal ? '-' : ($ayahWanita?->pekerjaan ?: '-') }}</td><td>{{ $ibuWanita?->pekerjaan ?: '-' }}</td></tr>
            <tr><td>PENDIDIKAN</td><td>-</td><td>-</td><td>-</td></tr>
            <tr><td>STATUS</td><td>{!! strtolower((string) ($catinWanita?->status_kawin ?? '')) === 'belum_kawin' ? 'Belum Menikah (<i>Perawan</i>)' : e($catinWanita?->status_kawin ?: '-') !!}</td><td>{{ $ayahWanitaMeninggal ? '-' : ($ayahWanita?->status_hidup ?: '-') }}</td><td>{{ $ibuWanita?->status_hidup ?: '-' }}</td></tr>
            <tr><td>NO. HP</td><td>-</td><td>-</td><td>-</td></tr>
            <tr><td>E-MAIL</td><td>-</td><td>-</td><td>-</td></tr>
            <tr><td>ALAMAT</td><td>{{ \App\Support\Alamat::pendek($catinWanita?->alamat) }}</td><td>{{ $ayahWanitaMeninggal ? '-' : \App\Support\Alamat::pendek($ayahWanita?->alamat) }}</td><td>{{ \App\Support\Alamat::pendek($ibuWanita?->alamat) }}</td></tr>
            <tr><td>TANDA TANGAN</td><td class="signature-cell"></td><td class="signature-cell"></td><td class="signature-cell"></td></tr>
        </tbody>
    </table>

    <!-- DATA WALI & SAKSI -->
    <table class="wali-table">
        <thead>
            <tr>
                <th style="width:18.75%;">DATA</th>
                <th style="width:27.08%;">WALI</th>
                <th style="width:27.08%;">SAKSI 1</th>
                <th style="width:27.09%;">SAKSI 2</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>NIK</td><td>{{ $isWaliHakim ? '-' : ($guardian?->nik ?: '-') }}</td><td>{{ $saksi1?->nik ?: '-' }}</td><td>{{ $saksi2?->nik ?: '-' }}</td></tr>
            <tr><td>NAMA</td><td>{{ $isWaliHakim ? '-' : $formatWaliName($guardian) }}</td><td>{{ $formatPartyName($saksi1) }}</td><td>{{ $formatPartyName($saksi2) }}</td></tr>
            <tr><td>TEMPAT/TGL</td><td>{{ $isWaliHakim ? '-' : $formatTtl($guardian) }}</td><td>{{ $formatPartyTtl($saksi1) }}</td><td>{{ $formatPartyTtl($saksi2) }}</td></tr>
            <tr><td>PEKERJAAN</td><td>{{ $isWaliHakim ? '-' : ($guardian?->pekerjaan ?: '-') }}</td><td>{{ $saksi1?->occupation ?: '-' }}</td><td>{{ $saksi2?->occupation ?: '-' }}</td></tr>
            <tr><td>PENDIDIKAN</td><td>-</td><td>-</td><td>-</td></tr>
            <tr><td>STATUS</td><td>{{ $isWaliHakim ? '-' : ($guardian?->hubungan ?: '-') }}</td><td>{{ $saksi1?->status_hidup ?: '-' }}</td><td>{{ $saksi2?->status_hidup ?: '-' }}</td></tr>
            <tr><td>NO. HP</td><td>-</td><td>-</td><td>-</td></tr>
            <tr><td>ALAMAT</td><td>{{ $isWaliHakim ? '-' : \App\Support\Alamat::pendek($guardian?->alamat) }}</td><td>{{ \App\Support\Alamat::pendek($saksi1?->address) }}</td><td>{{ \App\Support\Alamat::pendek($saksi2?->address) }}</td></tr>
            <tr><td>TANDA TANGAN</td><td class="signature-cell"></td><td class="signature-cell"></td><td class="signature-cell"></td></tr>
        </tbody>
    </table>

</body>
</html>