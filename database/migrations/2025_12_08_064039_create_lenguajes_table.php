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
        Schema::create('lenguajes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('icono')->nullable();
            $table->string('color')->default('#007AFF');
            $table->timestamps();
        });

        Schema::table('materias', function (Blueprint $table) {
            // Agregamos la columna. 'nullable' es importante por si ya tienes datos, no se rompa.
            $table->foreignId('lenguaje_id')
                  ->nullable() 
                  ->after('id')
                  ->constrained('lenguajes')
                  ->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materias', function (Blueprint $table) {
            $table->dropForeign(['lenguaje_id']);
            $table->dropColumn('lenguaje_id');
        });
        
        Schema::dropIfExists('lenguajes');
    }
};
