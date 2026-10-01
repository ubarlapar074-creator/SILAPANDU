<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvinceSeeder extends Seeder
{
    public function run(): void
    {
        $path = storage_path('app/provinsi.csv');

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

            $rows[] = [
                'id' => trim($row[0]),
                'name' => trim($row[1]),
            ];
        }

        fclose($handle);

        if ($rows) {
            DB::table('provinces')->upsert(
                $rows,
                ['id'],
                ['name']
            );
        }
    }
}