<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('document_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catin_id')->unique()->constrained('catins')->onDelete('cascade');
            $table->boolean('doc_ktp')->default(false);
            $table->boolean('doc_kk')->default(false);
            $table->boolean('doc_birth')->default(false);
            $table->boolean('doc_immunization')->default(false);
            $table->boolean('doc_photo')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('document_checklists');
    }
};