<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrationParty extends Model
{
    protected $table = 'registration_parties';

    protected $fillable = [
        'registration_id',
        'warga_id',
        'role',
        'nama_sementara',
        'desa_asal',
        'rt',
        'rw',
        'status_hubungan',
        'status_hidup',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function warga(): BelongsTo
    {
        return $this->belongsTo(Warga::class);
    }

    /**
     * Nama partai: data warga bila NIK tersedia, selain itu nama sementara.
     */
    public function getNameAttribute(): ?string
    {
        return $this->warga?->nama_lengkap ?? $this->nama_sementara;
    }

    public function getNikAttribute(): ?string
    {
        return $this->warga?->nik;
    }

    public function getBirthPlaceAttribute(): ?string
    {
        return $this->warga?->tempat_lahir;
    }

    public function getBirthDateAttribute(): ?\DateTimeInterface
    {
        return $this->warga?->tgl_lahir;
    }

    public function getReligionAttribute(): ?string
    {
        return $this->warga?->agama;
    }

    public function getOccupationAttribute(): ?string
    {
        return $this->warga?->pekerjaan;
    }

    public function getAddressAttribute(): ?string
    {
        return $this->warga?->alamat;
    }

    public function getMaritalStatusAttribute(): ?string
    {
        return $this->warga?->status_kawin;
    }

    public function getGenderAttribute(): ?string
    {
        return $this->warga?->jenis_kelamin;
    }

    /**
     * @param  array  $excludedRoles  role pihak yang dikecualikan (mis. saksi_1)
     * @param  int|null  $excludedPartyId  id pihak yang sedang diedit
     * @param  array  $excludedParentTypes  tipe orang tua yang sedang diedit
     *                                       (mis. bapak_pria, ibu_wanita)
     *
     * Pengecualian ini agar mode edit tidak menuduh seseorang bertabrakan
     * dengan data lamanya sendiri.
     */
    public static function isNikUsedInRegistration(int $registrationId, string $nik, array $excludedRoles = [], ?int $excludedPartyId = null, array $excludedParentTypes = []): bool
    {
        // Cek duplikasi pada partai (calon pria, wanita, saksi)
        $inParties = self::where('registration_id', $registrationId)
            ->whereNotIn('role', $excludedRoles)
            ->whereHas('warga', function ($query) use ($nik) {
                $query->where('nik', $nik);
            });

        if ($excludedPartyId) {
            $inParties->where('id', '!=', $excludedPartyId);
        }

        // Cek duplikasi pada data orang tua (bapak, ibu, wali, dsb)
        $inParents = RegistrationParent::where('registration_id', $registrationId)
            ->where('nik', $nik);

        if ($excludedParentTypes !== []) {
            $inParents->whereNotIn('type', $excludedParentTypes);
        }

        return $inParties->exists() || $inParents->exists();
    }
}
