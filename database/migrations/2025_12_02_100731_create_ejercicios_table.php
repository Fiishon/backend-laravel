<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ejercicios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subtema_id')->constrained()->onDelete('cascade');
            $table->string('titulo');
            $table->enum('dificultad', ['facil', 'medio', 'dificil']);
            $table->text('descripcion');
            $table->text('starter_code')->nullable();
            $table->text('solucion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ejercicios');
    }
};
