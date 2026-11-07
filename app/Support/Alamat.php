<?php

namespace App\Support;

class Alamat
{
    /**
     * Format alamat untuk dokumen PDF.
     *
     * Menampilkan: Nama Jalan (RT/RW), Desa, Kecamatan, Kab/Kota, dan Provinsi.
     * Segmen "Prov. ..." ditormalisasi, label "Desa "/"Kec. "/"Kab. "/"Prov."
     * dinormalisasi (mis. "Desa/Kel." -> "Desa", "Kabupaten" -> "Kab."),
     * dan Nama Jalan (mis. "Jl. ...") ikut ditampilkan bila tersedia.
     *
     * Contoh:
     * "Jl. Sabana RT 01 RW 02, Desa/Kel. Kadipaten, Kec. Kadipaten, Kabupaten Majalengka, Prov. Jawa Barat"
     *  -> "Jl. Sabana RT 01 RW 02, Desa Kadipaten, Kec. Kadipaten, Kab. Majalengka, Prov. Jawa Barat"
     */
    public static function pendek(?string $alamat): string
    {
        $alamat = trim((string) $alamat);

        if ($alamat === '') {
            return '-';
        }

        $segments = preg_split('/\s*,\s*/', $alamat);
        $hasil = [];
        $total = count($segments);

        foreach ($segments as $index => $segment) {
            $segment = trim($segment);

            if ($segment === '') {
                continue;
            }

            // Label dinormalisasi: "Desa/Kel. " -> "Desa ", "Kec. " -> "Kec. ", dst.
            $segment = preg_replace('/^(desa\/kel\.|desa\.|kel\.|kelurahan)\s+/i', 'Desa ', $segment);
            $segment = preg_replace('/^(kec\.|kecamatan)\s+/i', 'Kec. ', $segment);
            $segment = preg_replace('/^kabupaten\s+/i', 'Kab. ', $segment);
            $segment = preg_replace('/^provinsi\s+/i', 'Prov. ', $segment);

            // Segmen administrasi sebelum "Prov. ..." yang belum bernama label
            // dianggap Kabupaten/Kota (mis. format lama tanpa label "Kab.").
            if ($index > 0 && $index < $total - 1 && preg_match('/^Prov\.\s/i', $segments[$index + 1])) {
                $labelPrefix = '/^(Desa|Kec|Kab|Prov|RT|RW|Jl|Jln)\b/i';

                if (! preg_match($labelPrefix, $segment)) {
                    $segment = 'Kab. '.$segment;
                }
            }

            $hasil[] = $segment;
        }

        return $hasil ? implode(', ', $hasil) : '-';
    }
}
