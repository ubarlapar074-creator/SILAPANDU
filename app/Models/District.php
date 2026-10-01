<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class District extends Model
{
    protected $table = 'districts';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'regency_id',
        'name',
    ];

    /**
     * Relasi ke Regency (Kabupaten/Kota)
     */
    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class, 'regency_id', 'id');
    }

    /**
     * Relasi ke Village (Desa/Kelurahan)
     */
    public function villages(): HasMany
    {
        return $this->hasMany(Village::class, 'district_id', 'id');
    }
}
