<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use SplFileObject;

class ImportWilayah extends Command
{
    protected $signature = 'wilayah:import
                            {--dry-run : Hanya validasi CSV tanpa memasukkan data}';

    protected $description = 'Import data wilayah Indonesia dari CSV lokal';

    private const CHUNK_SIZE = 1000;

    public function handle(): int
    {
        $this->info('=== IMPORT WILAYAH INDONESIA ===');

        $files = [
            'provinces' => storage_path('app/provinsi.csv'),
            'regencies' => storage_path('app/kabupaten_kota.csv'),
            'districts' => storage_path('app/kecamatan.csv'),
            'villages' => storage_path('app/kelurahan.csv'),
        ];

        $this->checkFiles($files);

        if ($this->hasExistingData()) {
            $this->error(
                'IMPORT DIBATALKAN: tabel wilayah tidak kosong.'
            );

            $this->line(
                'Untuk keamanan V1, importer hanya boleh berjalan pada database wilayah kosong.'
            );

            return self::FAILURE;
        }

        $this->info('Database wilayah: KOSONG ✓');

        if ($this->option('dry-run')) {
            $this->newLine();
            $this->info('MODE DRY-RUN');
            $this->info('CSV akan divalidasi tanpa mengubah database.');

            return $this->validateAll($files);
        }

        if (!$this->confirm(
            'CSV valid akan dimasukkan ke database. Lanjutkan?',
            true
        )) {
            $this->warn('Import dibatalkan oleh operator.');

            return self::SUCCESS;
        }

        return $this->importAll($files);
    }

    private function checkFiles(array $files): void
    {
        foreach ($files as $table => $file) {
            if (!is_file($file)) {
                throw new RuntimeException(
                    "File CSV tidak ditemukan untuk {$table}: {$file}"
                );
            }

            if (!is_readable($file)) {
                throw new RuntimeException(
                    "File CSV tidak dapat dibaca: {$file}"
                );
            }
        }
    }

    private function hasExistingData(): bool
    {
        foreach ([
            'provinces',
            'regencies',
            'districts',
            'villages',
        ] as $table) {
            if (DB::table($table)->exists()) {
                return true;
            }
        }

        return false;
    }

    private function validateAll(array $files): int
    {
        $success = true;

        $success = $this->validateLevel(
            $files['provinces'],
            'provinces',
            null
        ) && $success;

        $success = $this->validateLevel(
            $files['regencies'],
            'regencies',
            'provinces'
        ) && $success;

        $success = $this->validateLevel(
            $files['districts'],
            'districts',
            'regencies'
        ) && $success;

        $success = $this->validateLevel(
            $files['villages'],
            'villages',
            'districts'
        ) && $success;

        if ($success) {
            $this->newLine();
            $this->info('DRY-RUN PASS ✓');
            $this->info('Seluruh CSV lolos validasi dasar.');
        } else {
            $this->error('DRY-RUN FAILED ✗');
        }

        return $success ? self::SUCCESS : self::FAILURE;
    }

    private function validateLevel(
        string $file,
        string $table,
        ?string $parentTable
    ): bool {
        $this->line("Validasi {$table}...");

        $csv = $this->openCsv($file);

        $header = $this->readRow($csv);

        if ($header !== ['id', 'name']) {
            $this->error(
                "Header {$table} tidak sesuai: " .
                implode(',', $header ?? [])
            );

            return false;
        }

        $count = 0;
        $errors = 0;

        while (!$csv->eof()) {
            $row = $this->readRow($csv);

            if ($row === null) {
                continue;
            }

            if (count($row) !== 2) {
                $errors++;
                continue;
            }

            [$id, $name] = array_map('trim', $row);

            if ($id === '' || $name === '') {
                $errors++;
                continue;
            }

            $count++;

            if ($parentTable !== null) {
                $parentId = $this->extractParentId($id);

                if (
                    $parentId === null ||
                    !DB::table($parentTable)
                        ->where('id', $parentId)
                        ->exists()
                ) {
                    /*
                     * Pada dry-run database parent masih kosong.
                     * Validasi parent dilakukan saat import.
                     */
                }
            }
        }

        $csv = null;

        if ($errors > 0) {
            $this->error(
                "{$table}: {$errors} baris bermasalah."
            );

            return false;
        }

        $this->info(
            "{$table}: {$count} baris valid ✓"
        );

        return true;
    }

    private function importAll(array $files): int
    {
        try {
            DB::transaction(function () use ($files) {
                $this->importLevel(
                    $files['provinces'],
                    'provinces'
                );

                $this->importLevel(
                    $files['regencies'],
                    'regencies'
                );

                $this->importLevel(
                    $files['districts'],
                    'districts'
                );

                $this->importLevel(
                    $files['villages'],
                    'villages'
                );
            });

            $this->newLine();
            $this->info('IMPORT BERHASIL ✓');

            $this->showCounts();

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('IMPORT GAGAL ✗');
            $this->error($e->getMessage());

            $this->warn(
                'Transaksi dibatalkan. Database tidak menyimpan import parsial.'
            );

            return self::FAILURE;
        }
    }

    private function importLevel(
        string $file,
        string $table
    ): void {
        $this->line("Import {$table}...");

        $csv = $this->openCsv($file);

        $header = $this->readRow($csv);

        if ($header !== ['id', 'name']) {
            throw new RuntimeException(
                "Header CSV {$table} tidak valid."
            );
        }

        $batch = [];
        $count = 0;

        while (!$csv->eof()) {
            $row = $this->readRow($csv);

            if ($row === null) {
                continue;
            }

            if (count($row) !== 2) {
                throw new RuntimeException(
                    "Jumlah kolom tidak valid pada {$table}."
                );
            }

            [$id, $name] = array_map('trim', $row);

            if ($id === '' || $name === '') {
                throw new RuntimeException(
                    "ID atau nama kosong pada {$table}."
                );
            }

            $data = [
                'id' => $id,
                'name' => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if ($table !== 'provinces') {
                $parentId = $this->extractParentId($id);

                if ($parentId === null) {
                    throw new RuntimeException(
                        "Parent ID tidak dapat ditentukan dari {$id}."
                    );
                }

                $parentTable = match ($table) {
                    'regencies' => 'provinces',
                    'districts' => 'regencies',
                    'villages' => 'districts',
                };

                $parentColumn = match ($table) {
                    'regencies' => 'province_id',
                    'districts' => 'regency_id',
                    'villages' => 'district_id',
                };

                if (
                    !DB::table($parentTable)
                        ->where('id', $parentId)
                        ->exists()
                ) {
                    throw new RuntimeException(
                        "Parent {$parentId} tidak ditemukan untuk {$id}."
                    );
                }

                $data[$parentColumn] = $parentId;
            }

            $batch[] = $data;
            $count++;

            if (count($batch) >= self::CHUNK_SIZE) {
                DB::table($table)->insert($batch);
                $batch = [];
            }
        }

        if ($batch !== []) {
            DB::table($table)->insert($batch);
        }

        $this->info("  {$count} baris masuk ✓");
    }

    private function openCsv(string $file): SplFileObject
    {
        $csv = new SplFileObject($file);
        $csv->setFlags(
            SplFileObject::READ_CSV |
            SplFileObject::SKIP_EMPTY |
            SplFileObject::DROP_NEW_LINE
        );
        $csv->setCsvControl(',');

        return $csv;
    }

    private function readRow(SplFileObject $csv): ?array
    {
        $row = $csv->fgetcsv();

        if ($row === false || $row === [null]) {
            return null;
        }

        return $row;
    }

    private function extractParentId(string $id): ?string
    {
        $position = strrpos($id, '.');

        if ($position === false) {
            return null;
        }

        return substr($id, 0, $position);
    }

    private function showCounts(): void
    {
        foreach ([
            'provinces',
            'regencies',
            'districts',
            'villages',
        ] as $table) {
            $count = DB::table($table)->count();

            $this->line(
                sprintf('%-15s %s', $table, number_format($count))
            );
        }
    }
}