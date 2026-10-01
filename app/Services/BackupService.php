<?php

namespace App\Services;

use App\Models\BackupSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupService
{
    public const DISK = 'local';

    public const DIR = 'backups';

    public function backup(): string
    {
        $dbPath = database_path('database.sqlite');
        if (! file_exists($dbPath)) {
            throw new \RuntimeException('Database file tidak ditemukan.');
        }

        if (DB::getDriverName() !== 'sqlite') {
            throw new \RuntimeException('Backup hanya untuk SQLite lokal.');
        }

        if (! app()->runningUnitTests()) {
            try {
                DB::statement('VACUUM');
            } catch (\Throwable $e) {
            }
        }

        $filename = 'simanis-'.now()->format('Ymd-His').'.simanis';
        $storagePath = self::DIR.'/'.$filename;

        if (! Storage::disk(self::DISK)->exists(self::DIR)) {
            Storage::disk(self::DISK)->makeDirectory(self::DIR);
        }

        $content = file_get_contents($dbPath);
        if ($content === false) {
            throw new \RuntimeException('Gagal membaca database.');
        }

        Storage::disk(self::DISK)->put($storagePath, $content);

        $fullPath = Storage::disk(self::DISK)->path($storagePath);
        if (! file_exists($fullPath) || filesize($fullPath) === 0) {
            throw new \RuntimeException('Gagal menulis file backup.');
        }

        if (! $this->validateSqliteFile($fullPath)) {
            Storage::disk(self::DISK)->delete($storagePath);
            throw new \RuntimeException('File backup corrupt setelah dibuat.');
        }

        BackupSetting::singleton()->update([
            'last_backup_at' => now(),
            'reminder_dismissed_at' => null,
        ]);

        return $storagePath;
    }

    public function listBackups(): array
    {
        $files = Storage::disk(self::DISK)->files(self::DIR);
        $result = [];
        foreach ($files as $file) {
            if (! str_ends_with($file, '.simanis') && ! str_ends_with($file, '.sqlite')) {
                continue;
            }
            $full = Storage::disk(self::DISK)->path($file);
            $result[] = [
                'path' => $file,
                'name' => basename($file),
                'size' => file_exists($full) ? filesize($full) : 0,
                'modified' => file_exists($full) ? filemtime($full) : 0,
            ];
        }
        usort($result, fn ($a, $b) => $b['modified'] <=> $a['modified']);

        return $result;
    }

    public function validateUpload(string $tmpPath): void
    {
        if (! file_exists($tmpPath)) {
            throw new \RuntimeException('File tidak ditemukan.');
        }
        if (filesize($tmpPath) === 0) {
            throw new \RuntimeException('File kosong.');
        }
        if (filesize($tmpPath) > 100 * 1024 * 1024) {
            throw new \RuntimeException('File terlalu besar (>100MB).');
        }
        if (! $this->validateSqliteFile($tmpPath)) {
            throw new \RuntimeException('File bukan database SQLite yang valid atau corrupt.');
        }
    }

    public function restore(string $tmpPath): void
    {
        $this->validateUpload($tmpPath);

        $dbPath = database_path('database.sqlite');
        $backupBeforeRestore = $dbPath.'.before-restore-'.now()->format('Ymd-His');

        if (file_exists($dbPath)) {
            if (! copy($dbPath, $backupBeforeRestore)) {
                throw new \RuntimeException('Gagal membuat backup sebelum restore.');
            }
        }

        $tmpValidated = $tmpPath.'.validated';
        if (! copy($tmpPath, $tmpValidated)) {
            throw new \RuntimeException('Gagal memproses file restore.');
        }

        if (! $this->validateSqliteFile($tmpValidated)) {
            @unlink($tmpValidated);
            throw new \RuntimeException('File restore corrupt.');
        }

        $content = file_get_contents($tmpValidated);
        @unlink($tmpValidated);
        if ($content === false) {
            throw new \RuntimeException('Gagal membaca file restore.');
        }

        $bytes = file_put_contents($dbPath, $content);
        if ($bytes === false || $bytes === 0) {
            if (file_exists($backupBeforeRestore)) {
                copy($backupBeforeRestore, $dbPath);
            }
            throw new \RuntimeException('Gagal menulis database.');
        }

        if (! $this->validateSqliteFile($dbPath)) {
            if (file_exists($backupBeforeRestore)) {
                copy($backupBeforeRestore, $dbPath);
            }
            throw new \RuntimeException('Database setelah restore corrupt — dikembalikan ke sebelumnya.');
        }

        if (! app()->runningUnitTests()) {
            try {
                DB::purge('sqlite');
                DB::reconnect('sqlite');
                DB::statement('PRAGMA foreign_keys=ON');
                DB::table('migrations')->count();
            } catch (\Throwable $e) {
                if (file_exists($backupBeforeRestore)) {
                    copy($backupBeforeRestore, $dbPath);
                    DB::purge('sqlite');
                    DB::reconnect('sqlite');
                }
                throw new \RuntimeException('Restore gagal validasi koneksi: '.$e->getMessage());
            }
        } else {
            try {
                $pdo = new \PDO('sqlite:'.$dbPath);
                $pdo->query('SELECT COUNT(*) FROM migrations')->fetchColumn();
            } catch (\Throwable $e) {
                if (file_exists($backupBeforeRestore)) {
                    copy($backupBeforeRestore, $dbPath);
                }
                throw new \RuntimeException('Restore gagal validasi: '.$e->getMessage());
            }
        }
    }

    public function validateSqliteFile(string $path): bool
    {
        if (! file_exists($path) || filesize($path) < 100) {
            return false;
        }
        $header = file_get_contents($path, false, null, 0, 16);
        if ($header === false) {
            return false;
        }
        if (! str_starts_with($header, 'SQLite format 3')) {
            return false;
        }

        try {
            $pdo = new \PDO('sqlite:'.$path);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $pdo->query('SELECT name FROM sqlite_master WHERE type="table" LIMIT 1')->fetch();
            $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='migrations'");
            $hasMigrations = $stmt->fetch() !== false;
            if (! $hasMigrations) {
                return false;
            }
            $pdo = null;

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function downloadPath(string $filename): string
    {
        $safe = basename($filename);
        if (! preg_match('/^simanis-\d{8}-\d{6}\.(simanis|sqlite)$/', $safe)) {
            throw new \RuntimeException('Format file tidak valid.');
        }
        $full = Storage::disk(self::DISK)->path(self::DIR.'/'.$safe);
        if (! file_exists($full)) {
            throw new \RuntimeException('File backup tidak ditemukan.');
        }

        return $full;
    }
}
