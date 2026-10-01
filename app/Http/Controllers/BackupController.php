<?php

namespace App\Http\Controllers;

use App\Models\BackupSetting;
use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BackupController extends Controller
{
    public function index(BackupService $service)
    {
        $setting = BackupSetting::singleton();
        $backups = $service->listBackups();

        return view('backups.index', [
            'setting' => $setting,
            'backups' => $backups,
            'shouldWarn' => $setting->shouldShowWarning(),
        ]);
    }

    public function store(BackupService $service)
    {
        try {
            $path = $service->backup();

            return redirect()->route('backups.index')->with('success', 'Cadangan berhasil dibuat: '.basename($path));
        } catch (\Throwable $e) {
            Log::warning('Backup store failed: '.$e->getMessage());
            $msg = app()->hasDebugModeEnabled() ? 'Gagal membuat cadangan: '.$e->getMessage() : 'Gagal membuat cadangan. Silakan coba lagi.';

            return redirect()->route('backups.index')->with('error', $msg);
        }
    }

    public function download(string $filename, BackupService $service)
    {
        try {
            $full = $service->downloadPath($filename);

            return response()->download($full, basename($filename));
        } catch (\Throwable $e) {
            abort(404);
        }
    }

    public function dismiss(Request $request)
    {
        $days = (int) $request->input('snooze_days', 14);
        if (!in_array($days, [7, 14, 30], true)) $days = 14;
        $setting = BackupSetting::singleton();
        $setting->update(['reminder_dismissed_at' => now(), 'reminder_snooze_days' => $days]);
        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'snooze_days' => $days, 'snooze_until' => $setting->fresh()->snooze_until]);
        }
        return redirect()->back()->with('success', "Pengingat ditunda {$days} hari.");
    }

    public function restore(Request $request, BackupService $service)
    {
        $request->validate([
            'backup_file' => ['required', 'file', 'max:102400'],
        ]);

        $file = $request->file('backup_file');
        $tmpPath = $file->getRealPath();

        $countBefore = DB::table('registrations')->count();

        try {
            $service->restore($tmpPath);
        } catch (\Throwable $e) {
            Log::warning('Backup restore failed: '.$e->getMessage());
            $countAfter = DB::table('registrations')->count();
            $detail = app()->hasDebugModeEnabled() ? ': '.$e->getMessage() : '. Silakan coba lagi.';
            if ($countAfter !== $countBefore) {
                return redirect()->route('backups.index')->with('error', 'Restore gagal dan data mungkin tidak konsisten'.$detail);
            }

            return redirect()->route('backups.index')->with('error', 'Restore gagal'.$detail);
        }

        return redirect()->route('backups.index')->with('success', 'Restore berhasil — data telah dipulihkan.');
    }
}
