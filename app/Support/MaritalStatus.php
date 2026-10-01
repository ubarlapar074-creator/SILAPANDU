<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;

class MaritalStatus
{
    public const BELUM_KAWIN = 'BELUM_KAWIN';
    public const KAWIN = 'KAWIN';
    public const CERAI_HIDUP = 'CERAI_HIDUP';
    public const CERAI_MATI = 'CERAI_MATI';

    public const UNKNOWN = null;

    private const MAP = [
        'BELUM KAWIN' => self::BELUM_KAWIN,
        'BELUM_KAWIN' => self::BELUM_KAWIN,
        'KAWIN' => self::KAWIN,
        'KAWIN TERCATAT' => self::KAWIN,
        'KAWIN_TERCATAT' => self::KAWIN,
        'CERAI HIDUP' => self::CERAI_HIDUP,
        'CERAI_HIDUP' => self::CERAI_HIDUP,
        'CERAI TERCATAT' => self::CERAI_HIDUP,
        'CERAI_TERCATAT' => self::CERAI_HIDUP,
        'CERAI MATI' => self::CERAI_MATI,
        'CERAI_MATI' => self::CERAI_MATI,
    ];

    public static function canonicals(): array
    {
        return [self::BELUM_KAWIN, self::KAWIN, self::CERAI_HIDUP, self::CERAI_MATI];
    }

    public static function validRaws(): array
    {
        return array_keys(self::MAP);
    }

    public static function normalize(?string $raw): ?string
    {
        if ($raw === null || trim($raw) === '') {
            return null;
        }
        $key = strtoupper(trim(preg_replace('/\s+/', ' ', str_replace(['-', '_'], ' ', $raw))));
        $key = preg_replace('/\s+/', ' ', $key);
        $keyUnderscore = str_replace(' ', '_', $key);
        $keySpace = str_replace('_', ' ', $key);
        foreach ([str_replace(' ', '_', $keySpace), $keySpace, $keyUnderscore, $key] as $candidate) {
            if (isset(self::MAP[$candidate])) {
                return self::MAP[$candidate];
            }
        }
        $normalized = self::MAP[$key] ?? self::MAP[$keySpace] ?? self::MAP[$keyUnderscore] ?? null;
        if ($normalized !== null) {
            return $normalized;
        }
        Log::warning('MaritalStatus unknown raw value', ['raw' => $raw, 'normalized_key' => $key]);
        return null;
    }

    public static function isValidRaw(?string $raw): bool
    {
        if ($raw === null) {
            return false;
        }
        $key = strtoupper(trim(preg_replace('/\s+/', ' ', str_replace(['-', '_'], ' ', $raw))));
        $key = preg_replace('/\s+/', ' ', $key);
        return isset(self::MAP[$key]) || isset(self::MAP[str_replace(' ', '_', $key)]) || isset(self::MAP[str_replace('_', ' ', $key)]);
    }

    public static function labelForCanonical(?string $canonical, string $role = 'PRIA'): string
    {
        return match ($canonical) {
            self::BELUM_KAWIN => 'Belum Kawin',
            self::KAWIN => 'Kawin',
            self::CERAI_HIDUP => strtoupper($role) === 'PRIA' ? 'Duda (Cerai Hidup)' : 'Janda (Cerai Hidup)',
            self::CERAI_MATI => strtoupper($role) === 'PRIA' ? 'Duda (Cerai Mati)' : 'Janda (Cerai Mati)',
            default => 'Status Tidak Dikenal',
        };
    }

    public static function labelForRaw(?string $raw, string $role = 'PRIA'): string
    {
        if ($raw === null || trim($raw) === '') {
            return '-';
        }
        $key = strtoupper(trim(preg_replace('/\s+/', ' ', str_replace(['-', '_'], ' ', $raw))));
        $key = preg_replace('/\s+/', ' ', $key);
        return match ($key) {
            'BELUM KAWIN', 'BELUM_KAWIN' => 'Belum Kawin',
            'KAWIN' => 'Kawin',
            'KAWIN TERCATAT', 'KAWIN_TERCATAT' => 'Kawin',
            'CERAI HIDUP', 'CERAI_HIDUP' => strtoupper($role) === 'PRIA' ? 'Duda (Cerai Hidup)' : 'Janda (Cerai Hidup)',
            'CERAI TERCATAT', 'CERAI_TERCATAT' => strtoupper($role) === 'PRIA' ? 'Duda (Cerai Hidup)' : 'Janda (Cerai Hidup)',
            'CERAI MATI', 'CERAI_MATI' => strtoupper($role) === 'PRIA' ? 'Duda (Cerai Mati)' : 'Janda (Cerai Mati)',
            default => trim($raw),
        };
    }

    public static function labelForStatement(?string $raw, string $role = 'PRIA'): string
    {
        $c = self::normalize($raw);
        if ($c === self::BELUM_KAWIN) return strtoupper($role) === 'PRIA' ? 'Jejaka' : 'Perawan';
        if ($c !== null) return self::labelForCanonical($c, $role);
        if ($raw === null || trim($raw) === '') return '-';
        return trim($raw);
    }

    public static function dropdownOptions(): array
    {
        return [
            'BELUM_KAWIN' => 'Belum Kawin',
            'CERAI_HIDUP' => 'Cerai Hidup',
            'CERAI_MATI' => 'Cerai Mati',
        ];
    }
}
