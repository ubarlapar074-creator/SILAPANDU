<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $hasTable = Schema::hasTable('registrations');
        if (!$hasTable) return;

        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            try {
                DB::statement('PRAGMA foreign_keys=off');
            } catch (\Throwable $e) {}

            $columns = DB::select("PRAGMA table_info('registrations')");
            $hasColumn = collect($columns)->pluck('name')->contains('registration_path');
            $needsRebuild = false;
            foreach ($columns as $col) {
                if ($col->name === 'registration_path' && str_contains(strtoupper($col->type ?? ''), 'ENUM')) {
                    $needsRebuild = true;
                }
            }
            $typeIsEnum = false;
            $sql = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name='registrations'");
            $createSql = $sql[0]->sql ?? '';
            if (str_contains($createSql, 'CHECK') && str_contains($createSql, 'registration_path')) {
                $needsRebuild = true;
            }

            if (!$needsRebuild) {
                try {
                    DB::table('registrations')->where('registration_path', 'DALAM_DESA')->update(['registration_path' => 'NIKAH_BEDOL']);
                } catch (\Throwable $e) {
                    $needsRebuild = true;
                }
                try {
                    DB::table('registrations')->where('registration_path', 'ANDON')->update(['registration_path' => 'PEMBUATAN_ANDON_NIKAH']);
                } catch (\Throwable $e) {
                    $needsRebuild = true;
                }
            }

            if ($needsRebuild) {
                $tmpExists = Schema::hasTable('registrations_tmp_backup');
                if ($tmpExists) Schema::dropIfExists('registrations_tmp_backup');
                Schema::create('registrations_tmp_backup', function (Blueprint $table) {
                    $table->id();
                    $table->string('registration_number')->unique();
                    $table->string('registration_path');
                    $table->string('status')->default('DRAFT');
                    $table->date('marriage_date')->nullable();
                    $table->time('marriage_time')->nullable();
                    $table->string('marriage_dowry')->nullable();
                    $table->string('marriage_dowry_type', 20)->nullable();
                    $table->string('marriage_dowry_money', 100)->nullable();
                    $table->string('marriage_dowry_gold', 100)->nullable();
                    $table->string('marriage_dowry_other')->nullable();
                    $table->text('marriage_location_address')->nullable();
                    $table->timestamps();
                });

                $existing = DB::table('registrations')->get();
                foreach ($existing as $row) {
                    $arr = (array) $row;
                    if (($arr['registration_path'] ?? null) === 'DALAM_DESA') $arr['registration_path'] = 'NIKAH_BEDOL';
                    if (($arr['registration_path'] ?? null) === 'ANDON') $arr['registration_path'] = 'PEMBUATAN_ANDON_NIKAH';
                    $valid = ['NIKAH_BEDOL', 'NIKAH_KANTOR', 'PEMBUATAN_ANDON_NIKAH'];
                    if (!in_array($arr['registration_path'], $valid, true)) $arr['registration_path'] = 'NIKAH_BEDOL';
                    DB::table('registrations_tmp_backup')->insert($arr);
                }

                Schema::dropIfExists('registrations');
                Schema::create('registrations', function (Blueprint $table) {
                    $table->id();
                    $table->string('registration_number')->unique();
                    $table->string('registration_path');
                    $table->string('status')->default('DRAFT');
                    $table->date('marriage_date')->nullable();
                    $table->time('marriage_time')->nullable();
                    $table->string('marriage_dowry')->nullable();
                    $table->string('marriage_dowry_type', 20)->nullable();
                    $table->string('marriage_dowry_money', 100)->nullable();
                    $table->string('marriage_dowry_gold', 100)->nullable();
                    $table->string('marriage_dowry_other')->nullable();
                    $table->text('marriage_location_address')->nullable();
                    $table->timestamps();
                });

                $tmpRows = DB::table('registrations_tmp_backup')->get();
                foreach ($tmpRows as $row) {
                    DB::table('registrations')->insert((array) $row);
                }
                Schema::dropIfExists('registrations_tmp_backup');
            } else {
                $regs = DB::table('registrations')->select('id', 'registration_path')->get();
                $valid = ['NIKAH_BEDOL', 'NIKAH_KANTOR', 'PEMBUATAN_ANDON_NIKAH'];
                foreach ($regs as $r) {
                    if (!in_array($r->registration_path, $valid, true)) {
                        DB::table('registrations')->where('id', $r->id)->update(['registration_path' => 'NIKAH_BEDOL']);
                    }
                }

                $tmpExists = Schema::hasTable('registrations_tmp_backup');
                if (!$tmpExists) {
                    Schema::create('registrations_tmp_backup', function (Blueprint $table) {
                        $table->id();
                        $table->string('registration_number')->unique();
                        $table->string('registration_path');
                        $table->string('status')->default('DRAFT');
                        $table->date('marriage_date')->nullable();
                        $table->time('marriage_time')->nullable();
                        $table->string('marriage_dowry')->nullable();
                        $table->string('marriage_dowry_type', 20)->nullable();
                        $table->string('marriage_dowry_money', 100)->nullable();
                        $table->string('marriage_dowry_gold', 100)->nullable();
                        $table->string('marriage_dowry_other')->nullable();
                        $table->text('marriage_location_address')->nullable();
                        $table->timestamps();
                    });
                }

                $existing = DB::table('registrations')->get();
                foreach ($existing as $row) {
                    $exists = DB::table('registrations_tmp_backup')->where('id', $row->id)->exists();
                    if (!$exists) {
                        DB::table('registrations_tmp_backup')->insert((array) $row);
                    }
                }

                Schema::dropIfExists('registrations');
                Schema::create('registrations', function (Blueprint $table) {
                    $table->id();
                    $table->string('registration_number')->unique();
                    $table->string('registration_path');
                    $table->string('status')->default('DRAFT');
                    $table->date('marriage_date')->nullable();
                    $table->time('marriage_time')->nullable();
                    $table->string('marriage_dowry')->nullable();
                    $table->string('marriage_dowry_type', 20)->nullable();
                    $table->string('marriage_dowry_money', 100)->nullable();
                    $table->string('marriage_dowry_gold', 100)->nullable();
                    $table->string('marriage_dowry_other')->nullable();
                    $table->text('marriage_location_address')->nullable();
                    $table->timestamps();
                });

                $tmpRows = DB::table('registrations_tmp_backup')->get();
                foreach ($tmpRows as $row) {
                    DB::table('registrations')->insert((array) $row);
                }
                Schema::dropIfExists('registrations_tmp_backup');
            }

            try {
                DB::statement('PRAGMA foreign_keys=on');
            } catch (\Throwable $e) {}
        } else {
            DB::table('registrations')->where('registration_path', 'DALAM_DESA')->update(['registration_path' => 'NIKAH_BEDOL']);
            DB::table('registrations')->where('registration_path', 'ANDON')->update(['registration_path' => 'PEMBUATAN_ANDON_NIKAH']);

            Schema::table('registrations', function (Blueprint $table) {
                $table->string('registration_path')->change();
            });
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'sqlite') {
            DB::table('registrations')->where('registration_path', 'NIKAH_BEDOL')->update(['registration_path' => 'DALAM_DESA']);
            DB::table('registrations')->where('registration_path', 'NIKAH_KANTOR')->update(['registration_path' => 'DALAM_DESA']);
            DB::table('registrations')->where('registration_path', 'PEMBUATAN_ANDON_NIKAH')->update(['registration_path' => 'ANDON']);
        } else {
            DB::table('registrations')->where('registration_path', 'NIKAH_BEDOL')->update(['registration_path' => 'DALAM_DESA']);
            DB::table('registrations')->where('registration_path', 'NIKAH_KANTOR')->update(['registration_path' => 'DALAM_DESA']);
            DB::table('registrations')->where('registration_path', 'PEMBUATAN_ANDON_NIKAH')->update(['registration_path' => 'ANDON']);
        }
    }
};
