<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('catins', function (Blueprint $table) {
            $table->id();

            $table->foreignId('registration_id')
                ->constrained('registrations')
                ->onDelete('cascade');

            $table->enum('role', ['PRIA', 'WANITA']);

            // Identitas Catin
            $table->string('nik', 16);
            $table->string('name');
            $table->string('bin_binti')->nullable();


            // Kontak Catin
            $table->string('no_hp', 20)->nullable();
            $table->string('email')->nullable();

            // Data Kelahiran & Perkawinan
            $table->string('birth_place');
            $table->date('birth_date');
            $table->string('nationality')->nullable();
            $table->string('religion')->nullable();
            $table->string('marital_status');
            $table->string('occupation')->nullable();
            $table->text('address');

            // Relasi Wilayah
            // String ID menyesuaikan tabel wilayah
            $table->string('province_id');
            $table->string('regency_id');
            $table->string('district_id');
            $table->string('village_id');

            $table->foreign('province_id')
                ->references('id')
                ->on('provinces');

            $table->foreign('regency_id')
                ->references('id')
                ->on('regencies');

            $table->foreign('district_id')
                ->references('id')
                ->on('districts');

            $table->foreign('village_id')
                ->references('id')
                ->on('villages');

            // Satu PRIA dan satu WANITA per pendaftaran
            $table->unique(['registration_id', 'role']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catins');
    }
};