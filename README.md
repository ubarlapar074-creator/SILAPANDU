# SILAPANDU

**Sistem Layanan Administrasi Pendaftaran Nikah**

SILAPANDU adalah aplikasi berbasis web untuk membantu pengelolaan administrasi pelayanan pendaftaran nikah secara terstruktur, mulai dari pencatatan data pendaftaran, calon pengantin, orang tua, wali, saksi, kelengkapan dokumen, hingga pengelolaan dokumen administrasi.

Aplikasi ini dirancang untuk digunakan pada lingkungan pelayanan yang membutuhkan **akses lokal dan dapat tetap digunakan tanpa koneksi internet**.

---

## Fitur

SILAPANDU menyediakan beberapa fungsi utama untuk mendukung proses pelayanan administrasi pendaftaran nikah:

* Dashboard pelayanan
* Pendaftaran nikah
* Pengelolaan data calon pengantin
* Pengelolaan data orang tua calon pengantin
* Pengelolaan data wali
* Pengelolaan data saksi
* Pengelolaan data wilayah administratif

  * Provinsi
  * Kabupaten/Kota
  * Kecamatan
  * Desa/Kelurahan
* Validasi data pendaftaran
* Kelengkapan dokumen administrasi
* Pengelolaan arsip dokumen
* Administrasi dokumen terkait proses N1–N6
* Nomor pendaftaran
* Status pendaftaran
* Tanggal pelaksanaan pernikahan
* Backup database
* Penyimpanan data secara lokal
* Antarmuka yang dapat digunakan melalui jaringan lokal

> Fitur yang tersedia dapat berkembang sesuai kebutuhan pelayanan dan pengembangan versi berikutnya.

---

## Prinsip Penggunaan

SILAPANDU dikembangkan dengan beberapa prinsip utama:

### Offline-first

Aplikasi dirancang agar proses pelayanan utama dapat dilakukan pada lingkungan lokal tanpa ketergantungan terhadap koneksi internet.

### Data lokal

Data aplikasi disimpan pada database lokal sehingga operasional pelayanan tidak bergantung pada server internet.

### Sederhana

Antarmuka dan alur aplikasi diarahkan agar mudah digunakan oleh operator pelayanan tanpa membutuhkan proses teknis yang rumit.

### Terstruktur

Data pendaftaran, calon pengantin, keluarga, wali, saksi, wilayah, serta dokumen disimpan dalam struktur yang saling berhubungan sehingga lebih mudah dikelola dan ditelusuri.

---

## Teknologi

SILAPANDU dibangun menggunakan:

* **Laravel 13**
* **PHP 8.3+**
* **SQLite**
* **Blade**
* **Tailwind CSS**
* **Alpine.js**
* **Vite**
* **PHPUnit**
* **Playwright**

Dependency PHP dikelola menggunakan Composer, sedangkan dependency frontend dikelola menggunakan NPM.

---

## Persyaratan Sistem

Untuk menjalankan SILAPANDU secara lokal, minimal diperlukan:

* PHP 8.3 atau lebih baru
* Composer
* Node.js dan NPM
* SQLite
* Web browser modern

Ekstensi PHP yang diperlukan mengikuti kebutuhan Laravel dan dependency yang digunakan oleh aplikasi.

Untuk penggunaan pada jaringan lokal, komputer yang menjalankan SILAPANDU dapat digunakan sebagai server lokal yang diakses oleh perangkat lain melalui jaringan yang sama.

---

## Instalasi

### 1. Clone repository

```bash
git clone <URL-REPOSITORY>
cd silapandu
```

### 2. Install dependency PHP

```bash
composer install
```

### 3. Install dependency frontend

```bash
npm install
```

### 4. Buat file environment

Salin `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Pada Windows:

```powershell
copy .env.example .env
```

### 5. Generate application key

```bash
php artisan key:generate
```

---

## Konfigurasi Database

SILAPANDU menggunakan **SQLite** sebagai database lokal.

Pastikan konfigurasi `.env` menggunakan:

```env
DB_CONNECTION=sqlite
```

Buat file database:

```bash
touch database/database.sqlite
```

Pada Windows PowerShell:

```powershell
New-Item database/database.sqlite -ItemType File
```

Kemudian jalankan migration:

```bash
php artisan migrate
```

Jika project menyediakan seeder yang diperlukan:

```bash
php artisan db:seed
```

Atau:

```bash
php artisan migrate --seed
```

---

## Menjalankan Aplikasi

Untuk menjalankan server Laravel:

```bash
php artisan serve
```

Secara default aplikasi dapat diakses melalui:

```text
http://127.0.0.1:8000
```

Kemudian buka alamat tersebut melalui browser.

---

## Penggunaan pada Jaringan Lokal

SILAPANDU dapat dijalankan pada komputer pelayanan dan diakses oleh perangkat lain melalui jaringan lokal.

Jalankan Laravel dengan host jaringan:

```bash
php artisan serve --host=0.0.0.0
```

Kemudian perangkat lain yang berada pada jaringan yang sama dapat mengakses alamat IP komputer server, misalnya:

```text
http://192.168.1.100:8000
```

Alamat IP tersebut hanya contoh. Gunakan alamat IP lokal komputer yang menjalankan SILAPANDU.

> Pastikan firewall dan konfigurasi jaringan lokal mengizinkan koneksi ke port yang digunakan.

---

## Mode Offline

SILAPANDU tidak dirancang dengan asumsi bahwa koneksi internet selalu tersedia.

Operasional utama aplikasi dapat dijalankan pada lingkungan lokal menggunakan:

```text
Browser
   │
   ▼
SILAPANDU / Laravel
   │
   ▼
SQLite Lokal
```

Dengan pendekatan tersebut, proses pelayanan dapat tetap berjalan ketika koneksi internet tidak tersedia.

Koneksi internet hanya diperlukan apabila terdapat kebutuhan eksternal yang memang bergantung pada layanan di luar aplikasi lokal.

---

## Struktur Modul

Struktur aplikasi mengikuti pola Laravel dengan beberapa komponen utama:

```text
app/
├── Actions/
├── Console/
├── Http/
│   ├── Controllers/
│   └── Requests/
├── Models/
├── Services/
└── Support/

database/
├── factories/
├── migrations/
└── seeders/

resources/
├── css/
├── js/
└── views/

routes/
└── web.php

tests/
├── Feature/
├── Unit/
└── e2e/
```

Struktur tersebut dapat berkembang seiring kebutuhan aplikasi.

---

## Data Pendaftaran

Data pendaftaran mencakup informasi yang diperlukan dalam proses administrasi, antara lain:

* Identitas pendaftaran
* Jalur/status pendaftaran
* Tanggal pernikahan
* Data calon pengantin pria
* Data calon pengantin wanita
* Data orang tua
* Data wali
* Data saksi
* Data wilayah
* Kelengkapan dokumen
* Arsip dokumen
* Status proses pelayanan

Relasi antar data dikelola melalui database Laravel sehingga informasi tidak perlu dicatat secara terpisah-pisah.

---

## Dokumen Administrasi

SILAPANDU menyediakan struktur pengelolaan dokumen yang berkaitan dengan proses administrasi pendaftaran nikah.

Dokumen dapat dikelompokkan berdasarkan kebutuhan pelayanan dan status kelengkapannya.

Data arsip dan dokumen yang bersifat pribadi **tidak disertakan dalam repository GitHub**.

---

## Backup Database

SILAPANDU memiliki dukungan pengelolaan backup database untuk membantu menjaga keamanan data aplikasi.

Backup database yang berisi data pelayanan **harus disimpan di lingkungan lokal yang aman** dan tidak boleh dimasukkan ke repository publik.

File database SQLite lokal juga tidak menjadi bagian dari repository.

---

## Keamanan Data

SILAPANDU dapat menangani data administrasi yang bersifat pribadi.

Karena itu:

* Jangan memasukkan file `.env` ke repository.
* Jangan memasukkan database SQLite berisi data pelayanan ke repository.
* Jangan memasukkan backup database ke repository.
* Jangan memasukkan dokumen pribadi warga ke repository.
* Jangan memasukkan log aplikasi yang mengandung data sensitif ke repository.
* Gunakan `.env.example` hanya sebagai template konfigurasi.
* Simpan backup pada media penyimpanan yang memiliki akses terbatas.

Repository source code dan data operasional harus diperlakukan sebagai dua hal yang berbeda.

---

## Testing

Untuk menjalankan test Laravel:

```bash
php artisan test
```

Untuk menjalankan test frontend/E2E apabila environment Playwright telah tersedia:

```bash
npx playwright test
```

Sebelum melakukan deployment, disarankan menjalankan test dan pemeriksaan aplikasi secara keseluruhan.

---

## Pemeriksaan Aplikasi

Beberapa perintah Laravel yang berguna:

```bash
php artisan about
```

Melihat daftar route:

```bash
php artisan route:list
```

Memeriksa status migration:

```bash
php artisan migrate:status
```

Membersihkan cache aplikasi jika diperlukan:

```bash
php artisan optimize:clear
```

---

## Pengembangan

Untuk development dengan Vite:

```bash
npm run dev
```

Untuk membuat asset production:

```bash
npm run build
```

Kemudian jalankan aplikasi Laravel:

```bash
php artisan serve
```

---

## Deployment

SILAPANDU diprioritaskan untuk penggunaan pada lingkungan lokal atau jaringan internal.

Contoh arsitektur sederhana:

```text
                Jaringan Lokal
                      │
          ┌───────────┴───────────┐
          │                       │
     Komputer Operator       Perangkat lain
          │                       │
          └───────────┬───────────┘
                      │
                SILAPANDU
                      │
                 SQLite Lokal
```

Untuk lingkungan pelayanan, komputer server lokal sebaiknya:

* memiliki backup rutin;
* menggunakan akun sistem yang aman;
* memiliki akses jaringan yang terbatas;
* menggunakan browser yang diperbarui;
* memiliki media backup terpisah;
* tidak mengekspos database atau direktori aplikasi secara langsung ke internet.

---

## Status Project

**Status:** Aktif dalam pengembangan.

SILAPANDU dikembangkan secara bertahap berdasarkan kebutuhan operasional pelayanan administrasi pendaftaran nikah.

Versi saat ini berfokus pada fondasi pengelolaan data pendaftaran dan administrasi pelayanan.

---

## Kontribusi

Perubahan terhadap SILAPANDU sebaiknya mempertimbangkan:

1. kebutuhan operasional pelayanan;
2. keamanan data;
3. kemampuan penggunaan secara offline;
4. integritas data;
5. kompatibilitas dengan struktur database yang sudah berjalan.

Untuk perubahan besar, buat branch terpisah dan dokumentasikan perubahan yang dilakukan.

---

## Lisensi

Lisensi project akan ditentukan sesuai kebutuhan pemilik dan pengelola SILAPANDU.

Jika project menggunakan lisensi tertentu, informasi lisensi harus dicantumkan secara resmi pada repository.

---

## Catatan

SILAPANDU menangani data administrasi yang dapat bersifat sensitif. Repository source code tidak boleh digunakan sebagai tempat penyimpanan database operasional, backup database, dokumen warga, atau data pelayanan nyata.

Gunakan data dummy/anonymized untuk kebutuhan development, testing, dokumentasi, dan demonstrasi.

---

**SILAPANDU**
*Sistem Layanan Administrasi Pendaftaran Nikah*
