<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regencies', function (Blueprint $table) {
            $table->string('id', 10)->primary();
            $table->string('province_id', 10);
            $table->string('name');
            $table->timestamps();

            $table->index('province_id');

            $table->foreign('province_id')
                ->references('id')
                ->on('provinces')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regencies');
    }
};