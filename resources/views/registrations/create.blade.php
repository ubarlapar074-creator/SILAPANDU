@extends('layouts.app')

@section('title', isset($registration) ? 'Edit Pendaftaran Nikah' : 'Pendaftaran Nikah Baru')

@php
    $registration = $registration ?? null;
    $isEditMode = !empty($registration);
    $pria = $pria ?? null;
    $wanita = $wanita ?? null;
    $guardian = $guardian ?? ($registration?->guardian ?? null);
    $registrationPathValue = old('registration_path', $registration?->registration_path ?? '');
    $marriageDateValue = old('marriage_date', $registration?->marriage_date ? \Carbon\Carbon::parse($registration?->marriage_date)->format('Y-m-d') : '');
    $marriageTimeValue = old('marriage_time', $registration?->marriage_time ?? '');
    $marriageDowryValue = old('marriage_dowry', $registration?->marriage_dowry ?? '');
    $marriageDowryTypeValue = old('marriage_dowry_type', $registration?->marriage_dowry_type ?? ($registration?->marriage_dowry ?? '' ? 'LAINNYA' : ''));
    $marriageDowryMoneyValue = old('marriage_dowry_money', $registration?->marriage_dowry_money ?? '');
    $marriageDowryGoldValue = old('marriage_dowry_gold', $registration?->marriage_dowry_gold ?? '');
    $marriageDowryOtherValue = old('marriage_dowry_other', $registration?->marriage_dowry_other ?? ((!$registration?->marriage_dowry_type || $registration?->marriage_dowry_type === 'LAINNYA') ? ($registration?->marriage_dowry ?? '') : ''));
    $marriageLocationAddressValue = old('marriage_location_address', $registration?->marriage_location_address ?? '');
@endphp

@section('content')

<div class="mb-6">
    <div class="mb-2 flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('dashboard') }}" @click.prevent="isEditMode && hasUnsaved ? requestBackUrl($el.href) : window.location.href=$el.href" class="hover:text-emerald-600">Dashboard</a>
        <span>/</span>
        <span class="text-gray-700">{{ $isEditMode ? 'Edit Pendaftaran' : 'Pendaftaran Baru' }}</span>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">
        {{ $isEditMode ? 'Edit Data Pendaftaran Nikah' : 'Formulir Pendaftaran Nikah' }}
    </h1>
    <p class="mt-1 text-sm text-gray-500">
        {{ $isEditMode ? 'Perbarui data yang sudah tersimpan. Perubahan tersimpan otomatis.' : 'Lengkapi data secara bertahap. Data akan disimpan sebagai satu transaksi pendaftaran.' }}
    </p>
</div>

<div
    x-data="{
        step: {{ old('_step', 1) }},
        totalSteps: 7,
        isEditMode: {{ $isEditMode ? 'true' : 'false' }},
        autosaveUrl: '{{ $isEditMode ? route('registrations.autosave', $registration->id) : '' }}',
        autosaveStatus: '',
        autosaveMessage: '',
        hasUnsaved: false,
        autosaveTimer: null,
        autosaveController: null,
        showBackDialog: false,
        pendingBackStep: null,
        pendingBackUrl: null,
        draftKey: 'nikah:draft:create:v1',
        draftTimer: null,
        _stepKey: null,
        showDraftBanner: false,
        _pendingDraft: null,

        fatherStatusPria: '{{ old('pria_father_status', $pria?->father?->status ?? 'HIDUP') }}',
        motherStatusPria: '{{ old('pria_mother_status', $pria?->mother?->status ?? 'HIDUP') }}',
        fatherStatusWanita: '{{ old('wanita_father_status', $wanita?->father?->status ?? 'HIDUP') }}',
        motherStatusWanita: '{{ old('wanita_mother_status', $wanita?->mother?->status ?? 'HIDUP') }}',

        relationship: '{{ old('guardian_relationship', $guardian?->relationship ?? '') }}',
        sebabWali: @js(old('guardian_sebab_wali', $guardian?->sebab_wali ?? '')),
        init() { 
            if (this.relationship) this.prevSebabDefault = this.getSebabDefault(this.relationship);
            this.initPersist(); 
            if(this.isEditMode) window.addEventListener('beforeunload', (e)=>{ if(this.hasUnsaved){ e.preventDefault(); e.returnValue=''; } });
        },
        prevSebabDefault: '',
        sebabDefaults: {
            'AYAH_KANDUNG': 'Wali nasab Ayah Kandung',
            'KAKEK': 'Ayah kandung telah meninggal dunia, perwalian beralih kepada Kakek dari garis ayah sebagai wali nasab sesuai urutan ashobah',
            'SAUDARA_KANDUNG_LAKI': 'Ayah kandung telah meninggal dunia, perwalian beralih kepada Saudara Kandung Laki-laki sebagai wali nasab',
            'PAMAN': 'Ayah kandung telah meninggal dunia, perwalian beralih kepada Paman dari pihak ayah (\u2018am) sebagai wali nasab',
            'WALI_HAKIM': 'Tidak terdapat wali nasab yang memenuhi syarat (ayah kandung meninggal dan wali ashobah tidak ada/berhalangan), perwalian beralih kepada Wali Hakim KUA'
        },
        sebabDefaultsBerhalangan: {
            'KAKEK': 'Ayah kandung berhalangan, perwalian digantikan oleh Kakek dari garis ayah sebagai wali nasab',
            'SAUDARA_KANDUNG_LAKI': 'Ayah kandung berhalangan, perwalian digantikan oleh Saudara Kandung Laki-laki sebagai wali nasab',
            'PAMAN': 'Ayah kandung berhalangan, perwalian digantikan oleh Paman dari pihak ayah (\u2018am) sebagai wali nasab',
            'WALI_HAKIM': 'Tidak terdapat wali nasab yang memenuhi syarat atau wali adlal/berhalangan, perwalian beralih kepada Wali Hakim KUA'
        },
        getSebabDefault(rel) {
            if (!rel) return '';
            if (rel === 'AYAH_KANDUNG') return this.sebabDefaults[rel] || '';
            if (this.isFatherMeninggal) return this.sebabDefaults[rel] || '';
            return this.sebabDefaultsBerhalangan[rel] || this.sebabDefaults[rel] || '';
        },
        showN6Alert: false,

        get isFatherMeninggal() {
            return this.fatherStatusWanita === 'MENINGGAL';
        },

        handleStepWarning() {
            if (this.step === 6 && this.isFatherMeninggal) {
                this.showN6Alert = true;
            } else if (this.step !== 6 || !this.isFatherMeninggal) {
                this.showN6Alert = false;
            }
        },

        handleRelationshipChange() {
            if (this.relationship === 'AYAH_KANDUNG' && this.isFatherMeninggal) {
                this.showN6Alert = true;
            }
            const def = this.getSebabDefault(this.relationship);
            if (!this.relationship) { this.prevSebabDefault = ''; return; }
            if (!this.sebabWali || this.sebabWali === this.prevSebabDefault) {
                this.sebabWali = def;
            }
            this.prevSebabDefault = def;
        },

        handleN6Action(action) {
            this.showN6Alert = false;
            if (action === 'buat_sekarang') {
                this.step = 7;
            }
        },

        markDirty() {
            if (!this.isEditMode) return;
            this.hasUnsaved = true;
            this.autosaveStatus = 'unsaved';
            this.autosaveMessage = 'Perubahan belum tersimpan';
            this.scheduleAutosave();
        },

        scheduleAutosave() {
            if (!this.isEditMode) return;
            clearTimeout(this.autosaveTimer);
            this.autosaveTimer = setTimeout(() => this.doAutosave(), 900);
        },

        scheduleDraft() { if(this.isEditMode) return; window.NikahDraft.schedule(this); },
        persistDraft() { window.NikahDraft.persist(this); },
        restoreDraftCheck() { window.NikahDraft.check(this); },
        applyDraft() { window.NikahDraft.apply(this); },
        discardDraft() { window.NikahDraft.discard(this); },
        clearDraft() { window.NikahDraft.clear(this); },
        persistStep() { window.NikahDraft.saveStep(this); },
        restoreStep() { return window.NikahDraft.restoreStep(this); },
        initPersist() { window.NikahDraft.init(this); },

        async doAutosave() {
            if (!this.isEditMode || !this.hasUnsaved || !this.autosaveUrl) return;
            clearTimeout(this.autosaveTimer);
            if (this.autosaveController) this.autosaveController.abort();
            this.autosaveController = new AbortController();
            this.autosaveStatus = 'saving';
            this.autosaveMessage = 'Menyimpan...';
            try {
                const form = this.$refs.editForm;
                const fd = new FormData(form);
                fd.delete('_method');
                fd.delete('_token');
                fd.delete('_step');
                const filtered = new FormData();
                for (const [k, v] of fd.entries()) {
                    filtered.append(k, this.normalizeDateValue(k, v));
                }
                filtered.append('_method', 'PATCH');
                const token = document.querySelector('meta[name=csrf-token]')?.content || '';
                const res = await fetch(this.autosaveUrl, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: filtered,
                    signal: this.autosaveController.signal
                });
                if (!res.ok) {
                    if (res.status === 429) {
                        const retryAfter = parseInt(res.headers.get('Retry-After') || '5', 10);
                        this.autosaveStatus = 'saving';
                        this.autosaveMessage = `Terlalu sering menyimpan, coba lagi ${retryAfter}d...`;
                        setTimeout(() => this.doAutosave(), retryAfter * 1000);
                        return;
                    }
                    const j = await res.json().catch(()=>({}));
                    throw new Error(j.message || 'Gagal menyimpan otomatis');
                }
                this.hasUnsaved = false;
                this.autosaveStatus = 'saved';
                this.autosaveMessage = 'Tersimpan otomatis ' + new Date().toLocaleTimeString('id-ID');
                setTimeout(() => { if (this.autosaveStatus === 'saved') this.autosaveMessage = 'Semua perubahan tersimpan'; }, 3000);
            } catch (e) {
                if (e.name === 'AbortError') return;
                this.autosaveStatus = 'error';
                this.autosaveMessage = e.message || 'Gagal autosave';
            }
        },

        validateCurrentStep() {
            const form = this.$refs.editForm;
            if (!form) return true;
            const sections = [...this.$el.querySelectorAll('section[x-show]')];
            let stepSection = sections.find((section) => {
                const show = section.getAttribute('x-show') || '';
                return show.includes(`step === ${this.step}`);
            });
            if (!stepSection && sections.length >= this.step) stepSection = sections[this.step - 1];
            const fields = stepSection ? [...stepSection.querySelectorAll('input, select, textarea')] : [...form.querySelectorAll('input, select, textarea')];
            const isVisible = (el) => {
                if (el.disabled || el.hidden) return false;
                const style = window.getComputedStyle(el);
                if (style.display === 'none' || style.visibility === 'hidden') return false;
                if (el.offsetParent === null && style.display === 'none') return false;
                let parent = el.parentElement;
                while (parent && parent !== stepSection && parent !== form && parent !== this.$el) {
                    const ps = window.getComputedStyle(parent);
                    if (ps.display === 'none' || ps.visibility === 'hidden') return false;
                    parent = parent.parentElement;
                }
                return true;
            };
            const required = fields.filter((field) => field.required && isVisible(field));
            for (const field of required) {
                if (!field.checkValidity()) {
                    field.reportValidity();
                    return false;
                }
            }
            return true;
        },

        normalizeDateValue(name, value) {
            if (!name.includes('date') || typeof value !== 'string') return value;
            const match = value.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
            return match ? `${match[3]}-${match[2]}-${match[1]}` : value;
        },

        normalizeVisibleBirthDates() {
            const form = this.$refs.editForm;
            if (!form) return;

            const inputs = form.querySelectorAll('input[name*=date]');
            inputs.forEach((input) => {
                input.value = this.normalizeDateValue(input.name, input.value);
            });
        },

        nextStep() {
            if (this.step < this.totalSteps && this.validateCurrentStep()) {
                this.step++;
                this.handleStepWarning();
                this.persistStep(); this.scheduleDraft();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },

        requestBack() {
            if (!this.isEditMode || !this.hasUnsaved) {
                this.doPrevStep();
                return;
            }
            this.pendingBackStep = this.step;
            this.pendingBackUrl = null;
            this.showBackDialog = true;
        },

        requestBackUrl(url) {
            if (!this.isEditMode || !this.hasUnsaved) {
                window.location.href = url;
                return;
            }
            this.pendingBackUrl = url;
            this.pendingBackStep = null;
            this.showBackDialog = true;
        },

        doPrevStep() {
            if (this.step > 1) {
                this.step--;
                this.handleStepWarning();
                this.persistStep(); this.scheduleDraft();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },

        prevStep() {
            this.requestBack();
        },

        async confirmBackWithSave() {
            await this.doAutosave();
            this.showBackDialog = false;
            if (this.pendingBackUrl) {
                window.location.href = this.pendingBackUrl;
            } else if (this.pendingBackStep !== null) {
                this.hasUnsaved = false;
                this.step = this.pendingBackStep;
                this.handleStepWarning();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                this.hasUnsaved = false;
                this.doPrevStep();
            }
            this.pendingBackStep = null;
            this.pendingBackUrl = null;
        },

        confirmBackWithoutSave() {
            clearTimeout(this.autosaveTimer);
            this.hasUnsaved = false;
            this.autosaveStatus = '';
            this.autosaveMessage = '';
            this.showBackDialog = false;
            if (this.pendingBackUrl) {
                window.location.href = this.pendingBackUrl;
            } else if (this.pendingBackStep !== null) {
                this.step = this.pendingBackStep;
                this.handleStepWarning();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                this.doPrevStep();
            }
            this.pendingBackStep = null;
            this.pendingBackUrl = null;
        },

        cancelBackDialog() {
            this.showBackDialog = false;
            this.pendingBackStep = null;
            this.pendingBackUrl = null;
        },

        goToStep(s) {
            if (s >= 1 && s <= this.totalSteps) {
                if (this.isEditMode && this.hasUnsaved && s < this.step) {
                    this.pendingBackStep = s;
                    this.pendingBackUrl = null;
                    this.showBackDialog = true;
                    return;
                }
                this.step = s;
                this.handleStepWarning();
                this.persistStep(); this.scheduleDraft();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }
    }"
    x-init="init()"
    class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm"
>

    {{-- STEP INDICATOR --}}
    <div class="border-b border-gray-200 bg-gray-50 px-4 py-4 sm:px-6">
        @include('registrations.components.steps')
    </div>

    <div x-show="!isEditMode && showDraftBanner" x-cloak class="mx-5 mt-4 flex items-center justify-between gap-2 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-xs">
        <span class="font-medium text-amber-800">Ditemukan draft sebelumnya — lanjutkan pengisian?</span>
        <span class="flex gap-2">
            <button type="button" @click="applyDraft()" class="rounded bg-amber-600 px-3 py-1.5 font-semibold text-white hover:bg-amber-700">Lanjutkan</button>
            <button type="button" @click="discardDraft()" class="rounded border border-amber-300 bg-white px-3 py-1.5 font-semibold text-amber-700 hover:bg-amber-100">Hapus</button>
        </span>
    </div>

    <div x-show="isEditMode && autosaveMessage" x-cloak class="mx-5 mt-4 flex items-center gap-2 rounded-lg border px-3 py-2 text-xs" :class="autosaveStatus==='saving' ? 'border-amber-200 bg-amber-50 text-amber-800' : autosaveStatus==='saved' ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : autosaveStatus==='error' ? 'border-red-200 bg-red-50 text-red-800' : 'border-slate-200 bg-slate-50 text-slate-600'">
        <span x-show="autosaveStatus==='saving'" class="h-2 w-2 animate-pulse rounded-full bg-amber-500"></span>
        <span x-show="autosaveStatus==='saved'" class="text-emerald-600">✓</span>
        <span x-show="autosaveStatus==='error'" class="text-red-600">!</span>
        <span x-text="autosaveMessage"></span>
        <span x-show="hasUnsaved" class="ml-auto text-[10px] font-bold uppercase tracking-wider text-amber-700">Belum tersimpan</span>
    </div>

    {{-- FORM UTAMA --}}
    <form x-ref="editForm" @input="markDirty(); scheduleDraft()" @change="markDirty(); scheduleDraft()" @submit="if (!validateCurrentStep()) { $event.preventDefault(); } else { normalizeVisibleBirthDates(); clearDraft(); }" action="{{ $isEditMode ? route('registrations.update', $registration->id) : route('registrations.store') }}" method="POST" novalidate class="p-5 sm:p-6 lg:p-8">
        @csrf

        @if($isEditMode)
            @method('PUT')
        @endif

        <input type="hidden" name="_step" x-bind:value="step">

        {{-- STEP 1 — INFORMASI PENDAFTARAN & LOKASI AKAD --}}
        <section x-show="step === 1" x-cloak x-transition.opacity>
            <div class="mb-6">
                <h2 class="text-lg font-bold text-gray-900">Informasi Pendaftaran & Lokasi Akad</h2>
                <p class="mt-1 text-sm text-gray-500">Tentukan jalur, rencana tanggal, mahar, serta lokasi pelaksanaan pernikahan.</p>
            </div>
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label for="registration_path" class="block text-sm font-medium text-gray-700">Jalur Pendaftaran <span class="text-red-500">*</span></label>
                    <select id="registration_path" name="registration_path" required class="mt-1.5 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        <option value="">Pilih Jalur Pendaftaran</option>
                        <option value="DALAM_DESA" @selected($registrationPathValue === 'DALAM_DESA')>Dalam Desa</option>
                        <option value="ANDON" @selected($registrationPathValue === 'ANDON')>Andon Nikah</option>
                    </select>
                </div>
                <div>
                    <label for="marriage_date" class="block text-sm font-medium text-gray-700">Rencana Tanggal Nikah <span class="text-red-500">*</span></label>
                    <input id="marriage_date" type="date" name="marriage_date" value="{{ $marriageDateValue }}" placeholder="dd/mm/yyyy" lang="id-ID" required class="mt-1.5 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>
                <div>
                    <label for="marriage_time" class="block text-sm font-medium text-gray-700">Waktu Akad <span class="text-red-500">*</span></label>
                    <input id="marriage_time" type="time" name="marriage_time" value="{{ $marriageTimeValue }}" required class="mt-1.5 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                </div>
                <div class="md:col-span-2" x-data="{ dowryType: '{{ $marriageDowryTypeValue }}' }">
                    <label for="marriage_dowry_type" class="block text-sm font-medium text-gray-700">Mahar / Mas Kawin <span class="text-red-500">*</span></label>
                    <select id="marriage_dowry_type" name="marriage_dowry_type" x-model="dowryType" required class="mt-1.5 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        <option value="">Pilih jenis mahar</option>
                        <option value="UANG_EMAS">Uang + Emas</option>
                        <option value="UANG">Uang saja</option>
                        <option value="EMAS">Emas saja</option>
                        <option value="LAINNYA">Lainnya</option>
                    </select>
                    @error('marriage_dowry_type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    <input type="hidden" name="marriage_dowry" value="{{ $marriageDowryValue }}">
                    <div x-show="dowryType === 'UANG_EMAS' || dowryType === 'UANG'" x-cloak class="mt-3">
                        <label for="marriage_dowry_money" class="block text-xs font-medium text-gray-600">Nominal uang</label>
                        <input id="marriage_dowry_money" type="text" name="marriage_dowry_money" value="{{ $marriageDowryMoneyValue }}" placeholder="Contoh: Rp 1.000.000" :required="dowryType === 'UANG_EMAS' || dowryType === 'UANG'" class="mt-1.5 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm">
                    </div>
                    <div x-show="dowryType === 'UANG_EMAS' || dowryType === 'EMAS'" x-cloak class="mt-3">
                        <label for="marriage_dowry_gold" class="block text-xs font-medium text-gray-600">Berat emas</label>
                        <input id="marriage_dowry_gold" type="text" name="marriage_dowry_gold" value="{{ $marriageDowryGoldValue }}" placeholder="Contoh: 3,5 gram" :required="dowryType === 'UANG_EMAS' || dowryType === 'EMAS'" class="mt-1.5 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm">
                    </div>
                    <div x-show="dowryType === 'LAINNYA'" x-cloak class="mt-3">
                        <label for="marriage_dowry_other" class="block text-xs font-medium text-gray-600">Keterangan mahar</label>
                        <input id="marriage_dowry_other" type="text" name="marriage_dowry_other" value="{{ $marriageDowryOtherValue }}" placeholder="Contoh: Seperangkat alat salat" :required="dowryType === 'LAINNYA'" class="mt-1.5 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm">
                    </div>
                </div>
                <div class="md:col-span-2">
                    <label for="marriage_location_address" class="block text-sm font-medium text-gray-700">Alamat Detail Tempat Akad <span class="text-red-500">*</span></label>
                    <textarea id="marriage_location_address" name="marriage_location_address" rows="2" placeholder="Contoh: Masjid Al-Ikhlas Desa Sukamaju" required class="mt-1.5 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2.5 text-sm shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500">{{ $marriageLocationAddressValue }}</textarea>
                </div>
                <div class="md:col-span-2 rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <h3 class="mb-3 text-xs font-bold uppercase tracking-wider text-gray-700">Wilayah Administratif Tempat Akad Nikah</h3>
                    @include('components.region-selector', ['prefix' => 'marriage', 'provinces' => $provinces, 'selectedProvince' => old('marriage_province_id', $registration?->marriage_province_id ?? ''), 'selectedRegency' => old('marriage_regency_id', $registration?->marriage_regency_id ?? ''), 'selectedDistrict' => old('marriage_district_id', $registration?->marriage_district_id ?? ''), 'selectedVillage' => old('marriage_village_id', $registration?->marriage_village_id ?? '')])
                </div>
            </div>
        </section>

        {{-- STEP 2 — CATIN PRIA --}}
        <section x-show="step === 2" x-cloak x-transition.opacity>
            @include('registrations.components.catin-pria')
        </section>

        {{-- STEP 3 — ORANG TUA PRIA --}}
        <section x-show="step === 3" x-cloak x-transition.opacity>
            @include('registrations.components.parents-pria')
        </section>

        {{-- STEP 4 — CATIN WANITA --}}
        <section x-show="step === 4" x-cloak x-transition.opacity>
            @include('registrations.components.catin-wanita')
        </section>

        {{-- STEP 5 — ORANG TUA WANITA --}}
        <section x-show="step === 5" x-cloak x-transition.opacity>
            @include('registrations.components.parents-wanita')
        </section>

        {{-- STEP 6 — WALI & SAKSI --}}
        <section x-show="step === 6" x-cloak x-transition.opacity>
            <div class="space-y-8">
                @include('registrations.components.guardian')
                @include('registrations.components.witnesses')
            </div>
        </section>

        {{-- STEP 7 — DOKUMEN --}}
        <section x-show="step === 7" x-cloak x-transition.opacity>
            @include('registrations.components.documents')
        </section>

        {{-- NAVIGASI WIZARD + OPSI A: SIMPAN FLEKSIBEL --}}
        <div x-show="isEditMode" x-cloak class="sticky bottom-0 z-10 -mx-5 -mb-5 mt-8 flex flex-wrap items-center justify-between gap-3 border-t border-emerald-200 bg-emerald-50/95 px-5 py-4 backdrop-blur sm:-mx-6 lg:-mx-8">
            <div class="flex items-center gap-2 text-xs">
                <span class="hidden sm:inline font-medium text-emerald-800">Mode Edit — tidak perlu urut step</span>
                <span x-show="autosaveStatus==='saving'" class="text-amber-700">Menyimpan...</span>
                <span x-show="autosaveStatus==='saved'" class="text-emerald-700">Tersimpan ✓</span>
                <span x-show="hasUnsaved" class="font-bold uppercase tracking-wider text-amber-700">Belum tersimpan</span>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('dashboard') }}" @click.prevent="requestBackUrl('{{ route('dashboard') }}')" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50">← Ke Dashboard</a>
                <button type="button" @click="doAutosave(); setTimeout(()=>{ if(!hasUnsaved) window.location.href='{{ route('dashboard') }}'; }, 700)" class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">Simpan & Kembali ke Dashboard</button>
                <button type="button" @click="doAutosave()" class="inline-flex items-center gap-2 rounded-lg border border-emerald-600 bg-white px-4 py-2.5 text-sm font-semibold text-emerald-700 hover:bg-emerald-50">Simpan & Tetap</button>
            </div>
        </div>
        <div class="mt-8 flex items-center justify-between border-t border-gray-200 pt-6">
            <div>
                <button
                    type="button"
                    x-show="step > 1"
                    x-cloak
                    @click="prevStep()"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                >
                    Kembali
                </button>
            </div>

            <div>
                <button
                    type="button"
                    x-show="step < totalSteps"
                    x-cloak
                    @click="nextStep()"
                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700"
                >
                    Selanjutnya
                </button>

                <button
                    type="submit"
                    x-show="step === totalSteps"
                    x-cloak
                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700"
                >
                    <span x-text="isEditMode ? 'Simpan Perubahan' : 'Simpan Pendaftaran'"></span>
                </button>
            </div>
        </div>

    </form>

    <div x-show="showBackDialog" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm" @click.self="cancelBackDialog()" @keydown.escape.window="cancelBackDialog()">
        <div x-show="showBackDialog" x-transition class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-amber-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900">Kembali ke halaman sebelumnya?</h3>
            <p class="mt-2 text-sm leading-relaxed text-gray-600">Kembali ke halaman sebelumnya menyebabkan data perubahan tidak tersimpan.</p>
            <div class="mt-6 flex flex-col gap-2">
                <button type="button" @click="confirmBackWithSave()" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">Kembali dengan menyimpan data</button>
                <button type="button" @click="confirmBackWithoutSave()" class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">Hanya kembali saja tanpa menyimpan data</button>
                <button type="button" @click="cancelBackDialog()" class="mt-1 text-sm font-medium text-gray-500 hover:text-gray-700">Batal</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    [x-cloak] {
        display: none !important;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => { if (window.initNikLookup) window.initNikLookup(); });
</script>
<script>
(function(){ const oldInline = window.NikahDraftInline; window.__NIKAH_DRAFT_INLINE_DISABLED__ = true; window.NikahDraftInline = {
    schedule(ctx){ if(ctx.isEditMode) return; clearTimeout(ctx.draftTimer); ctx.draftTimer=setTimeout(()=>this.persist(ctx),800); },
    persist(ctx){
        if(ctx.isEditMode) return;
        try{
            const form=ctx.$refs.editForm; if(!form) return;
            const fd=new FormData(form); const values={};
            for(const [k,v] of fd.entries()){
                if(k==='_token'||k==='_method') continue;
                if(v instanceof File) continue;
                if(/nik|no_hp|phone|email/i.test(k)) continue;
                if(values[k]===undefined) values[k]=v;
                else if(Array.isArray(values[k])) values[k].push(v);
                else values[k]=[values[k],v];
            }
            values._step=String(ctx.step);
            localStorage.setItem(ctx.draftKey, JSON.stringify({ts:Date.now(), step:ctx.step, values}));
        }catch(e){}
    },
    check(ctx){
        if(ctx.isEditMode) return;
        try{
            const raw=localStorage.getItem(ctx.draftKey); if(!raw) return;
            const d=JSON.parse(raw);
            if(!d||!d.values||Date.now()-(d.ts||0)>86400000){ localStorage.removeItem(ctx.draftKey); return; }
            const hasOld={{ old('_step') ? 'true' : 'false' }};
            if(hasOld) return;
            ctx._pendingDraft=d; ctx.showDraftBanner=true;
        }catch(e){}
    },
    apply(ctx){
        const d=ctx._pendingDraft; if(!d) return;
        ctx.step=d.step||1;
        ctx.$nextTick(async ()=>{
            const form=ctx.$refs.editForm; if(!form) return;
            const regionGroups={};
            for(const [k,rawV] of Object.entries(d.values)){
                if(k==='_step') continue;
                const vs=Array.isArray(rawV)?rawV:[rawV];
                if(k.endsWith('_province_id')||k.endsWith('_regency_id')||k.endsWith('_district_id')||k.endsWith('_village_id')){
                    const prefix=k.replace(/_(province|regency|district|village)_id$/,'');
                    if(!regionGroups[prefix]) regionGroups[prefix]={province:'',regency:'',district:'',village:''};
                    if(k.endsWith('_province_id')) regionGroups[prefix].province=vs[0]||'';
                    else if(k.endsWith('_regency_id')) regionGroups[prefix].regency=vs[0]||'';
                    else if(k.endsWith('_district_id')) regionGroups[prefix].district=vs[0]||'';
                    else if(k.endsWith('_village_id')) regionGroups[prefix].village=vs[0]||'';
                    continue;
                }
                const sel='[name=\''+k.replace(/'/g,'\\\'')+'\']';
                let els=form.querySelectorAll(sel);
                if(!els.length) continue;
                els.forEach((el,idx)=>{
                    const v=vs[idx]!==undefined?vs[idx]:vs[0];
                    if(el.type==='checkbox'||el.type==='radio'){
                        el.checked = vs.includes(el.value);
                    } else { el.value=v??''; }
                    el.dispatchEvent(new Event('input',{bubbles:true}));
                    el.dispatchEvent(new Event('change',{bubbles:true}));
                });
            }
            for(const [prefix, ids] of Object.entries(regionGroups)){
                if(!ids.province) continue;
                try{
                    const prov=form.querySelector('[name=\''+prefix+'_province_id\']');
                    const container=prov ? prov.closest('[x-data]') : null;
                    if(container && window.Alpine){
                        const data=window.Alpine.$data(container);
                        if(data && typeof data.getRegencies==='function'){
                            data.province=ids.province;
                            await data.getRegencies(true);
                            if(ids.regency){ data.regency=ids.regency; await data.getDistricts(true); }
                            if(ids.district){ data.district=ids.district; await data.getVillages(true); }
                            if(ids.village) data.village=ids.village;
                            continue;
                        }
                    }
                }catch(e){}
            }
            ctx._pendingDraft=null; ctx.showDraftBanner=false; ctx.handleStepWarning();
        });
    },
    discard(ctx){ try{localStorage.removeItem(ctx.draftKey);}catch(e){} ctx.showDraftBanner=false; ctx._pendingDraft=null; },
    clear(ctx){ try{localStorage.removeItem(ctx.draftKey);}catch(e){} },
    saveStep(ctx){
        if(!ctx.isEditMode) return;
        try{ const m=ctx.autosaveUrl.match(/registrations\/(\d+)\/autosave/); if(m) sessionStorage.setItem('nikah:step:'+m[1], String(ctx.step)); }catch(e){}
    },
    restoreStep(ctx){
        if(!ctx.isEditMode) return false;
        try{
            const m=ctx.autosaveUrl.match(/registrations\/(\d+)\/autosave/);
            if(!m) return false;
            const s=sessionStorage.getItem('nikah:step:'+m[1]);
            if(s && !isNaN(parseInt(s,10))){ const n=parseInt(s,10); if(n>=1&&n<=ctx.totalSteps){ ctx.step=n; return true; } }
        }catch(e){} return false;
    },
    init(ctx){
        if(ctx.isEditMode){ this.restoreStep(ctx); ctx.handleStepWarning(); }
        else { this.check(ctx); ctx.handleStepWarning(); }
    }
};
(function() {
    const priaNik = document.getElementById('pria_nik');
    const wanitaNik = document.getElementById('wanita_nik');
    const priaStatus = document.getElementById('pria_nik_status');
    const wanitaStatus = document.getElementById('wanita_nik_status');
    if (!priaNik || !wanitaNik) return;

    function showStatus(el, msg, ok) {
        el.textContent = msg;
        el.className = 'mt-1 text-xs ' + (ok ? 'text-emerald-600' : 'text-amber-600');
        el.classList.remove('hidden');
    }
    function hideStatus(el) { el.classList.add('hidden'); el.textContent = ''; }

    async function setRegion(prefix, province_id, regency_id, district_id, village_id) {
        if (!province_id) return;
        const prov = document.querySelector('[name="' + prefix + '_province_id"]');
        if (!prov) return;
        const container = prov.closest('[x-data]');
        try {
            if (window.Alpine && container) {
                const data = window.Alpine.$data(container);
                if (data && typeof data.getRegencies === 'function') {
                    data.province = province_id;
                    await data.getRegencies(true);
                    if (regency_id) {
                        data.regency = regency_id;
                        await data.getDistricts(true);
                    }
                    if (district_id) {
                        data.district = district_id;
                        await data.getVillages(true);
                    }
                    if (village_id) data.village = village_id;
                    return;
                }
            }
        } catch (e) {}
        prov.value = province_id;
        prov.dispatchEvent(new Event('change', { bubbles: true }));
        await new Promise(r => setTimeout(r, 700));
        const reg = document.querySelector('[name="' + prefix + '_regency_id"]');
        if (reg && regency_id) {
            reg.value = regency_id;
            reg.dispatchEvent(new Event('change', { bubbles: true }));
            await new Promise(r => setTimeout(r, 700));
        }
        const dist = document.querySelector('[name="' + prefix + '_district_id"]');
        if (dist && district_id) {
            dist.value = district_id;
            dist.dispatchEvent(new Event('change', { bubbles: true }));
            await new Promise(r => setTimeout(r, 700));
        }
        const vil = document.querySelector('[name="' + prefix + '_village_id"]');
        if (vil && village_id) {
            vil.value = village_id;
            vil.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }

    async function lookup(nik, prefix) {
        const statusEl = prefix === 'pria' ? priaStatus : wanitaStatus;
        if (!/^\d{16}$/.test(nik)) { hideStatus(statusEl); return; }
        showStatus(statusEl, 'Mencari data NIK...', false);
        try {
            const token = document.querySelector('meta[name=csrf-token]')?.content || '';
            const res = await fetch('/internal/catins/lookup', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token }, body: JSON.stringify({ nik }) });
            const json = await res.json();
            if (!json.found || !json.data) {
                showStatus(statusEl, 'NIK belum terdaftar — data baru akan disimpan.', true);
                return;
            }
            const d = json.data;
            showStatus(statusEl, 'NIK ditemukan — data existing di-prefill (bisa menikah kembali).', true);
            const map = { name: prefix + '_name', bin_binti: prefix + '_bin_binti', birth_place: prefix + '_birth_place', birth_date: prefix + '_birth_date', nationality: prefix + '_nationality', religion: prefix + '_religion', marital_status: prefix + '_marital_status', no_hp: prefix + '_no_hp', email: prefix + '_email', occupation: prefix + '_occupation', education: prefix + '_education', address: prefix + '_address' };
            for (const [k, name] of Object.entries(map)) {
                if (d[k] == null) continue;
                const el = document.querySelector('[name="' + name + '"]');
                if (el) {
                    el.value = d[k];
                    el.dispatchEvent(new Event('input', { bubbles: true }));
                    el.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }
            await setRegion(prefix, d.province_id, d.regency_id, d.district_id, d.village_id);
            for (const role of ['father', 'mother']) {
                const p = d[role];
                if (!p) continue;
                const prefix2 = prefix + '_' + role;
                const m2 = { status: prefix2 + '_status', name: prefix2 + '_name', nik: prefix2 + '_nik', birth_place: prefix2 + '_birth_place', birth_date: prefix2 + '_birth_date', address: prefix2 + '_address', education: prefix2 + '_education', occupation: prefix2 + '_occupation' };
                for (const [k, name] of Object.entries(m2)) {
                    if (p[k] == null) continue;
                    const el = document.querySelector('[name="' + name + '"]');
                    if (el) {
                        el.value = p[k];
                        el.dispatchEvent(new Event('input', { bubbles: true }));
                        el.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                }
                await setRegion(prefix2, p.province_id, p.regency_id, p.district_id, p.village_id);
            }
        } catch (e) {
            showStatus(statusEl, 'Gagal lookup NIK.', false);
        }
    }

    let t1, t2;
    priaNik.addEventListener('input', () => { clearTimeout(t1); t1 = setTimeout(() => lookup(priaNik.value.trim(), 'pria'), 500); });
    wanitaNik.addEventListener('input', () => { clearTimeout(t2); t2 = setTimeout(() => lookup(wanitaNik.value.trim(), 'wanita'), 500); });
    priaNik.addEventListener('blur', () => lookup(priaNik.value.trim(), 'pria'));
    wanitaNik.addEventListener('blur', () => lookup(wanitaNik.value.trim(), 'wanita'));
})();
</script>
@endpush