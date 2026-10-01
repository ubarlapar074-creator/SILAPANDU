<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guardians', function (Blueprint $table) {
            if (!Schema::hasColumn('guardians', 'sebab_wali')) {
                $table->string('sebab_wali', 500)->nullable()->after('relationship');
            }
        });
    }

    public function down(): void
    {
        Schema::table('guardians', function (Blueprint $table) {
            if (Schema::hasColumn('guardians', 'sebab_wali')) {
                $table->dropColumn('sebab_wali');
            }
        });
    }
};
