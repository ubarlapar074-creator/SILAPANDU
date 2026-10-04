
{{-- =========================================================
     STEP 7 — DOKUMEN PENDAFTARAN
========================================================== --}}

<div
    x-data="{
        n5Modal: false,
        n6Modal: false
    }"
    class="space-y-8"
>

    {{-- =====================================================
         HEADER
    ====================================================== --}}
    <div>

        <h2 class="text-lg font-bold text-gray-900">
            Dokumen Pendaftaran
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            Periksa kelengkapan dokumen sebelum pendaftaran disimpan.
        </p>

    </div>

    {{-- =====================================================
         DOKUMEN WAJIB
    ====================================================== --}}
    <div class="space-y-4">

        <div>
            <h3 class="text-sm font-bold text-gray-900">
                Dokumen Wajib
            </h3>

            <p class="mt-1 text-xs text-gray-500">
                Centang dokumen yang sudah diterima atau diperiksa.
            </p>
        </div>

        {{-- =================================================
             CATIN PRIA
        ================================================== --}}
        <div class="rounded-xl border border-gray-200 bg-white">

            <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                <h4 class="text-sm font-semibold text-gray-800">
                    Catin Pria
                </h4>
            </div>

            <div class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2">

                <label class="flex cursor-pointer items-center gap-3">
                    <input
                        type="checkbox"
                        name="catin_pria_checklist[doc_ktp]"
                        value="1"
                        @checked(old('catin_pria_checklist.doc_ktp'))
                        class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                    >

                    <span class="text-sm text-gray-700">
                        KTP Catin Pria
                    </span>
                </label>

                <label class="flex cursor-pointer items-center gap-3">
                    <input
                        type="checkbox"
                        name="catin_pria_checklist[doc_kk]"
                        value="1"
                        @checked(old('catin_pria_checklist.doc_kk'))
                        class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                    >

                    <span class="text-sm text-gray-700">
                        KK Catin Pria
                    </span>
                </label>

                <label class="flex cursor-pointer items-center gap-3">
                    <input
                        type="checkbox"
                        name="catin_pria_checklist[doc_birth]"
                        value="1"
                        @checked(old('catin_pria_checklist.doc_birth'))
                        class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                    >

                    <span class="text-sm text-gray-700">
                        Akta / Surat Kelahiran
                    </span>
                </label>

                <label class="flex cursor-pointer items-center gap-3">
                    <input
                        type="checkbox"
                        name="catin_pria_checklist[doc_immunization]"
                        value="1"
                        @checked(old('catin_pria_checklist.doc_immunization'))
                        class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                    >

                    <span class="text-sm text-gray-700">
                        Bukti Imunisasi
                    </span>
                </label>

                <label class="flex cursor-pointer items-center gap-3">
                    <input
                        type="checkbox"
                        name="catin_pria_checklist[doc_photo]"
                        value="1"
                        @checked(old('catin_pria_checklist.doc_photo'))
                        class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                    >

                    <span class="text-sm text-gray-700">
                        Pas Foto
                    </span>
                </label>

            </div>

        </div>

        {{-- =================================================
             CATIN WANITA
        ================================================== --}}
        <div class="rounded-xl border border-gray-200 bg-white">

            <div class="border-b border-gray-200 bg-gray-50 px-4 py-3">
                <h4 class="text-sm font-semibold text-gray-800">
                    Catin Wanita
                </h4>
            </div>

            <div class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2">

                <label class="flex cursor-pointer items-center gap-3">
                    <input
                        type="checkbox"
                        name="catin_wanita_checklist[doc_ktp]"
                        value="1"
                        @checked(old('catin_wanita_checklist.doc_ktp'))
                        class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                    >

                    <span class="text-sm text-gray-700">
                        KTP Catin Wanita
                    </span>
                </label>

                <label class="flex cursor-pointer items-center gap-3">
                    <input
                        type="checkbox"
                        name="catin_wanita_checklist[doc_kk]"
                        value="1"
                        @checked(old('catin_wanita_checklist.doc_kk'))
                        class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                    >

                    <span class="text-sm text-gray-700">
                        KK Catin Wanita
                    </span>
                </label>

                <label class="flex cursor-pointer items-center gap-3">
                    <input
                        type="checkbox"
                        name="catin_wanita_checklist[doc_birth]"
                        value="1"
                        @checked(old('catin_wanita_checklist.doc_birth'))
                        class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                    >

                    <span class="text-sm text-gray-700">
                        Akta / Surat Kelahiran
                    </span>
                </label>

                <label class="flex cursor-pointer items-center gap-3">
                    <input
                        type="checkbox"
                        name="catin_wanita_checklist[doc_immunization]"
                        value="1"
                        @checked(old('catin_wanita_checklist.doc_immunization'))
                        class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                    >

                    <span class="text-sm text-gray-700">
                        Bukti Imunisasi
                    </span>
                </label>

                <label class="flex cursor-pointer items-center gap-3">
                    <input
                        type="checkbox"
                        name="catin_wanita_checklist[doc_photo]"
                        value="1"
                        @checked(old('catin_wanita_checklist.doc_photo'))
                        class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                    >

                    <span class="text-sm text-gray-700">
                        Pas Foto
                    </span>
                </label>

            </div>

        </div>

    </div>

    {{-- =====================================================
         DOKUMEN PENDAFTARAN
    ====================================================== --}}
    <div class="space-y-4">

        <div>
            <h3 class="text-sm font-bold text-gray-900">
                Dokumen Pendaftaran
            </h3>

            <p class="mt-1 text-xs text-gray-500">
                Dokumen administrasi utama pendaftaran nikah.
            </p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white">

            <div class="divide-y divide-gray-100">

                {{-- N1-N4 --}}
                <label class="flex cursor-pointer items-center gap-3 px-4 py-4">

                    <input
                        type="checkbox"
                        name="registration_checklist[doc_n1_n4]"
                        value="1"
                        @checked(old('registration_checklist.doc_n1_n4'))
                        class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                    >

                    <div>
                        <p class="text-sm font-semibold text-gray-800">
                            N1 – N4
                        </p>

                        <p class="text-xs text-gray-500">
                            Dokumen utama administrasi pendaftaran nikah.
                        </p>
                    </div>

                </label>

            </div>

        </div>

    </div>

    {{-- =====================================================
         DOKUMEN KONDISIONAL
    ====================================================== --}}
    <div class="space-y-4">

        <div>

            <h3 class="text-sm font-bold text-gray-900">
                Dokumen Kondisional
            </h3>

            <p class="mt-1 text-xs text-gray-500">
                Dokumen berikut muncul berdasarkan kondisi pendaftaran.
            </p>

        </div>

        {{-- =================================================
             N5
        ================================================== --}}
        @if(isset($requirements['n5']) && $requirements['n5']['required'])

            <div class="rounded-xl border border-amber-200 bg-amber-50">

                <div class="flex flex-col gap-4 p-4 sm:flex-row sm:items-center sm:justify-between">

                    <div class="flex items-start gap-3">

                        <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                            !
                        </div>

                        <div>

                            <p class="text-sm font-bold text-gray-900">
                                N5 — Surat Izin Orang Tua / Wali
                            </p>

                            <p class="mt-1 text-xs leading-relaxed text-gray-600">
                                Dokumen ini diperlukan karena terdapat Catin
                                yang berusia di bawah 21 tahun.
                            </p>

                            <span class="mt-2 inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-semibold text-amber-700">
                                Belum dibuat
                            </span>

                        </div>

                    </div>

                    <button
                        type="button"
                        @click="n5Modal = true"
                        class="inline-flex shrink-0 items-center justify-center rounded-lg bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-amber-700"
                    >
                        Kelola N5
                    </button>

                </div>

            </div>

        @endif

        {{-- =================================================
             N6
        ================================================== --}}
        @if(isset($requirements['n6']) && $requirements['n6']['required'])

            <div class="rounded-xl border border-blue-200 bg-blue-50">

                <div class="flex flex-col gap-4 p-4 sm:flex-row sm:items-center sm:justify-between">

                    <div class="flex items-start gap-3">

                        <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-700">
                            !
                        </div>

                        <div>

                            <p class="text-sm font-bold text-gray-900">
                                N6 — Kartu Kuning / Wali Pengganti
                            </p>

                            <p class="mt-1 text-xs leading-relaxed text-gray-600">
                                {{ $requirements['n6']['reason'] ?? 'Dokumen N6 diperlukan untuk kondisi wali pendaftaran ini.' }}
                            </p>

                            <span class="mt-2 inline-flex rounded-full bg-blue-100 px-2.5 py-1 text-[11px] font-semibold text-blue-700">
                                Belum dibuat
                            </span>

                        </div>

                    </div>

                    <button
                        type="button"
                        @click="n6Modal = true"
                        class="inline-flex shrink-0 items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700"
                    >
                        Kelola N6
                    </button>

                </div>

            </div>

        @endif

        {{-- =================================================
             TIDAK ADA DOKUMEN KONDISIONAL
        ================================================== --}}
        @if(
            (!isset($requirements['n5']) || !$requirements['n5']['required'])
            && (!isset($requirements['n6']) || !$requirements['n6']['required'])
        )

            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4">

                <div class="flex items-center gap-3">

                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                        ✓
                    </div>

                    <div>

                        <p class="text-sm font-semibold text-emerald-800">
                            Tidak ada dokumen kondisional.
                        </p>

                        <p class="mt-1 text-xs text-emerald-700">
                            Tidak ditemukan kondisi yang mengharuskan N5, N6,
                            atau surat kematian.
                        </p>

                    </div>

                </div>

            </div>

        @endif

    </div>

    {{-- =====================================================
         MODAL N5
    ====================================================== --}}
    <div
        x-show="n5Modal"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        @keydown.escape.window="n5Modal = false"
    >

        <div
            x-show="n5Modal"
            x-transition
            @click.outside="n5Modal = false"
            class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl"
        >

            <h3 class="text-lg font-bold text-gray-900">
                N5 Diperlukan
            </h3>

            <p class="mt-2 text-sm leading-relaxed text-gray-600">
                Salah satu Catin berusia di bawah 21 tahun.
                Apakah N5 ingin dibuat sekarang?
            </p>

            <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">

                <button
                    type="button"
                    @click="n5Modal = false"
                    class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                    Nanti Dulu
                </button>

                <button
                    type="button"
                    @click="
                        n5Modal = false;
                        /* alert removed */
                    "
                    class="rounded-lg bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-amber-700"
                >
                    Buat N5 Sekarang
                </button>

            </div>

        </div>

    </div>

    {{-- =====================================================
         MODAL N6
    ====================================================== --}}
    <div
        x-show="n6Modal"
        x-cloak
        x-transition.opacity
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        @keydown.escape.window="n6Modal = false"
    >

        <div
            x-show="n6Modal"
            x-transition
            @click.outside="n6Modal = false"
            class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl"
        >

            <h3 class="text-lg font-bold text-gray-900">
                N6 Diperlukan
            </h3>

            <p class="mt-2 text-sm leading-relaxed text-gray-600">
                {{ $requirements['n6']['reason'] ?? 'Pendaftaran ini membutuhkan dokumen N6.' }}
            </p>

            <p class="mt-3 text-sm text-gray-600">
                Apakah N6 ingin dibuat sekarang?
            </p>

            <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">

                <button
                    type="button"
                    @click="n6Modal = false"
                    class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >
                    Nanti Dulu
                </button>

                <button
                    type="button"
                    @click="
                        n6Modal = false;
                        /* alert removed */
                    "
                    class="rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700"
                >
                    Buat N6 Sekarang
                </button>

            </div>

        </div>

    </div>

</div>

