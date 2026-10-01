<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VillageProfile extends Model
{
    protected $fillable = ['village_name', 'district_name', 'regency_name', 'office_address', 'village_head_name', 'village_head_id', 'service_officer_name', 'service_officer_id', 'kua_head_name', 'kua_head_nip'];
}
