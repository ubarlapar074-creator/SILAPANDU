Tugas: Menyempurnakan alur pendaftaran (UX) dengan menerapkan logika orang tua meninggal, autofill orang tua, membersihkan redundansi di Card 5, dan membuat fitur Halaman Review sebelum penyimpanan.

ATURAN KETAT:

JANGAN merusak UI Dashboard yang sudah ada (Dark Mode).

Fokus pada perbaikan View (Blade), Controller, Service, dan Form Request.

Gunakan Clean Code (penamaan variabel jelas, beri komentar).

INSTRUKSI LANGKAH DEMI LANGKAH:

1. Perbaikan UX Form Pria & Wanita (Termasuk Autofill Orang Tua)

Autofill Bapak & Ibu: Saat operator mengetik NIK di form Pria/Wanita, API checkNik akan mengembalikan data. Pastikan data nama_ayah dan nama_ibu dari API ini otomatis mengisi field Nama Bapak dan Nama Ibu di form (bisa di-edit manual jika perlu).

Tombol Salin Alamat: Tambahkan tombol kecil "Salin Alamat dari Calon" di bagian Data Bapak dan Ibu. Saat diklik (Alpine.js), field alamat orang tua otomatis terisi dengan alamat calon pengantin.

Logika Disable: Jika dropdown status_hidup (Bapak/Ibu) diubah menjadi meninggal atau tidak_diketahui, semua input (NIK, TTL, Pekerjaan, Alamat, dll) otomatis disabled (abu-abu). PENGECUALIAN: Input Nama tetap aktif dan wajib diisi.

Backend Validasi: Sesuaikan StorePriaRequest dan StoreWanitaRequest. Jika orang tua meninggal, field selain nama menjadi nullable.

2. Pembersihan Form Card 5 (Jadwal Akad & Mahar)

Buat file baru: form-akad.blade.php.

HAPUS input "Nama Pria" dan "Nama Wanita" dari form ini. Ganti dengan teks read-only: "Pernikahan antara: [Nama Pria] & [Nama Wanita]".

Form ini fokus pada input: Tanggal Akad, Jam Akad, Jenis Mahar, Nominal/Rincian Mahar, dan Dropdown Wilayah.

3. Halaman Review (Mencegah Typo)

Route & Controller: Buat route GET /registration/{registration}/review (name: registration.review).

UI Halaman Review: Buat file review.blade.php. Tampilkan ringkasan semua data (Pria, Wanita, Wali, Saksi, Akad) dalam bentuk read-only (seperti struk/formulir cetak).

Tombol Aksi:

Tombol "Simpan Pendaftaran" (mengubah status registrasi menjadi valid atau selesai).

Tombol "Edit" di samping setiap bagian data.

4. Logika Tombol Edit di Halaman Review

Saat operator klik "Edit Data Pria" di Review, arahkan ke route('registration.form.pria', $registration->id).

Pastikan form Pria/Wanita/Akad memuat data yang sudah ada dari database (sehingga saat diedit, field sudah terisi dan tinggal diubah yang typo).

5. Update Dashboard (5 Card)

Pastikan tombol di Card 5 mengarah ke form Akad.

Tambahkan tombol "Review & Simpan Final" di bawah Dashboard. Mengarah ke halaman Review.

LAPORAN AKHIR:
Pastikan alur Isi Form -> Dashboard -> Review -> Edit/Selesai berjalan tanpa error. Konfirmasi file apa saja yang dibuat/diubah.