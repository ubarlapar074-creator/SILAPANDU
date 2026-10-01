<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_number_settings', function (Blueprint $table) {
            $table->string('kode_klasifikasi_arsip', 20)->default('470')->after('prefix_n6');
            $table->string('kode_desa_kecamatan', 30)->nullable()->after('kode_klasifikasi_arsip');
        });
        $existing = \Illuminate\Support\Facades\DB::table('document_number_settings')->where('id', 1)->first();
        if ($existing) {
            \Illuminate\Support\Facades\DB::table('document_number_settings')->where('id', 1)->update([
                'kode_klasifikasi_arsip' => $existing->prefix_n1 ?? '470',
                'kode_desa_kecamatan' => null,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('document_number_settings', function (Blueprint $table) {
            $table->dropColumn(['kode_klasifikasi_arsip', 'kode_desa_kecamatan']);
        });
    }
};
