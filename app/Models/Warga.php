<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warga extends Model
{
    protected $table = 'wargas';

    protected $fillable = [
        'nik',
        'nama_lengkap',
        'tempat_lahir',
        'tgl_lahir',
        'jenis_kelamin',
        'agama',
        'pekerjaan',
        'pendidikan',
        'no_hp',
        'email',
        'alamat',
        'rt',
        'rw',
        'nama_ayah',
        'nama_ibu',
        'status_kawin',
        'nationality',
    ];

    protected $casts = [
        'tgl_lahir' => 'date',
    ];

    public function registrationParties(): HasMany
    {
        return $this->hasMany(RegistrationParty::class);
    }

    /**
     * Nama lengkap warga.
     */
    public function getNameAttribute(): ?string
    {
        return $this->nama_lengkap;
    }

    /**
     * Nama warga dengan gelar.
     *
     * // ponytail: Warga tidak memiliki kolom status_hidup, sehingga accessor ini
     * //              tidak menambahkan gelar Alm./Almh. Orang tua yang meninggal
     * //              selalu direpresentasikan sebagai RegistrationParent (yang
     * //              memiliki status_hidup), tempat gelar Alm./Almh diterapkan.
     */
    public function getNamaDenganGelarAttribute(): ?string
    {
        return $this->nama_lengkap;
    }

    public function getBirthPlaceAttribute(): ?string
    {
        return $this->tempat_lahir;
    }

    public function getBirthDateAttribute(): ?\DateTimeInterface
    {
        return $this->tgl_lahir;
    }

    public function getReligionAttribute(): ?string
    {
        return $this->agama;
    }

    public function getOccupationAttribute(): ?string
    {
        return $this->pekerjaan;
    }

    public function getAddressAttribute(): ?string
    {
        return $this->alamat;
    }

    public function getMaritalStatusAttribute(): ?string
    {
        return $this->status_kawin;
    }

    public function getGenderAttribute(): ?string
    {
        return $this->jenis_kelamin;
    }

    /**
     * Kewarganegaraan (default WNI).
     */
    public function getNationalityAttribute(): ?string
    {
        return $this->attributes['nationality'] ?? 'WNI';
    }

    /**
     * Bin (pria) / binti (wanita) berdasarkan nama ayah pada kartu keluarga.
     */
    public function getBinBintiAttribute(): ?string
    {
        if (! $this->nama_ayah) {
            return null;
        }

        return ($this->jenis_kelamin === 'P' ? 'binti ' : 'bin ').$this->nama_ayah;
    }
}
