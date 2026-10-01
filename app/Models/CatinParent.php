<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatinParent extends Model
{
    protected $table = 'parents';

    protected $fillable = ['catin_id', 'role', 'status', 'name', 'bin_binti', 'nik', 'birth_place', 'birth_date', 'address', 'education', 'occupation', 'marital_status', 'phone', 'email', 'province_id', 'regency_id', 'district_id', 'village_id'];

    protected $casts = [
        'birth_date' => 'date',
    ];

    protected $appends = [];

    public function getIsDeceasedAttribute(): bool
    {
        $s = strtoupper(trim((string) ($this->attributes['status'] ?? $this->status ?? '')));
        return $s === 'MENINGGAL' || $s === 'WAFAT';
    }

    private function hasAlmMarker(string $name): bool
    {
        if (preg_match('/\(Almh?\)/i', $name)) return true;
        if (preg_match('/^\s*Almh?\.?\s+/i', $name)) return true;
        return false;
    }

    public function getDisplayNameAttribute(): string
    {
        $name = trim((string) ($this->attributes['name'] ?? $this->name ?? ''));
        if ($name === '') return '-';
        if ($this->hasAlmMarker($name)) return $name;
        if ($this->is_deceased) {
            $suffix = $this->role === 'IBU' ? ' (Almh)' : ' (Alm)';
            return $name . $suffix;
        }
        return $name;
    }

    public function getFormattedNameAttribute(): string
    {
        $raw = trim((string) ($this->attributes['name'] ?? $this->name ?? ''));
        if ($raw === '') return '-';
        $bin = trim((string) ($this->attributes['bin_binti'] ?? $this->bin_binti ?? ''));
        $base = $raw;
        if ($bin !== '') $base .= ($this->role === 'AYAH' ? ' Bin ' : ' Binti ') . $bin;
        if (!$this->hasAlmMarker($raw) && !$this->hasAlmMarker($base) && $this->is_deceased) {
            $suffix = $this->role === 'IBU' ? ' (Almh)' : ' (Alm)';
            $base .= $suffix;
        }
        return $base;
    }

    public function catin(): BelongsTo
    {
        return $this->belongsTo(Catin::class);
    }

    // --- Relasi Wilayah ---

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }
}
