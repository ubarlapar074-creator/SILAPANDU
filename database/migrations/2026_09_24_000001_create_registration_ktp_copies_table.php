<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registration_ktp_copies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('registrations')->cascadeOnDelete();
            $table->string('slot', 32);
            $table->string('path');
            $table->string('original_name')->nullable();
            $table->timestamps();
            $table->unique(['registration_id', 'slot']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_ktp_copies');
    }
};
