<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentArchive extends Model
{
    protected $table = 'document_archives';

    protected $fillable = ['registration_id', 'document_type', 'payload_json'];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }
}
