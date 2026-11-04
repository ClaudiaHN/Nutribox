<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\View\View;

class TiendaController extends Controller
{
    public function index(): View
    {
        // Los productos y sus imágenes se consultan desde MySQL
        // cada vez que el visitante abre o actualiza la tienda.
        $productos = Producto::query()
            ->with([
                'categoria',
                'imagenes' => fn ($consulta) => $consulta->orderBy('orden'),
            ])
            ->where('activo', true)
            ->orderBy('id')
            ->get();

        return view('tienda.index', compact('productos'));
    }
}
