@extends('layouts.app')

@section('title', 'Preview & Cetak Dokumen Perkawinan')

@section('content')
    @if (! $registration)
        <div class="max-w-3xl mx-auto py-16 px-4">
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-8 text-center">
                <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                </div>
                <h1 class="text-xl font-bold text-gray-900">Silakan pilih pendaftaran dari Dashboard</h1>
                <p class="mt-2 text-sm text-gray-600">
                    Halaman cetak dokumen hanya dapat dibuka melalui data pendaftaran tertentu
                    (<code class="text-xs bg-gray-100 px-1 py-0.5 rounded">?registration_id=...</code>).
                </p>
                <a href="{{ route('registration.index') }}"
                   class="inline-flex items-center gap-2 mt-6 px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                    Buka Dashboard Pendaftaran
                </a>
            </div>
        </div>
    @else
        @php
            $documents = [
                [
                    'key'         => 'N1',
                    'badge'       => 'bg-blue-100 text-blue-800',
                    'icon'        => '<svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21l-5-5m0 0l-5 5m5-5l5-5m5 5l-5 5m0 0l3-3m3 3l-3-3m7-9v10a7 7 0 01-7 7H3a7 7 0 01-7-7V4a2 2 0 012-2h3.28a2 2 0 011.715 2 7 7 0 001.06 5.94l.35-.05a2.865 2.865 0 005.94 1.06l-.05.35z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h6v6M7 7l5 5"/></svg>',
                    'title'       => 'Form N1 — Formulir Pengantar Nikah',
                    'subtitle'    => 'Surat Pengantar Nikah dari Desa/Kelurahan',
                    'template'    => 'documents.templates.n1',
                    'active'      => true,
                ],
                [
                    'key'         => 'N2',
                    'badge'       => 'bg-blue-100 text-blue-800',
                    'icon'        => '<svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2zM9 9h6v6H9V9z"/></svg>',
                    'title'       => 'Form N2 — Permohonan Kehendak Nikah',
                    'subtitle'    => 'Permohonan kehendak nikah kepada Kepala KUA',
                    'template'    => 'documents.templates.n2',
                    'active'      => !$isAndon,
                ],
                [
                    'key'         => 'N4',
                    'badge'       => 'bg-emerald-100 text-emerald-800',
                    'icon'        => '<svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.5-1.5L11 7l-4 4l4 4m-7-7v10a7 7 0 007 7h6a2 2 0 002-2v-6a2 2 0 00-2-2h-3.5M9 9l3 3-3 3"/></svg>',
                    'title'       => 'Form N4 — Persetujuan Calon Pengantin',
                    'subtitle'    => 'Persetujuan calon mempelai untuk menikah',
                    'template'    => 'documents.templates.n4',
                    'active'      => true,
                ],
                [
                    'key'         => 'N5',
                    'badge'       => 'bg-amber-100 text-amber-800',
                    'icon'        => '<svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20v-7l-2-3l2-3l4 1l-2 5l2 5l-2 3zM5 10l7-7l7 7l-7 7l-7-7z"/></svg>',
                    'title'       => 'Form N5 — Izin Orang Tua',
                    'subtitle'    => 'Surat izin orang tua (jika usia di bawah standar)',
                    'template'    => 'documents.templates.n5',
                    'active'      => true,
                ],
                [
                    'key'         => 'N6',
                    'badge'       => 'bg-rose-100 text-rose-800',
                    'icon'        => '<svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016zM9 12V8m0 0v-2m0 2h6"/></svg>',
                    'title'       => 'Form N6 — Keterangan Kematian Suami/Istri',
                    'subtitle'    => 'Untuk duda/janda yang mengajukan kembali',
                    'template'    => 'documents.templates.n6',
                    'active'      => true,
                ],
                [
                    'key'         => 'N7',
                    'badge'       => 'bg-purple-100 text-purple-800',
                    'icon'        => '<svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-14 0h14m-2 0l2 2v-2zM5 21l-2-2v2h2z"/></svg>',
                    'title'       => 'Surat Keterangan Wali',
                    'subtitle'    => 'Surat keterangan wali nasab atau hakim',
                    'template'    => 'documents.templates.surat-pernyataan-wali',
                    'active'      => true,
                ],
                [
                    'key'         => 'STATUS',
                    'badge'       => 'bg-indigo-100 text-indigo-800',
                    'icon'        => '<svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M9 12V4m0 0l-2 2m2-2l2 2m-7 8h12a2 2 0 002-2V8a2 2 0 00-2-2h-2M6 9h2m-2 8h2"/></svg>',
                    'title'       => 'Surat Pernyataan Status',
                    'subtitle'    => 'Pernyataan status pernikahan (pria & wanita)',
                    'template'    => 'documents.templates.surat-pernyataan-status',
                    'active'      => true,
                ],
            ];

            if (in_array($registration->service_type, ['bedol', 'kantor'], true)) {
                $documents[] = [
                    'key'         => 'DETAIL',
                    'badge'       => 'bg-slate-100 text-slate-800',
                    'icon'        => '<svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
                    'title'       => 'Detail Pendaftaran',
                    'subtitle'    => 'Ringkasan lengkap data pendaftaran nikah',
                    'template'    => 'documents.templates.detail-pendaftaran',
                    'active'      => true,
                ];
            }
        @endphp

        <div class="max-w-7xl mx-auto py-6 px-4" x-data="documentPreview()">

            {{-- Header read-only --}}
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-4 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        Preview Dokumen: {{ $namaPria }} &amp; {{ $namaWanita }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-600">
                        Data pendaftaran {{ $registration->registration_number }} — read-only.
                        Klik salah satu formulir di bawah untuk melihat preview-nya.
                    </p>
                </div>
                <div class="bg-gray-50 border border-gray-200 px-4 py-2 rounded-lg text-sm text-gray-700">
                    <span class="font-semibold">Status:</span> {{ $registration->status_label }}
                </div>
            </div>

            {{-- Pilihan manual pihak pemohon untuk layanan Andon --}}
            @if ($isAndon)
                <div class="mb-4 bg-white border border-gray-200 rounded-xl shadow-sm p-4">
                    <p class="text-sm font-semibold text-gray-900">Pihak yang Mengajukan Andon</p>
                    <p class="mt-1 text-xs text-gray-600">
                        Layanan Andon tidak dapat dicocokkan otomatis dari desa asal.
                        Pilih pihak yang mengajukan surat agar dokumen tercetak sesuai pilihan.
                    </p>
                    <div class="mt-3 flex flex-wrap items-center gap-6">
                        <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-800 cursor-pointer">
                            <input type="radio" name="pihak_andon" value="{{ route('documents.index', ['registration_id' => $registration->id, 'role' => 'pria']) }}"
                                   @checked($selectedRole === 'pria')
                                   onchange="window.location.href = this.value"
                                   class="text-blue-600 focus:ring-blue-500">
                            Calon Pria
                        </label>
                        <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-800 cursor-pointer">
                            <input type="radio" name="pihak_andon" value="{{ route('documents.index', ['registration_id' => $registration->id, 'role' => 'wanita']) }}"
                                   @checked($selectedRole === 'wanita')
                                   onchange="window.location.href = this.value"
                                   class="text-blue-600 focus:ring-blue-500">
                            Calon Wanita
                        </label>
                    </div>
                </div>
            @endif

            {{-- Peringatan data orang tua meninggal --}}
            @if (isset($bapakWanita) && $bapakWanita->status_hidup === 'meninggal')
                <div class="mb-4 rounded-lg border border-amber-300 bg-amber-50 p-4">
                    <p class="text-sm font-semibold text-amber-900">
                        Data orang tua berstatus meninggal: {{ $orangTuaMeninggal->implode(', ') }}.
                    </p>
                    <p class="mt-1 text-sm text-amber-800">
                        Form N6 wajib dilengkapi sebagai keterangan kematian.
                    </p>
                </div>
            @endif

            {{-- Peringatan calon pengantin di bawah 21 tahun --}}
            @if ($butuhIzinOrtu)
                <div class="mb-4 rounded-lg border border-rose-300 bg-rose-50 p-4">
                    <p class="text-sm font-semibold text-rose-900">
                        Usia calon pengantin di bawah 21 tahun:
                        {{ $calonDibawahUmur->map(fn ($usia, $label) => $label.' ('.$usia.' tahun)')->implode(', ') }}.
                    </p>
                    <p class="mt-1 text-sm text-rose-800">
                        Form N5 (Izin Orang Tua) wajib diisi dan dilengkapi.
                    </p>
                </div>
            @endif

            {{-- Grid Card Formulir --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
                @foreach ($documents as $doc)
                    @if (! $doc['active']) @continue @endif
                    @php
                        $isN5 = $doc['key'] === 'N5';
                        $isN6 = $doc['key'] === 'N6';
                        $isN7 = $doc['key'] === 'N7';
                        $n6Disabled = $isN6 && ! (isset($bapakWanita) && $bapakWanita->status_hidup === 'meninggal');
                        $n5Disabled = $isN5 && ($n5SignerBlocked ?? false);
                        $n7Disabled = $isN7 && ($waliHakim ?? false);
                        $docDisabled = $n5Disabled || $n6Disabled || $n7Disabled;
                    @endphp
                    <div class="group border rounded-xl p-4 shadow-sm transition-all
                                {{ $docDisabled
                                    ? 'bg-gray-100 border-gray-300 cursor-not-allowed opacity-70'
                                    : 'bg-white border-gray-200 hover:shadow-md hover:border-blue-300 cursor-pointer' }}"
                         @if (! $docDisabled)
                             @click="selectDocument('{{ $doc['key'] }}', '{{ str_replace('documents.templates.', '', $doc['template']) }}', '{{ addslashes($doc['title']) }}')"
                         @endif
                         @if ($docDisabled) aria-disabled="true" @endif>
                        <div class="flex items-start gap-3">
                            <span class="{{ $docDisabled ? 'grayscale' : '' }}">{!! $doc['icon'] !!}</span>
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-1">
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold {{ $doc['badge'] }}">
                                        Form {{ $doc['key'] }}
                                    </span>
                                    @if ($isN5)
                                        @if ($n5Disabled)
                                            <span class="inline-flex items-center text-xs font-semibold px-2 py-0.5 rounded-full bg-rose-100 text-rose-700">
                                                Diblokir
                                            </span>
                                        @elseif ($butuhIzinOrtu)
                                            <span class="inline-flex items-center text-xs font-semibold px-2 py-0.5 rounded-full bg-rose-100 text-rose-700">
                                                Wajib Dilengkapi
                                            </span>
                                        @else
                                            <span class="inline-flex items-center text-xs font-semibold px-2 py-0.5 rounded-full bg-gray-200 text-gray-600">
                                                Opsional
                                            </span>
                                        @endif
                                    @endif
                                </div>
                                <h3 class="mt-1 font-semibold {{ $docDisabled ? 'text-gray-500' : 'text-gray-800 group-hover:text-blue-700' }}">
                                    {{ $doc['title'] }}
                                </h3>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $doc['subtitle'] }}</p>
                                @if ($docDisabled)
                                    <p class="mt-2 text-xs font-medium text-gray-600">
                                        @if ($n5Disabled)
                                            N5 tidak dapat dicetak: kedua orang tua calon sudah meninggal (tidak ada penandatangan hidup).
                                        @elseif ($isN6 && $n6Disabled)
                                            N6 tidak diperlukan karena Ayah Calon Wanita masih hidup.
                                        @elseif ($isN6)
                                            N6 memerlukan data Ayah Calon Wanita.
                                        @endif
                                        @if ($n7Disabled)
                                            Surat Pernyataan Wali tidak dapat dicetak karena wali menggunakan format KUA.
                                        @endif
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if ($role === 'invalid')
                <div class="mb-4 rounded-lg border border-rose-300 bg-rose-50 p-4">
                    <p class="text-sm font-semibold text-rose-900">
                        Cetak dokumen dinonaktifkan.
                    </p>
                    <p class="mt-1 text-sm text-rose-800">
                        Operator desa hanya dapat mencetak dokumen untuk warga yang terdaftar di desa Anda.
                    </p>
                </div>
            @endif

            {{-- Area Preview + Aksi --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="flex items-center justify-between p-4 border-b border-gray-200 no-print" x-show="selected !== null">
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-medium text-gray-700" x-text="selectedTitle"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        @if ($role === 'both' && ! $isAndon)
                            <span class="text-xs font-medium text-gray-600">Pilih Format:</span>
                            <button type="button" @click='selectedRole="pria"'
                                class="px-2.5 py-1 text-xs font-medium border border-gray-300 rounded hover:bg-gray-200"
                                :class="selectedRole === 'pria' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'">Pria</button>
                            <button type="button" @click='selectedRole="wanita"'
                                class="px-2.5 py-1 text-xs font-medium border border-gray-300 rounded hover:bg-gray-200"
                                :class="selectedRole === 'wanita' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'">Wanita</button>
                        @endif
                        <button type="button" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700" @click="downloadPdf()">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Download PDF
                        </button>
                    </div>
                </div>

                <div class="p-4 bg-gray-100 overflow-auto" style="height: 600px;">
                    <template x-if="selected === null">
                        <div class="h-full flex flex-col items-center justify-center text-center py-12">
                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-700">Pilih Formulir untuk Preview</h3>
                            <p class="text-sm text-gray-500 mt-1 max-w-sm">Klik salah satu kartu formulir di atas untuk melihat preview-nya.</p>
                        </div>
                    </template>

                    <iframe :src="previewUrl" x-show="selected !== null" class="w-full h-full border border-gray-300 bg-white shadow rounded-lg" title="Preview Dokumen"></iframe>
                </div>
            </div>
        </div>

        @push('scripts')
            <script>
                document.addEventListener('alpine:init', () => {
                    const previewBase = @json(route('documents.preview', '__template__'));
                    // Base URL DomPDF true-PDF untuk tombol Cetak/Download
                    const cetakBase = @json($registration ? route('registration.cetak.dokumen', [$registration->id, '__template__']) : '');

                    Alpine.data('documentPreview', () => ({
                        selected: null,
                        selectedTemplate: null,
                        selectedTitle: null,
                        registrationId: @json($registration?->id ?? ''),
                        previewUrl: '',
                        selectedRole: @json($selectedRole ?? 'pria'),
                        isAndon: @json($isAndon ?? false),
                        canChooseFormat: @json($role === 'both' && ! ($isAndon ?? false)),
                        canDownload: @json($role !== 'invalid'),

                        selectDocument(key, template, title) {
                            this.selected = key || this.selected;
                            this.selectedTemplate = template;
                            this.selectedTitle = title;

                            const params = new URLSearchParams({ _t: Date.now() });
                            if (this.registrationId) {
                                params.set('registration_id', this.registrationId);
                            }
                            if (this.isAndon) {
                                params.set('role', this.selectedRole || 'pria');
                            }

                            this.previewUrl = previewBase.replace('__template__', template) + `?${params.toString()}`;
                        },

                        downloadPdf() {
                            if (!this.previewUrl) return;
                            if (!this.canDownload) {
                                alert('Anda hanya dapat mencetak dokumen untuk warga desa yang terdaftar di sistem.');
                                return;
                            }
                            // Selama ada registrasi, PDF dihasilkan DomPDF (registration.cetak), bukan preview HTML
                            const template = this.selectedTemplate;
                            let target = this.registrationId && template
                                ? cetakBase.replace('__template__', template)
                                : this.previewUrl;

                            // Pilihan pihak (Andon manual atau role=both) dikirim lewat query ?role=
                            if (this.canChooseFormat || this.isAndon) {
                                target += (target.includes('?') ? '&' : '?') + 'role=' + (this.selectedRole || 'pria');
                            }
                            window.open(target, '_blank');
                        }
                    }));
                });
            </script>
        @endpush
    @endif
@endsection
