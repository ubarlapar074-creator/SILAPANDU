<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VillageSeeder extends Seeder
{
    public function run(): void
    {
        $path = storage_path('app/kelurahan.csv');

        if (! file_exists($path)) {
            throw new \RuntimeException("File CSV tidak ditemukan: {$path}");
        }

        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new \RuntimeException("Gagal membuka file: {$path}");
        }

        fgetcsv($handle);

        $rows = [];
        $batchSize = 1000;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 2) {
                continue;
            }

            $id = trim($row[0]);

            $rows[] = [
                'id' => $id,
                'district_id' => substr($id, 0, 8),
                'name' => trim($row[1]),
            ];

            if (count($rows) >= $batchSize) {
                DB::table('villages')->upsert(
                    $rows,
                    ['id'],
                    ['district_id', 'name']
                );

                $rows = [];
            }
        }

        if ($rows) {
            DB::table('villages')->upsert(
                $rows,
                ['id'],
                ['district_id', 'name']
            );
        }

        fclose($handle);
    }
}