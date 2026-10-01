<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Lampiran Fotokopi KTP — {{ $registration->registration_number }}</title>
<style>
@page { size: A4 portrait; margin: 10mm 10mm 10mm 10mm; }
* { box-sizing: border-box; }
body { margin:0; padding:0; font-family:"Times New Roman", Times, serif; color:#111; font-size:10pt; line-height:1.25; }
.header { text-align:center; border-bottom:2px solid #111; padding-bottom:8px; margin-bottom:10px; }
.header h1 { margin:0; font-size:14pt; letter-spacing:1px; text-transform:uppercase; }
.header p { margin:2px 0 0; font-size:9pt; }
.meta { width:100%; border-collapse:collapse; margin-bottom:10px; font-size:9pt; }
.meta td { padding:1px 0; vertical-align:top; }
.meta td:first-child { width:28%; font-weight:bold; }
.toolbar { display:flex; justify-content:flex-end; gap:8px; margin-bottom:12px; }
.btn { border:1px solid #111; background:#fff; padding:6px 12px; font-size:9pt; cursor:pointer; }
.grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
.card { border:1.2px solid #111; padding:6px; page-break-inside:avoid; break-inside:avoid; }
.card-title { font-weight:bold; font-size:8.5pt; text-align:center; text-transform:uppercase; border-bottom:1px solid #111; padding-bottom:4px; margin-bottom:6px; letter-spacing:0.3px; }
.box { width:100%; height:42mm; border:1px solid #999; display:flex; align-items:center; justify-content:center; overflow:hidden; background:#fff; }
.box img { width:100%; height:100%; object-fit:contain; display:block; }
.box .empty { font-size:8pt; color:#888; text-align:center; padding:6px; }
.footer { margin-top:10px; border-top:1px solid #111; padding-top:6px; font-size:8pt; text-align:center; color:#444; }
@media print {
  .no-print { display:none !important; }
  body { -webkit-print-color-adjust:exact; print-color-adjust:exact; }
  .grid { gap:8px; }
  .box { height:40mm; }
}
</style>
</head>
<body>
<div class="no-print toolbar">
  <a href="{{ route('registrations.ktp.edit', $registration) }}" style="text-decoration:none"><button type="button" class="btn">Kembali ke Form</button></a>
  <button type="button" class="btn" onclick="window.print()">Cetak / Simpan PDF</button>
</div>
<div class="header">
  <h1>Lampiran Fotokopi Kartu Tanda Penduduk (KTP)</h1>
</div>

<div class="grid">
  @foreach($slots as $slot => $label)
  @php $copy = $copies->get($slot); @endphp
  <div class="card">
    <div class="card-title">{{ $loop->iteration }}. {{ $label }}</div>
    <div class="box">
      @if($copy)
        <img src="{{ $copy->url() }}" alt="{{ $label }}">
      @else
        <span class="empty">Belum ada fotokopi KTP<br><span style="font-size:7pt">({{ $slot }})</span></span>
      @endif
    </div>
  </div>
  @endforeach
</div>

</body>
</html>
