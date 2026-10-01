<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=off');
            $hasTable = Schema::hasTable('n6_documents');
            if ($hasTable) {
                $cols = DB::select("PRAGMA table_info(n6_documents)");
                $colNames = collect($cols)->pluck('name');
                if ($colNames->contains('registration_id')) {
                    DB::statement('CREATE TABLE _n6_documents_new AS SELECT * FROM n6_documents WHERE 0');
                    try { DB::statement('DROP TABLE _n6_documents_new'); } catch (\Throwable $e) {}
                    Schema::dropIfExists('_n6_documents_new');
                    DB::statement('CREATE TABLE _n6_documents_new (id integer primary key autoincrement not null, registration_id integer, deceased_name varchar not null, deceased_bin_binti varchar not null, deceased_nik varchar not null, deceased_birth_place varchar not null, deceased_birth_date date not null, deceased_nationality varchar, deceased_religion varchar, deceased_occupation varchar, deceased_address text not null, death_date date not null, death_place varchar, document_number varchar not null, issue_date date not null, signed_by varchar not null, signed_by_position varchar not null, status varchar not null, created_by integer, created_at datetime, updated_at datetime, foreign key(registration_id) references registrations(id) on delete set null, foreign key(created_by) references users(id) on delete set null)');
                    DB::statement('INSERT INTO _n6_documents_new SELECT id, registration_id, deceased_name, deceased_bin_binti, deceased_nik, deceased_birth_place, deceased_birth_date, deceased_nationality, deceased_religion, deceased_occupation, deceased_address, death_date, death_place, document_number, issue_date, signed_by, signed_by_position, status, created_by, created_at, updated_at FROM n6_documents');
                    DB::statement('DROP TABLE n6_documents');
                    DB::statement('ALTER TABLE _n6_documents_new RENAME TO n6_documents');
                    DB::statement('CREATE UNIQUE INDEX n6_documents_document_number_unique ON n6_documents(document_number)');
                }
            }
            DB::statement('PRAGMA foreign_keys=on');
            return;
        }
        Schema::table('n6_documents', function (Blueprint $table) {
            $table->dropForeign(['registration_id']);
            $table->foreignId('registration_id')->nullable()->change();
        });
        Schema::table('n6_documents', function (Blueprint $table) {
            $table->foreign('registration_id')->references('id')->on('registrations')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('n6_documents', function (Blueprint $table) {
            $table->dropForeign(['registration_id']);
            $table->foreignId('registration_id')->nullable(false)->change();
        });

        Schema::table('n6_documents', function (Blueprint $table) {
            $table->foreign('registration_id')
                ->references('id')
                ->on('registrations')
                ->cascadeOnDelete();
        });
    }
};
