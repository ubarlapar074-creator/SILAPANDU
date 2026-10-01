<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('villages', function (Blueprint $table) {
            $table->string('id', 20)->primary();
            $table->string('district_id', 15);
            $table->string('name');
            $table->timestamps();

            $table->index('district_id');

            $table->foreign('district_id')
                ->references('id')
                ->on('districts')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('villages');
    }
};