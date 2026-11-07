<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registration_parents', function (Blueprint $table) {
            // Tambahkan kolom agama setelah kolom pendidikan (atau sesuaikan)
            $table->string('agama', 50)->nullable()->after('pendidikan');
        });
    }

    public function down(): void
    {
        Schema::table('registration_parents', function (Blueprint $table) {
            $table->dropColumn('agama');
        });
    }
};
