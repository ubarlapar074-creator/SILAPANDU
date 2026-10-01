<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->string('marriage_dowry_type', 20)->nullable()->after('marriage_dowry');
            $table->string('marriage_dowry_money', 100)->nullable()->after('marriage_dowry_type');
            $table->string('marriage_dowry_gold', 100)->nullable()->after('marriage_dowry_money');
            $table->string('marriage_dowry_other')->nullable()->after('marriage_dowry_gold');
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn([
                'marriage_dowry_type',
                'marriage_dowry_money',
                'marriage_dowry_gold',
                'marriage_dowry_other',
            ]);
        });
    }
};