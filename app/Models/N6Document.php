<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class N6Document extends Model
{
    protected $fillable = ['registration_id', 'deceased_name', 'deceased_bin_binti', 'deceased_nik', 'deceased_birth_place', 'deceased_birth_date', 'deceased_nationality', 'deceased_religion', 'deceased_occupation', 'deceased_address', 'death_date', 'death_place', 'document_number', 'issue_date', 'signed_by', 'signed_by_position', 'status', 'created_by'];

    protected $casts = [
        'deceased_birth_date' => 'date',
        'death_date' => 'date',
        'issue_date' => 'date',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get data catin wanita dari registration
     */
    public function getCatinWanitaAttribute()
    {
        return $this->registration->catins
            ->firstWhere('role', 'WANITA');
    }
}
