<?php

namespace App\Models;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Seluruh pengaturan sebagai array key => value.
     *
     * @return array<string, string|null>
     */
    public static function allAsArray(): array
    {
        return static::query()->pluck('value', 'key')->all();
    }

    /**
     * Simpan beberapa pengaturan (insert atau perbarui berdasarkan key).
     *
     * @param  array<string, string|null>  $settings
     */
    public static function saveMany(array $settings): void
    {
        foreach ($settings as $key => $value) {
            static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }

    public static function lastBackupAt(): ?CarbonInterface
    {
        $value = static::query()->where('key', 'last_backup_at')->value('value');

        return filled($value) ? CarbonImmutable::parse($value) : null;
    }

    public static function daysSinceLastBackup(): ?int
    {
        $lastBackupAt = static::lastBackupAt();

        return $lastBackupAt?->startOfDay()->diffInDays(now()->startOfDay());
    }

    public static function backupNeedsAttention(): bool
    {
        $daysSinceLastBackup = static::daysSinceLastBackup();

        return $daysSinceLastBackup === null || $daysSinceLastBackup > 14;
    }

    /**
     * Profil desa untuk kop surat, nama pejabat, dan tanda tangan dokumen.
     * Nilai kosong dibiarkan null agar template menampilkan tanda "-".
     *
     * @return object{
     *     village_name: ?string,
     *     district_name: ?string,
     *     regency_name: ?string,
     *     office_address: ?string,
     *     village_head_name: ?string,
     *     village_head_nip: ?string,
     *     service_officer_name: ?string,
     *     service_officer_nip: ?string,
     *     kua_head_name: ?string,
     *     kua_head_nip: ?string
     * }
     */
    public static function villageProfile(): object
    {
        $settings = static::allAsArray();

        $get = static fn (string $key): ?string => trim((string) ($settings[$key] ?? '')) ?: null;

        return (object) [
            'village_name' => $get('nama_desa'),
            'district_name' => $get('kecamatan'),
            'regency_name' => $get('kabupaten'),
            'office_address' => $get('alamat_kantor'),
            'village_head_name' => $get('kepala_desa.nama'),
            'village_head_nip' => $get('kepala_desa.nip'),
            'service_officer_name' => $get('kasi_pelayanan.nama'),
            'service_officer_nip' => $get('kasi_pelayanan.nip'),
            'kua_head_name' => $get('kepala_kua.nama'),
            'kua_head_nip' => $get('kepala_kua.nip'),
        ];
    }
}
