<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SILAPANDU — @yield('title', 'Dashboard')</title>

    {{-- Google Fonts: Plus Jakarta Sans --}}
    
    {{-- Font Awesome Icons --}}
    

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body x-data="sidebar" class="h-full bg-slate-950 text-slate-100 antialiased flex flex-col md:flex-row overflow-hidden">

    @php
        $currentRoute = request()->route() ? request()->route()->getName() : '';
        $backupDays = \App\Models\Setting::daysSinceLastBackup();
        $backupNeedsAttention = \App\Models\Setting::backupNeedsAttention();

        $isActive = function (string $routeName) use ($currentRoute): bool {
            return $currentRoute === $routeName || str($currentRoute)->startsWith($routeName.'.');
        };

        $breadcrumb = match (true) {
            $currentRoute === 'dashboard' => 'Dashboard Utama',
            str($currentRoute)->startsWith('registration.start.service') => 'Pendaftaran Baru',
            $currentRoute === 'registration.index' => 'Riwayat & Data Nikah',
            str($currentRoute)->startsWith('registration.') => 'Pendaftaran Nikah',
            str($currentRoute)->startsWith('residents.') => 'Data Warga',
            str($currentRoute)->startsWith('settings.') => 'Pengaturan',
            str($currentRoute)->startsWith('documents.') => 'Dokumen & Cetak',
            default => 'Dashboard Utama',
        };
    @endphp

    {{-- MOBILE HEADER NAVIGATION TOGGLE --}}
    <div class="md:hidden glass-header border-b border-slate-800 p-4 flex items-center justify-between z-50">
        <div class="flex items-center space-x-3">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-600 to-emerald-400 flex items-center justify-center text-white font-bold shadow-lg shadow-emerald-500/20">
                <i class="fa-solid fa-kaaba text-base"></i>
            </div>
            <div>
                <h1 class="font-extrabold text-sm tracking-wide text-white"> SILAPANDU </h1>
                <p class="text-[9px] text-emerald-400 uppercase tracking-wider font-semibold">Layanan Pernikahan</p>
            </div>
        </div>
        <button type="button" @click="toggle()" :aria-expanded="sidebarOpen.toString()" aria-label="Buka menu" class="p-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 hover:text-white">
            <i class="fa-solid fa-bars text-lg"></i>
        </button>
    </div>

    {{-- MAIN SIDEBAR NAVIGATION --}}
    <aside id="sidebar"
           class="fixed md:static inset-y-0 left-0 bg-slate-900/95 border-r border-slate-800 flex flex-col justify-between z-40 transition-all duration-300 shadow-2xl md:shadow-none"
           :class="sidebarOpen ? 'w-72 translate-x-0' : 'w-72 -translate-x-full md:w-20 md:translate-x-0'">

        <div class="flex flex-col h-full overflow-y-auto">
            {{-- Branding Header --}}
            <div class="border-b border-slate-800/80 space-y-4" :class="sidebarOpen ? 'p-5' : 'p-3'">
                <div class="flex items-center space-x-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-emerald-600 to-emerald-400 flex items-center justify-center text-white font-extrabold text-lg shadow-lg shadow-emerald-500/25 ring-2 ring-emerald-400/20 shrink-0">
                        <i class="fa-solid fa-kaaba"></i>
                    </div>
                    <div x-show="sidebarOpen" class="min-w-0">
                        <h1 class="font-black text-base tracking-tight text-white flex items-center gap-1.5">
                            {{ config('app.name', 'SILAPANDU') }}
                            
                        </h1>
                        <p class="text-[10px] text-slate-400 font-medium leading-tight mt-0.5">Sistem Layanan Pencatatan Pernikahan Terpadu</p>
                    </div>
                </div>

                {{-- Village Logo & Badge Banner --}}
                <div x-show="sidebarOpen" class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800 flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-800/80 border border-slate-700/60 flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-building-columns text-emerald-400 text-lg"></i>
                    </div>
                    <div class="overflow-hidden">
                        <p class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Kantor Desa</p>
                        <p class="text-xs font-bold text-slate-100 truncate">{{ $villageProfile->village_name ?? 'Desa Silapandu' }}</p>
                        <p class="text-[10px] text-emerald-400 font-medium truncate">Desa Terintegrasi SIMKAH</p>
                    </div>
                </div>
            </div>

            {{-- Navigation Menu Links --}}
            <nav class="p-4 space-y-1.5 flex-1 text-xs font-medium" @click="closeOnMobile($event)">

                {{-- Dashboard Utama --}}
                <a href="{{ route('dashboard') }}"
                   title="Dashboard Utama"
                   @class([
                       'w-full py-3 rounded-xl flex items-center transition-all border',
                       'text-emerald-400 bg-emerald-950/50 border-emerald-500/30 font-bold' => $isActive('dashboard'),
                       'text-slate-300 hover:text-white hover:bg-slate-800/60 border-transparent' => ! $isActive('dashboard'),
                   ])
                   :class="sidebarOpen ? 'px-3.5 justify-between' : 'px-2 justify-center'">
                    <span class="flex items-center gap-3">
                        <i class="fa-solid fa-chart-pie text-sm w-4"></i>
                        <span x-show="sidebarOpen">Dashboard Utama</span>
                    </span>
                    <span x-show="sidebarOpen" @class(['w-1.5 h-1.5 rounded-full bg-emerald-400' => $isActive('dashboard')])></span>
                </a>

                {{-- Pendaftaran Baru (Accordion Menu) --}}
                <div class="space-y-1">
                    <button type="button" onclick="toggleSubmenu('submenu-pendaftaran', 'arrow-pendaftaran')"
                            title="Pendaftaran Baru"
                            @class([
                                'w-full py-3 rounded-xl flex items-center transition-all border',
                                'text-white bg-slate-800/70 border-slate-700 font-bold' => str($currentRoute)->startsWith('registration.start.service'),
                                'text-slate-300 hover:text-white hover:bg-slate-800/60 border-transparent' => ! str($currentRoute)->startsWith('registration.start.service'),
                            ])
                            :class="sidebarOpen ? 'px-3.5 justify-between' : 'px-2 justify-center'">
                        <span class="flex items-center gap-3">
                            <i class="fa-solid fa-file-circle-plus text-sm w-4 text-emerald-400"></i>
                            <span x-show="sidebarOpen">Pendaftaran Baru</span>
                        </span>
                        <i id="arrow-pendaftaran" x-show="sidebarOpen" class="fa-solid fa-chevron-down text-[10px] text-slate-400 transition-transform duration-200"
                           style="{{ str($currentRoute)->startsWith('registration.start.service') ? 'transform: rotate(180deg);' : '' }}"></i>
                    </button>

                    <div id="submenu-pendaftaran" x-show="sidebarOpen" @class(['submenu-enter pl-9 space-y-1', 'submenu-open' => str($currentRoute)->startsWith('registration.start.service')])>
                        <a href="{{ route('registration.start.service', 'kantor') }}"
                           class="w-full py-2 px-3 rounded-lg text-slate-400 hover:text-emerald-300 hover:bg-slate-800/40 text-left flex items-center justify-between transition-colors">
                            <span>1. Nikah Kantor</span>
                            <i class="fa-solid fa-building text-[10px] text-slate-500"></i>
                        </a>
                        <a href="{{ route('registration.start.service', 'bedol') }}"
                           class="w-full py-2 px-3 rounded-lg text-slate-400 hover:text-amber-300 hover:bg-slate-800/40 text-left flex items-center justify-between transition-colors">
                            <span>2. Nikah Bedolan / Luar KUA</span>
                            <i class="fa-solid fa-house-user text-[10px] text-slate-500"></i>
                        </a>
                        <a href="{{ route('registration.start.service', 'andon') }}"
                           class="w-full py-2 px-3 rounded-lg text-slate-400 hover:text-cyan-300 hover:bg-slate-800/40 text-left flex items-center justify-between transition-colors">
                            <span>3. Pembuatan Andon Nikah</span>
                            <i class="fa-solid fa-paper-plane text-[10px] text-slate-500"></i>
                        </a>
                    </div>
                </div>

                {{-- Riwayat & Data Nikah --}}
                <a href="{{ route('registration.index') }}"
                   title="Riwayat &amp; Data Nikah"
                   @class([
                       'w-full py-3 rounded-xl flex items-center transition-all border',
                       'text-emerald-400 bg-emerald-950/50 border-emerald-500/30 font-bold' => $currentRoute === 'registration.index',
                       'text-slate-300 hover:text-white hover:bg-slate-800/60 border-transparent' => $currentRoute !== 'registration.index',
                   ])
                   :class="sidebarOpen ? 'px-3.5 justify-between' : 'px-2 justify-center'">
                    <span class="flex items-center gap-3">
                        <i class="fa-solid fa-folder-open text-sm w-4 text-slate-400"></i>
                        <span x-show="sidebarOpen">Riwayat &amp; Data Nikah</span>
                    </span>
                    <span x-show="sidebarOpen" @class([
                        'px-2 py-0.5 text-[9px] font-bold rounded-full border',
                        'bg-emerald-950 text-emerald-400 border-emerald-500/30' => $currentRoute === 'registration.index',
                        'bg-slate-800 text-slate-300 border-slate-700' => $currentRoute !== 'registration.index',
                    ])>{{ \App\Models\Registration::count() }}</span>
                </a>

                {{-- Data Warga --}}
                <a href="{{ route('residents.index') }}"
                   title="Data Warga"
                   @class([
                       'w-full py-3 rounded-xl flex items-center transition-all border',
                       'text-emerald-400 bg-emerald-950/50 border-emerald-500/30 font-bold' => $isActive('residents'),
                       'text-slate-300 hover:text-white hover:bg-slate-800/60 border-transparent' => ! $isActive('residents'),
                   ])
                   :class="sidebarOpen ? 'px-3.5 justify-between' : 'px-2 justify-center'">
                    <span class="flex items-center gap-3">
                        <i class="fa-solid fa-users text-sm w-4 text-slate-400"></i>
                        <span x-show="sidebarOpen">Data Warga</span>
                    </span>
                </a>

                <div x-show="sidebarOpen" class="pt-3 pb-1">
                    <p class="px-3.5 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Konfigurasi</p>
                </div>

                {{-- Pengaturan --}}
                <a href="{{ route('settings.index') }}"
                   title="Pengaturan"
                   @class([
                       'w-full py-3 rounded-xl flex items-center transition-all border',
                       'text-emerald-400 bg-emerald-950/50 border-emerald-500/30 font-bold' => $isActive('settings'),
                       'text-slate-300 hover:text-white hover:bg-slate-800/60 border-transparent' => ! $isActive('settings'),
                   ])
                   :class="sidebarOpen ? 'px-3.5 justify-between' : 'px-2 justify-center'">
                    <span class="flex items-center gap-3">
                        <i class="fa-solid fa-sliders text-sm w-4 text-slate-400"></i>
                        <span x-show="sidebarOpen">Pengaturan</span>
                    </span>
                </a>

                {{-- Panduan Aplikasi --}}
                <button type="button" onclick="openModal('modalGuide')" title="Panduan Aplikasi"
                        class="w-full py-3 rounded-xl flex items-center text-slate-300 hover:text-white hover:bg-slate-800/60 transition-all"
                        :class="sidebarOpen ? 'px-3.5 justify-between' : 'px-2 justify-center'">
                    <span class="flex items-center gap-3">
                        <i class="fa-solid fa-book-bookmark text-sm w-4 text-amber-400"></i>
                        <span x-show="sidebarOpen">Panduan Aplikasi</span>
                    </span>
                    <i x-show="sidebarOpen" class="fa-solid fa-circle-question text-slate-500 text-xs"></i>
                </button>

                {{-- Tentang Aplikasi --}}
                <button type="button" onclick="openModal('modalAbout')" title="Tentang Aplikasi"
                        class="w-full py-3 rounded-xl flex items-center text-slate-300 hover:text-white hover:bg-slate-800/60 transition-all"
                        :class="sidebarOpen ? 'px-3.5 justify-between' : 'px-2 justify-center'">
                    <span class="flex items-center gap-3">
                        <i class="fa-solid fa-circle-info text-sm w-4 text-slate-400"></i>
                        <span x-show="sidebarOpen">Tentang Aplikasi</span>
                    </span>
                </button>

            </nav>

            {{-- Database Backup Status --}}
            <div class="px-4 pb-4">
                <div class="rounded-2xl border p-3 space-y-2"
                     :class="sidebarOpen ? 'border-slate-800 bg-slate-950/60' : 'border-transparent bg-transparent p-1.5'">
                    <a href="{{ route('settings.backup') }}"
                       title="{{ $backupNeedsAttention ? 'Backup diperlukan' : 'Unduh backup database' }}"
                       class="flex items-center transition-all"
                       :class="sidebarOpen ? 'gap-2.5' : 'justify-center'">
                        <i class="fa-solid fa-download text-sm shrink-0"
                           @class(['text-amber-400' => $backupNeedsAttention, 'text-emerald-400' => ! $backupNeedsAttention])></i>
                        <span x-show="sidebarOpen"
                              @class([
                                  'text-[11px] font-semibold truncate',
                                  'text-amber-300' => $backupNeedsAttention,
                                  'text-slate-400' => ! $backupNeedsAttention,
                              ])>
                            @if ($backupNeedsAttention)
                                ⚠️ Backup database diperlukan
                            @else
                                Backup database siap
                            @endif
                        </span>
                    </a>
                    <span x-show="sidebarOpen" class="block text-[10px] text-slate-500">
                        @if ($backupDays === null)
                            Belum pernah backup
                        @else
                            Terakhir: {{ $backupDays }} hari lalu
                        @endif
                    </span>
                    <a href="{{ route('settings.backup') }}"
                       x-show="sidebarOpen"
                       @class([
                           'w-full inline-flex items-center justify-center rounded-lg px-2.5 py-2 text-[10px] font-bold transition-colors',
                           'bg-amber-500 text-slate-950 hover:bg-amber-400' => $backupNeedsAttention,
                           'bg-emerald-500/15 text-emerald-300 hover:bg-emerald-500/25' => ! $backupNeedsAttention,
                       ])>
                        {{ $backupNeedsAttention ? 'Backup Sekarang' : 'Unduh Backup' }}
                    </a>
                </div>
            </div>

            {{-- User Profile Badge & Logout --}}
            <div class="p-4 border-t border-slate-800/80 space-y-3">
                <div class="flex items-center rounded-2xl bg-slate-950/60 border border-slate-800"
                     :class="sidebarOpen ? 'justify-between p-2.5' : 'justify-center p-1.5'">
                    <div class="flex items-center space-x-3 overflow-hidden">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-600 to-emerald-400 flex items-center justify-center text-white font-bold text-xs ring-2 ring-emerald-500/30 shrink-0">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <div x-show="sidebarOpen" class="overflow-hidden">
                            <p class="text-xs font-bold text-white truncate">{{ auth()->user()->name ?? 'Operator' }}</p>
                            <p class="text-[10px] text-slate-400 truncate">{{ auth()->user() ? 'Administrator Desa' : 'Belum masuk (Guest)' }}</p>
                        </div>
                    </div>
                </div>

                <button type="button" onclick="openModal('modalLogout')" title="Keluar (Logout)"
                        class="w-full py-2.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/20 font-bold text-xs flex items-center justify-center gap-2 transition-all"
                        :class="sidebarOpen ? 'px-4' : 'px-2'">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span x-show="sidebarOpen">Keluar (Logout)</span>
                </button>
            </div>
        </div>
    </aside>

    {{-- MAIN CONTENT CANVAS --}}
    <div class="flex-1 flex flex-col h-full overflow-hidden">

        {{-- Top App Header Bar --}}
        <header class="glass-header border-b border-slate-800 px-6 py-4 flex items-center justify-between gap-4 shrink-0">
            <div class="flex items-center gap-3 min-w-0">
                {{-- Toggle Sidebar (Desktop) --}}
                <button type="button" @click="toggle()" aria-label="Lipat atau buka sidebar"
                        :title="sidebarOpen ? 'Lipat sidebar' : 'Buka sidebar'"
                        class="hidden md:inline-flex items-center justify-center w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 hover:text-white hover:bg-slate-800 transition-colors shrink-0">
                    <i class="fa-solid fa-angles-left text-sm transition-transform duration-300" :class="sidebarOpen ? '' : 'rotate-180'"></i>
                </button>

                {{-- Breadcrumb Navigation --}}
                <div class="flex items-center gap-2 text-xs text-slate-400 truncate">
                    <i class="fa-solid fa-house text-emerald-400"></i>
                    <span>/</span>
                    <span id="breadcrumb-active" class="text-white font-semibold">@yield('breadcrumb', $breadcrumb)</span>
                </div>
            </div>

            {{-- Global Search & System Status Badge --}}
            <div class="flex items-center space-x-4">
                <form action="{{ route('residents.index') }}" method="GET" role="search"
                      class="hidden sm:flex items-center bg-slate-900 border border-slate-800 rounded-xl px-3 py-1.5 text-xs text-slate-300 w-64 focus-within:ring-2 focus-within:ring-emerald-500/50">
                    <i class="fa-solid fa-magnifying-glass text-slate-500 mr-2"></i>
                    <input type="search" name="q" value="{{ request('q') }}" aria-label="Cari data warga"
                           placeholder="Cari NIK / Nama Pemohon..."
                           class="bg-transparent border-none focus:outline-none w-full placeholder-slate-500">
                </form>

                <div class="flex items-center space-x-2 bg-emerald-950/60 border border-emerald-500/30 px-3 py-1.5 rounded-xl text-xs font-semibold text-emerald-400">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="hidden md:inline">Server Online</span>
                </div>

                <button type="button" onclick="showToast('Notifikasi: 3 Berkas baru siap divalidasi')" aria-label="Notifikasi"
                        class="p-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-300 hover:text-white relative">
                    <i class="fa-regular fa-bell"></i>
                    <span class="absolute top-1 right-1 w-2 h-2 rounded-full bg-amber-400"></span>
                </button>
            </div>
        </header>

        {{-- Dynamic Content Body Container --}}
        <main class="flex-1 overflow-y-auto bg-slate-50 text-slate-900">
            @yield('content')
        </main>
    </div>

    {{-- MODAL 1: Panduan Aplikasi --}}
    <div id="modalGuide" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-slate-900 rounded-3xl max-w-lg w-full p-6 border border-slate-800 space-y-4 shadow-2xl">
            <div class="flex justify-between items-center border-b border-slate-800 pb-3">
                <h3 class="font-extrabold text-white text-base flex items-center gap-2">
                    <i class="fa-solid fa-book-bookmark text-amber-400"></i> Panduan Aplikasi SILAPANDU
                </h3>
                <button type="button" onclick="closeModal('modalGuide')" aria-label="Tutup" class="text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="space-y-3 text-xs text-slate-300 max-h-80 overflow-y-auto pr-1">
                <div class="p-3 bg-slate-950 rounded-xl border border-slate-800">
                    <h4 class="font-bold text-emerald-400 mb-1">1. Alur Nikah Kantor & Bedolan</h4>
                    <p class="text-slate-400">Pilih menu "Pendaftaran Baru" &gt; Masukkan data Caten Pria &amp; Wanita &gt; Verifikasi syarat N1-N6 &gt; Cetak berkas fisik KUA.</p>
                </div>
                <div class="p-3 bg-slate-950 rounded-xl border border-slate-800">
                    <h4 class="font-bold text-cyan-400 mb-1">2. Pembuatan Andon Nikah</h4>
                    <p class="text-slate-400">Khusus warga desa yang akan menikah di luar kecamatan. Sistem menerbitkan Surat Rekomendasi Numpang Nikah.</p>
                </div>
                <div class="p-3 bg-slate-950 rounded-xl border border-slate-800">
                    <h4 class="font-bold text-amber-400 mb-1">3. Filter Data di Dashboard</h4>
                    <p class="text-slate-400">Gunakan tombol filter (Semua / Nikah Kantor / Bedolan / Andon) untuk menyaring tabel pendaftaran terbaru.</p>
                </div>
            </div>
            <div class="pt-2 flex justify-end">
                <button type="button" onclick="closeModal('modalGuide')" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-semibold text-xs">Tutup</button>
            </div>
        </div>
    </div>

    {{-- MODAL 2: Tentang Aplikasi --}}
    <div id="modalAbout" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-slate-900 rounded-3xl max-w-sm w-full p-6 border border-slate-800 text-center space-y-4 shadow-2xl">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center mx-auto text-xl">
                <i class="fa-solid fa-kaaba"></i>
            </div>
            <div>
                <h3 class="font-extrabold text-white text-base">SILAPANDU</h3>
                <p class="text-xs text-slate-400 mt-1">Sistem Layanan Pencatatan Pernikahan Terpadu</p>
            </div>
            <p class="text-[11px] text-slate-500 leading-relaxed">
                Dikembangkan untuk digitalisasi pelayanan administrasi nikah tingkat Desa / KUA secara efektif, efisien, dan ramah pengguna.
            </p>
            <button type="button" onclick="closeModal('modalAbout')" class="w-full py-2 rounded-xl bg-slate-800 text-slate-300 font-semibold text-xs">Tutup</button>
        </div>
    </div>

    {{-- MODAL 3: Logout Confirmation --}}
    <div id="modalLogout" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 flex items-center justify-center p-4 hidden">
        <div class="bg-slate-900 rounded-3xl max-w-xs w-full p-6 border border-slate-800 text-center space-y-4 shadow-2xl">
            <div class="w-12 h-12 rounded-2xl bg-rose-500/10 text-rose-400 flex items-center justify-center mx-auto text-xl">
                <i class="fa-solid fa-right-from-bracket"></i>
            </div>
            <div>
                <h3 class="font-extrabold text-white text-base">Konfirmasi Keluar</h3>
                <p class="text-xs text-slate-400 mt-1">Apakah Anda yakin ingin keluar dari sesi SILAPANDU?</p>
            </div>
            <div class="grid grid-cols-2 gap-2 pt-2">
                <button type="button" onclick="closeModal('modalLogout')" class="py-2.5 rounded-xl bg-slate-800 text-slate-300 text-xs font-semibold">Batal</button>
                {{-- Placeholder: modul autentikasi/login belum dibuat, jadi tombol hanya menampilkan teks --}}
                <button type="button" onclick="showToast('Logout belum aktif — modul login belum dibuat.'); closeModal('modalLogout');" class="py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold">Ya, Keluar</button>
            </div>
        </div>
    </div>

    {{-- TOAST NOTIFICATION --}}
    <div id="toast" class="fixed bottom-6 right-6 bg-slate-900 text-slate-100 border border-slate-700 px-4 py-3 rounded-2xl shadow-2xl text-xs font-semibold flex items-center space-x-3 transform translate-y-16 opacity-0 transition-all duration-300 z-50">
        <i class="fa-solid fa-circle-check text-emerald-400 text-base"></i>
        <span id="toastMsg">Sistem diperbarui secara otomatis.</span>
    </div>

    {{-- JAVASCRIPT CONTROLLERS (UI Dummy Only) --}}
    <script>
        // Toggle Submenu Accordions
        function toggleSubmenu(menuId, arrowId) {
            const menu = document.getElementById(menuId);
            const arrow = document.getElementById(arrowId);

            if (menu.classList.contains('submenu-open')) {
                menu.classList.remove('submenu-open');
                arrow.style.transform = 'rotate(0deg)';
            } else {
                menu.classList.add('submenu-open');
                arrow.style.transform = 'rotate(180deg)';
            }
        }

        // Modal Helpers
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        // Toast Helper
        function showToast(msg) {
            const toast = document.getElementById('toast');
            document.getElementById('toastMsg').textContent = msg;
            toast.classList.remove('translate-y-16', 'opacity-0');
            setTimeout(() => {
                toast.classList.add('translate-y-16', 'opacity-0');
            }, 3000);
        }

        // Tutup modal saat klik area latar belakang
        document.querySelectorAll('.backdrop-blur-md').forEach((overlay) => {
            overlay.addEventListener('click', (event) => {
                if (event.target === overlay) {
                    overlay.classList.add('hidden');
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
