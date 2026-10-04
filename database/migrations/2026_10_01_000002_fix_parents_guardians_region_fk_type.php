<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'sqlite') {
            Schema::table('parents', function ($table) {
                $table->string('province_id', 10)->nullable()->change();
                $table->string('regency_id', 10)->nullable()->change();
                $table->string('district_id', 15)->nullable()->change();
                $table->string('village_id', 20)->nullable()->change();
            });
            Schema::table('guardians', function ($table) {
                $table->string('province_id', 10)->nullable()->change();
                $table->string('regency_id', 10)->nullable()->change();
                $table->string('district_id', 15)->nullable()->change();
                $table->string('village_id', 20)->nullable()->change();
            });
            return;
        }

        DB::statement('PRAGMA foreign_keys=off');

        DB::statement('CREATE TABLE _parents_new (id integer primary key autoincrement not null, catin_id integer not null, role varchar check (role in (\'AYAH\', \'IBU\')) not null, status varchar check (status in (\'HIDUP\', \'MENINGGAL\')) not null, name varchar not null, nik varchar, birth_place varchar, birth_date date, address text, province_id varchar(10), regency_id varchar(10), district_id varchar(15), village_id varchar(20), education varchar, occupation varchar, created_at datetime, updated_at datetime, bin_binti varchar, marital_status varchar, phone varchar, email varchar, foreign key(catin_id) references catins(id) on delete cascade, foreign key(province_id) references provinces(id) on delete set null, foreign key(regency_id) references regencies(id) on delete set null, foreign key(district_id) references districts(id) on delete set null, foreign key(village_id) references villages(id) on delete set null, unique(catin_id, role))');
        DB::statement('INSERT INTO _parents_new (id, catin_id, role, status, name, nik, birth_place, birth_date, address, province_id, regency_id, district_id, village_id, education, occupation, created_at, updated_at, bin_binti, marital_status, phone, email) SELECT id, catin_id, role, status, name, nik, birth_place, birth_date, address, CAST(province_id AS TEXT), CAST(regency_id AS TEXT), CAST(district_id AS TEXT), CAST(village_id AS TEXT), education, occupation, created_at, updated_at, bin_binti, marital_status, phone, email FROM parents');
        DB::statement('DROP TABLE parents');
        DB::statement('ALTER TABLE _parents_new RENAME TO parents');

        DB::statement('CREATE TABLE _guardians_new (id integer primary key autoincrement not null, registration_id integer not null, guardian_source varchar check (guardian_source in (\'PARENT\', \'OTHER\')) not null, parent_id integer, relationship varchar not null, name varchar, nik varchar, bin_binti varchar, birth_place varchar, birth_date date, occupation varchar, address text, province_id varchar(10), regency_id varchar(10), district_id varchar(15), village_id varchar(20), created_at datetime, updated_at datetime, sebab_wali varchar, marital_status varchar, foreign key(registration_id) references registrations(id) on delete cascade, foreign key(parent_id) references parents(id) on delete set null, foreign key(province_id) references provinces(id) on delete set null, foreign key(regency_id) references regencies(id) on delete set null, foreign key(district_id) references districts(id) on delete set null, foreign key(village_id) references villages(id) on delete set null, unique(registration_id))');
        DB::statement('INSERT INTO _guardians_new (id, registration_id, guardian_source, parent_id, relationship, name, nik, bin_binti, birth_place, birth_date, occupation, address, province_id, regency_id, district_id, village_id, created_at, updated_at, sebab_wali, marital_status) SELECT id, registration_id, guardian_source, parent_id, relationship, name, nik, bin_binti, birth_place, birth_date, occupation, address, CAST(province_id AS TEXT), CAST(regency_id AS TEXT), CAST(district_id AS TEXT), CAST(village_id AS TEXT), created_at, updated_at, sebab_wali, marital_status FROM guardians');
        DB::statement('DROP TABLE guardians');
        DB::statement('ALTER TABLE _guardians_new RENAME TO guardians');
        DB::statement('CREATE INDEX IF NOT EXISTS guardians_parent_id_index ON guardians(parent_id)');

        DB::statement('PRAGMA foreign_keys=on');
    }

    public function down(): void
    {
    }
};
