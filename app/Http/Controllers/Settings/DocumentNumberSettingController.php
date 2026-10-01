<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\DocumentNumberSetting;
use App\Models\VillageProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentNumberSettingController extends Controller
{
    public function edit(): View
    {
        $settings = DocumentNumberSetting::firstOrCreate(['id' => 1], [
            'prefix_n1' => '470', 'prefix_n2' => '471', 'prefix_n4' => '474', 'prefix_n5' => '475', 'prefix_n6' => '472',
            'format' => '{prefix}/{seq:3}/{type}/{roman}/{year}',
        ]);
        $profile = VillageProfile::query()->first();
        return view('settings.document-numbers', ['settings' => $settings, 'profile' => $profile]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_klasifikasi_arsip' => ['required', 'string', 'max:20'],
            'kode_desa_kecamatan' => ['required', 'string', 'max:30'],
        ]);
        DocumentNumberSetting::updateOrCreate(['id' => 1], $validated);
        return redirect()->route('settings.document-numbers.edit')->with('success', 'Format surat berhasil disimpan.');
    }
}
