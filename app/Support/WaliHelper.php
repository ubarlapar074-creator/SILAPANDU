<?php

namespace App\Support;

class WaliHelper
{
    public const RELATIONSHIPS = ['AYAH_KANDUNG', 'KAKEK', 'SAUDARA_KANDUNG_LAKI', 'PAMAN', 'WALI_HAKIM'];

    public static function label(string $relationship): string
    {
        return match ($relationship) {
            'AYAH_KANDUNG' => 'Ayah Kandung',
            'KAKEK' => 'Kakek (dari Ayah)',
            'SAUDARA_KANDUNG_LAKI' => 'Saudara Kandung Laki-laki',
            'PAMAN' => 'Paman (pihak Ayah)',
            'WALI_HAKIM' => 'Wali Hakim',
            default => $relationship,
        };
    }

    public static function defaultSebab(string $relationship, ?string $ayahStatus = null): string
    {
        $meninggal = strtoupper(trim((string) $ayahStatus)) === 'MENINGGAL';

        return match ($relationship) {
            'AYAH_KANDUNG' => 'Wali nasab Ayah Kandung',
            'KAKEK' => $meninggal
                ? 'Ayah kandung telah meninggal dunia, perwalian beralih kepada Kakek dari garis ayah sebagai wali nasab sesuai urutan ashobah'
                : 'Ayah kandung berhalangan, perwalian digantikan oleh Kakek dari garis ayah sebagai wali nasab',
            'SAUDARA_KANDUNG_LAKI' => $meninggal
                ? 'Ayah kandung telah meninggal dunia, perwalian beralih kepada Saudara Kandung Laki-laki sebagai wali nasab'
                : 'Ayah kandung berhalangan, perwalian digantikan oleh Saudara Kandung Laki-laki sebagai wali nasab',
            'PAMAN' => $meninggal
                ? 'Ayah kandung telah meninggal dunia, perwalian beralih kepada Paman dari pihak ayah (\u2018am) sebagai wali nasab'
                : 'Ayah kandung berhalangan, perwalian digantikan oleh Paman dari pihak ayah (\u2018am) sebagai wali nasab',
            'WALI_HAKIM' => $meninggal
                ? 'Tidak terdapat wali nasab yang memenuhi syarat (ayah kandung meninggal dan wali ashobah tidak ada/berhalangan), perwalian beralih kepada Wali Hakim KUA'
                : 'Tidak terdapat wali nasab yang memenuhi syarat atau wali adlal/berhalangan, perwalian beralih kepada Wali Hakim KUA',
            default => 'Wali nikah sesuai ketentuan',
        };
    }

    public static function resolveSebab(?string $stored, string $relationship, ?string $ayahStatus = null): string
    {
        $trimmed = trim((string) $stored);
        if ($trimmed !== '') return $trimmed;
        return self::defaultSebab($relationship, $ayahStatus);
    }
}
