<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('parents', function (Blueprint $table) {
            $table->string('bin_binti')->nullable()->after('name');
            $table->string('marital_status')->nullable()->after('occupation');
            $table->string('phone', 20)->nullable()->after('marital_status');
            $table->string('email')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('parents', function (Blueprint $table) {
            $table->dropColumn(['bin_binti', 'marital_status', 'phone', 'email']);
        });
    }
};
