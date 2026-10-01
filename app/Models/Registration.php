<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Registration extends Model
{
    protected $fillable = ['registration_number', 'registration_path', 'status', 'marriage_date', 'marriage_time', 'marriage_dowry', 'marriage_dowry_type', 'marriage_dowry_money', 'marriage_dowry_gold', 'marriage_dowry_other', 'marriage_location_address'];

    protected $casts = [
        'marriage_date' => 'date',
    ];

    public function getMarriageDowryDisplayAttribute(): string
    {
        if ($this->marriage_dowry_type === 'UANG_EMAS') {
            return trim(collect([
                $this->marriage_dowry_money ? 'Uang '.$this->marriage_dowry_money : null,
                $this->marriage_dowry_gold ? 'Emas '.$this->marriage_dowry_gold : null,
            ])->filter()->implode(' dan '));
        }

        if ($this->marriage_dowry_type === 'UANG') {
            return $this->marriage_dowry_money ? 'Uang '.$this->marriage_dowry_money : '';
        }

        if ($this->marriage_dowry_type === 'EMAS') {
            return $this->marriage_dowry_gold ? 'Emas '.$this->marriage_dowry_gold : '';
        }

        if ($this->marriage_dowry_type === 'LAINNYA') {
            return (string) ($this->marriage_dowry_other ?? '');
        }

        return (string) ($this->marriage_dowry ?? '');
    }

    public function catins(): BelongsToMany
    {
        return $this->belongsToMany(Catin::class, 'registration_catin')
            ->withTimestamps();
    }

    public function catinsLegacy(): HasMany
    {
        return $this->hasMany(Catin::class);
    }

    public function guardian(): HasOne
    {
        return $this->hasOne(Guardian::class);
    }

    public function witnesses(): HasMany
    {
        return $this->hasMany(Witness::class);
    }

    public function registrationDocumentChecklist(): HasOne
    {
        return $this->hasOne(RegistrationDocumentChecklist::class);
    }

    public function documentArchives(): HasMany
    {
        return $this->hasMany(DocumentArchive::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(RegistrationDocument::class);
    }

    public function n6Document(): HasOne
    {
        return $this->hasOne(N6Document::class);
    }

    public function ktpCopies(): HasMany
    {
        return $this->hasMany(RegistrationKtpCopy::class);
    }
}
