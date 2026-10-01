<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('village_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('village_profiles', 'kua_head_name')) {
                $table->string('kua_head_name')->nullable()->after('service_officer_id');
            }
            if (!Schema::hasColumn('village_profiles', 'kua_head_nip')) {
                $table->string('kua_head_nip')->nullable()->after('kua_head_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('village_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('village_profiles', 'kua_head_name')) {
                $table->dropColumn('kua_head_name');
            }
            if (Schema::hasColumn('village_profiles', 'kua_head_nip')) {
                $table->dropColumn('kua_head_nip');
            }
        });
    }
};
