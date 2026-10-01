<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('districts', function (Blueprint $table) {
            $table->string('id', 15)->primary();
            $table->string('regency_id', 10);
            $table->string('name');
            $table->timestamps();

            $table->index('regency_id');

            $table->foreign('regency_id')
                ->references('id')
                ->on('regencies')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};