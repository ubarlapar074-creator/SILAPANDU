<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Support\MaritalStatus;
use Illuminate\Support\Facades\DB;

class Catin extends Model
{
    protected $table = 'catins';

    protected $fillable = ['registration_id', 'role', 'nik', 'name', 'bin_binti', 'birth_place', 'birth_date', 'nationality', 'religion', 'marital_status', 'no_hp', 'email', 'occupation', 'education', 'address', 'province_id', 'regency_id', 'district_id', 'village_id'];

    protected $casts = [
        'birth_date' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | REGISTRATION
    |--------------------------------------------------------------------------
    */

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function registrations(): BelongsToMany
    {
        return $this->belongsToMany(Registration::class, 'registration_catin')
            ->withTimestamps();
    }

    protected static function booted(): void
    {
        static::created(function (Catin $catin) {
            if ($catin->registration_id) {
                try {
                    DB::table('registration_catin')->updateOrInsert(
                        ['registration_id' => $catin->registration_id, 'catin_id' => $catin->id],
                        ['created_at' => now(), 'updated_at' => now()]
                    );
                } catch (\Throwable $e) {
                }
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | PARENTS
    |--------------------------------------------------------------------------
    |
    | Parents adalah SINGLE SOURCE OF TRUTH untuk:
    | - AYAH
    | - IBU
    |
    | Jangan menyimpan ulang data orang tua di Catin.
    |
    */

    public function parents(): HasMany
    {
        return $this->hasMany(CatinParent::class);
    }

    public function father(): HasOne
    {
        return $this->hasOne(
            CatinParent::class
        )->where('role', 'AYAH');
    }

    public function mother(): HasOne
    {
        return $this->hasOne(
            CatinParent::class
        )->where('role', 'IBU');
    }

    /*
    |--------------------------------------------------------------------------
    | DOCUMENT CHECKLIST
    |--------------------------------------------------------------------------
    |
    | Satu Catin memiliki satu checklist dokumen.
    |
    */

    public function documentChecklist(): HasOne
    {
        return $this->hasOne(DocumentChecklist::class);
    }

    /*
    |--------------------------------------------------------------------------
    | WILAYAH
    |--------------------------------------------------------------------------
    */

    public function province(): BelongsTo
    {
        return $this->belongsTo(
            Province::class,
            'province_id'
        );
    }

    public function regency(): BelongsTo
    {
        return $this->belongsTo(
            Regency::class,
            'regency_id'
        );
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(
            District::class,
            'district_id'
        );
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(
            Village::class,
            'village_id'
        );
    }

    public function getMaritalStatusCanonicalAttribute(): ?string
    {
        return MaritalStatus::normalize($this->marital_status);
    }

    public function getMaritalStatusRawAttribute(): ?string
    {
        return $this->marital_status;
    }

    /*
    |--------------------------------------------------------------------------
    | N6 DOCUMENT (Surat Keterangan Kematian)
    |--------------------------------------------------------------------------
    |
    | Mendapatkan data N6 yang terkait dengan Catin ini
    | melalui Registration
    |
    */

    public function n6Document(): HasOne
    {
        return $this->hasOneThrough(
            N6Document::class,
            Registration::class,
            'id', // Foreign key on Registration
            'registration_id', // Foreign key on N6Document
            'registration_id', // Local key on Catin
            'id' // Local key on Registration
        );
    }
}
