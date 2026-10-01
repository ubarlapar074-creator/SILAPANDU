<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('parents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catin_id')->constrained('catins')->onDelete('cascade');
            $table->enum('role', ['AYAH', 'IBU']);
            $table->enum('status', ['HIDUP', 'MENINGGAL']);
            $table->string('name');
            $table->string('nik', 16)->nullable();
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->text('address')->nullable();
            
            // Relasi Wilayah untuk Orang Tua (Menghindari teks bebas)
            $table->foreignId('province_id')->nullable()->constrained('provinces')->nullOnDelete();
            $table->foreignId('regency_id')->nullable()->constrained('regencies')->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained('districts')->nullOnDelete();
            $table->foreignId('village_id')->nullable()->constrained('villages')->nullOnDelete();

            $table->string('education')->nullable();
            $table->string('occupation')->nullable();
            
            // KUNCI INTEGRITAS: 1 Catin hanya boleh punya tepat 1 Ayah dan 1 Ibu
            $table->unique(['catin_id', 'role']);
            
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('parents');
    }
};