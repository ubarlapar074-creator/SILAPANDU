<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->string('registration_number')->unique();
            $table->enum('registration_path', ['DALAM_DESA', 'ANDON']);
            $table->enum('status', ['DRAFT', 'READY', 'GENERATED', 'ARCHIVED'])->default('DRAFT');
            $table->date('marriage_date')->nullable();
            $table->string('marriage_dowry')->nullable();
            $table->text('marriage_location_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('registrations');
    }
};