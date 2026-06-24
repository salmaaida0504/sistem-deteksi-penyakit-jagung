<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_opt')->nullable()->constrained('opt', 'id_opt')->onDelete('set null');
            $table->string('image_path');
            $table->string('predicted_class', 100);
            $table->decimal('confidence', 5, 2);
            $table->json('all_predictions');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detections');
    }
};
