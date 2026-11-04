<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    protected $fillable = ['categoria_id', 'sku', 'nombre', 'slug', 'objetivo', 'descripcion', 'precio', 'activo', 'disponible', 'porcion_referencia', 'proteinas_g', 'energia_kcal', 'hierro_mg', 'azucares_g', 'fuente_nutricional', 'datos_verificados', 'alergenos'];
    protected $casts = ['precio'=>'decimal:2', 'activo'=>'boolean', 'disponible'=>'boolean', 'datos_verificados'=>'boolean', 'proteinas_g'=>'decimal:2', 'energia_kcal'=>'decimal:2', 'hierro_mg'=>'decimal:2', 'azucares_g'=>'decimal:2'];

    public function categoria(): BelongsTo { return $this->belongsTo(Categoria::class); }
    public function detalles(): HasMany { return $this->hasMany(ProductoDetalle::class); }
    public function imagenes(): HasMany { return $this->hasMany(ProductoImagen::class); }
    public function recomendaciones(): HasMany { return $this->hasMany(Recomendacion::class); }
    public function pedidoDetalles(): HasMany { return $this->hasMany(PedidoDetalle::class); }
}
