<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMKAH Premium - Dashboard Pendaftaran Nikah Modern</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        emerald: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                            950: '#022c22',
                        },
                        gold: {
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                        },
                        navy: {
                            800: '#0f172a',
                            900: '#0a0f1d',
                            950: '#050810',
                        }
                    },
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                    },
                    boxShadow: {
                        'glow-emerald': '0 0 25px -5px rgba(16, 185, 129, 0.3)',
                        'glow-gold': '0 0 20px -5px rgba(245, 158, 11, 0.25)',
                    }
                }
            }
        }
    </script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: radial-gradient(circle at top right, #0f172a 0%, #050810 100%);
            color: #f1f5f9;
        }

        /* Custom Scrollbars */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #0f172a;
        }
        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #10b981;
        }

        /* Glassmorphism Classes */
        .glass-panel {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .glass-card {
            background: rgba(30, 41, 59, 0.6);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.07);
        }

        .glass-card:hover {
            border-color: rgba(16, 185, 129, 0.3);
            background: rgba(30, 41, 59, 0.8);
        }

        /* Smooth Progress animation */
        .progress-smooth {
            transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between selection:bg-emerald-500 selection:text-white">

    <!-- Top Navigation Header -->
    <header class="sticky top-0 z-40 glass-panel border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">
            
            <!-- Brand Logo & Context -->
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-emerald-600 to-emerald-400 flex items-center justify-center text-white shadow-lg shadow-emerald-500/20 ring-2 ring-emerald-400/30">
                        <i class="fa-solid fa-ring text-xl"></i>
                    </div>
                    <span class="absolute -bottom-1 -right-1 flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </span>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-base font-extrabold tracking-tight text-white">SIMKAH <span class="text-emerald-400 font-normal">PREMIUM</span></h1>
                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-md bg-emerald-950/80 text-emerald-400 border border-emerald-500/30">v2.4 Online</span>
                    </div>
                    <p class="text-xs text-slate-400">KUA Kec. Jatiraga &middot; Kab. Majalengka</p>
                </div>
            </div>

            <!-- Global Search Input -->
            <div class="hidden md:flex flex-1 max-w-md mx-6">
                <div class="relative w-full">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" id="globalSearch" placeholder="Cari NIK, Nama Pemohon, atau No. Registrasi..." onkeyup="searchHandler(this.value)" class="w-full bg-slate-900/90 border border-slate-700/70 rounded-xl pl-9 pr-10 py-2 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 transition-all">
                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] text-slate-500 font-mono bg-slate-800 px-1.5 py-0.5 rounded border border-slate-700">⌘K</span>
                </div>
            </div>

            <!-- Profile & Actions -->
            <div class="flex items-center space-x-3">
                <button onclick="showToast('Notifikasi: 2 Berkas perlu revisi ulang oleh Penghulu')" class="relative p-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700/50 text-slate-300 hover:text-white transition-all">
                    <i class="fa-regular fa-bell text-sm"></i>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-gold-500 animate-pulse"></span>
                </button>

                <div class="h-8 w-px bg-slate-800"></div>

                <div class="flex items-center space-x-3 pl-1">
                    <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=120" alt="Petugas KUA" class="w-9 h-9 rounded-xl object-cover ring-2 ring-emerald-500/40">
                    <div class="hidden lg:block text-left">
                        <p class="text-xs font-bold text-slate-200">H. Mohammad Ridwan, S.Ag</p>
                        <p class="text-[10px] text-emerald-400 font-medium">Penghulu Muda / Admin</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full space-y-8 flex-grow">

        <!-- Top Information & Banner Section -->
        <div class="glass-panel rounded-3xl p-6 sm:p-8 relative overflow-hidden shadow-2xl border border-slate-800">
            <!-- Decorative Subtle Ambient Light -->
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-gold-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <!-- Title & Meta Info -->
                <div class="space-y-3">
                    <div class="flex flex-wrap items-center gap-2 text-xs text-slate-400">
                        <a href="#" class="hover:text-emerald-400 transition-colors"><i class="fa-solid fa-house mr-1"></i> Dashboard</a>
                        <span>/</span>
                        <a href="#" class="hover:text-emerald-400 transition-colors">Pelayanan Nikah</a>
                        <span>/</span>
                        <span class="text-slate-200 font-semibold">Detail Registrasi</span>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Dashboard Pendaftaran Nikah</h2>
                        <span id="headerStatusBadge" class="px-3 py-1 rounded-full text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/30 flex items-center gap-1.5 shadow-sm">
                            <i class="fa-solid fa-hourglass-half text-[10px] animate-spin"></i>
                            <span id="headerStatusText">Dalam Proses Review</span>
                        </span>
                    </div>

                    <p class="text-xs sm:text-sm text-slate-400 flex flex-wrap items-center gap-y-2 gap-x-4">
                        <span class="font-mono bg-slate-900/80 px-2.5 py-1 rounded-lg border border-slate-700/80 text-emerald-400 font-semibold">
                            <i class="fa-solid fa-hashtag mr-1 text-slate-500"></i>REG/008/2026
                        </span>
                        <span class="flex items-center text-slate-300">
                            <i class="fa-solid fa-handshake mr-1.5 text-gold-400"></i> Nikah Bedol (Luar Kantor)
                        </span>
                        <span class="flex items-center text-slate-300">
                            <i class="fa-solid fa-calendar-day mr-1.5 text-emerald-400"></i> Sabtu, 31 Oktober 2026
                        </span>
                        <span class="flex items-center text-slate-300">
                            <i class="fa-solid fa-location-dot mr-1.5 text-rose-400"></i> Jatiraga Barat RT 05 RW 03
                        </span>
                    </p>
                </div>

                <!-- Overall Progress Metric Widget -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 min-w-[280px] flex flex-col justify-between space-y-3 shadow-inner">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-400 font-semibold flex items-center gap-1.5">
                            <i class="fa-solid fa-chart-line text-emerald-400"></i> Kelengkapan Berkas
                        </span>
                        <span id="overallPercentText" class="text-xl font-extrabold text-emerald-400">20%</span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full bg-slate-800 h-3 rounded-full overflow-hidden p-0.5 border border-slate-700/50">
                        <div id="overallProgressBar" class="bg-gradient-to-r from-emerald-500 to-teal-300 h-full rounded-full progress-smooth shadow-glow-emerald w-[20%]"></div>
                    </div>

                    <div class="flex justify-between text-[11px] text-slate-400">
                        <span id="completedStepsCount">1 dari 5 Tahap Selesai</span>
                        <span class="text-emerald-400 font-medium" id="statusCompletionLabel">Progres Draf Valid</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dynamic Stepper Timeline Navigation (Wizard) -->
        <div class="glass-panel rounded-2xl p-5 overflow-x-auto border border-slate-800">
            <div class="min-w-[750px] flex items-center justify-between relative">
                <!-- Connecting Line -->
                <div class="absolute top-1/2 left-8 right-8 -translate-y-1/2 h-0.5 bg-slate-800 -z-0"></div>
                <div id="timelineLineProgress" class="absolute top-1/2 left-8 -translate-y-1/2 h-0.5 bg-gradient-to-r from-emerald-500 to-teal-400 -z-0 transition-all duration-500 w-[15%]"></div>

                <!-- Step 1 Node -->
                <button onclick="scrollToStep('step-1')" class="relative z-10 flex flex-col items-center group">
                    <div id="node-1" class="w-10 h-10 rounded-xl bg-emerald-500 text-slate-950 font-bold text-sm flex items-center justify-center ring-4 ring-slate-900 shadow-lg shadow-emerald-500/20 transition-all">
                        <i class="fa-solid fa-check text-base"></i>
                    </div>
                    <span class="text-[11px] font-bold text-emerald-400 mt-2">1. Caten Pria</span>
                    <span class="text-[10px] text-slate-500">Terisi</span>
                </button>

                <!-- Step 2 Node -->
                <button onclick="scrollToStep('step-2')" class="relative z-10 flex flex-col items-center group">
                    <div id="node-2" class="w-10 h-10 rounded-xl bg-slate-800 text-slate-400 font-bold text-sm flex items-center justify-center ring-4 ring-slate-900 transition-all border border-slate-700">
                        2
                    </div>
                    <span class="text-[11px] font-medium text-slate-400 group-hover:text-slate-200 mt-2 transition-colors">2. Caten Wanita</span>
                    <span class="text-[10px] text-amber-400/80">Belum Lengkap</span>
                </button>

                <!-- Step 3 Node -->
                <button onclick="scrollToStep('step-3')" class="relative z-10 flex flex-col items-center group">
                    <div id="node-3" class="w-10 h-10 rounded-xl bg-slate-800 text-slate-400 font-bold text-sm flex items-center justify-center ring-4 ring-slate-900 transition-all border border-slate-700">
                        3
                    </div>
                    <span class="text-[11px] font-medium text-slate-400 group-hover:text-slate-200 mt-2 transition-colors">3. Wali Nikah</span>
                    <span class="text-[10px] text-slate-500">Belum Diisi</span>
                </button>

                <!-- Step 4 Node -->
                <button onclick="scrollToStep('step-4')" class="relative z-10 flex flex-col items-center group">
                    <div id="node-4" class="w-10 h-10 rounded-xl bg-slate-800 text-slate-400 font-bold text-sm flex items-center justify-center ring-4 ring-slate-900 transition-all border border-slate-700">
                        4
                    </div>
                    <span class="text-[11px] font-medium text-slate-400 group-hover:text-slate-200 mt-2 transition-colors">4. Saksi & Lokasi</span>
                    <span class="text-[10px] text-slate-500">Belum Diisi</span>
                </button>

                <!-- Step 5 Node -->
                <button onclick="scrollToStep('step-5')" class="relative z-10 flex flex-col items-center group">
                    <div id="node-5" class="w-10 h-10 rounded-xl bg-slate-800 text-slate-400 font-bold text-sm flex items-center justify-center ring-4 ring-slate-900 transition-all border border-slate-700">
                        5
                    </div>
                    <span class="text-[11px] font-medium text-slate-400 group-hover:text-slate-200 mt-2 transition-colors">5. Pemeriksaan KUA</span>
                    <span class="text-[10px] text-slate-500">Menunggu</span>
                </button>
            </div>
        </div>

        <!-- Grid Layout: Cards (Left 8 cols) & Verification Summary Widget (Right 4 cols) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <!-- Left: Interactive Stage Cards Container (8 cols) -->
            <div class="lg:col-span-8 space-y-6">

                <!-- Stage Card 1: Groom -->
                <div id="step-1" class="glass-card rounded-2xl p-6 transition-all duration-300 relative group border-l-4 border-l-emerald-500">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-4 border-b border-slate-800">
                        <div class="flex items-center space-x-3.5">
                            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center justify-center font-bold">
                                <i class="fa-solid fa-mars text-base"></i>
                            </div>
                            <div>
                                <h3 class="font-extrabold text-white text-base">Tahap 1: Calon Pengantin Laki-Laki</h3>
                                <p class="text-xs text-slate-400">Data identitas & syarat administrasi N1/N2/N4</p>
                            </div>
                        </div>
                        <span id="badge-groom" class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-check text-[10px]"></i> Selesai & Valid
                        </span>
                    </div>

                    <!-- Card Body / Summary Preview -->
                    <div class="py-4 grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                        <div class="flex space-x-3 items-center sm:col-span-1">
                            <img id="avatar-groom" src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=150" alt="Foto Pria" class="w-12 h-12 rounded-xl object-cover ring-2 ring-emerald-500/30">
                            <div>
                                <p class="text-slate-400 text-[10px] uppercase tracking-wider font-semibold">Nama Lengkap</p>
                                <p id="name-groom" class="font-bold text-slate-100 text-sm">Ahmad Subagja, S.T.</p>
                            </div>
                        </div>

                        <div>
                            <p class="text-slate-400 text-[10px] uppercase tracking-wider font-semibold">NIK & TTL</p>
                            <p id="nik-groom" class="font-mono text-slate-300 font-medium">3201982701920001</p>
                            <p id="ttl-groom" class="text-slate-400 text-[11px]">Majalengka, 12 Agu 1995</p>
                        </div>

                        <div>
                            <p class="text-slate-400 text-[10px] uppercase tracking-wider font-semibold">Status Berkas Dokumen</p>
                            <p class="text-emerald-400 font-medium flex items-center gap-1 mt-0.5">
                                <i class="fa-solid fa-file-shield"></i> KTP, KK, N1 Lengkap
                            </p>
                        </div>
                    </div>

                    <!-- Card Footer Actions -->
                    <div class="pt-3 border-t border-slate-800/80 flex flex-wrap items-center justify-between gap-3">
                        <span class="text-slate-400 text-xs flex items-center gap-1">
                            <i class="fa-solid fa-clock-rotate-left text-slate-500"></i> Terakhir diperbarui oleh Petugas
                        </span>
                        <div class="flex items-center space-x-2">
                            <button onclick="openUploadModal('groom')" class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium text-xs transition-colors border border-slate-700">
                                <i class="fa-solid fa-upload mr-1 text-emerald-400"></i> Upload Dokumen
                            </button>
                            <button onclick="openFormModal('groom')" class="px-4 py-1.5 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 border border-emerald-500/40 font-semibold text-xs transition-colors flex items-center gap-1">
                                <i class="fa-solid fa-pen-to-square text-xs"></i> Edit Data
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stage Card 2: Bride -->
                <div id="step-2" class="glass-card rounded-2xl p-6 transition-all duration-300 relative group border-l-4 border-l-amber-500">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-4 border-b border-slate-800">
                        <div class="flex items-center space-x-3.5">
                            <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-400 flex items-center justify-center font-bold">
                                <i class="fa-solid fa-venus text-base"></i>
                            </div>
                            <div>
                                <h3 class="font-extrabold text-white text-base">Tahap 2: Calon Pengantin Wanita</h3>
                                <p class="text-xs text-slate-400">Data identitas, foto & kepastian status wali</p>
                            </div>
                        </div>
                        <span id="badge-bride" class="px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/30 flex items-center gap-1.5">
                            <i class="fa-solid fa-triangle-exclamation text-[10px]"></i> Belum Lengkap
                        </span>
                    </div>

                    <!-- Card Body -->
                    <div class="py-4 grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                        <div class="flex space-x-3 items-center sm:col-span-1">
                            <div class="w-12 h-12 rounded-xl bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-500">
                                <i class="fa-solid fa-user-large text-lg"></i>
                            </div>
                            <div>
                                <p class="text-slate-400 text-[10px] uppercase tracking-wider font-semibold">Nama Lengkap</p>
                                <p id="name-bride" class="font-bold text-slate-100 text-sm">Siti Nurhaliza, S.Pd.</p>
                            </div>
                        </div>

                        <div>
                            <p class="text-slate-400 text-[10px] uppercase tracking-wider font-semibold">NIK & TTL</p>
                            <p id="nik-bride" class="font-mono text-slate-300 font-medium">3201985403980002</p>
                            <p id="ttl-bride" class="text-slate-400 text-[11px]">Bandung, 18 Mei 1997</p>
                        </div>

                        <div>
                            <p class="text-slate-400 text-[10px] uppercase tracking-wider font-semibold">Kelengkapan Syarat</p>
                            <p id="doc-status-bride" class="text-amber-400 font-medium flex items-center gap-1 mt-0.5">
                                <i class="fa-solid fa-circle-exclamation"></i> Kurang Surat Persetujuan N4
                            </p>
                        </div>
                    </div>

                    <!-- Card Footer Actions -->
                    <div class="pt-3 border-t border-slate-800/80 flex flex-wrap items-center justify-between gap-3">
                        <span class="text-amber-400/80 text-xs flex items-center gap-1">
                            <i class="fa-solid fa-circle-info"></i> Disarankan melengkapi N4 & Foto latar biru
                        </span>
                        <div class="flex items-center space-x-2">
                            <button onclick="openUploadModal('bride')" class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium text-xs transition-colors border border-slate-700">
                                <i class="fa-solid fa-upload mr-1 text-amber-400"></i> Upload Dokumen
                            </button>
                            <button onclick="openFormModal('bride')" class="px-4 py-1.5 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 font-semibold text-xs transition-colors flex items-center gap-1">
                                <i class="fa-solid fa-pen-to-square text-xs"></i> Isi & Lengkapi Data
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stage Card 3: Guardian -->
                <div id="step-3" class="glass-card rounded-2xl p-6 transition-all duration-300 relative group border-l-4 border-l-slate-700">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-4 border-b border-slate-800">
                        <div class="flex items-center space-x-3.5">
                            <div class="w-10 h-10 rounded-xl bg-slate-800 text-slate-400 flex items-center justify-center font-bold">
                                <i class="fa-solid fa-user-shield text-base"></i>
                            </div>
                            <div>
                                <h3 class="font-extrabold text-white text-base">Tahap 3: Data Wali Nikah</h3>
                                <p class="text-xs text-slate-400">Wali Nasab (Ayah Kandung / Saudara) atau Wali Hakim</p>
                            </div>
                        </div>
                        <span id="badge-guardian" class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-800 text-slate-400 border border-slate-700">
                            Belum Diisi
                        </span>
                    </div>

                    <!-- Card Body Placeholder -->
                    <div class="py-4 text-xs text-slate-500 italic flex items-center justify-center border border-dashed border-slate-800 rounded-xl my-2">
                        Data Wali Nikah belum dimasukkan ke sistem. Klik "Isi Data Wali" di bawah.
                    </div>

                    <!-- Card Footer Actions -->
                    <div class="pt-3 border-t border-slate-800/80 flex items-center justify-between">
                        <span class="text-slate-500 text-xs">Prasyarat: Data Caten Wanita harus terisi</span>
                        <button onclick="openFormModal('guardian')" class="px-4 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs transition-all shadow-md shadow-emerald-600/20 flex items-center gap-1">
                            <i class="fa-solid fa-plus text-xs"></i> Isi Data Wali
                        </button>
                    </div>
                </div>

                <!-- Stage Card 4: Witness & Location -->
                <div id="step-4" class="glass-card rounded-2xl p-6 transition-all duration-300 relative group border-l-4 border-l-slate-700">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-4 border-b border-slate-800">
                        <div class="flex items-center space-x-3.5">
                            <div class="w-10 h-10 rounded-xl bg-slate-800 text-slate-400 flex items-center justify-center font-bold">
                                <i class="fa-solid fa-users text-base"></i>
                            </div>
                            <div>
                                <h3 class="font-extrabold text-white text-base">Tahap 4: Saksi-Saksi & Penugasan Penghulu</h3>
                                <p class="text-xs text-slate-400">2 Orang Saksi sah & penetapan Penghulu KUA</p>
                            </div>
                        </div>
                        <span id="badge-witness" class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-800 text-slate-400 border border-slate-700">
                            Belum Diisi
                        </span>
                    </div>

                    <div class="py-4 text-xs text-slate-500 italic flex items-center justify-center border border-dashed border-slate-800 rounded-xl my-2">
                        Data Saksi 1 & Saksi 2 belum ditetapkan.
                    </div>

                    <div class="pt-3 border-t border-slate-800/80 flex items-center justify-end">
                        <button onclick="openFormModal('witness')" class="px-4 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-semibold text-xs transition-colors flex items-center gap-1">
                            <i class="fa-solid fa-plus text-xs"></i> Input Data Saksi
                        </button>
                    </div>
                </div>

                <!-- Stage Card 5: Verification & Audit -->
                <div id="step-5" class="glass-card rounded-2xl p-6 transition-all duration-300 relative group border-l-4 border-l-slate-700">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-4 border-b border-slate-800">
                        <div class="flex items-center space-x-3.5">
                            <div class="w-10 h-10 rounded-xl bg-slate-800 text-slate-400 flex items-center justify-center font-bold">
                                <i class="fa-solid fa-stamp text-base"></i>
                            </div>
                            <div>
                                <h3 class="font-extrabold text-white text-base">Tahap 5: Pemeriksaan & Verifikasi KUA</h3>
                                <p class="text-xs text-slate-400">Pemeriksaan fisik (N7), pengumuman nikah (N8) & persetujuan Kepala KUA</p>
                            </div>
                        </div>
                        <span id="badge-verification" class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-800 text-slate-400 border border-slate-700">
                            Menunggu Tahap 1-4
                        </span>
                    </div>

                    <div class="py-4 text-xs text-slate-400 space-y-2">
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                            <span class="flex items-center gap-2"><i class="fa-regular fa-square-check text-slate-500"></i> Verifikasi Halang Nalar (N7)</span>
                            <span class="text-slate-500">Pending</span>
                        </div>
                        <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                            <span class="flex items-center gap-2"><i class="fa-regular fa-square-check text-slate-500"></i> Pengumuman Kehendak Nikah (N8)</span>
                            <span class="text-slate-500">Pending</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right: Verification Checklist & Quick Actions Sidebar (4 cols) -->
            <div class="lg:col-span-4 space-y-6">

                <!-- Primary Action Hub Card -->
                <div class="glass-panel rounded-2xl p-5 border border-slate-800 space-y-4 shadow-xl sticky top-28">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2 border-b border-slate-800 pb-3">
                        <i class="fa-solid fa-sliders text-emerald-400"></i> Panel Aksi & Penerbitan
                    </h3>

                    <div class="space-y-2.5">
                        <button onclick="saveDraft()" class="w-full py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-semibold text-xs flex items-center justify-between transition-all">
                            <span class="flex items-center gap-2"><i class="fa-regular fa-floppy-disk text-slate-400"></i> Simpan Draft Pendaftaran</span>
                            <i class="fa-solid fa-angle-right text-slate-500 text-xs"></i>
                        </button>

                        <button id="btnValidate" onclick="triggerValidation()" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs flex items-center justify-between shadow-lg shadow-emerald-600/20 transition-all">
                            <span class="flex items-center gap-2"><i class="fa-solid fa-shield-halved"></i> Validasi Berkas KUA</span>
                            <i class="fa-solid fa-check text-xs"></i>
                        </button>

                        <button id="btnPrintForms" onclick="printForms()" class="w-full py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 font-semibold text-xs flex items-center justify-between transition-all">
                            <span class="flex items-center gap-2"><i class="fa-solid fa-print text-gold-400"></i> Cetak Formulir N1 – N6</span>
                            <i class="fa-solid fa-download text-slate-500 text-xs"></i>
                        </button>

                        <button id="btnIssueBook" disabled onclick="issueMarriageBook()" class="w-full py-2.5 px-4 rounded-xl bg-slate-800/50 text-slate-500 border border-slate-800 font-bold text-xs flex items-center justify-between cursor-not-allowed transition-all">
                            <span class="flex items-center gap-2"><i class="fa-solid fa-book-bookmark"></i> Terbit Akta / Buku Nikah</span>
                            <i class="fa-solid fa-lock text-xs"></i>
                        </button>
                    </div>

                    <!-- Checklist Ringkasan Persyaratan -->
                    <div class="pt-4 border-t border-slate-800 space-y-3">
                        <div class="flex justify-between items-center">
                            <h4 class="text-xs font-bold text-slate-300">Checklist Syarat Administratif</h4>
                            <span id="checklistCount" class="text-[10px] font-mono font-bold text-emerald-400 bg-emerald-950 px-2 py-0.5 rounded border border-emerald-500/30">2/6 Terpenuhi</span>
                        </div>

                        <div class="space-y-2 text-xs">
                            <label class="flex items-center space-x-2.5 cursor-pointer">
                                <input type="checkbox" checked disabled class="rounded border-slate-700 bg-slate-900 text-emerald-500 focus:ring-emerald-500/30 w-4 h-4">
                                <span class="text-slate-300 line-through">Surat Pengantar N1 dari Desa/Kelurahan</span>
                            </label>
                            <label class="flex items-center space-x-2.5 cursor-pointer">
                                <input type="checkbox" checked disabled class="rounded border-slate-700 bg-slate-900 text-emerald-500 focus:ring-emerald-500/30 w-4 h-4">
                                <span class="text-slate-300 line-through">Fotokopi KTP & Kartu Keluarga</span>
                            </label>
                            <label class="flex items-center space-x-2.5 cursor-pointer">
                                <input type="checkbox" id="chk-n4" onchange="toggleChecklist(this)" class="rounded border-slate-700 bg-slate-900 text-emerald-500 focus:ring-emerald-500/30 w-4 h-4">
                                <span class="text-slate-400" id="lbl-n4">Surat Persetujuan Mempelai N4</span>
                            </label>
                            <label class="flex items-center space-x-2.5 cursor-pointer">
                                <input type="checkbox" id="chk-n2" onchange="toggleChecklist(this)" class="rounded border-slate-700 bg-slate-900 text-emerald-500 focus:ring-emerald-500/30 w-4 h-4">
                                <span class="text-slate-400" id="lbl-n2">Surat Permohonan Kehendak Nikah N2</span>
                            </label>
                            <label class="flex items-center space-x-2.5 cursor-pointer">
                                <input type="checkbox" id="chk-suntik" onchange="toggleChecklist(this)" class="rounded border-slate-700 bg-slate-900 text-emerald-500 focus:ring-emerald-500/30 w-4 h-4">
                                <span class="text-slate-400" id="lbl-suntik">Sertifikat Layak Nikah / Imunisasi TT</span>
                            </label>
                            <label class="flex items-center space-x-2.5 cursor-pointer">
                                <input type="checkbox" id="chk-foto" onchange="toggleChecklist(this)" class="rounded border-slate-700 bg-slate-900 text-emerald-500 focus:ring-emerald-500/30 w-4 h-4">
                                <span class="text-slate-400" id="lbl-foto">Pasfoto 2x3 & 4x6 Latar Biru</span>
                            </label>
                        </div>
                    </div>

                    <!-- KUA Contact Help Card -->
                    <div class="mt-4 p-3 rounded-xl bg-slate-900/80 border border-slate-800 flex items-center justify-between text-xs">
                        <div class="flex items-center space-x-2 text-slate-400">
                            <i class="fa-solid fa-headset text-emerald-400 text-sm"></i>
                            <span>Bantuan Petugas KUA</span>
                        </div>
                        <a href="#" onclick="showToast('Menghubungi Layanan Bantuan KUA...')" class="text-emerald-400 font-bold hover:underline">Hubungi</a>
                    </div>
                </div>

            </div>

        </div>

    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800/80 py-6 text-center text-xs text-slate-500 mt-12 glass-panel">
        <p>&copy; 2026 Kementerian Agama RI &middot; SIMKAH (Sistem Informasi Manajemen Nikah) Online. Hak Cipta Dilindungi.</p>
    </footer>

    <!-- Interactive Data Entry Modal -->
    <div id="formModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 flex items-center justify-center p-4 hidden">
        <div class="glass-panel rounded-3xl max-w-xl w-full p-6 sm:p-8 shadow-2xl border border-slate-700/60 space-y-6 max-h-[90vh] overflow-y-auto">
            
            <div class="flex justify-between items-center border-b border-slate-800 pb-4">
                <div>
                    <h3 id="modalTitle" class="font-extrabold text-white text-lg">Input Data</h3>
                    <p class="text-xs text-slate-400">Lengkapi formulir dengan data asli sesuai KTP/Dukcapil</p>
                </div>
                <button onclick="closeFormModal()" class="w-8 h-8 rounded-xl bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form id="activeModalForm" onsubmit="saveModalData(event)" class="space-y-4 text-xs">
                <input type="hidden" id="modalCategoryKey">

                <div>
                    <label class="block text-slate-300 font-semibold mb-1.5">NIK (Nomor Induk Kependudukan)</label>
                    <input type="text" id="modalNik" maxlength="16" required placeholder="16 Digit NIK KTP..." class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-slate-100 placeholder-slate-500 focus:outline-none focus:border-emerald-500 font-mono">
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1.5">Nama Lengkap (Sesuai KTP & Gelar)</label>
                    <input type="text" id="modalName" required placeholder="Nama lengkap..." class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-slate-100 placeholder-slate-500 focus:outline-none focus:border-emerald-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1.5">Tempat Lahir</label>
                        <input type="text" id="modalBirthplace" required placeholder="Kota/Kabupaten" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-slate-100 placeholder-slate-500 focus:outline-none focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1.5">Tanggal Lahir</label>
                        <input type="date" id="modalBirthdate" required class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-slate-100 focus:outline-none focus:border-emerald-500">
                    </div>
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1.5">Alamat Domisili Lengkap</label>
                    <textarea id="modalAddress" rows="2" placeholder="Jalan, RT/RW, Desa, Kecamatan..." class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-slate-100 placeholder-slate-500 focus:outline-none focus:border-emerald-500"></textarea>
                </div>

                <div class="p-3 bg-slate-900/90 rounded-2xl border border-slate-800 flex items-center space-x-3">
                    <input type="checkbox" id="modalCheckComplete" class="rounded border-slate-700 bg-slate-950 text-emerald-500 focus:ring-emerald-500/30 w-4 h-4">
                    <label for="modalCheckComplete" class="text-xs text-slate-300 font-medium cursor-pointer">
                        Konfirmasi seluruh fisik dokumen & identitas sudah diverifikasi
                    </label>
                </div>

                <div class="pt-3 border-t border-slate-800 flex justify-end space-x-3">
                    <button type="button" onclick="closeFormModal()" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold">Batal</button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold shadow-lg shadow-emerald-600/20 transition-all">Simpan & Perbarui</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Upload Document Modal -->
    <div id="uploadModal" class="fixed inset-0 bg-slate-950/80 backdrop-blur-md z-50 flex items-center justify-center p-4 hidden">
        <div class="glass-panel rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-700/60 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-800 pb-3">
                <h3 class="font-bold text-white text-base">Upload Dokumen Pendukung</h3>
                <button onclick="closeUploadModal()" class="text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            
            <div class="border-2 border-dashed border-slate-700 hover:border-emerald-500/50 rounded-2xl p-8 text-center bg-slate-900/50 transition-all cursor-pointer" onclick="document.getElementById('fileInput').click()">
                <i class="fa-solid fa-cloud-arrow-up text-3xl text-emerald-400 mb-2"></i>
                <p class="text-xs text-slate-200 font-semibold">Klik untuk memilih berkas scan KTP/N1/Foto</p>
                <p class="text-[10px] text-slate-500 mt-1">Format PDF, JPG, atau PNG (Maks 5MB)</p>
                <input type="file" id="fileInput" class="hidden" onchange="handleFileSelect(event)">
            </div>

            <div class="flex justify-end space-x-2 pt-2">
                <button onclick="closeUploadModal()" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs font-semibold">Selesai</button>
            </div>
        </div>
    </div>

    <!-- Toast Notification Widget -->
    <div id="toast" class="fixed bottom-6 right-6 bg-slate-900 text-slate-100 border border-slate-700 px-5 py-3.5 rounded-2xl shadow-2xl text-xs font-semibold flex items-center space-x-3 transform translate-y-16 opacity-0 transition-all duration-300 z-50">
        <div id="toastBadge" class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center">
            <i id="toastIcon" class="fa-solid fa-circle-check text-sm"></i>
        </div>
        <span id="toastMsg">Sistem diperbarui secara otomatis.</span>
    </div>

    <script>
        // Reactive State Management
        const state = {
            groom: { filled: true, complete: true, name: 'Ahmad Subagja, S.T.', nik: '3201982701920001', birthplace: 'Majalengka', birthdate: '1995-08-12', address: 'Jatiraga Barat RT 05 RW 03' },
            bride: { filled: true, complete: false, name: 'Siti Nurhaliza, S.Pd.', nik: '3201985403980002', birthplace: 'Bandung', birthdate: '1997-05-18', address: 'Kec. Jatiraga' },
            guardian: { filled: false, complete: false, name: '', nik: '', birthplace: '', birthdate: '', address: '' },
            witness: { filled: false, complete: false, name: '', nik: '', birthplace: '', birthdate: '', address: '' },
            verification: { filled: false, complete: false }
        };

        const titles = {
            groom: 'Calon Pengantin Laki-Laki',
            bride: 'Calon Pengantin Wanita',
            guardian: 'Wali Nikah',
            witness: 'Saksi & Lokasi'
        };

        // Scroll helper
        function scrollToStep(id) {
            const el = document.getElementById(id);
            if (el) {
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        // Search Filter
        function searchHandler(query) {
            if(!query) return;
            const q = query.toLowerCase();
            if('ahmad subagja'.includes(q) || '3201982701920001'.includes(q)) {
                scrollToStep('step-1');
            } else if('siti nurhaliza'.includes(q) || '3201985403980002'.includes(q)) {
                scrollToStep('step-2');
            }
        }

        // Open Form Modal
        function openFormModal(category) {
            document.getElementById('modalCategoryKey').value = category;
            document.getElementById('modalTitle').textContent = `Form Data ${titles[category]}`;

            const data = state[category];
            document.getElementById('modalName').value = data.name || '';
            document.getElementById('modalNik').value = data.nik || '';
            document.getElementById('modalBirthplace').value = data.birthplace || '';
            document.getElementById('modalBirthdate').value = data.birthdate || '';
            document.getElementById('modalAddress').value = data.address || '';
            document.getElementById('modalCheckComplete').checked = data.complete || false;

            document.getElementById('formModal').classList.remove('hidden');
        }

        function closeFormModal() {
            document.getElementById('formModal').classList.add('hidden');
        }

        function saveModalData(e) {
            e.preventDefault();
            const key = document.getElementById('modalCategoryKey').value;
            const isComplete = document.getElementById('modalCheckComplete').checked;

            state[key] = {
                filled: true,
                complete: isComplete,
                name: document.getElementById('modalName').value,
                nik: document.getElementById('modalNik').value,
                birthplace: document.getElementById('modalBirthplace').value,
                birthdate: document.getElementById('modalBirthdate').value,
                address: document.getElementById('modalAddress').value
            };

            updateCardUI(key);
            recalculateProgress();
            closeFormModal();
            showToast(`Data ${titles[key]} berhasil disimpan!`);
        }

        function updateCardUI(key) {
            const data = state[key];
            const card = document.getElementById(`step-${key === 'groom' ? 1 : key === 'bride' ? 2 : key === 'guardian' ? 3 : 4}`);
            const badge = document.getElementById(`badge-${key}`);

            if(!badge || !card) return;

            if (data.filled) {
                if (data.complete) {
                    badge.className = "px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 flex items-center gap-1.5";
                    badge.innerHTML = `<i class="fa-solid fa-circle-check text-[10px]"></i> Selesai & Valid`;
                    card.className = card.className.replace(/border-l-slate-700|border-l-amber-500/, 'border-l-emerald-500');
                } else {
                    badge.className = "px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/30 flex items-center gap-1.5";
                    badge.innerHTML = `<i class="fa-solid fa-triangle-exclamation text-[10px]"></i> Belum Lengkap`;
                    card.className = card.className.replace(/border-l-slate-700|border-l-emerald-500/, 'border-l-amber-500');
                }

                // Update text preview if elements exist
                const nameEl = document.getElementById(`name-${key}`);
                const nikEl = document.getElementById(`nik-${key}`);
                if (nameEl) nameEl.textContent = data.name;
                if (nikEl) nikEl.textContent = data.nik;
            }
        }

        function recalculateProgress() {
            const keys = ['groom', 'bride', 'guardian', 'witness', 'verification'];
            let filledCount = 0;
            
            keys.forEach(k => {
                if (state[k] && state[k].filled) filledCount++;
            });

            const percent = Math.round((filledCount / 5) * 100);

            // Update UI Progress Bar
            document.getElementById('overallPercentText').textContent = `${percent}%`;
            document.getElementById('overallProgressBar').style.width = `${percent}%`;
            document.getElementById('timelineLineProgress').style.width = `${percent}%`;
            document.getElementById('completedStepsCount').textContent = `${filledCount} dari 5 Tahap Selesai`;

            // Node visual updates
            keys.forEach((k, idx) => {
                const node = document.getElementById(`node-${idx + 1}`);
                if (node && state[k] && state[k].filled) {
                    node.className = "w-10 h-10 rounded-xl bg-emerald-500 text-slate-950 font-bold text-sm flex items-center justify-center ring-4 ring-slate-900 shadow-lg shadow-emerald-500/20 transition-all";
                    node.innerHTML = `<i class="fa-solid fa-check text-base"></i>`;
                }
            });

            // Unlock Book Issue button if 100%
            const issueBtn = document.getElementById('btnIssueBook');
            const headerBadge = document.getElementById('headerStatusBadge');
            const headerText = document.getElementById('headerStatusText');

            if (percent === 100) {
                issueBtn.disabled = false;
                issueBtn.className = "w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-gold-500 to-amber-600 hover:from-gold-400 hover:to-amber-500 text-slate-950 font-extrabold text-xs flex items-center justify-between shadow-glow-gold cursor-pointer transition-all";
                
                headerBadge.className = "px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 flex items-center gap-1.5 shadow-sm";
                headerText.textContent = "Siap Diterbitkan Buku Nikah";
            }
        }

        // Checklist Items Toggle
        function toggleChecklist(checkbox) {
            const labelMap = {
                'chk-n4': 'lbl-n4',
                'chk-n2': 'lbl-n2',
                'chk-suntik': 'lbl-suntik',
                'chk-foto': 'lbl-foto'
            };
            const label = document.getElementById(labelMap[checkbox.id]);
            if (checkbox.checked) {
                label.className = "text-slate-300 line-through";
            } else {
                label.className = "text-slate-400";
            }
            updateChecklistCount();
        }

        function updateChecklistCount() {
            let total = 2; // initial disabled checkboxes
            ['chk-n4', 'chk-n2', 'chk-suntik', 'chk-foto'].forEach(id => {
                const el = document.getElementById(id);
                if (el && el.checked) total++;
            });
            document.getElementById('checklistCount').textContent = `${total}/6 Terpenuhi`;
        }

        // Upload Modal Functions
        function openUploadModal(cat) {
            document.getElementById('uploadModal').classList.remove('hidden');
        }
        function closeUploadModal() {
            document.getElementById('uploadModal').classList.add('hidden');
        }
        function handleFileSelect(e) {
            if(e.target.files.length > 0) {
                showToast(`File "${e.target.files[0].name}" berhasil diunggah!`);
                closeUploadModal();
            }
        }

        // Quick Actions
        function saveDraft() {
            showToast('Draft pendaftaran nikah berhasil disimpan ke server KUA.');
        }

        function triggerValidation() {
            showToast('Memulai pemeriksaan otomatis dokumen & status Dukcapil...', 'info');
            setTimeout(() => {
                showToast('Verifikasi Berkas KUA selesai: Data Caten Valid.');
            }, 1800);
        }

        function printForms() {
            showToast('Menyiapkan pencetakan dokumen N1–N6 (PDF)...', 'print');
        }

        function issueMarriageBook() {
            showToast('Akta & Buku Nikah Resmi berhasil diterbitkan!', 'success');
        }

        // Toast Helper
        function showToast(msg, type = 'success') {
            const toast = document.getElementById('toast');
            const toastMsg = document.getElementById('toastMsg');
            const toastIcon = document.getElementById('toastIcon');
            const toastBadge = document.getElementById('toastBadge');

            toastMsg.textContent = msg;

            if(type === 'print') {
                toastIcon.className = "fa-solid fa-print text-sm";
                toastBadge.className = "w-8 h-8 rounded-xl bg-gold-500/20 text-gold-400 flex items-center justify-center";
            } else if(type === 'info') {
                toastIcon.className = "fa-solid fa-circle-info text-sm";
                toastBadge.className = "w-8 h-8 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center";
            } else {
                toastIcon.className = "fa-solid fa-circle-check text-sm";
                toastBadge.className = "w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center";
            }

            toast.classList.remove('translate-y-16', 'opacity-0');
            setTimeout(() => {
                toast.classList.add('translate-y-16', 'opacity-0');
            }, 3200);
        }
    </script>
</body>
</html>