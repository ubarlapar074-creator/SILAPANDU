<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegencySeeder extends Seeder
{
    public function run(): void
    {
        $path = storage_path('app/kabupaten_kota.csv');

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
                'province_id' => substr($id, 0, 2),
                'name' => trim($row[1]),
            ];
        }

        fclose($handle);

        if ($rows) {
            DB::table('regencies')->upsert(
                $rows,
                ['id'],
                ['province_id', 'name']
            );
        }
    }
}