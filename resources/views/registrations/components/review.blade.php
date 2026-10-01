<div
    class="space-y-6 text-sm"
    x-data="reviewDocuments()"
    x-init="init()"
>

    {{-- =========================================================
         HEADER REVIEW
    ========================================================== --}}
    <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-lg text-emerald-900">
        <h4 class="font-bold text-base mb-1">
            Konfirmasi & Review Pendaftaran
        </h4>

        <p class="text-xs text-emerald-700">
            Periksa kembali seluruh data sebelum menyimpan pendaftaran.
            Pastikan NIK, nama, tanggal lahir, data orang tua, wali,
            dan saksi sudah sesuai dokumen asli.
        </p>
    </div>

    {{-- =========================================================
         CHECKLIST CATIN PRIA
    ========================================================== --}}
    <div class="bg-slate-50 p-4 rounded-lg border border-slate-200 space-y-3">

        <h4 class="font-bold text-slate-800 text-xs uppercase tracking-wider">
            Checklist Dokumen Catin Pria
        </h4>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">

            <label class="flex items-center space-x-2 cursor-pointer">
                <input
                    type="checkbox"
                    name="catin_pria_checklist[doc_ktp]"
                    value="1"
                    class="rounded text-emerald-600 focus:ring-emerald-500"
                >
                <span>Fotokopi KTP</span>
            </label>

            <label class="flex items-center space-x-2 cursor-pointer">
                <input
                    type="checkbox"
                    name="catin_pria_checklist[doc_kk]"
                    value="1"
                    class="rounded text-emerald-600 focus:ring-emerald-500"
                >
                <span>Fotokopi KK</span>
            </label>

            <label class="flex items-center space-x-2 cursor-pointer">
                <input
                    type="checkbox"
                    name="catin_pria_checklist[doc_birth]"
                    value="1"
                    class="rounded text-emerald-600 focus:ring-emerald-500"
                >
                <span>Akta Kelahiran / Ijazah</span>
            </label>

            <label class="flex items-center space-x-2 cursor-pointer">
                <input
                    type="checkbox"
                    name="catin_pria_checklist[doc_immunization]"
                    value="1"
                    class="rounded text-emerald-600 focus:ring-emerald-500"
                >
                <span>Dokumen Imunisasi / Kesehatan</span>
            </label>

            <label class="flex items-center space-x-2 cursor-pointer">
                <input
                    type="checkbox"
                    name="catin_pria_checklist[doc_photo]"
                    value="1"
                    class="rounded text-emerald-600 focus:ring-emerald-500"
                >
                <span>Pas Foto</span>
            </label>

        </div>
    </div>

    {{-- =========================================================
         CHECKLIST CATIN WANITA
    ========================================================== --}}
    <div class="bg-slate-50 p-4 rounded-lg border border-slate-200 space-y-3">

        <h4 class="font-bold text-slate-800 text-xs uppercase tracking-wider">
            Checklist Dokumen Catin Wanita
        </h4>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">

            <label class="flex items-center space-x-2 cursor-pointer">
                <input
                    type="checkbox"
                    name="catin_wanita_checklist[doc_ktp]"
                    value="1"
                    class="rounded text-emerald-600 focus:ring-emerald-500"
                >
                <span>Fotokopi KTP</span>
            </label>

            <label class="flex items-center space-x-2 cursor-pointer">
                <input
                    type="checkbox"
                    name="catin_wanita_checklist[doc_kk]"
                    value="1"
                    class="rounded text-emerald-600 focus:ring-emerald-500"
                >
                <span>Fotokopi KK</span>
            </label>

            <label class="flex items-center space-x-2 cursor-pointer">
                <input
                    type="checkbox"
                    name="catin_wanita_checklist[doc_birth]"
                    value="1"
                    class="rounded text-emerald-600 focus:ring-emerald-500"
                >
                <span>Akta Kelahiran / Ijazah</span>
            </label>

            <label class="flex items-center space-x-2 cursor-pointer">
                <input
                    type="checkbox"
                    name="catin_wanita_checklist[doc_immunization]"
                    value="1"
                    class="rounded text-emerald-600 focus:ring-emerald-500"
                >
                <span>Dokumen Imunisasi / Kesehatan</span>
            </label>

            <label class="flex items-center space-x-2 cursor-pointer">
                <input
                    type="checkbox"
                    name="catin_wanita_checklist[doc_photo]"
                    value="1"
                    class="rounded text-emerald-600 focus:ring-emerald-500"
                >
                <span>Pas Foto</span>
            </label>

        </div>
    </div>

    {{-- =========================================================
         CHECKLIST N1-N4
    ========================================================== --}}
    <div class="bg-slate-50 p-4 rounded-lg border border-slate-200 space-y-3">

        <h4 class="font-bold text-slate-800 text-xs uppercase tracking-wider">
            Checklist Dokumen Pendaftaran
        </h4>

        <label class="flex items-center space-x-2 cursor-pointer text-xs">
            <input
                type="checkbox"
                name="registration_checklist[doc_n1_n4]"
                value="1"
                class="rounded text-emerald-600 focus:ring-emerald-500"
            >

            <span>
                Surat Pengantar Desa (N1 - N4)
            </span>
        </label>

    </div>

    {{-- =========================================================
         DOKUMEN KONDISIONAL
    ========================================================== --}}
    <div class="space-y-4">

        <div>
            <h4 class="font-bold text-slate-800 text-xs uppercase tracking-wider">
                Dokumen Kondisional
            </h4>

            <p class="mt-1 text-xs text-slate-500">
                Sistem menentukan dokumen berikut berdasarkan kondisi
                data pendaftaran.
            </p>
        </div>

        {{-- =====================================================
             N5
        ====================================================== --}}
        <template x-if="n5Required">

            <div class="border border-amber-200 bg-amber-50 rounded-lg p-4">

                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

                    <div>

                        <div class="flex items-center gap-2">

                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-100 text-amber-700 font-bold">
                                !
                            </span>

                            <div>

                                <p class="font-bold text-amber-900">
                                    N5 — Surat Izin Orang Tua / Wali
                                </p>

                                <p class="text-xs text-amber-700 mt-1">
                                    N5 diperlukan karena terdapat Catin
                                    yang berusia di bawah 21 tahun.
                                </p>

                            </div>

                        </div>

                        <div class="mt-3 space-y-2">

                            <template x-if="agePria !== null && agePria < 21">

                                <div class="bg-white border border-amber-100 rounded p-3 text-xs">

                                    <span class="font-semibold">
                                        Catin Pria
                                    </span>

                                    <span class="text-slate-500">
                                        — usia <span x-text="agePria"></span> tahun
                                    </span>

                                </div>

                            </template>

                            <template x-if="ageWanita !== null && ageWanita < 21">

                                <div class="bg-white border border-amber-100 rounded p-3 text-xs">

                                    <span class="font-semibold">
                                        Catin Wanita
                                    </span>

                                    <span class="text-slate-500">
                                        — usia <span x-text="ageWanita"></span> tahun
                                    </span>

                                </div>

                            </template>

                        </div>

                    </div>

                    <button
                        type="button"
                        @click="n5Modal = true"
                        class="shrink-0 rounded-lg bg-amber-600 px-4 py-2.5 text-xs font-semibold text-white hover:bg-amber-700"
                    >
                        Kelola N5
                    </button>

                </div>

            </div>

        </template>

        {{-- =====================================================
             N6
        ====================================================== --}}
        <template x-if="n6Required">

            <div class="border border-blue-200 bg-blue-50 rounded-lg p-4">

                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

                    <div>

                        <div class="flex items-center gap-2">

                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-blue-700 font-bold">
                                !
                            </span>

                            <div>

                                <p class="font-bold text-blue-900">
                                    N6 — Kartu Kuning / Wali Pengganti
                                </p>

                                <p class="text-xs text-blue-700 mt-1"
                                   x-text="n6Reason">
                                </p>

                            </div>

                        </div>

                        <div class="mt-3 bg-white border border-blue-100 rounded p-3">

                            <p class="text-xs text-slate-600">

                                <span class="font-semibold">
                                    Ayah Catin Wanita:
                                </span>

                                <span x-text="fatherName || 'Tidak ditemukan'"></span>

                            </p>

                            <p class="text-xs text-slate-600 mt-1">

                                <span class="font-semibold">
                                    Status:
                                </span>

                                <span x-text="fatherStatus || 'Belum ditentukan'"></span>

                            </p>

                            <p class="text-xs text-slate-600 mt-1">

                                <span class="font-semibold">
                                    Wali:
                                </span>

                                <span x-text="guardianRelationship || 'Belum ditentukan'"></span>

                            </p>

                        </div>

                    </div>

                    <button
                        type="button"
                        @click="n6Modal = true"
                        class="shrink-0 rounded-lg bg-blue-600 px-4 py-2.5 text-xs font-semibold text-white hover:bg-blue-700"
                    >
                        Kelola N6
                    </button>

                </div>

            </div>

        </template>

        {{-- =====================================================
             SURAT KEMATIAN
        ====================================================== --}}
        <template x-if="deathRequired">

            <div class="border border-red-200 bg-red-50 rounded-lg p-4">

                <div class="flex items-start gap-3">

                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-700 font-bold">
                        !
                    </span>

                    <div class="flex-1">

                        <p class="font-bold text-red-900">
                            Surat Kematian
                        </p>

                        <p class="text-xs text-red-700 mt-1">
                            Diperlukan karena terdapat orang tua yang
                            berstatus meninggal.
                        </p>

                        <div class="mt-3 space-y-2">

                            <template x-for="parent in deceasedParents" :key="parent.key">

                                <div class="bg-white border border-red-100 rounded p-3">

                                    <p
                                        class="text-xs font-semibold text-slate-800"
                                        x-text="parent.label"
                                    ></p>

                                    <p
                                        class="text-xs text-slate-500 mt-1"
                                        x-text="parent.name"
                                    ></p>

                                </div>

                            </template>

                        </div>

                    </div>

                </div>

            </div>

        </template>

        {{-- =====================================================
             TIDAK ADA KONDISIONAL
        ====================================================== --}}
        <template x-if="!n5Required && !n6Required && !deathRequired">

            <div class="border border-emerald-200 bg-emerald-50 rounded-lg p-4">

                <div class="flex items-center gap-3">

                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 font-bold">
                        ✓
                    </span>

                    <div>

                        <p class="font-semibold text-emerald-800">
                            Tidak ada dokumen kondisional.
                        </p>

                        <p class="text-xs text-emerald-700 mt-1">
                            Tidak ditemukan kondisi yang mengharuskan
                            N5, N6, atau surat kematian.
                        </p>

                    </div>

                </div>

            </div>

        </template>

    </div>

    {{-- =========================================================
         CHECKBOX N6 FISIK
    ========================================================== --}}
    <template x-if="n6Required">

        <div class="border border-blue-200 bg-white rounded-lg p-4">

            <label class="flex items-start gap-3 cursor-pointer">

                <input
                    type="checkbox"
                    name="registration_checklist[doc_n6]"
                    value="1"
                    class="mt-1 rounded text-blue-600 focus:ring-blue-500"
                >

                <span>

                    <span class="block text-xs font-semibold text-slate-800">
                        N6 sudah diterima / tersedia
                    </span>

                    <span class="block mt-1 text-xs text-slate-500">
                        Centang hanya jika dokumen N6 benar-benar sudah
                        diterima atau tersedia.
                    </span>

                </span>

            </label>

        </div>

    </template>

    {{-- =========================================================
         MODAL N5
    ========================================================== --}}
    <div
        x-show="n5Modal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
    >

        <div
            @click.outside="n5Modal = false"
            class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl"
        >

            <h3 class="text-lg font-bold text-slate-900">
                N5 Diperlukan
            </h3>

            <p class="mt-2 text-sm text-slate-600">
                Sistem mendeteksi Catin berusia di bawah 21 tahun.
                N5 diperlukan untuk pendaftaran ini.
            </p>

            <div class="mt-6 flex justify-end gap-2">

                <button
                    type="button"
                    @click="n5Modal = false"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm"
                >
                    Nanti Dulu
                </button>

                <button
                    type="button"
                    @click="
                        n5Modal = false;
                        /* alert removed */
                    "
                    class="rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white"
                >
                    Buat N5 Sekarang
                </button>

            </div>

        </div>

    </div>

    {{-- =========================================================
         MODAL N6
    ========================================================== --}}
    <div
        x-show="n6Modal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
    >

        <div
            @click.outside="n6Modal = false"
            class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl"
        >

            <h3 class="text-lg font-bold text-slate-900">
                N6 Diperlukan
            </h3>

            <p
                class="mt-2 text-sm text-slate-600"
                x-text="n6Reason"
            ></p>

            <p class="mt-3 text-sm text-slate-600">
                Apakah N6 ingin dibuat sekarang?
            </p>

            <div class="mt-6 flex justify-end gap-2">

                <button
                    type="button"
                    @click="n6Modal = false"
                    class="rounded-lg border border-slate-300 px-4 py-2 text-sm"
                >
                    Nanti Dulu
                </button>

                <button
                    type="button"
                    @click="
                        n6Modal = false;
                        /* alert removed */
                    "
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white"
                >
                    Buat N6 Sekarang
                </button>

            </div>

        </div>

    </div>

    {{-- =========================================================
         PERNYATAAN OPERATOR
    ========================================================== --}}
    <div class="border border-amber-200 bg-amber-50 rounded-lg p-4">

        <label class="flex items-start gap-3 cursor-pointer">

            <input
                type="checkbox"
                name="confirm_data"
                value="1"
                required
                class="mt-1 rounded text-emerald-600 focus:ring-emerald-500"
            >

            <span class="text-sm text-amber-900">
                Saya menyatakan bahwa seluruh data pendaftaran yang
                saya masukkan telah diperiksa dan sesuai dengan
                dokumen yang diberikan oleh pemohon.
            </span>

        </label>

    </div>

    {{-- =========================================================
         CATATAN OPERATOR
    ========================================================== --}}
    <div class="border border-slate-200 rounded-lg p-4 space-y-2 bg-white">

        <h4 class="font-bold text-slate-800 text-xs uppercase tracking-wider border-b pb-2">
            Catatan Operator
        </h4>

        <p class="text-xs text-slate-500">
            Pastikan seluruh nomor NIK, ejaan nama, tanggal lahir,
            data orang tua, wali, dan saksi telah sesuai dengan
            dokumen asli.
        </p>

        <div class="pt-2">

            <button
                type="button"
                @click="goToStep(1)"
                class="text-xs text-emerald-600 font-semibold hover:underline"
            >
                &larr; Kembali & Perbaiki Data
            </button>

        </div>

    </div>

</div>

<script>
function reviewDocuments() {

    return {

        n5Modal: false,
        n6Modal: false,

        n5Required: false,
        n6Required: false,
        deathRequired: false,

        agePria: null,
        ageWanita: null,

        fatherName: '',
        fatherStatus: '',
        guardianRelationship: '',

        n6Reason: '',

        deceasedParents: [],

        init() {
            // Mencegah race condition saat DOM belum sepenuhnya ter-mount
            setTimeout(() => {
                this.refreshRequirements();
            }, 0);

            const formElement = document.querySelector('form');

            if (formElement) {
                formElement.addEventListener('input', () => {
                    this.refreshRequirements();
                });

                formElement.addEventListener('change', () => {
                    this.refreshRequirements();
                });
            }
        },

        getValue(name) {
            const element = document.querySelector(
                `[name="${name}"]`
            );

            if (!element) return null;

            // Membersihkan spasi dan memastikan tipe nilai konsisten
            return element.value ? element.value.trim() : null;
        },

        calculateAge(birthDate, marriageDate) {

            if (!birthDate || !marriageDate) {
                return null;
            }

            const birth = new Date(birthDate + 'T00:00:00');
            const marriage = new Date(marriageDate + 'T00:00:00');

            if (isNaN(birth.getTime()) || isNaN(marriage.getTime())) {
                return null;
            }

            let age =
                marriage.getFullYear() -
                birth.getFullYear();

            const month =
                marriage.getMonth() -
                birth.getMonth();

            const day =
                marriage.getDate() -
                birth.getDate();

            if (
                month < 0 ||
                (month === 0 && day < 0)
            ) {
                age--;
            }

            return age;
        },

        refreshRequirements() {

            const marriageDate =
                this.getValue('marriage_date');

            const priaBirthDate =
                this.getValue('pria_birth_date');

            const wanitaBirthDate =
                this.getValue('wanita_birth_date');

            this.agePria =
                this.calculateAge(
                    priaBirthDate,
                    marriageDate
                );

            this.ageWanita =
                this.calculateAge(
                    wanitaBirthDate,
                    marriageDate
                );

            /*
            |--------------------------------------------------------------------------
            | N5
            |--------------------------------------------------------------------------
            */

            this.n5Required =
                (
                    this.agePria !== null &&
                    this.agePria < 21
                )
                ||
                (
                    this.ageWanita !== null &&
                    this.ageWanita < 21
                );

            /*
            |--------------------------------------------------------------------------
            | DATA AYAH WANITA
            |--------------------------------------------------------------------------
            */

            this.fatherName =
                this.getValue('wanita_father_name') || '';

            this.fatherStatus =
                (this.getValue('wanita_father_status') || '').toUpperCase();

            this.guardianRelationship =
                (this.getValue('guardian_relationship') || '').toUpperCase();

            /*
            |--------------------------------------------------------------------------
            | N6 (Murni Berdasarkan Ayah Meninggal / Wali Lain)
            |--------------------------------------------------------------------------
            */

            this.n6Required = false;
            this.n6Reason = '';

            if (this.fatherStatus === 'MENINGGAL') {

                this.n6Required = true;

                this.n6Reason =
                    'Ayah kandung Catin Wanita berstatus meninggal ' +
                    'sehingga diperlukan N6.';

            } else if (
                this.guardianRelationship &&
                this.guardianRelationship !== 'AYAH_KANDUNG'
            ) {

                this.n6Required = true;

                this.n6Reason =
                    'Wali yang dipilih bukan Ayah Kandung ' +
                    'sehingga diperlukan N6.';
            }

            /*
            |--------------------------------------------------------------------------
            | SURAT KEMATIAN
            |--------------------------------------------------------------------------
            */

            this.deceasedParents = [];

            const parents = [

                {
                    status: (this.getValue('pria_father_status') || '').toUpperCase(),
                    name: this.getValue('pria_father_name'),
                    role: 'Ayah',
                    catin: 'Pria'
                },

                {
                    status: (this.getValue('pria_mother_status') || '').toUpperCase(),
                    name: this.getValue('pria_mother_name'),
                    role: 'Ibu',
                    catin: 'Pria'
                },

                {
                    status: (this.getValue('wanita_father_status') || '').toUpperCase(),
                    name: this.getValue('wanita_father_name'),
                    role: 'Ayah',
                    catin: 'Wanita'
                },

                {
                    status: (this.getValue('wanita_mother_status') || '').toUpperCase(),
                    name: this.getValue('wanita_mother_name'),
                    role: 'Ibu',
                    catin: 'Wanita'
                }

            ];

            parents.forEach((parent, index) => {

                if (parent.status === 'MENINGGAL') {
                    let nm = parent.name || 'Nama tidak tersedia';
                    if (!/\(Almh?\)/i.test(nm)) nm += parent.role === 'Ayah' ? ' (Alm)' : ' (Almh)';

                    this.deceasedParents.push({

                        key: index,

                        label:
                            `${parent.role} Catin ${parent.catin}`,

                        name: nm

                    });

                }

            });

            this.deathRequired =
                this.deceasedParents.length > 0;
        }
    };
}
</script>