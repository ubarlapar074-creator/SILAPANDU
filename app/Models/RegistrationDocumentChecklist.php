<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrationDocumentChecklist extends Model
{
    protected $table = 'registration_document_checklists';

    protected $fillable = ['registration_id', 'doc_n1_n4', 'doc_n6', 'doc_n5', 'doc_death'];

    protected $casts = [
        'doc_n1_n4' => 'boolean',
        'doc_death' => 'boolean',
        'doc_n5' => 'boolean',
        'doc_n6' => 'boolean',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }
}
