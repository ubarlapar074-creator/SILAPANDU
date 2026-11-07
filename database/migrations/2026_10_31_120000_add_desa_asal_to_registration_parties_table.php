<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registration_parties', function (Blueprint $table) {
            $table->string('desa_asal', 150)->nullable()->comment('Desa/kelurahan asal calon pengantin; dipakai Smart Match pemilihan pihak utama dokumen.');
        });
    }

    public function down(): void
    {
        Schema::table('registration_parties', function (Blueprint $table) {
            $table->dropColumn('desa_asal');
        });
    }
};
