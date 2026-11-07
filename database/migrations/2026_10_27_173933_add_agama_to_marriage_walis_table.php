<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('marriage_walis', function (Blueprint $table) {
            // Tambahkan kolom agama setelah pekerjaan
            $table->string('agama', 50)->nullable()->after('pekerjaan');
        });
    }

    public function down(): void
    {
        Schema::table('marriage_walis', function (Blueprint $table) {
            $table->dropColumn('agama');
        });
    }
};
