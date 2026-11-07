<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ ($registration->registration_path ?? '') === 'PEMBUATAN_ANDON_NIKAH' ? 'Surat Keterangan Andon Nikah - Model N1' : 'Formulir Pengantar Nikah - Model N1' }}</title>
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
        $isAndonN1 = ($registration->registration_path ?? '') === 'PEMBUATAN_ANDON_NIKAH';

        // Nama desa selalu statis dari Pengaturan Desa (villageProfile).
        $desaKop = trim((string) ($villageProfile?->village_name ?? '')) ?: '-';
        $desaTtd = trim((string) ($villageProfile?->village_name ?? ''));

        $fmtAlamat = fn ($m) => \App\Support\Alamat::pendek($m?->alamat);
        $fmtParentName = function ($p) {
            if (! $p) {
                return '-';
            }
            return $p->nama_dengan_gelar ?: '-';
        };
        // Label "Bin" (ayah) / "Binti" (ibu) dan nilainya (nama ayah kandung masing-masing orang tua).
        $parentBinLabel = function($p) {
            if (!$p) return 'Bin';
            return str_starts_with(strtolower((string) ($p->type ?? '')), 'ibu') ? 'Binti' : 'Bin';
        };
        $parentBinValue = function($p) { if (!$p || !$p->nama_ayah_kandung) return '-'; return $p->nama_ayah_kandung; };
    @endphp

    <div class="header">
        <p class="lampiran">LAMPIRAN V</p>
        <p class="lampiran">KEPUTUSAN DIREKTUR JENDERAL BIMBINGAN MASYARAKAT ISLAM</p>
        <p class="lampiran">NOMOR 473 TAHUN 2020</p>
        <p class="lampiran">TENTANG PETUNJUK TEKNIS PELAKSANAAN PENCATATAN PERNIKAHAN</p>
        <h1>{{ $isAndonN1 ? 'SURAT KETERANGAN ANDON NIKAH' : 'FORMULIR PENGANTAR NIKAH' }}</h1>
        <h2>Model N1</h2>
    </div>

    <table class="identity">
        <tr><td>KANTOR DESA/KELURAHAN</td><td>: {{ $desaKop }}</td></tr>
        <tr><td>KECAMATAN</td><td>: {{ $villageProfile?->district_name ?? '-' }}</td></tr>
        <tr><td>KABUPATEN/KOTA</td><td>: {{ $villageProfile?->regency_name ?? '-' }}</td></tr>
    </table>

    <p class="number">Nomor : {{ $registration->nomor_surat ?: '-' }}</p>

    <p class="intro">Yang bertanda tangan di bawah ini menjelaskan dengan sesungguhnya bahwa:</p>

    {{-- DATA CALON PENGANTIN --}}
    <table class="identity">
        <tr><td>1. Nama</td><td>: <x-nama :nama="$catin?->name" /></td></tr>
        <tr><td>2. Nomor Induk Kependudukan (NIK)</td><td>: {{ $catin?->nik ?: '-' }}</td></tr>
        <tr><td>3. Jenis Kelamin</td><td>: {{ $catinRole === 'WANITA' ? 'Perempuan' : 'Laki-laki' }}</td></tr>
        <tr><td>4. Tempat dan Tanggal Lahir</td><td>: {{ $catin?->tempat_lahir ?: '-' }}, {{ $catin?->tgl_lahir?->format('d-m-Y') ?: '-' }}</td></tr>
        <tr><td>5. Kewarganegaraan</td><td>: Indonesia</td></tr>
        <tr><td>6. Agama</td><td>: {{ $catin?->agama ?: '-' }}</td></tr>
        <tr><td>7. Pekerjaan</td><td>: {{ $catin?->pekerjaan ?: '-' }}</td></tr>
        <tr><td>8. Alamat</td><td>: {{ $fmtAlamat($catin) }}</td></tr>
        @php
            $n1StatusLabel = match (strtolower((string) ($catin?->status_kawin ?? ''))) {
                'belum_kawin', 'perjaka', 'perawan' => $catinRole === 'WANITA'
                    ? 'Belum Menikah (<i>Perawan</i>)'
                    : 'Belum Menikah (<i>Perjaka</i>)',
                'kawin' => 'Kawin',
                'duda' => 'Duda',
                'janda' => 'Janda',
                default => '-',
            };
        @endphp
        <tr><td>9. Status perkawinan</td><td>: {!! $n1StatusLabel !!}</td></tr>
    </table>

    {{-- ORANG TUA CALON PENGANTIN --}}
    <p>Adalah benar anak dari pernikahan seorang pria:</p>
    <table class="identity">
        <tr><td>Nama lengkap dan alias</td><td>: <x-nama :nama="$fmtParentName($ayahUtama)" /></td></tr>
        <tr><td>{{ $parentBinLabel($ayahUtama) }}</td><td>: {{ $parentBinValue($ayahUtama) }}</td></tr>
        <tr><td>Nomor Induk Kependudukan (NIK)</td><td>: {{ $ayahUtama?->nik ?: '-' }}</td></tr>
        <tr><td>Tempat dan Tanggal Lahir</td><td>: {{ $ayahUtama?->tempat_lahir ?: '-' }}, {{ $ayahUtama?->tgl_lahir?->format('d-m-Y') ?: '-' }}</td></tr>
        <tr><td>Agama</td><td>: {{ $ayahUtama?->agama ?: '-' }}</td></tr>
        <tr><td>Pekerjaan</td><td>: {{ $ayahUtama?->pekerjaan ?: '-' }}</td></tr>
        <tr><td>Alamat</td><td>: {{ $fmtAlamat($ayahUtama) }}</td></tr>
        <tr><td>Status Perkawinan</td><td>: {{ $ayahUtama?->status_kawin_ortu ?: '-' }}</td></tr>
    </table>

    <p>Dengan seorang wanita:</p>
    <table class="identity">
        <tr><td>Nama lengkap dan alias</td><td>: <x-nama :nama="$fmtParentName($ibuUtama)" /></td></tr>
        <tr><td>{{ $parentBinLabel($ibuUtama) }}</td><td>: {{ $parentBinValue($ibuUtama) }}</td></tr>
        <tr><td>Nomor Induk Kependudukan (NIK)</td><td>: {{ $ibuUtama?->nik ?: '-' }}</td></tr>
        <tr><td>Tempat dan Tanggal Lahir</td><td>: {{ $ibuUtama?->tempat_lahir ?: '-' }}, {{ $ibuUtama?->tgl_lahir?->format('d-m-Y') ?: '-' }}</td></tr>
        <tr><td>Agama</td><td>: {{ $ibuUtama?->agama ?: '-' }}</td></tr>
        <tr><td>Pekerjaan</td><td>: {{ $ibuUtama?->pekerjaan ?: '-' }}</td></tr>
        <tr><td>Alamat</td><td>: {{ $fmtAlamat($ibuUtama) }}</td></tr>
        <tr><td>Status Perkawinan</td><td>: {{ $ibuUtama?->status_kawin_ortu ?: '-' }}</td></tr>
    </table>

    <p class="closing">Demikian surat pengantar ini dibuat dengan mengingat sumpah jabatan dan untuk dipergunakan sebagaimana mestinya.</p>

    <div class="signature">
        <div>{{ ($desaTtd !== '' ? $desaTtd : '-') . ', ' }}{{ now()->format('d F Y') }}</div>
        <div>Kepala Desa{{ $desaTtd !== '' ? ' ' . $desaTtd : '' }}</div>
        <div class="name"><x-nama :nama="$villageProfile?->village_head_name" /></div>
    </div>
</body>
</html>