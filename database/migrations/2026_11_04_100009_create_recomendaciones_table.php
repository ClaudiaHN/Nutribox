<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recomendaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnDelete();
            $table->string('tipo_objetivo', 50);
            $table->string('titulo', 120);
            $table->text('mensaje');
            $table->boolean('validada')->default(false);
            $table->string('revisado_por', 120)->nullable();
            $table->string('fuente', 300)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->index(['tipo_objetivo', 'validada', 'activo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recomendaciones');
    }
};
