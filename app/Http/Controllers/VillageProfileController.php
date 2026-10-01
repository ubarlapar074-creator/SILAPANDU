<?php

namespace App\Http\Controllers;

use App\Models\VillageProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VillageProfileController extends Controller
{
    public function edit(): View
    {
        return view('settings.village-profile', [
            'profile' => VillageProfile::query()->first(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'village_name' => ['nullable', 'string', 'max:255'],
            'district_name' => ['nullable', 'string', 'max:255'],
            'regency_name' => ['nullable', 'string', 'max:255'],
            'office_address' => ['nullable', 'string', 'max:500'],
            'village_head_name' => ['nullable', 'string', 'max:255'],
            'village_head_id' => ['nullable', 'string', 'max:100'],
            'service_officer_name' => ['nullable', 'string', 'max:255'],
            'service_officer_id' => ['nullable', 'string', 'max:100'],
            'kua_head_name' => ['nullable', 'string', 'max:255'],
            'kua_head_nip' => ['nullable', 'string', 'max:100'],
        ]);

        VillageProfile::query()->updateOrCreate(['id' => 1], $validated);

        return redirect()
            ->route('settings.village-profile.edit')
            ->with('success', 'Profil desa berhasil disimpan.');
    }
}
