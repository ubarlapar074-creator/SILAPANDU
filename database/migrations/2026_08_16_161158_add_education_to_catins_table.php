<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('catins', function (Blueprint $table) {
            $table->string('education')->nullable()->after('occupation');
        });
    }

    public function down(): void
    {
        Schema::table('catins', function (Blueprint $table) {
            $table->dropColumn('education');
        });
    }
};