<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    protected $fillable = ['user_id', 'telefono', 'distrito', 'direccion'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function pedidos(): HasMany { return $this->hasMany(Pedido::class); }
}
