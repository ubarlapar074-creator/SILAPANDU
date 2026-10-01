<div
    x-data="{
        get wizardData() {
            const form = document.querySelector('form');
            return form?.closest('[x-data]')?._x_dataStack?.[0];
        },
        get step() {
            const data = this.wizardData;
            if (data) {
                return data.step;
            }
            return 1;
        },
        get totalSteps() {
            const data = this.wizardData;
            if (data) {
                return data.totalSteps;
            }
            return 7;
        },
        goToStep(step) {
            const data = this.wizardData;
            if (data?.goToStep) {
                data.goToStep(step);
            }
        }
    }"
    class="bg-white"
>

    {{-- =========================================================
         PROGRESS BAR & STEP COUNTER
    ========================================================== --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

        <div class="flex w-fit items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">
            <span>Langkah</span>

            <span
                class="flex h-5 min-w-5 items-center justify-center rounded-full bg-emerald-600 px-1.5 text-white"
                x-text="step"
            ></span>

            <span>dari <span x-text="totalSteps"></span></span>
        </div>

        <div class="text-xs text-gray-500">
            <span x-text="{
                1: 'Informasi Pendaftaran',
                2: 'Data Catin Pria',
                3: 'Data Orang Tua Pria',
                4: 'Data Catin Wanita',
                5: 'Data Orang Tua Wanita',
                6: 'Data Wali & Saksi',
                7: 'Dokumen Pendaftaran'
            }[step]"></span>
        </div>

    </div>

    {{-- =========================================================
         PROGRESS BAR
    ========================================================== --}}
    <div class="mt-4">

        <div class="h-2.5 w-full overflow-hidden rounded-full bg-gray-200">

            <div
                class="h-full rounded-full bg-emerald-600 transition-all duration-500 ease-out"
                :style="'width: ' + ((step / totalSteps) * 100) + '%'"
            ></div>

        </div>

    </div>

     <div x-show="wizardData?.isEditMode" x-cloak class="mt-4">
        <p class="text-[11px] font-semibold uppercase tracking-wider text-emerald-700">Mode Edit — klik tab langsung tanpa urutan</p>
        <p class="mt-1 text-[11px] text-gray-500">Ubah hanya tab yang diperlukan, lalu gunakan tombol Simpan & Kembali ke Dashboard di bawah.</p>
     </div>
     {{-- =========================================================
         DESKTOP STEP NAVIGATION
    ========================================================== --}}
    <div class="mt-5 hidden md:grid md:grid-cols-7 md:gap-2">

        {{-- STEP 1 --}}
        <button
            type="button"
            @click="goToStep(1)"
            class="group text-left"
        >
            <div
                class="flex items-center gap-2"
                :class="step >= 1 ? 'text-emerald-600' : 'text-gray-400'"
            >

                <span
                    class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full border text-xs font-bold transition"
                    :class="
                        step === 1
                            ? 'border-emerald-600 bg-emerald-600 text-white'
                            : step > 1
                                ? 'border-emerald-600 bg-emerald-50 text-emerald-600'
                                : 'border-gray-300 bg-white text-gray-400'
                    "
                >
                    <span x-show="step <= 1">1</span>
                    <span x-show="step > 1">✓</span>
                </span>

                <span class="hidden lg:block text-xs font-semibold leading-tight">
                    Pendaftaran
                </span>

            </div>
        </button>

        {{-- STEP 2 --}}
        <button
            type="button"
            @click="goToStep(2)"
            class="group text-left"
        >
            <div
                class="flex items-center gap-2"
                :class="step >= 2 ? 'text-emerald-600' : 'text-gray-400'"
            >

                <span
                    class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full border text-xs font-bold transition"
                    :class="
                        step === 2
                            ? 'border-emerald-600 bg-emerald-600 text-white'
                            : step > 2
                                ? 'border-emerald-600 bg-emerald-50 text-emerald-600'
                                : 'border-gray-300 bg-white text-gray-400'
                    "
                >
                    <span x-show="step <= 2">2</span>
                    <span x-show="step > 2">✓</span>
                </span>

                <span class="hidden lg:block text-xs font-semibold leading-tight">
                    Catin Pria
                </span>

            </div>
        </button>

        {{-- STEP 3 --}}
        <button
            type="button"
            @click="goToStep(3)"
            class="group text-left"
        >
            <div
                class="flex items-center gap-2"
                :class="step >= 3 ? 'text-emerald-600' : 'text-gray-400'"
            >

                <span
                    class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full border text-xs font-bold transition"
                    :class="
                        step === 3
                            ? 'border-emerald-600 bg-emerald-600 text-white'
                            : step > 3
                                ? 'border-emerald-600 bg-emerald-50 text-emerald-600'
                                : 'border-gray-300 bg-white text-gray-400'
                    "
                >
                    <span x-show="step <= 3">3</span>
                    <span x-show="step > 3">✓</span>
                </span>

                <span class="hidden lg:block text-xs font-semibold leading-tight">
                    O. Tua Pria
                </span>

            </div>
        </button>

        {{-- STEP 4 --}}
        <button
            type="button"
            @click="goToStep(4)"
            class="group text-left"
        >
            <div
                class="flex items-center gap-2"
                :class="step >= 4 ? 'text-emerald-600' : 'text-gray-400'"
            >

                <span
                    class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full border text-xs font-bold transition"
                    :class="
                        step === 4
                            ? 'border-emerald-600 bg-emerald-600 text-white'
                            : step > 4
                                ? 'border-emerald-600 bg-emerald-50 text-emerald-600'
                                : 'border-gray-300 bg-white text-gray-400'
                    "
                >
                    <span x-show="step <= 4">4</span>
                    <span x-show="step > 4">✓</span>
                </span>

                <span class="hidden lg:block text-xs font-semibold leading-tight">
                    Catin Wanita
                </span>

            </div>
        </button>

        {{-- STEP 5 --}}
        <button
            type="button"
            @click="goToStep(5)"
            class="group text-left"
        >
            <div
                class="flex items-center gap-2"
                :class="step >= 5 ? 'text-emerald-600' : 'text-gray-400'"
            >

                <span
                    class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full border text-xs font-bold transition"
                    :class="
                        step === 5
                            ? 'border-emerald-600 bg-emerald-600 text-white'
                            : step > 5
                                ? 'border-emerald-600 bg-emerald-50 text-emerald-600'
                                : 'border-gray-300 bg-white text-gray-400'
                    "
                >
                    <span x-show="step <= 5">5</span>
                    <span x-show="step > 5">✓</span>
                </span>

                <span class="hidden lg:block text-xs font-semibold leading-tight">
                    O. Tua Wanita
                </span>

            </div>
        </button>

        {{-- STEP 6 --}}
        <button
            type="button"
            @click="goToStep(6)"
            class="group text-left"
        >
            <div
                class="flex items-center gap-2"
                :class="step >= 6 ? 'text-emerald-600' : 'text-gray-400'"
            >

                <span
                    class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full border text-xs font-bold transition"
                    :class="
                        step === 6
                            ? 'border-emerald-600 bg-emerald-600 text-white'
                            : step > 6
                                ? 'border-emerald-600 bg-emerald-50 text-emerald-600'
                                : 'border-gray-300 bg-white text-gray-400'
                    "
                >
                    <span x-show="step <= 6">6</span>
                    <span x-show="step > 6">✓</span>
                </span>

                <span class="hidden lg:block text-xs font-semibold leading-tight">
                    Wali & Saksi
                </span>

            </div>
        </button>

        {{-- STEP 7 --}}
        <button
            type="button"
            @click="goToStep(7)"
            class="group text-left"
        >
            <div
                class="flex items-center gap-2"
                :class="step >= 7 ? 'text-emerald-600' : 'text-gray-400'"
            >

                <span
                    class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full border text-xs font-bold transition"
                    :class="
                        step === 7
                            ? 'border-emerald-600 bg-emerald-600 text-white'
                            : step > 7
                                ? 'border-emerald-600 bg-emerald-50 text-emerald-600'
                                : 'border-gray-300 bg-white text-gray-400'
                    "
                >
                    <span x-show="step <= 7">7</span>
                    <span x-show="step > 7">✓</span>
                </span>

                <span class="hidden lg:block text-xs font-semibold leading-tight">
                    Dokumen
                </span>

            </div>
        </button>

    </div>

    {{-- =========================================================
         MOBILE STEP INFO
    ========================================================== --}}
    <div class="mt-4 md:hidden">

        <div class="flex items-center justify-between">

            <span class="text-xs font-semibold text-gray-500">
                Tahap saat ini
            </span>

            <span
                class="text-xs font-bold text-emerald-600"
                x-text="{
                    1: 'Informasi Pendaftaran',
                    2: 'Catin Pria',
                    3: 'Orang Tua Pria',
                    4: 'Catin Wanita',
                    5: 'Orang Tua Wanita',
                    6: 'Wali & Saksi',
                    7: 'Dokumen'
                }[step]"
            ></span>

        </div>

    </div>

</div>