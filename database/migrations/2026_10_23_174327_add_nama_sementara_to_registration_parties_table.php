<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registration_parties', function (Blueprint $table) {
            $table->string('nama_sementara')->nullable()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('registration_parties', function (Blueprint $table) {
            $table->dropColumn('nama_sementara');
        });
    }
};
