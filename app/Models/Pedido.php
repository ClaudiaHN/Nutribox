<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pedido extends Model
{
    protected $fillable = ['cliente_id', 'codigo', 'telefono_contacto', 'distrito_entrega', 'direccion_entrega', 'observaciones', 'acepta_datos', 'estado', 'total'];
    protected $casts = ['acepta_datos'=>'boolean', 'total'=>'decimal:2'];

    public function cliente(): BelongsTo { return $this->belongsTo(Cliente::class); }
    public function detalles(): HasMany { return $this->hasMany(PedidoDetalle::class); }
}
