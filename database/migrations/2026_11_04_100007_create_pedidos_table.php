<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->restrictOnDelete();
            $table->string('codigo', 25)->unique()->nullable();
            $table->string('telefono_contacto', 9);
            $table->string('distrito_entrega', 60);
            $table->string('direccion_entrega', 200);
            $table->string('observaciones', 300)->nullable();
            $table->boolean('acepta_datos');
            $table->string('estado', 20)->default('Pendiente');
            $table->decimal('total', 10, 2);
            $table->timestamps();
            $table->index(['estado', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
