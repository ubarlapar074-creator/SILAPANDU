<?php

namespace App\Support;

class AddressHelper
{
    /**
     * Gabungkan alamat jalan dengan RT/RW opsional.
     * Dipakai saat simpan pendaftaran — frontend kirim rt/rw terpisah.
     * Contoh: "Jl. Mawar" + "01" + "02" => "Jl. Mawar RT1/RW2"
     */
    public static function mergeWithRtRw(?string $address, ?string $rt, ?string $rw): ?string
    {
        if ($address === null) return null;
        $result = trim($address);
        if ($rt !== null && $rt !== '') $result .= ' RT' . (ltrim($rt, '0') ?: $rt);
        if ($rw !== null && $rw !== '') $result .= '/RW' . (ltrim($rw, '0') ?: $rw);
        return $result;
    }

    /**
     * Terapkan merge RT/RW ke array validated yang datang dari request.
     * Menangani: pria, wanita, 4 orang tua, dan witnesses.
     */
    public static function normalizeValidatedAddresses(array &$validated): void
    {
        $pairs = [
            'pria_address' => ['pria_address_rt', 'pria_address_rw'],
            'wanita_address' => ['wanita_address_rt', 'wanita_address_rw'],
            'pria_father_address' => ['pria_father_address_rt', 'pria_father_address_rw'],
            'pria_mother_address' => ['pria_mother_address_rt', 'pria_mother_address_rw'],
            'wanita_father_address' => ['wanita_father_address_rt', 'wanita_father_address_rw'],
            'wanita_mother_address' => ['wanita_mother_address_rt', 'wanita_mother_address_rw'],
        ];
        foreach ($pairs as $addrKey => [$rtKey, $rwKey]) {
            if (array_key_exists($addrKey, $validated)) {
                $validated[$addrKey] = self::mergeWithRtRw($validated[$addrKey], $validated[$rtKey] ?? null, $validated[$rwKey] ?? null);
            }
        }
        if (isset($validated['witnesses']) && is_array($validated['witnesses'])) {
            foreach ($validated['witnesses'] as $i => $w) {
                if (isset($w['address'])) $validated['witnesses'][$i]['address'] = self::mergeWithRtRw($w['address'], $w['address_rt'] ?? null, $w['address_rw'] ?? null);
            }
        }
    }

    /**
     * Bersihkan input NIK: hanya digit 0-9, kosong jadi null.
     * Dipakai sebelum validasi agar "3201-0101..." tetap lolos digits:16.
     * Handle spasi, dash, newline, fullwidth (０-９), invisible unicode.
     */
    public static function sanitizeNikInputs(\Illuminate\Http\Request $request): void
    {
        $keys = ['pria_nik', 'wanita_nik', 'pria_father_nik', 'pria_mother_nik', 'wanita_father_nik', 'wanita_mother_nik', 'guardian_nik'];
        foreach ($keys as $key) {
            if (!$request->exists($key)) continue;
            $raw = $request->input($key);
            if ($raw === null || $raw === '') continue;
            $digits = self::normalizeNikValue($raw);
            $request->merge([$key => $digits === '' ? null : $digits]);
        }
        $witnesses = $request->input('witnesses');
        if (!is_array($witnesses)) return;
        $changed = false;
        foreach ($witnesses as $i => $w) {
            if (!is_array($w) || !array_key_exists('nik', $w)) continue;
            $raw = $w['nik'];
            if ($raw === null || $raw === '') continue;
            $digits = self::normalizeNikValue($raw);
            $witnesses[$i]['nik'] = $digits === '' ? null : $digits;
            $changed = true;
        }
        if ($changed) $request->merge(['witnesses' => $witnesses]);
    }

    private static function normalizeNikValue(mixed $raw): string
    {
        $str = trim((string) $raw);
        $str = strtr($str, ['０'=>'0','１'=>'1','２'=>'2','３'=>'3','４'=>'4','５'=>'5','６'=>'6','７'=>'7','８'=>'8','９'=>'9']);
        $str = preg_replace('/[^\d]/u', '', $str) ?? '';
        return $str;
    }
}
