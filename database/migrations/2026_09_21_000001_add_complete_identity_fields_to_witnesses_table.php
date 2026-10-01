<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('witnesses', function (Blueprint $table) {
            $table->string('gender', 1)->nullable()->after('name');
            $table->string('bin_binti')->nullable()->after('gender');
            $table->string('birth_place')->nullable()->after('nik');
            $table->date('birth_date')->nullable()->after('birth_place');
            $table->string('nationality')->nullable()->after('birth_date');
            $table->string('religion')->nullable()->after('nationality');
            $table->string('marital_status')->nullable()->after('religion');
            $table->string('education')->nullable()->after('occupation');
            $table->string('phone', 20)->nullable()->after('education');
            $table->string('email')->nullable()->after('phone');
            $table->string('province_id')->nullable()->after('address');
            $table->string('regency_id')->nullable()->after('province_id');
            $table->string('district_id')->nullable()->after('regency_id');
            $table->string('village_id')->nullable()->after('district_id');
        });
    }

    public function down(): void
    {
        Schema::table('witnesses', function (Blueprint $table) {
            $table->dropColumn([
                'gender', 'bin_binti', 'birth_place', 'birth_date', 'nationality',
                'religion', 'marital_status', 'education', 'phone', 'email',
                'province_id', 'regency_id', 'district_id', 'village_id',
            ]);
        });
    }
};