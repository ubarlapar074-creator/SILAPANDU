<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('n6_documents', function (Blueprint $table) {
            $table->id();

            // Relasi ke Registration
            $table->foreignId('registration_id')
                ->constrained('registrations')
                ->cascadeOnDelete();

            // === BAGIAN A - DATA ALMARHUM (AYAH CATIN WANITA) ===
            $table->string('deceased_name');
            $table->string('deceased_bin_binti');
            $table->string('deceased_nik', 16);
            $table->string('deceased_birth_place');
            $table->date('deceased_birth_date');
            $table->string('deceased_nationality')->nullable();
            $table->string('deceased_religion')->nullable();
            $table->string('deceased_occupation')->nullable();
            $table->text('deceased_address');

            // === INFORMASI KEMATIAN ===
            $table->date('death_date');
            $table->string('death_place')->nullable();

            // === INFORMASI SURAT ===
            $table->string('document_number')->unique();
            $table->date('issue_date');
            $table->string('signed_by');
            $table->string('signed_by_position');

            // === STATUS ===
            $table->enum('status', ['DRAFT', 'PUBLISHED', 'ARCHIVED'])
                ->default('DRAFT');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('n6_documents');
    }
};