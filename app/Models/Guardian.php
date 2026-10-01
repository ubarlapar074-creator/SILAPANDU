<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Guardian extends Model
{
    protected $fillable = ['registration_id', 'guardian_source', 'parent_id', 'relationship', 'sebab_wali', 'marital_status', 'name', 'nik', 'bin_binti', 'birth_place', 'birth_date', 'occupation', 'address', 'province_id', 'regency_id', 'district_id', 'village_id'];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(CatinParent::class, 'parent_id');
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
