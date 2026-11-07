<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAkadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tanggal_akad' => 'required|date',
            'jam_akad' => 'required',
            'jenis_mahar' => 'nullable|string',
            'nominal_uang' => 'nullable|numeric',
            'rincian_mahar' => 'nullable|string',
            'marriage_dowry_display' => 'nullable|string',
            'marriage_location_address' => 'nullable|string',
            'province_id' => 'nullable|string',
            'regency_id' => 'nullable|string',
            'district_id' => 'nullable|string',
            'village_id' => 'nullable|string',
        ];
    }
}
