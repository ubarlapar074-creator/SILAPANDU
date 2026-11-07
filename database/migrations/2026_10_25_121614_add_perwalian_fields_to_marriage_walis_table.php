<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('marriage_walis', function (Blueprint $table) {
            $table->date('tgl_meninggal')->nullable()->after('alamat');
            $table->string('tempat_meninggal')->nullable()->after('tgl_meninggal');
            $table->boolean('is_pengecualian_wali')->default(false)->after('tempat_meninggal');
            $table->string('alasan_wali_hakim')->nullable()->after('is_pengecualian_wali');
        });

        Schema::table('registration_parents', function (Blueprint $table) {
            $table->date('tgl_meninggal')->nullable()->after('alamat');
            $table->string('tempat_meninggal')->nullable()->after('tgl_meninggal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marriage_walis', function (Blueprint $table) {
            $table->dropColumn(['tgl_meninggal', 'tempat_meninggal', 'is_pengecualian_wali', 'alasan_wali_hakim']);
        });

        Schema::table('registration_parents', function (Blueprint $table) {
            $table->dropColumn(['tgl_meninggal', 'tempat_meninggal']);
        });
    }
};
