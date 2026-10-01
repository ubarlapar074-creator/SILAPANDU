<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registration_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('registration_id')
                ->constrained('registrations')
                ->cascadeOnDelete();
            $table->enum('document_type', ['N1', 'N2', 'N4', 'N5', 'N6']);
            $table->string('document_number')->unique();
            $table->date('issue_date')->nullable();
            $table->string('signed_by')->nullable();
            $table->string('signed_by_position')->nullable();
            $table->enum('status', ['DRAFT', 'PUBLISHED', 'ARCHIVED'])
                ->default('DRAFT');
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();

            $table->unique(['registration_id', 'document_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_documents');
    }
};
