<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrationParent extends Model
{
    protected $table = 'registration_parents';

    protected $fillable = [
        'registration_id',
        'type',
        'nama',
        'nama_ayah_kandung',
        'nik',
        'tempat_lahir',
        'tgl_lahir',
        'pekerjaan',
        'agama',
        'pendidikan',
        'status_hubungan',
        'status_kawin_ortu',
        'status_hidup',
        'no_hp',
        'email',
        'alamat',
        'rt',
        'rw',
        'tgl_meninggal',
        'tempat_meninggal',
    ];

    protected $casts = [
        'tgl_lahir' => 'date',
        'tgl_meninggal' => 'date',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function getNameAttribute(): ?string
    {
        return $this->nama;
    }

    /**
     * Nama orang tua dengan gelar ( ALM ) bapak / ( ALMH ) ibu bila sudah
     * meninggal. Gelar dalam tanda kurung di akhir nama, sesuai format dokumen.
     */
    public function getNamaDenganGelarAttribute(): ?string
    {
        if (! $this->nama) {
            return null;
        }

        if (! \App\Support\Nama::meninggal($this->status_hidup)) {
            return $this->nama;
        }

        $gelar = \App\Support\Nama::gelar(str_starts_with(strtolower((string) $this->type), 'ibu'));

        $nama = trim((string) $this->nama);

        // Lepas dulu bila nama sudah diketik dengan gelar sebagai prefix
        // (mis. "Alm. Abdullah"), agar tidak muncul dobel di depan dan belakang.
        $nama = trim(preg_replace('/^\s*Alm(h)?\.?\s+/iu', '', $nama));

        // Jangan tambahkan gelar bila nama sudah diakhiri gelar yang sama.
        if (preg_match('/\(\s*Alm(h)?\.?\s*\)\s*$/iu', $nama)) {
            return $nama;
        }

        return trim($nama).' ( '.$gelar.' )';
    }

    public function getBirthPlaceAttribute(): ?string
    {
        return $this->tempat_lahir;
    }

    public function getBirthDateAttribute(): ?\DateTimeInterface
    {
        return $this->tgl_lahir;
    }

    public function getOccupationAttribute(): ?string
    {
        return $this->pekerjaan;
    }

    public function getAddressAttribute(): ?string
    {
        return $this->alamat;
    }

    /**
     * Status hidup (hidup / meninggal / tidak_diketahui) dipakai template dokumen.
     */
    public function getStatusAttribute(): ?string
    {
        return $this->status_hidup;
    }
}
