<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DistrictSeeder extends Seeder
{
    public function run(): void
    {
        $path = storage_path('app/kecamatan.csv');

        if (! file_exists($path)) {
            throw new \RuntimeException("File CSV tidak ditemukan: {$path}");
        }

        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new \RuntimeException("Gagal membuka file: {$path}");
        }

        fgetcsv($handle);

        $rows = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 2) {
                continue;
            }

            $id = trim($row[0]);

            $rows[] = [
                'id' => $id,
                'regency_id' => substr($id, 0, 5),
                'name' => trim($row[1]),
            ];
        }

        fclose($handle);

        if ($rows) {
            DB::table('districts')->upsert(
                $rows,
                ['id'],
                ['regency_id', 'name']
            );
        }
    }
}