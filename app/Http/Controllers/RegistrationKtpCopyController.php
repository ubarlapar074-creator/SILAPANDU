<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\RegistrationKtpCopy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class RegistrationKtpCopyController extends Controller
{
    public function edit(Registration $registration): View
    {
        $registration->load(['ktpCopies', 'catins.parents', 'witnesses']);
        $copies = $registration->ktpCopies->keyBy('slot');

        return view('registrations.ktp.edit', [
            'registration' => $registration,
            'copies' => $copies,
            'slots' => RegistrationKtpCopy::SLOTS,
        ]);
    }

    public function update(Request $request, Registration $registration): RedirectResponse
    {
        $request->validate([
            'ktp' => ['nullable', 'array'],
            'ktp.*' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
        ]);

        $allowed = array_keys(RegistrationKtpCopy::SLOTS);
        $uploaded = $request->file('ktp', []);
        if (!is_array($uploaded)) $uploaded = $uploaded ? [$uploaded] : [];
        $inputKeys = array_keys($uploaded);
        if (empty($inputKeys) && is_array($request->input('ktp'))) {
            $inputKeys = array_keys($request->input('ktp'));
        }
        $invalid = array_diff($inputKeys, $allowed);
        if (!empty($invalid)) {
            abort(422, 'Slot KTP tidak valid: '.implode(', ', $invalid));
        }
        foreach ($inputKeys as $k) {
            if (str_contains((string) $k, '/') || str_contains((string) $k, '\\') || str_contains((string) $k, '..') || $k !== trim($k)) {
                abort(422, 'Slot KTP tidak valid.');
            }
        }

        $files = $uploaded;

        foreach (RegistrationKtpCopy::SLOTS as $slot => $label) {
            if (! isset($files[$slot]) || ! $files[$slot] || ! $files[$slot]->isValid()) {
                continue;
            }
            $file = $files[$slot];
            $clientExt = strtolower($file->getClientOriginalExtension() ?: '');
            $mime = $file->getMimeType() ?: '';
            $ext = $clientExt;
            if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                if (str_contains($mime, 'png')) $ext = 'png';
                elseif (str_contains($mime, 'webp')) $ext = 'webp';
                elseif (str_contains($mime, 'jpeg') || str_contains($mime, 'jpg')) $ext = 'jpg';
                else $ext = 'jpg';
            }
            if ($ext === 'jpeg') $ext = 'jpg';
            $path = $file->storeAs('ktp/'.$registration->id, $slot.'.'.$ext, 'local');
            $existing = RegistrationKtpCopy::where('registration_id', $registration->id)->where('slot', $slot)->first();
            if ($existing && $existing->path !== $path && Storage::disk('local')->exists($existing->path)) {
                Storage::disk('local')->delete($existing->path);
            }
            RegistrationKtpCopy::updateOrCreate(
                ['registration_id' => $registration->id, 'slot' => $slot],
                ['path' => $path, 'original_name' => $file->getClientOriginalName()]
            );
        }

        return redirect()->route('registrations.ktp.edit', $registration)->with('success', 'Fotokopi KTP berhasil disimpan.');
    }

    public function file(Registration $registration, string $slot)
    {
        if (! array_key_exists($slot, RegistrationKtpCopy::SLOTS)) {
            abort(404);
        }
        $copy = RegistrationKtpCopy::where('registration_id', $registration->id)->where('slot', $slot)->first();
        if (! $copy || ! Storage::disk('local')->exists($copy->path)) {
            abort(404);
        }

        return Storage::disk('local')->response($copy->path);
    }

    public function print(Registration $registration): View
    {
        $registration->load(['ktpCopies', 'catins.parents', 'witnesses']);
        $copies = $registration->ktpCopies->keyBy('slot');

        return view('registrations.print.ktp', [
            'registration' => $registration,
            'copies' => $copies,
            'slots' => RegistrationKtpCopy::SLOTS,
        ]);
    }

    public function destroy(Registration $registration, string $slot): RedirectResponse
    {
        if (! array_key_exists($slot, RegistrationKtpCopy::SLOTS)) {
            abort(404);
        }
        $copy = RegistrationKtpCopy::where('registration_id', $registration->id)->where('slot', $slot)->first();
        if ($copy) {
            if (Storage::disk('local')->exists($copy->path)) {
                Storage::disk('local')->delete($copy->path);
            }
            if (Storage::disk('public')->exists($copy->path)) {
                Storage::disk('public')->delete($copy->path);
            }
            $copy->delete();
        }

        return back()->with('success', 'Foto KTP '.$slot.' dihapus.');
    }
}
