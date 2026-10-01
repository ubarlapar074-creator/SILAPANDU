<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('backup_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('backup_settings', 'reminder_snooze_days')) {
                $table->integer('reminder_snooze_days')->nullable()->after('reminder_dismissed_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('backup_settings', function (Blueprint $table) {
            if (Schema::hasColumn('backup_settings', 'reminder_snooze_days')) {
                $table->dropColumn('reminder_snooze_days');
            }
        });
    }
};
