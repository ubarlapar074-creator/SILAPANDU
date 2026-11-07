<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wargas', function (Blueprint $table) {
            $table->string('pendidikan', 50)->nullable()->after('pekerjaan');
            $table->string('no_hp', 20)->nullable()->after('pendidikan');
            $table->string('email')->nullable()->after('no_hp');
        });
    }

    public function down(): void
    {
        Schema::table('wargas', function (Blueprint $table) {
            $table->dropColumn(['pendidikan', 'no_hp', 'email']);
        });
    }
};
