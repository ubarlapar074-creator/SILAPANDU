<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->unique();
            $table->string('registration_path')->default('BIASA');
            $table->string('service_type')->default('bedol');
            $table->dateTime('tanggal_akad');
            $table->time('jam_akad');
            $table->string('marriage_location_address')->nullable();
            $table->string('province_id', 4)->nullable();
            $table->string('regency_id', 8)->nullable();
            $table->string('district_id', 12)->nullable();
            $table->string('village_id', 16)->nullable();
            $table->string('marriage_dowry_display')->nullable();
            $table->tinyInteger('step')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};
