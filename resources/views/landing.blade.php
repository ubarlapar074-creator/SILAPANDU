<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SILAPANDU - Layanan Nikah Desa</title>

    @fonts

    <!-- Tailwind CSS v4 via Laravel Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* Custom properties for the landing page */
        :root {
            --emerald-light: #10b981;
            --emerald-dark: #059669;
            --slate-light: #f8fafc;
            --slate-dark: #0f172a;
        }
    </style>
</head>
<body class="bg-white text-slate-900 antialiased">

    <!-- Navigation -->
    <nav class="border-b border-slate-200 backdrop-blur-sm bg-white/95 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between sm:max-w-full">
            <!-- Left: Logo -->
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-600 to-emerald-500 flex items-center justify-center text-white font-extrabold text-sm shadow-sm shadow-emerald-400/20">
                    <i class="fa-solid fa-kaaba text-base"></i>
                </div>
                <div>
                    <h1 class="font-black text-xl tracking-tight text-slate-900">SILAPANDU</h1>
                    <p class="text-[10px] text-emerald-500 uppercase tracking-wider font-semibold">Layanan Pernikahan</p>
                </div>
            </div>

            <!-- Center: Menu -->
            <div class="hidden md:flex items-center gap-8">
                <a href="#features" class="text-slate-600 hover:text-emerald-600 font-medium transition-colors text-sm">Fitur</a>
                <a href="#comparison" class="text-slate-600 hover:text-emerald-600 font-medium transition-colors text-sm">Perbandingan</a>
                <a href="#comparison" class="text-slate-600 hover:text-emerald-600 font-medium transition-colors text-sm">Harga</a>
            </div>

            <!-- Right: CTA Button -->
            <div class="flex items-center gap-2">
                <a href="/dashboard" class="inline-flex items-center px-4 py-2.5 bg-emerald-600 text-white font-medium rounded-2xl text-sm font-semibold hover:bg-emerald-500 transition-colors shadow-sm shadow-emerald-400/15">
                    Masuk Aplikasi
                    <i class="fa-solid fa-arrow-right text-sm ml-1"></i>
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="min-h-[70vh] flex items-center justify-center px-6 py-24 md:py-32">
        <div class="max-w-7xl mx-auto text-center w-full">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-100 text-emerald-600 text-xs font-medium mb-8">
                <i class="fa-solid fa-star text-emerald-400"></i>
                <span>Versi 1.0 — Siap digunakan</span>
            </div>

            <h1 class="text-5xl md:text-7xl lg:text-8xl font-black leading-[1.1] text-slate-900 mb-6">
                Administrasi Nikah Desa, Kini Lebih Cerdas.
            </h1>

            <p class="text-xl md:text-2xl text-slate-600 mb-8 max-w-2xl mx-auto leading-relaxed">
                Kelola data warga, cetak formulir N1-N6, dan validasi hukum perwalian dalam satu aplikasi 100% Offline.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/registration/start" class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white font-bold rounded-2xl text-lg hover:bg-emerald-500 transition-all duration-300 shadow-lg shadow-emerald-400/20">
                    Mulai Sekarang
                    <i class="fa-solid fa-arrow-right text-lg ml-2"></i>
                </a>
                <a href="#features" class="inline-flex items-center px-6 py-3 border-2 border-emerald-400 text-emerald-600 font-medium rounded-2xl text-lg hover:border-emerald-500 hover:text-emerald-500 transition-all duration-300">
                    Pelajari Fitur
                    <i class="fa-solid fa-arrow-down text-lg ml-2"></i>
                </a>
            </div>
        </div>

        <!-- Mockup Laptop -->
        <div class="mt-16 md:mt-0 md:absolute right-0 md:w-80 md:h-64 md:translate-y-0 transform md:translate-y-0 md:rotate-0">
            <div class="relative w-full md:w-full md:h-full rounded-2xl overflow-hidden shadow-2xl md:shadow-lg" style="background: linear-gradient(135deg, #fff 0%, #f8fafc 100%);">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-50 via-white to-emerald-50"></div>
                <!-- Screen -->
                <div class="relative w-full h-full bg-white overflow-hidden rounded-2xl">
                    <div class="p-6">
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-slate-500 uppercase tracking-wider mb-2">Beranda</p>
                                <h2 class="text-xl font-bold text-slate-900">SILAPANDU</h2>
                                <p class="text-slate-500 text-sm">Selamat datang di dashboard administrasi nikah</p>
                            </div>
                            <div>
                                <p class="text-sm text-slate-500 uppercase tracking-wider mb-2">Pendaftaran</p>
                                <p class="text-slate-400 text-sm">3 registrasi hari ini</p>
                            </div>
                            <div>
                                <p class="text-sm text-slate-500 uppercase tracking-wider mb-2">Dokumen</p>
                                <p class="text-slate-400 text-sm">2 dokumen cetak hari ini</p>
                            </div>
                        </div>
                    </div>
                    <!-- Camera notch -->
                    <div class="absolute top-3 left-1/2 -translate-x-1/2 w-14 h-1 bg-slate-200 rounded-full"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Fitur Unggulan Section -->
    <section id="features" class="py-24 md:py-32 px-6 sm:px-12 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-100 text-emerald-600 text-xs font-medium mb-6">
                    <i class="fa-solid fa-lightbulb text-emerald-400"></i>
                    <span>Fitur Unggulan</span>
                </div>
                <h2 class="text-4xl md:text-5xl font-black text-slate-900 mb-4">Mengapa PILIH SILAPANDU?</h2>
                <p class="text-slate-600 text-lg max-w-2xl mx-auto leading-relaxed">Fitur lengkap untuk mengelola administrasi nikah desa dengan efisien.</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Fitur 1 -->
                <div class="group p-6 rounded-2xl bg-slate-50 hover:bg-white transition-all duration-300 shadow-sm hover:shadow-md border border-slate-100">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 flex items-center justify-center mb-4 group-hover:bg-emerald-200 transition-colors">
                        <i class="fa-solid fa-file-lines text-emerald-500 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-slate-900 mb-2">Smart Kop Surat</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">Sistem otomisikan pembuatan kendala kop surat berdasarkan data warga yang terdaftar.</p>
                </div>

                <!-- Fitur 2 -->
                <div class="group p-6 rounded-2xl bg-slate-50 hover:bg-white transition-all duration-300 shadow-sm hover:shadow-md border border-slate-100">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 flex items-center justify-center mb-4 group-hover:bg-emerald-200 transition-colors">
                        <i class="fa-solid fa-file-pdf text-emerald-500 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-slate-900 mb-2">Cetak PDF N1-N6</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">Cetak satu klik semua formulir pernikahan N1 hingga N6 dengan data yang sudah tervalidasi.</p>
                </div>

                <!-- Fitur 3 -->
                <div class="group p-6 rounded-2xl bg-slate-50 hover:bg-white transition-all duration-300 shadow-sm hover:shadow-md border border-slate-100">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 flex items-center justify-center mb-4 group-hover:bg-emerald-200 transition-colors">
                        <i class="fa-solid fa-handshake text-emerald-500 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-slate-900 mb-2">Logika Fikih Perwalian</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">Validasi hukum otomatis perwalian: wali hakim, wali nasab, dan kasus khusus ayah sambung/meninggal.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Perbandingan Section -->
    <section id="comparison" class="py-24 md:py-32 px-6 sm:px-12">
        <div class="max-w-7xl mx-auto">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Kiril: Cara Manual -->
                <div class="rounded-2xl bg-slate-100 p-8 border border-slate-200">
                    <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center mb-4">
                        <i class="fa-solid fa-xmark text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-3">Cara Manual</h3>
                    <ul class="space-y-3 text-slate-600 text-sm">
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-times text-rose-400"></i>
                            <span>❌ Ketik ulang data warga</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-times text-rose-400"></i>
                            <span>❌ Rawan typo NIK</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-times text-rose-400"></i>
                            <span>❌ Cetak N1-N6 manual</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-times text-rose-400"></i>
                            <span>❌ Rentan salah wali</span>
                        </li>
                    </ul>
                </div>

                <!-- Kanan: SILAPANDU -->
                <div class="rounded-2xl bg-emerald-50 border border-emerald-200 p-8">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center mb-4">
                        <i class="fa-solid fa-check text-emerald-500 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-emerald-800 mb-3">SILAPANDU</h3>
                    <ul class="space-y-3 text-emerald-700 text-sm">
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-check text-emerald-400"></i>
                            <span>✅ Autofill data via NIK</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-check text-emerald-400"></i>
                            <span>✅ Anti Typo</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-check text-emerald-400"></i>
                            <span>✅ Cetak N1-N6 sekali klik</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-check text-emerald-400"></i>
                            <span>✅ Logika Fikih otomatis</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Portfolio/Testimoni Section -->
    <section id="portfolio" class="py-24 md:py-32 px-6 sm:px-12 bg-slate-50">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-100 text-emerald-600 text-xs font-medium mb-6">
                    <i class="fa-solid fa-user-group text-emerald-400"></i>
                    <span>Testimoni</span>
                </div>
                <h2 class="text-4xl md:text-5xl font-black text-slate-900 mb-4">Dipercaya oleh Operator Desa</h2>
                <p class="text-slate-600 text-lg">Ribuan operator desa sudah menggunakan SILAPANDU untuk administrasi nikah harian.</p>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 justify-center">
                <div class="col-span-1 col-span-2 md:col-span-2 lg:col-span-3 bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-100 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-user-md text-emerald-500 text-xl"></i>
                    </div>
                    <p class="text-slate-500 text-sm leading-relaxed">"Sistem sangat memudahkan pelayanan kami. Data otomatis terisi via NIK, dan cetak N1-N6 langsung dalam hitungan detik."</p>
                    <p class="text-emerald-600 font-medium mt-2">Operator Desa — Kecamatan X</p>
                </div>

                <div class="col-span-1 col-span-2 md:col-span-2 lg:col-span-3 bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-100 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-handshake text-emerald-500 text-xl"></i>
                    </div>
                    <p class="text-slate-500 text-sm leading-relaxed">"Logika perwalian sudah benar sesuai hukum fikih. Tidak perlu lagi repot mencari wali secara manual."</p>
                    <p class="text-emerald-600 font-medium mt-2">Petugas KUA — Kabupaten Y</p>
                </div>

                <div class="col-span-1 col-span-2 md:col-span-2 lg:col-span-3 bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-100 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-chart-bar text-emerald-500 text-xl"></i>
                    </div>
                    <p class="text-slate-500 text-sm leading-relaxed">"Cetak PDF N1-N6 sekali klik menghemat waktu operasional kami hingga 80%.</p>
                    <p class="text-emerald-600 font-medium mt-2">Lurah Desa — Kecamatan Z</p>
                </div>

                <div class="col-span-1 col-span-2 md:col-span-2 lg:col-span-3 bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
                    <div class="w-16 h-16 rounded-2xl bg-emerald-100 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-building-text text-emerald-500 text-xl"></i>
                    </div>
                    <p class="text-slate-500 text-sm leading-relaxed">"Aplikasi off-line kami bisa digunakan tanpa internet pun. Sangat support area kami yang still terbatas."</p>
                    <p class="text-emerald-600 font-medium mt-2">Operator Desa — Kecamatan W</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-12 px-6 sm:px-12 bg-slate-950 text-slate-400 border-t border-slate-800">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-600 to-emerald-500 flex items-center justify-center text-white font-bold text-sm">
                        <i class="fa-solid fa-kaaba"></i>
                    </div>
                    <span class="font-black text-lg">SILAPANDU</span>
                </div>
                <p class="text-[11px] text-slate-500">© 2026 SILAPANDU. Semua hak cipta dilindungi.</p>
                <div class="flex gap-6 text-slate-500 text-sm">
                    <a href="#" class="hover:text-emerald-400 transition-colors">Fitur</a>
                    <a href="#" class="hover:text-emerald-400 transition-colors">Bantuan</a>
                    <a href="#" class="hover:text-emerald-400 transition-colors">Tentang</a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>