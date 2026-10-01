<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Witness extends Model
{
    protected $fillable = ['registration_id', 'name', 'gender', 'bin_binti', 'nik', 'birth_place', 'birth_date', 'nationality', 'religion', 'marital_status', 'education', 'occupation', 'phone', 'email', 'address', 'province_id', 'regency_id', 'district_id', 'village_id'];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class, 'village_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function regency(): BelongsTo
    {
        return $this->belongsTo(Regency::class, 'regency_id');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id');
    }
}
