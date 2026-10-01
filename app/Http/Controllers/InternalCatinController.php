<?php

namespace App\Http\Controllers;

use App\Models\Catin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InternalCatinController extends Controller
{
    public function lookup(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nik' => ['required', 'digits:16'],
        ]);

        $catin = Catin::with(['parents', 'documentChecklist'])
            ->where('nik', $validated['nik'])
            ->first();

        if (!$catin) {
            return response()->json(['found' => false, 'data' => null]);
        }

        $father = $catin->parents->firstWhere('role', 'AYAH');
        $mother = $catin->parents->firstWhere('role', 'IBU');

        return response()->json([
            'found' => true,
            'data' => [
                'id' => $catin->id,
                'nik' => $catin->nik,
                'name' => $catin->name,
                'bin_binti' => $catin->bin_binti,
                'birth_place' => $catin->birth_place,
                'birth_date' => $catin->birth_date?->format('Y-m-d'),
                'nationality' => $catin->nationality,
                'religion' => $catin->religion,
                'marital_status' => $catin->marital_status,
                'no_hp' => $catin->no_hp,
                'email' => $catin->email,
                'occupation' => $catin->occupation,
                'education' => $catin->education,
                'address' => $catin->address,
                'province_id' => $catin->province_id,
                'regency_id' => $catin->regency_id,
                'district_id' => $catin->district_id,
                'village_id' => $catin->village_id,
                'role' => $catin->role,
                'father' => $father ? [
                    'status' => $father->status,
                    'name' => $father->name,
                    'nik' => $father->nik,
                    'birth_place' => $father->birth_place,
                    'birth_date' => $father->birth_date?->format('Y-m-d'),
                    'address' => $father->address,
                    'education' => $father->education,
                    'occupation' => $father->occupation,
                    'province_id' => $father->province_id,
                    'regency_id' => $father->regency_id,
                    'district_id' => $father->district_id,
                    'village_id' => $father->village_id,
                ] : null,
                'mother' => $mother ? [
                    'status' => $mother->status,
                    'name' => $mother->name,
                    'nik' => $mother->nik,
                    'birth_place' => $mother->birth_place,
                    'birth_date' => $mother->birth_date?->format('Y-m-d'),
                    'address' => $mother->address,
                    'education' => $mother->education,
                    'occupation' => $mother->occupation,
                    'province_id' => $mother->province_id,
                    'regency_id' => $mother->regency_id,
                    'district_id' => $mother->district_id,
                    'village_id' => $mother->village_id,
                ] : null,
            ],
        ]);
    }
}
