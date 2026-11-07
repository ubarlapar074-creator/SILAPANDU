<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dateTime('tanggal_akad')->nullable()->change();
            $table->time('jam_akad')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dateTime('tanggal_akad')->nullable(false)->change();
            $table->time('jam_akad')->nullable(false)->change();
        });
    }
};
