<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registration_document_checklists', function (Blueprint $table) {
            $table->id();

            $table->foreignId('registration_id')
                ->unique()
                ->constrained('registrations')
                ->onDelete('cascade');

            /*
            |--------------------------------------------------------------------------
            | DOKUMEN DASAR PENDAFTARAN
            |--------------------------------------------------------------------------
            */

            $table->boolean('doc_n1_n4')->default(false);

            /*
            |--------------------------------------------------------------------------
            | DOKUMEN KEMATIAN
            |--------------------------------------------------------------------------
            |
            | Otomatis diperlukan apabila terdapat orang tua/wali
            | yang berstatus MENINGGAL.
            |
            */

            $table->boolean('doc_death')->default(false);

            /*
            |--------------------------------------------------------------------------
            | DOKUMEN KONDISIONAL
            |--------------------------------------------------------------------------
            */

            $table->boolean('doc_n5')->default(false);
            $table->boolean('doc_n6')->default(false);

            /*
            |--------------------------------------------------------------------------
            | STATUS PEMBUATAN DOKUMEN
            |--------------------------------------------------------------------------
            |
            | PENDING   = diperlukan tetapi belum dibuat
            | GENERATED = sudah dibuat
            | NULL      = tidak diperlukan
            |
            */

            $table->string('n5_status')->nullable();
            $table->string('n6_status')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_document_checklists');
    }
};