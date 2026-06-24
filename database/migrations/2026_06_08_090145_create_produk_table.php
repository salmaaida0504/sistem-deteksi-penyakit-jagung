<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id('id_produk');
            $table->foreignId('id_jenis')->constrained('jenis_produk', 'id_jenis')->onDelete('cascade');
            $table->string('nama_produk', 300);
            $table->text('deskripsi_produk')->nullable();
            $table->string('foto_produk', 255)->nullable();
            $table->string('manfaat', 500)->nullable();
            $table->string('keunggulan', 500)->nullable();
            $table->string('dosis_penggunaan', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
