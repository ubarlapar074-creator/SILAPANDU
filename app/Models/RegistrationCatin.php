<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrationCatin extends Model
{
    protected $table = 'registration_catin';

    protected $fillable = ['registration_id', 'catin_id'];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function catin(): BelongsTo
    {
        return $this->belongsTo(Catin::class);
    }
}
