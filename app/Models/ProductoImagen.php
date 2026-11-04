<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductoImagen extends Model
{
    // Nombre exacto de la tabla en MySQL
    protected $table = 'producto_imagenes';

    protected $fillable = [
        'producto_id',
        'ruta',
        'texto_alternativo',
        'es_principal',
        'orden',
    ];

    protected $casts = [
        'es_principal' => 'boolean',
    ];

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
