<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentChecklist extends Model
{
    protected $table = 'document_checklists';

    protected $fillable = ['catin_id', 'doc_ktp', 'doc_kk', 'doc_birth', 'doc_immunization', 'doc_photo'];

    protected $casts = [
        'doc_ktp' => 'boolean',
        'doc_kk' => 'boolean',
        'doc_birth' => 'boolean',
        'doc_immunization' => 'boolean',
        'doc_photo' => 'boolean',
    ];

    public function catin(): BelongsTo
    {
        return $this->belongsTo(Catin::class);
    }
}
