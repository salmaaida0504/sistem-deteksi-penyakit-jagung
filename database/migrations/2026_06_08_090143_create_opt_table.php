<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opt', function (Blueprint $table) {
            $table->id('id_opt');
            $table->string('jenis_opt', 50);
            $table->string('nama_opt', 500);
            $table->string('penyebab', 500)->nullable();
            $table->string('gejala', 500)->nullable();
            $table->string('pencegahan', 500)->nullable();
            $table->string('penanganan', 500)->nullable();
            $table->string('foto_opt', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opt');
    }
};
