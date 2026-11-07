@props(['nama' => null])
@php
    // Nama di-render uppercase + tebal via CSS (.nama-pdf).
    // Gelar kematian (akhir "Nama ( ALM )"/"Nama ( ALMH )") dipisahkan agar
    // tetap title-case dan dikelilingi tanda kurung, sesuai format dokumen.
    // Format lama "(Alm.)"/"(Almh.)" tetap dikenali dan dinormalisasi.
    $raw = trim((string) ($nama ?? ''));
    $name = null;
    $gelar = null;
    if ($raw !== '' && $raw !== '-') {
        if (preg_match('/^(.*\S)\s*\(\s*(Alm(h)?)\.?\s*\)\s*$/iu', $raw, $_m)) {
            $name = trim($_m[1]);
            $gelar = \App\Support\Nama::gelar(stripos($_m[2], 'almh') !== false);
        } else {
            $name = $raw;
        }
    }
@endphp
@if($name)
    <span class="nama-pdf">{{ $name }}</span>@if($gelar)<span class="nama-gelar">( {{ $gelar }} )</span>@endif
@else
    -
@endif
