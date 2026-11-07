<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarriageWali extends Model
{
    protected $table = 'marriage_walis';

    protected $fillable = [
        'registration_id',
        'jenis_wali',
        'hubungan',
        'warga_id',
        'nama_wali',
        'nik_wali',
        'tempat_lahir',
        'tgl_lahir',
        'pekerjaan',
        'agama',
        'pendidikan',
        'no_hp',
        'alamat',
        'rt',
        'rw',
        'tgl_meninggal',
        'tempat_meninggal',
        'is_pengecualian_wali',
        'alasan_wali_hakim',
    ];

    protected $casts = [
        'tgl_lahir' => 'date',
        'tgl_meninggal' => 'date',
        'is_pengecualian_wali' => 'boolean',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function warga(): BelongsTo
    {
        return $this->belongsTo(Warga::class);
    }

    public function getNameAttribute(): ?string
    {
        return $this->nama_wali ?? $this->warga?->nama_lengkap;
    }

    public function getNikAttribute(): ?string
    {
        return $this->nik_wali ?? $this->warga?->nik;
    }

    public function getBirthPlaceAttribute(): ?string
    {
        return $this->tempat_lahir ?? $this->warga?->tempat_lahir;
    }

    public function getBirthDateAttribute(): ?\DateTimeInterface
    {
        return $this->tgl_lahir ?? $this->warga?->tgl_lahir;
    }

    public function getOccupationAttribute(): ?string
    {
        return $this->pekerjaan ?? $this->warga?->pekerjaan;
    }

    public function getAddressAttribute(): ?string
    {
        return $this->alamat ?? $this->warga?->alamat;
    }

    public function getRelationshipAttribute(): ?string
    {
        return $this->hubungan;
    }
}
