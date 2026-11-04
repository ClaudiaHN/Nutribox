<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductoDetalle extends Model
{
    protected $fillable = ['producto_id', 'ingrediente', 'cantidad', 'unidad', 'observacion', 'orden'];
    protected $casts = ['cantidad'=>'decimal:2'];

    public function producto(): BelongsTo { return $this->belongsTo(Producto::class); }
}
