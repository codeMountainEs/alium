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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('referencia')->unique();
            $table->json('fotos')->nullable();
            $table->json('documentos')->nullable();
            $table->decimal('precio', 10, 2);
            $table->text('descripcion');
            $table->enum('estado', ['curso', 'finalizado', 'abierto'])->default('abierto');
            $table->date('fecha_prevista');
            $table->date('fecha_finalizado')->nullable();
            $table->string('provincia');
            $table->string('localidad');
            $table->string('direccion');
            $table->string('postal');
            $table->string('pais')->default('España');
            $table->decimal('latitud', 10, 8)->nullable();
            $table->decimal('longitud', 11, 8)->nullable();
            $table->string('telefono');
            $table->string('email');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
