<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Regency extends Model
{
    protected $table = 'regencies';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'province_id',
        'name',
    ];

    /**
     * Relasi ke Province (Provinsi)
     */
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }

    /**
     * Relasi ke District (Kecamatan)
     */
    public function districts(): HasMany
    {
        return $this->hasMany(District::class, 'regency_id', 'id');
    }
}
