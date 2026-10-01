<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('document_archives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')->constrained('registrations')->onDelete('cascade');
            $table->string('document_type'); // Contoh: N1, N2, N3, N4, N5, N6
            $table->string('file_path');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('document_archives');
    }
};