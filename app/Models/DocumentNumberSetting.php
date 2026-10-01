<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentNumberSetting extends Model
{
    protected $fillable = ['prefix_n1', 'prefix_n2', 'prefix_n4', 'prefix_n5', 'prefix_n6', 'format', 'default_signed_by', 'default_position', 'defaults_json', 'kode_klasifikasi_arsip', 'kode_desa_kecamatan'];

    protected $casts = [
        'defaults_json' => 'array',
    ];
}
