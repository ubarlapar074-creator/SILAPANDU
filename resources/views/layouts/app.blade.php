<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @hasSection('title')
            @yield('title') — SILAPANDU
        @else
            SILAPANDU
        @endif
    </title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="min-h-screen bg-[#f5f7f6] text-slate-900 antialiased">

    <div
        x-data="{ introVisible: !sessionStorage.getItem('silapandu_intro_seen'), progress: 0, statusText: 'Menyiapkan sistem…' }"
        x-init="if (introVisible) { sessionStorage.setItem('silapandu_intro_seen', '1'); const steps=[12,28,45,62,78,88,100]; const labels=['Memuat wilayah…','Memeriksa berkas…','Menyiapkan form…','Menyinkronkan dokumen…','Mengoptimalkan…','Hampir siap','Siap digunakan']; let i=0; const tick=()=>{ if(!introVisible) return; progress=steps[i]??100; statusText=labels[i]??labels[labels.length-1]; i++; if(i < steps.length) setTimeout(tick, 1350); else setTimeout(()=> introVisible=false, 600); }; setTimeout(tick, 320); }"
        x-show="introVisible"
        x-cloak
        class="simanis-intro-finish fixed inset-0 z-[100] flex flex-col items-center justify-center bg-white px-6"
        aria-label="Membuka SILAPANDU"
        @keydown.escape.window="introVisible = false"
    >
        <div class="flex w-full max-w-sm flex-col items-center text-center">
            <div class="relative flex h-20 w-20 items-center justify-center">
                <div class="absolute inset-0 rounded-full border-[3px] border-slate-100" aria-hidden="true"></div>
                <div class="absolute inset-0 rounded-full border-[3px] border-transparent border-t-emerald-600 animate-spin-slow" aria-hidden="true"></div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-600 p-2.5 text-white shadow">
                    @include('components.silapandu-mark', ['attributes' => new \Illuminate\View\ComponentAttributeBag(['class' => 'h-full w-full'])])
                </div>
            </div>
            <div class="mt-5">
                <div class="text-lg font-bold tracking-[0.12em] text-slate-900">SILAPANDU</div>
                <div class="mt-1 text-xs font-medium tracking-wide text-emerald-700">Sistem Layanan Administrasi Pernikahan Terpadu</div>
            </div>
            <div class="mt-6 w-full max-w-[14rem]">
                <div class="flex items-center justify-between text-[11px]">
                    <span class="font-medium text-slate-600" x-text="statusText"></span>
                    <span class="font-semibold tabular-nums text-emerald-700" x-text="progress + '%'"></span>
                </div>
                <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-slate-100">
                    <div class="h-full rounded-full bg-emerald-600 transition-all duration-500 ease-out" :style="'width:'+progress+'%'"></div>
                </div>
            </div>
            <button type="button" @click="introVisible = false" class="mt-5 text-[11px] font-medium text-slate-400 hover:text-slate-600">Lewati →</button>
        </div>
    </div>

    <div x-data="{ sidebarOpen: false, sidebarCollapsed: false }" class="min-h-screen">
        <div x-show="sidebarOpen && !sidebarCollapsed" x-cloak @click="sidebarOpen = false" class="fixed inset-0 z-40 bg-slate-950/30 md:hidden"></div>
        <div x-show="sidebarCollapsed" x-cloak @click="sidebarCollapsed = false" class="fixed inset-0 z-30 bg-slate-950/10 hidden md:block"></div>

        <aside class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col border-r border-slate-200 bg-[#edf2ef] text-slate-800 transform transition-transform duration-300 ease-in-out" :class="sidebarCollapsed ? '-translate-x-full' : (sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0')">
            <div class="flex h-20 items-center justify-between gap-2 border-b border-slate-200 px-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-800 p-2 text-white">
                        @include('components.silapandu-mark', ['attributes' => new \Illuminate\View\ComponentAttributeBag(['class' => 'h-full w-full'])])
                    </div>
                    <div>
                        <div class="font-bold tracking-tight text-slate-900">SILAPANDU</div>
                        <div class="text-xs text-slate-500">Sistem Layanan Administrasi Pernikahan Terpadu</div>
                    </div>
                </div>
                <button type="button" @click="sidebarCollapsed = true" class="hidden md:inline-flex h-8 w-8 items-center justify-center rounded-md border border-slate-200 bg-white text-slate-500 hover:bg-slate-50" aria-label="Sembunyikan sidebar">‹</button>
                <button type="button" @click="sidebarOpen = false" class="inline-flex md:hidden h-8 w-8 items-center justify-center rounded-md border border-slate-200 bg-white text-slate-500" aria-label="Tutup menu">✕</button>
            </div>

            <nav class="flex-1 space-y-1 px-4 py-6" aria-label="Navigasi utama">
                <p class="px-3 pb-2 text-[10px] font-bold uppercase tracking-[0.18em] text-slate-500">Menu Utama</p>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'bg-emerald-800 text-white shadow-sm' : 'text-slate-600 hover:bg-white hover:text-emerald-900' }} flex items-center gap-3 rounded-md px-3 py-2.5 text-sm font-semibold transition">
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-8 9 8M5 10v10h14V10M9 20v-6h6v6"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('registrations.create') }}" class="{{ request()->routeIs('registrations.create') ? 'bg-emerald-800 text-white shadow-sm' : 'text-slate-600 hover:bg-white hover:text-emerald-900' }} flex items-center gap-3 rounded-md px-3 py-2.5 text-sm font-semibold transition">
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
                    Pendaftaran Baru
                </a>
                <a href="{{ route('n6.manual.index') }}" class="{{ request()->routeIs('n6.*') ? 'bg-emerald-800 text-white shadow-sm' : 'text-slate-600 hover:bg-white hover:text-emerald-900' }} flex items-center gap-3 rounded-md px-3 py-2.5 text-sm font-semibold transition">
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6 3h9l3 3v15H6zM9 12h6M9 16h6M9 8h3"/></svg>
                    N6 Manual
                </a>
                <p class="px-3 pb-2 pt-8 text-[10px] font-bold uppercase tracking-[0.18em] text-slate-500">Konfigurasi</p>
                <a href="{{ route('settings.village-profile.edit') }}" class="{{ request()->routeIs('settings.village-profile.*') ? 'bg-emerald-800 text-white shadow-sm' : 'text-slate-600 hover:bg-white hover:text-emerald-900' }} flex items-center gap-3 rounded-md px-3 py-2.5 text-sm font-semibold transition">
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15.5a3.5 3.5 0 100-7 3.5 3.5 0 000 7zM19 12a7 7 0 01-.1 1.2l2 1.5-2 3.4-2.4-1a7 7 0 01-2 1.2L14 21h-4l-.5-2.7a7 7 0 01-2-1.2l-2.4 1-2-3.4 2-1.5A7 7 0 015 12c0-.4 0-.8.1-1.2l-2-1.5 2-3.4 2.4 1a7 7 0 012-1.2L10 3h4l.5 2.7a7 7 0 012 1.2l2.4-1 2 3.4-2 1.5c.1.4.1.8.1 1.2z"/></svg>
                    Profil Desa & Penandatangan
                </a>
                <a href="{{ route('settings.document-numbers.edit') }}" class="{{ request()->routeIs('settings.document-numbers.*') ? 'bg-emerald-800 text-white shadow-sm' : 'text-slate-600 hover:bg-white hover:text-emerald-900' }} flex items-center gap-3 rounded-md px-3 py-2.5 text-sm font-semibold transition">
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M14 3H6a2 2 0 00-2 2v14a2 2 0 002 2h12a2 2 0 002-2V8z"/><path stroke-linecap="round" stroke-linejoin="round" d="M14 3v5h5M9 13h6M9 17h6"/></svg>
                    Penomoran Surat
                </a>
                <p class="px-3 pb-2 pt-6 text-[10px] font-bold uppercase tracking-[0.18em] text-slate-500">Pemeliharaan</p>
                <a href="{{ route('backups.index') }}" class="{{ request()->routeIs('backups.*') ? 'bg-emerald-800 text-white shadow-sm' : 'text-slate-600 hover:bg-white hover:text-emerald-900' }} flex items-center gap-3 rounded-md px-3 py-2.5 text-sm font-semibold transition">
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8M8 12h8M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Cadangan Data
                </a>
                <a href="{{ route('help.index') }}" class="{{ request()->routeIs('help.*') ? 'bg-emerald-800 text-white shadow-sm' : 'text-slate-600 hover:bg-white hover:text-emerald-900' }} flex items-center gap-3 rounded-md px-3 py-2.5 text-sm font-semibold transition">
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19V7a2 2 0 012-2h9l3 3v11a2 2 0 01-2 2H6a2 2 0 01-2-2z"/><path stroke-linecap="round" stroke-linejoin="round" d="M14 5v4h4M8 13h8M8 17h8"/><circle cx="18.5" cy="7.5" r="1.4" fill="currentColor" stroke="none"/><path stroke-linecap="round" stroke-linejoin="round" d="M9 10h1"/></svg>
                    Panduan Aplikasi
                </a>
            </nav>

            <div class="border-t border-slate-200 px-6 py-4 text-xs text-slate-500">Digitalisasi Pelayanan Desa</div>
        </aside>

        <div class="min-w-0 flex-1 transition-all duration-300 ease-in-out" :class="sidebarCollapsed ? 'md:ml-0' : 'md:ml-72'">
            <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 bg-white px-4 sm:px-6">
                <div class="flex items-center gap-2">
                    <button type="button" @click="sidebarOpen = true" class="rounded-md border border-slate-200 p-2 text-slate-600 hover:bg-slate-50 md:hidden" aria-label="Buka menu">☰</button>
                    <button type="button" @click="sidebarCollapsed = !sidebarCollapsed" class="hidden md:inline-flex h-8 w-8 items-center justify-center rounded-md border border-slate-200 bg-white text-slate-600 hover:bg-slate-50" :aria-label="sidebarCollapsed ? 'Buka sidebar' : 'Sembunyikan sidebar'"><span x-text="sidebarCollapsed ? '☰' : '‹'"></span></button>
                </div>
                <div class="hidden md:block">
                    <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">Sistem Layanan Administrasi Pernikahan Terpadu</p>
                    <p class="text-sm font-semibold text-slate-800">SILAPANDU<span class="font-normal text-slate-500"> · {{ \App\Models\VillageProfile::query()->value('village_name') ?: 'Desa belum diatur' }}</span></p>
                </div>
                @unless(request()->routeIs('dashboard') || request()->routeIs('registrations.create') || request()->routeIs('registrations.edit'))
                <div class="ml-auto flex items-center gap-2">
                    <a href="{{ route('registrations.create') }}" class="inline-flex items-center gap-2 rounded-md bg-emerald-800 px-3.5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-900">+ <span>Pendaftaran Baru</span></a>
                </div>
                @endunless
            </header>


    {{-- =========================================================
         MAIN CONTENT
    ========================================================== --}}
    <main class="min-h-[calc(100vh-8rem)] py-7 sm:py-9">

        <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">

            @php $backupWarn = \App\Models\BackupSetting::singleton(); @endphp
            @if ($backupWarn->shouldShowWarning() && !request()->routeIs('backups.*'))
                <div class="mb-6 rounded-xl border border-amber-300 bg-amber-50 p-4 flex items-start justify-between gap-4" role="alert">
                    <div>
                        <p class="text-sm font-semibold text-amber-900">Pengingat: Belum ada cadangan selama 14 hari</p>
                        <p class="mt-1 text-xs text-amber-800">Segera cadangkan data agar aman. @if($backupWarn->last_backup_at) Terakhir: {{ $backupWarn->last_backup_at->format('d/m/Y H:i') }} @else Belum pernah cadangan. @endif</p>
                    </div>
                    <div class="flex shrink-0 flex-col gap-2 sm:items-end">
                        <a href="{{ route('backups.index') }}" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Cadangkan Sekarang</a>
                        <form action="{{ route('backups.dismiss') }}" method="POST" class="flex items-center gap-2">@csrf
                            <select name="snooze_days" class="rounded-lg border border-amber-300 bg-white px-2 py-2 text-xs font-medium text-amber-800">
                                <option value="14" selected>Ingatkan 14 hari</option>
                                <option value="7">Ingatkan 7 hari</option>
                                <option value="30">Ingatkan 30 hari</option>
                            </select>
                            <button type="submit" class="rounded-lg border border-amber-300 bg-white px-4 py-2 text-sm font-semibold text-amber-800">Nanti</button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Flash Success --}}
            @if (session('success'))
                <div
                    class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800"
                    role="alert"
                >
                    {{ session('success') }}
                </div>
            @endif

            {{-- Flash Error --}}
            @if (session('error'))
                <div
                    class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"
                    role="alert"
                >
                    {{ session('error') }}
                </div>
            @endif

            {{-- Validation Summary --}}
            @if ($errors->any())
                <div
                    class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-4"
                    role="alert"
                >
                    <div class="mb-2 font-semibold text-red-800">
                        Ada data yang perlu diperiksa.
                    </div>

                    <ul class="list-disc space-y-1 pl-5 text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')

        </div>
    </main>


    {{-- =========================================================
         FOOTER
    ========================================================== --}}
    <footer class="border-t border-slate-200 bg-white">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-2 px-4 py-5 text-xs text-gray-500 sm:flex-row sm:px-6 lg:px-8">

            <span>
                &copy; {{ date('Y') }} SILAPANDU Desa
            </span>

            <span>
                Sistem Layanan Administrasi Pernikahan Terpadu · Versi Desktop Offline
            </span>

        </div>
    </footer>

        </div>
    </div>


    {{-- Page-specific scripts --}}
    @stack('scripts')

</body>
</html>