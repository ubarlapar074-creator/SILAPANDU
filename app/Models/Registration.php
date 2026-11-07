<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Registration extends Model
{
    protected $fillable = [
        'registration_number',
        'nomor_surat',
        'registration_path',
        'service_type',
        'tanggal_akad',
        'jam_akad',
        'marriage_location_address',
        'province_id',
        'regency_id',
        'district_id',
        'village_id',
        'marriage_dowry_display',
        'step',
        'status',
    ];

    protected $casts = [
        'tanggal_akad' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getServiceLabelAttribute(): string
    {
        return match ($this->service_type) {
            'bedol' => 'Nikah Bedol (Luar Kantor)',
            'kantor' => 'Nikah Kantor (KUA)',
            'andon' => 'Andon / Numpang Nikah',
            default => 'Tidak diketahui',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        if ($this->step >= 4 || in_array($this->status, ['lengkap', 'valid', 'selesai'])) {
            return 'Lengkap';
        }

        return 'Belum Lengkap';
    }

    public function getStatusBadgeClassAttribute(): string
    {
        if ($this->step >= 4 || in_array($this->status, ['lengkap', 'valid', 'selesai'])) {
            return 'bg-emerald-50 text-emerald-700 border-emerald-200';
        }

        return 'bg-amber-50 text-amber-700 border-amber-200';
    }

    /**
     * Nomor surat dibekukan setelah pendaftaran difinalisasi (dikonfirmasi & dikunci),
     * agar surat yang sudah tercetak tidak berubah saat ada pendaftaran baru atau
     * data lain dihapus. Status "lengkap" belum berarti finalisasi.
     */
    public function nomorSuratTerkunci(): bool
    {
        return in_array($this->status, ['valid', 'selesai'], true);
    }

    /**
     * Draft yang belum berisi data sama sekali: tidak punya pihak (calon pria/wanita)
     * dan belum punya tanggal akad. Draft seperti ini hanya sampah dari klik
     * "Pendaftaran Baru" yang langsung ditinggalkan operator.
     */
    #[Scope]
    protected function emptyDrafts(Builder $query): void
    {
        $query->where('status', 'draft')
            ->whereDoesntHave('parties')
            ->whereNull('tanggal_akad');
    }

    public function parties(): HasMany
    {
        return $this->hasMany(RegistrationParty::class);
    }

    public function parents(): HasMany
    {
        return $this->hasMany(RegistrationParent::class);
    }

    public function marriageWalis(): HasMany
    {
        return $this->hasMany(MarriageWali::class);
    }
}
