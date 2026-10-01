<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guardians', function (Blueprint $table) {
            if (!Schema::hasColumn('guardians', 'marital_status')) {
                $table->string('marital_status', 50)->nullable()->after('sebab_wali');
            }
        });
    }

    public function down(): void
    {
        Schema::table('guardians', function (Blueprint $table) {
            if (Schema::hasColumn('guardians', 'marital_status')) {
                $table->dropColumn('marital_status');
            }
        });
    }
};
