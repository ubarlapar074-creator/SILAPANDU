<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Province extends Model
{
    protected $table = 'provinces';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'id',
        'name',
    ];

    /**
     * Relasi ke Regency (Kabupaten/Kota)
     */
    public function regencies(): HasMany
    {
        return $this->hasMany(Regency::class, 'province_id', 'id');
    }
}
