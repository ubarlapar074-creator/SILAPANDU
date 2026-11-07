<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\RegistrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class SettingsController extends Controller
{
    /**
     * Halaman pengaturan: identitas desa & pejabat diambil dari tabel settings.
     */
    public function index()
    {
        $settings = Setting::allAsArray();

        $identitas = [
            'logo' => $settings['logo'] ?? null,
            'kop_surat' => $settings['kop_surat'] ?? null,
            'nama_desa' => $settings['nama_desa'] ?? '',
            'kecamatan' => $settings['kecamatan'] ?? '',
            'kabupaten' => $settings['kabupaten'] ?? '',
            'alamat_kantor' => $settings['alamat_kantor'] ?? '',
        ];

        $pejabat = [
            'kepala_desa' => [
                'nama' => $settings['kepala_desa.nama'] ?? '',
                'nip' => $settings['kepala_desa.nip'] ?? '',
                'ttd' => $settings['kepala_desa.ttd'] ?? null,
            ],
            'kasi_pelayanan' => [
                'nama' => $settings['kasi_pelayanan.nama'] ?? '',
                'nip' => $settings['kasi_pelayanan.nip'] ?? '',
                'ttd' => $settings['kasi_pelayanan.ttd'] ?? null,
            ],
            'kepala_kua' => [
                'nama' => $settings['kepala_kua.nama'] ?? '',
                'nip' => $settings['kepala_kua.nip'] ?? '',
                'ttd' => $settings['kepala_kua.ttd'] ?? null,
            ],
        ];

        $formatter = app(RegistrationService::class);
        $formatNomor = (string) ($settings['format_nomor_surat'] ?? '');

        $penomoran = [
            'format' => trim($formatNomor) !== '' ? $formatNomor : RegistrationService::DEFAULT_FORMAT_NOMOR,
            'contoh' => $formatter->contohNomorSurat($formatNomor),
        ];

        return view('settings.index', compact('identitas', 'pejabat', 'penomoran'));
    }

    /**
     * Simpan pengaturan identitas desa & pejabat (per tab, hanya field yang dikirim).
     */
    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_desa' => ['nullable', 'string', 'max:100'],
            'kecamatan' => ['nullable', 'string', 'max:100'],
            'kabupaten' => ['nullable', 'string', 'max:100'],
            'alamat_kantor' => ['nullable', 'string', 'max:255'],
            'kepala_desa' => ['nullable', 'array'],
            'kepala_desa.nama' => ['nullable', 'string', 'max:100'],
            'kepala_desa.nip' => ['nullable', 'string', 'max:30'],
            'kasi_pelayanan' => ['nullable', 'array'],
            'kasi_pelayanan.nama' => ['nullable', 'string', 'max:100'],
            'kasi_pelayanan.nip' => ['nullable', 'string', 'max:30'],
            'kepala_kua' => ['nullable', 'array'],
            'kepala_kua.nama' => ['nullable', 'string', 'max:100'],
            'kepala_kua.nip' => ['nullable', 'string', 'max:30'],
            // Penomoran
            'kode_desa' => ['nullable', 'string', 'max:50'],
            'tahun' => ['nullable', 'integer', 'min:2020', 'max:2099'],
            'format_nomor_surat' => ['nullable', 'string', 'max:100'],
        ]);

        $settings = [];

        foreach (['nama_desa', 'kecamatan', 'kabupaten', 'alamat_kantor', 'kode_desa', 'tahun', 'format_nomor_surat'] as $key) {
            if ($request->has($key)) {
                $settings[$key] = $data[$key] ?? null;
            }
        }

        foreach (['kepala_desa', 'kasi_pelayanan', 'kepala_kua'] as $group) {
            if ($request->has($group)) {
                foreach (['nama', 'nip'] as $field) {
                    $settings[$group.'.'.$field] = $data[$group][$field] ?? null;
                }
            }
        }

        Setting::saveMany($settings);

        app(RegistrationService::class)->resequenceNomorSurat();

        return redirect()
            ->route('settings.index')
            ->with('success', 'Pengaturan berhasil disimpan.');
    }

    public function backupDatabase(): BinaryFileResponse
    {
        $dbPath = config('database.connections.sqlite.database');

        if (! File::exists($dbPath)) {
            // If the database path from config doesn't exist, fallback to database_path('database.sqlite')
            $dbPath = database_path('database.sqlite');
        }

        if (! File::exists($dbPath)) {
            abort(404, 'Database file not found.');
        }

        $backupDirectory = storage_path('app/backups');
        File::ensureDirectoryExists($backupDirectory);

        $backupSqlite = $backupDirectory.'/backup_'.now()->format('Ymd_His').'.sqlite';
        $filename = 'silapandu_backup_'.now()->format('Ymd_His').'.zip';
        $archivePath = $backupDirectory.'/'.$filename;

        // Use VACUUM INTO to safely backup while database is in use, fallback to File::copy if in-memory
        try {
            DB::statement("VACUUM INTO '".$backupSqlite."'");
        } catch (\Exception $e) {
            File::copy($dbPath, $backupSqlite);
        }

        $archive = new ZipArchive;
        if ($archive->open($archivePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Backup archive could not be created.');
        }

        if (! $archive->addFile($backupSqlite, 'database.sqlite') || ! $archive->close()) {
            if (File::exists($archivePath)) {
                File::delete($archivePath);
            }
            abort(500, 'Database backup could not be created.');
        }

        // Cleanup
        File::delete($backupSqlite);

        Setting::saveMany(['last_backup_at' => now()->toDateTimeString()]);

        return response()->download($archivePath, $filename, [
            'Content-Type' => 'application/zip',
        ]);
    }

    public function backupStatus(): JsonResponse
    {
        $dbPath = config('database.connections.sqlite.database');

        if (! File::exists($dbPath)) {
            return response()->json([
                'exists' => false,
                'message' => 'Database file not found.',
            ]);
        }

        $size = File::size($dbPath);
        $modified = filemtime($dbPath);
        $tables = DB::select('SELECT name FROM sqlite_master WHERE type="table"');

        return response()->json([
            'exists' => true,
            'size' => $size,
            'size_human' => $this->formatBytes($size),
            'last_modified' => date('d M Y H:i:s', $modified),
            'tables_count' => count($tables),
            'db_path' => $dbPath,
        ]);
    }

    public function restoreDatabase(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:sqlite,sql,txt,zip',
        ]);

        $dbPath = config('database.connections.sqlite.database');
        $backupPath = $dbPath.'.bak_'.now()->format('Ymd_His');

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();

        try {
            // Backup current database
            if (File::exists($dbPath)) {
                File::copy($dbPath, $backupPath);
            }

            // Disconnect to release file lock
            DB::disconnect('sqlite');

            if ($extension === 'sqlite') {
                File::copy($file->getRealPath(), $dbPath);
            } elseif ($extension === 'sql') {
                $sql = file_get_contents($file->getRealPath());
                DB::unprepared($sql);
            }

            return response()->json([
                'success' => true,
                'message' => 'Database berhasil direstore. Backup tersimpan di: '.basename($backupPath),
            ]);
        } catch (\Exception $e) {
            // Restore backup if exists
            if (File::exists($backupPath)) {
                File::copy($backupPath, $dbPath);
            }

            return response()->json([
                'success' => false,
                'message' => 'Restore gagal: '.$e->getMessage(),
            ], 500);
        }
    }

    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes > 0 ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision).' '.$units[$pow];
    }
}
