<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registration_parents', function (Blueprint $table) {
            // Status perkawinan orang tua: Kawin / Cerai Mati / Cerai Hidup
            $table->string('status_kawin_ortu', 50)->nullable()->after('status_hubungan');
        });
    }

    public function down(): void
    {
        Schema::table('registration_parents', function (Blueprint $table) {
            $table->dropColumn('status_kawin_ortu');
        });
    }
};
