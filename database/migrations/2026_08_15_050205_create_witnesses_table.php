<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('witnesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('registrations')->onDelete('cascade');
            $table->string('name');
            $table->string('nik', 16)->nullable();
            $table->string('occupation')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('witnesses');
    }
};