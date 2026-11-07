<?php

namespace App\Support;

class Nama
{
    /**
     * Status hidup orang dianggap meninggal ('meninggal' / 'wafat').
     */
    public static function meninggal(?string $statusHidup): bool
    {
        return in_array(strtolower(trim((string) $statusHidup)), ['meninggal', 'wafat'], true);
    }

    /**
     * Gelar kematian: "ALM" (pria) / "ALMH" (wanita).
     */
    public static function gelar(bool $perempuan): string
    {
        return $perempuan ? 'ALMH' : 'ALM';
    }

    /**
     * Nama dengan gelar kematian sesuai format dokumen:
     * "Joko ( ALM )" untuk pria, "Eris ( ALMH )" untuk wanita.
     *
     * Mengembalikan nama apa adanya (tanpa gelar) bila status hidup bukan
     * meninggal. Gelar lama yang mungkin sudah tertulis ("Alm. Joko",
     * "Joko (Alm.)") dilepas terlebih dahulu agar tidak menumpuk.
     */
    public static function denganGelar(?string $nama, ?string $statusHidup = null, ?string $jenisKelamin = null): string
    {
        $nama = trim((string) $nama);

        if ($nama === '' || $nama === '-') {
            return $nama === '-' ? '-' : '';
        }

        if ($statusHidup === null || ! self::meninggal($statusHidup)) {
            return $nama;
        }

        // Jenis kelamin "P"/"Perempuan" -> ALMH, selain itu ALM.
        $jenisKelamin = strtoupper(substr(trim((string) $jenisKelamin), 0, 1));
        $gelar = self::gelar($jenisKelamin === 'P');

        $nama = preg_replace('/^\s*Alm(h)?\.?\s+/iu', '', $nama);
        $nama = preg_replace('/\s*\(\s*Alm(h)?\.?\s*\)\s*$/iu', '', $nama);

        return trim((string) $nama)." ( {$gelar} )";
    }
}
