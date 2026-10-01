<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class RegistrationKtpCopy extends Model
{
    protected $fillable = ['registration_id', 'slot', 'path', 'original_name'];

    public const SLOTS = [
        'pria' => 'Calon Suami',
        'wanita' => 'Calon Istri',
        'ayah_pria' => 'Bapak Calon Suami',
        'ibu_pria' => 'Ibu Calon Suami',
        'ayah_wanita' => 'Bapak Calon Istri',
        'ibu_wanita' => 'Ibu Calon Istri',
        'saksi_1' => 'Saksi 1',
        'saksi_2' => 'Saksi 2',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function url(): ?string
    {
        if (! $this->path) {
            return null;
        }
        if ($this->registration_id && $this->slot) {
            return route('registrations.ktp.file', [$this->registration_id, $this->slot]);
        }

        return Storage::disk('local')->exists($this->path) ? route('registrations.ktp.file', [$this->registration_id, $this->slot]) : null;
    }
}
