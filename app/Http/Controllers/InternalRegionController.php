<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Regency;
use App\Models\Village;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InternalRegionController extends Controller
{
    /**
     * Mengambil kabupaten/kota berdasarkan provinsi.
     */
    public function regencies(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'province_id' => ['required', 'string', 'exists:provinces,id'],
        ]);

        $regencies = Regency::query()
            ->where('province_id', $validated['province_id'])
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $regencies,
        ]);
    }

    /**
     * Mengambil kecamatan berdasarkan kabupaten/kota.
     */
    public function districts(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'regency_id' => ['required', 'string', 'exists:regencies,id'],
        ]);

        $districts = District::query()
            ->where('regency_id', $validated['regency_id'])
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $districts,
        ]);
    }

    /**
     * Mengambil desa/kelurahan berdasarkan kecamatan.
     */
    public function villages(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'district_id' => ['required', 'string', 'exists:districts,id'],
        ]);

        $villages = Village::query()
            ->where('district_id', $validated['district_id'])
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();

        return response()->json([
            'data' => $villages,
        ]);
    }
}