<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registration_documents', function (Blueprint $table) {
            $table->dropUnique('registration_documents_document_number_unique');
        });
    }

    public function down(): void
    {
        Schema::table('registration_documents', function (Blueprint $table) {
            $table->unique('document_number', 'registration_documents_document_number_unique');
        });
    }
};
