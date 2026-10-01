<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();

            $table->foreignId('registration_id')
                ->unique()
                ->constrained('registrations')
                ->cascadeOnDelete();

            $table->enum('guardian_source', [
                'PARENT',
                'OTHER',
            ]);

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('parents')
                ->nullOnDelete();

            $table->string('relationship');

            $table->string('name')->nullable();
            $table->string('nik', 16)->nullable();
            $table->string('bin_binti')->nullable();
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('occupation')->nullable();
            $table->text('address')->nullable();

            // Relasi Wilayah untuk Wali Lain (Menghindari teks bebas)
            $table->foreignId('province_id')->nullable()->constrained('provinces')->nullOnDelete();
            $table->foreignId('regency_id')->nullable()->constrained('regencies')->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained('districts')->nullOnDelete();
            $table->foreignId('village_id')->nullable()->constrained('villages')->nullOnDelete();

            $table->timestamps();

            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guardians');
    }
};