<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('registration_catin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('registrations')->cascadeOnDelete();
            $table->foreignId('catin_id')->constrained('catins')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['registration_id', 'catin_id']);
        });

        $catins = DB::table('catins')->get();
        foreach ($catins as $c) {
            try {
                DB::table('registration_catin')->insert([
                    'registration_id' => $c->registration_id,
                    'catin_id' => $c->id,
                    'created_at' => $c->created_at ?? now(),
                    'updated_at' => $c->updated_at ?? now(),
                ]);
            } catch (\Throwable $e) {
            }
        }

        DB::transaction(function () {
            $dups = DB::table('catins')
                ->select('nik', DB::raw('MIN(id) as keeper_id'), DB::raw('COUNT(*) as cnt'))
                ->groupBy('nik')
                ->having('cnt', '>', 1)
                ->get();

            foreach ($dups as $dup) {
                $keeperId = $dup->keeper_id;
                $ids = DB::table('catins')->where('nik', $dup->nik)->where('id', '!=', $keeperId)->pluck('id');
                foreach ($ids as $dupId) {
                    $regs = DB::table('registration_catin')->where('catin_id', $dupId)->get();
                    foreach ($regs as $row) {
                        $exists = DB::table('registration_catin')
                            ->where('registration_id', $row->registration_id)
                            ->where('catin_id', $keeperId)
                            ->exists();
                        if ($exists) {
                            DB::table('registration_catin')->where('id', $row->id)->delete();
                        } else {
                            DB::table('registration_catin')->where('id', $row->id)->update(['catin_id' => $keeperId]);
                        }
                    }
                    $keeperHasParents = DB::table('parents')->where('catin_id', $keeperId)->exists();
                    $dupParents = DB::table('parents')->where('catin_id', $dupId)->get();
                    foreach ($dupParents as $dp) {
                        $existsParent = DB::table('parents')->where('catin_id', $keeperId)->where('role', $dp->role)->exists();
                        if (!$existsParent && !$keeperHasParents) {
                            DB::table('parents')->insert([
                                'catin_id' => $keeperId,
                                'role' => $dp->role,
                                'status' => $dp->status,
                                'name' => $dp->name,
                                'nik' => $dp->nik,
                                'birth_place' => $dp->birth_place,
                                'birth_date' => $dp->birth_date,
                                'address' => $dp->address,
                                'education' => $dp->education,
                                'occupation' => $dp->occupation,
                                'province_id' => $dp->province_id,
                                'regency_id' => $dp->regency_id,
                                'district_id' => $dp->district_id,
                                'village_id' => $dp->village_id,
                                'created_at' => $dp->created_at,
                                'updated_at' => $dp->updated_at,
                            ]);
                        }
                    }
                    $keeperHasChecklist = DB::table('document_checklists')->where('catin_id', $keeperId)->exists();
                    $dupChecklist = DB::table('document_checklists')->where('catin_id', $dupId)->first();
                    if ($dupChecklist && !$keeperHasChecklist) {
                        DB::table('document_checklists')->insert([
                            'catin_id' => $keeperId,
                            'doc_ktp' => $dupChecklist->doc_ktp,
                            'doc_kk' => $dupChecklist->doc_kk,
                            'doc_birth' => $dupChecklist->doc_birth,
                            'doc_immunization' => $dupChecklist->doc_immunization,
                            'doc_photo' => $dupChecklist->doc_photo,
                            'created_at' => $dupChecklist->created_at,
                            'updated_at' => $dupChecklist->updated_at,
                        ]);
                    }
                    DB::table('parents')->where('catin_id', $dupId)->delete();
                    DB::table('document_checklists')->where('catin_id', $dupId)->delete();
                    DB::table('catins')->where('id', $dupId)->delete();
                }
            }
        });

        Schema::table('catins', function (Blueprint $table) {
            $table->string('nik', 16)->change();
        });

        try {
            Schema::table('catins', function (Blueprint $table) {
                $table->unique('nik', 'catins_nik_unique');
            });
        } catch (\Throwable $e) {
        }

        try {
            Schema::table('catins', function (Blueprint $table) {
                $table->unsignedBigInteger('registration_id')->nullable()->change();
            });
        } catch (\Throwable $e) {
            if (DB::connection()->getDriverName() === 'sqlite') {
                DB::statement('PRAGMA foreign_keys=off');
                DB::statement('CREATE TABLE _catins_new (id integer primary key autoincrement not null, registration_id integer, role varchar check (role in (\'PRIA\', \'WANITA\')) not null, nik varchar not null, name varchar not null, bin_binti varchar, no_hp varchar, email varchar, birth_place varchar not null, birth_date date not null, nationality varchar, religion varchar, marital_status varchar not null, occupation varchar, address text not null, province_id varchar not null, regency_id varchar not null, district_id varchar not null, village_id varchar not null, created_at datetime, updated_at datetime, education varchar, foreign key(registration_id) references registrations(id) on delete cascade, foreign key(province_id) references provinces(id), foreign key(regency_id) references regencies(id), foreign key(district_id) references districts(id), foreign key(village_id) references villages(id))');
                DB::statement('INSERT INTO _catins_new (id, registration_id, role, nik, name, bin_binti, no_hp, email, birth_place, birth_date, nationality, religion, marital_status, occupation, address, province_id, regency_id, district_id, village_id, created_at, updated_at, education) SELECT id, registration_id, role, nik, name, bin_binti, no_hp, email, birth_place, birth_date, nationality, religion, marital_status, occupation, address, province_id, regency_id, district_id, village_id, created_at, updated_at, education FROM catins');
                DB::statement('DROP TABLE catins');
                DB::statement('ALTER TABLE _catins_new RENAME TO catins');
                DB::statement('CREATE UNIQUE INDEX catins_nik_unique ON catins(nik)');
                DB::statement('CREATE UNIQUE INDEX catins_registration_id_role_unique ON catins(registration_id, role)');
                DB::statement('PRAGMA foreign_keys=on');
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_catin');
        try {
            Schema::table('catins', function (Blueprint $table) {
                $table->dropUnique('catins_nik_unique');
            });
        } catch (\Throwable $e) {
        }
    }
};
