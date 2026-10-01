<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_number_settings', function (Blueprint $table) {
            $table->id();
            $table->string('prefix_n1', 10)->default('470');
            $table->string('prefix_n2', 10)->default('471');
            $table->string('prefix_n4', 10)->default('474');
            $table->string('prefix_n5', 10)->default('475');
            $table->string('prefix_n6', 10)->default('472');
            $table->string('format')->default('{prefix}/{seq:3}/{type}/{roman}/{year}');
            $table->string('default_signed_by')->nullable();
            $table->string('default_position')->nullable();
            $table->json('defaults_json')->nullable();
            $table->timestamps();
        });
        \Illuminate\Support\Facades\DB::table('document_number_settings')->insert([
            'id' => 1,
            'prefix_n1' => '470',
            'prefix_n2' => '471',
            'prefix_n4' => '474',
            'prefix_n5' => '475',
            'prefix_n6' => '472',
            'format' => '{prefix}/{seq:3}/{type}/{roman}/{year}',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('document_number_settings');
    }
};
