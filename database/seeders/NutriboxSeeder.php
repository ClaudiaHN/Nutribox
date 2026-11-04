<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Seeder;

class NutriboxSeeder extends Seeder
{
    public function run(): void
    {
        $estandar = Categoria::updateOrCreate(
            ['slug' => 'estandar'],
            ['nombre' => 'Estándar', 'descripcion' => 'Cajas para el día a día', 'activo' => true, 'orden' => 1]
        );
        $premium = Categoria::updateOrCreate(
            ['slug' => 'premium'],
            ['nombre' => 'Premium', 'descripcion' => 'Selección especial', 'activo' => true, 'orden' => 2]
        );

        $cajas = [
            ['NB-PRO-001', 'Caja Proteína', 'caja-proteina', 'Proteína', 'Caja de snacks. Composición pendiente de confirmar.', 25.00, $estandar->id],
            ['NB-ENE-001', 'Caja Energía', 'caja-energia', 'Energía', 'Caja de snacks. Composición pendiente de confirmar.', 20.00, $estandar->id],
            ['NB-EQU-001', 'Caja Equilibrio', 'caja-equilibrio', 'Bajo en azúcar', 'Caja de snacks. Composición pendiente de confirmar.', 15.00, $estandar->id],
            ['NB-PRE-001', 'Caja Premium', 'caja-premium', 'Premium', 'Caja de snacks. Composición pendiente de confirmar.', 30.00, $premium->id],
        ];

        // firstOrCreate SOLO crea el producto si no existe.
        // Nunca sobreescribe precios ni descripciones modificados en MySQL.
        foreach ($cajas as [$sku, $nombre, $slug, $objetivo, $descripcion, $precio, $categoriaId]) {
            Producto::firstOrCreate(
                ['sku' => $sku],
                [
                    'categoria_id' => $categoriaId,
                    'nombre' => $nombre,
                    'slug' => $slug,
                    'objetivo' => $objetivo,
                    'descripcion' => $descripcion,
                    'precio' => $precio,
                    'porcion_referencia' => 'Porción pendiente de documentar',
                    'fuente_nutricional' => 'Datos nutricionales pendientes de verificar',
                    'datos_verificados' => false,
                    'activo' => true,
                    'disponible' => true,
                ]
            );
        }

        // No se inventan ingredientes, valores nutricionales ni recomendaciones médicas.
        // Cargar esos registros cuando existan fichas nutricionales verificadas.
    }
}
