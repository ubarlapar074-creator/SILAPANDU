<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registration_parents', function (Blueprint $table) {
            // Nama ayah kandung dari bapak calon (dipakai untuk penulisan bin/binti)
            $table->string('nama_ayah_kandung')->nullable()->after('nama');
        });
    }

    public function down(): void
    {
        Schema::table('registration_parents', function (Blueprint $table) {
            $table->dropColumn('nama_ayah_kandung');
        });
    }
};
