<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            $columns = DB::select("PRAGMA table_info('witnesses')");
            $hasNotNull = false;
            foreach ($columns as $col) {
                if ($col->name === 'name' && (int) $col->notnull === 1) {
                    $hasNotNull = true;
                    break;
                }
            }
            if (!$hasNotNull) return;

            DB::statement('PRAGMA foreign_keys=off');
            Schema::create('witnesses_tmp', function ($table) {
                $table->id();
                $table->foreignId('registration_id')->constrained('registrations')->onDelete('cascade');
                $table->string('name')->nullable();
                $table->string('nik', 16)->nullable();
                $table->string('occupation')->nullable();
                $table->text('address')->nullable();
                $table->string('gender', 1)->nullable();
                $table->string('bin_binti')->nullable();
                $table->string('birth_place')->nullable();
                $table->date('birth_date')->nullable();
                $table->string('nationality')->nullable();
                $table->string('religion')->nullable();
                $table->string('marital_status')->nullable();
                $table->string('education')->nullable();
                $table->string('phone', 20)->nullable();
                $table->string('email')->nullable();
                $table->string('province_id')->nullable();
                $table->string('regency_id')->nullable();
                $table->string('district_id')->nullable();
                $table->string('village_id')->nullable();
                $table->timestamps();
            });
            DB::statement('INSERT INTO witnesses_tmp (id, registration_id, name, nik, occupation, address, gender, bin_binti, birth_place, birth_date, nationality, religion, marital_status, education, phone, email, province_id, regency_id, district_id, village_id, created_at, updated_at) SELECT id, registration_id, name, nik, occupation, address, gender, bin_binti, birth_place, birth_date, nationality, religion, marital_status, education, phone, email, province_id, regency_id, district_id, village_id, created_at, updated_at FROM witnesses');
            Schema::drop('witnesses');
            Schema::rename('witnesses_tmp', 'witnesses');
            DB::statement('PRAGMA foreign_keys=on');
            return;
        }

        DB::statement('ALTER TABLE witnesses MODIFY name VARCHAR(255) NULL');
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=off');
            Schema::create('witnesses_tmp', function ($table) {
                $table->id();
                $table->foreignId('registration_id')->constrained('registrations')->onDelete('cascade');
                $table->string('name');
                $table->string('nik', 16)->nullable();
                $table->string('occupation')->nullable();
                $table->text('address')->nullable();
                $table->string('gender', 1)->nullable();
                $table->string('bin_binti')->nullable();
                $table->string('birth_place')->nullable();
                $table->date('birth_date')->nullable();
                $table->string('nationality')->nullable();
                $table->string('religion')->nullable();
                $table->string('marital_status')->nullable();
                $table->string('education')->nullable();
                $table->string('phone', 20)->nullable();
                $table->string('email')->nullable();
                $table->string('province_id')->nullable();
                $table->string('regency_id')->nullable();
                $table->string('district_id')->nullable();
                $table->string('village_id')->nullable();
                $table->timestamps();
            });
            DB::statement('INSERT INTO witnesses_tmp (id, registration_id, name, nik, occupation, address, gender, bin_binti, birth_place, birth_date, nationality, religion, marital_status, education, phone, email, province_id, regency_id, district_id, village_id, created_at, updated_at) SELECT id, registration_id, name, nik, occupation, address, gender, bin_binti, birth_place, birth_date, nationality, religion, marital_status, education, phone, email, province_id, regency_id, district_id, village_id, created_at, updated_at FROM witnesses WHERE name IS NOT NULL');
            Schema::drop('witnesses');
            Schema::rename('witnesses_tmp', 'witnesses');
            DB::statement('PRAGMA foreign_keys=on');
            return;
        }

        DB::statement('ALTER TABLE witnesses MODIFY name VARCHAR(255) NOT NULL');
    }
};
