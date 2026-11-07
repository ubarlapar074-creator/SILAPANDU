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
        Schema::table('registration_parties', function (Blueprint $table) {
            $table->string('rt', 3)->nullable()->after('desa_asal');
            $table->string('rw', 3)->nullable()->after('rt');
        });

        Schema::table('registration_parents', function (Blueprint $table) {
            $table->string('rt', 3)->nullable()->after('alamat');
            $table->string('rw', 3)->nullable()->after('rt');
        });

        Schema::table('marriage_walis', function (Blueprint $table) {
            $table->string('rt', 3)->nullable()->after('alamat');
            $table->string('rw', 3)->nullable()->after('rt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registration_parties', function (Blueprint $table) {
            $table->dropColumn(['rt', 'rw']);
        });

        Schema::table('registration_parents', function (Blueprint $table) {
            $table->dropColumn(['rt', 'rw']);
        });

        Schema::table('marriage_walis', function (Blueprint $table) {
            $table->dropColumn(['rt', 'rw']);
        });
    }
};
