<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recomendacion extends Model
{
    protected $table = 'recomendaciones';

    protected $fillable = [
        'producto_id',
        'tipo_objetivo',
        'titulo',
        'mensaje',
        'validada',
        'revisado_por',
        'fuente',
        'activo',
    ];

    protected $casts = [
        'validada' => 'boolean',
        'activo' => 'boolean',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
