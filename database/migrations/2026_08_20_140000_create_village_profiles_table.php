<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('village_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('village_name')->nullable();
            $table->string('district_name')->nullable();
            $table->string('regency_name')->nullable();
            $table->string('office_address')->nullable();
            $table->string('village_head_name')->nullable();
            $table->string('village_head_id')->nullable();
            $table->string('service_officer_name')->nullable();
            $table->string('service_officer_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('village_profiles');
    }
};
