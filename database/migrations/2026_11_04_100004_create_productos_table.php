<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->constrained('categorias')->restrictOnDelete();
            $table->string('sku', 30)->unique();
            $table->string('nombre', 100);
            $table->string('slug', 120)->unique();
            $table->string('objetivo', 40);
            $table->text('descripcion');
            $table->decimal('precio', 10, 2);
            $table->boolean('activo')->default(true);
            $table->boolean('disponible')->default(true);
            $table->string('porcion_referencia', 150);
            $table->decimal('proteinas_g', 7, 2)->nullable();
            $table->decimal('energia_kcal', 8, 2)->nullable();
            $table->decimal('hierro_mg', 7, 2)->nullable();
            $table->decimal('azucares_g', 7, 2)->nullable();
            $table->string('fuente_nutricional', 300)->nullable();
            $table->boolean('datos_verificados')->default(false);
            $table->string('alergenos', 250)->nullable();
            $table->timestamps();
            $table->index(['activo', 'disponible']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
