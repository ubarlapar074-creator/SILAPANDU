<?php

namespace App\Services;

use App\Models\DocumentNumberSetting;
use App\Models\RegistrationDocument;
use App\Models\VillageProfile;

class DocumentNumberService
{
    /**
     * Bangun nomor surat berikutnya untuk tahun/bulan tertentu.
     * Format: 001/470/2001-001/IX/2026 — seq reset tiap tahun, hindari duplikat dengan cek DB.
     * Dipakai saat simpan N1–N6/F1 dan saat preview di Penomoran Surat.
     */
    public function suggestedGlobal(?int $year = null, ?int $month = null): string
    {
        $year = $year ?? (int) now()->format('Y');
        $month = $month ?? (int) now()->format('n');
        // SSOT penomoran: ambil dari DocumentNumberSetting (diatur di /settings/document-numbers)
        $settings = DocumentNumberSetting::query()->first();
        $klasifikasi = $settings?->kode_klasifikasi_arsip ?? $settings?->prefix_n1 ?? '470';
        $desaKecRaw = trim((string) ($settings?->kode_desa_kecamatan ?? ''));
        $desaKec = $desaKecRaw !== '' ? $desaKecRaw : null;
        $roman = $this->romanMonth($month);
        $seq = $this->nextSeq($year);
        $attempt = 0;
        while ($attempt < 10) {
            $candidate = $this->formatGlobal($seq, $klasifikasi, $desaKec, $roman, $year);
            if (!RegistrationDocument::where('document_number', $candidate)->exists()) return $candidate;
            $seq++; $attempt++;
        }
        return $this->formatGlobal($seq, $klasifikasi, $desaKec, $roman, $year);
    }

    public function suggested(string $type, ?int $year = null, ?int $month = null): string
    {
        return $this->suggestedGlobal($year, $month);
    }

    /** Format nomor global — konsisten dipakai di preview & simpan. */
    public function formatGlobal(int $seq, string $klasifikasi, ?string $desaKec, string $roman, int $year): string
    {
        if ($desaKec !== null && trim($desaKec) !== '') {
            return sprintf('%03d/%s/%s/%s/%s', $seq, $klasifikasi, trim($desaKec), $roman, $year);
        }
        return sprintf('%03d/%s/%s/%s', $seq, $klasifikasi, $roman, $year);
    }

    /** Hitung urutan berikutnya untuk tahun — max seq + 1, fallback count + 1. */
    public function nextSeq(int $year): int
    {
        $count = RegistrationDocument::whereYear('created_at', $year)->count();
        if ($count === 0) return 1;
        $max = 0;
        $numbers = RegistrationDocument::whereYear('created_at', $year)->pluck('document_number');
        foreach ($numbers as $num) {
            if (preg_match('/^0*(\d+)\//', $num, $m)) {
                $v = (int) $m[1];
                if ($v > $max) $max = $v;
            }
        }
        return $max > 0 ? $max + 1 : $count + 1;
    }

    public function prefixFor(string $type, ?DocumentNumberSetting $settings): string
    {
        return $settings?->kode_klasifikasi_arsip ?? $settings?->prefix_n1 ?? '470';
    }

    /**
     * SSOT penandatangan — ambil dari VillageProfile (diatur sekali di Profil Desa & Penandatangan).
     * Dipakai sebagai default penandatangan N6 & dokumen lain bila belum diisi manual N6.
     */
    public function defaults(?VillageProfile $profile = null): array
    {
        $profile = $profile ?? VillageProfile::query()->first();
        return [
            'signed_by' => $profile?->village_head_name ?? $profile?->service_officer_name,
            'position' => $profile?->village_head_name ? 'Kepala Desa' : ($profile?->service_officer_name ? 'Kasi Pelayanan' : null),
            'profile' => $profile,
        ];
    }

    private function romanMonth(int $month): string
    {
        return ['', 'I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'][$month] ?? (string) $month;
    }
}
