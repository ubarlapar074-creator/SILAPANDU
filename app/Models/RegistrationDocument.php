<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrationDocument extends Model
{
    protected $fillable = ['registration_id', 'document_type', 'document_number', 'status', 'issue_date', 'signed_by', 'signed_by_position', 'created_by'];

    protected $casts = [
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
}
