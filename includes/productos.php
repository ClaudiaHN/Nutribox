<?php
/** Acceso a datos de productos (RN08: solo activos). */

function productosActivos(): array
{
    return db()->query(
        'SELECT * FROM producto WHERE activo = 1 ORDER BY id_producto'
    )->fetchAll();
}

function productoActivo(int $id): ?array
{
    $st = db()->prepare('SELECT * FROM producto WHERE id_producto = :id AND activo = 1');
    $st->execute(['id' => $id]);
    $p = $st->fetch();
    return $p ?: null;
}

/** Valor nutricional para mostrar: NULL → "No disponible" (RF08). */
function nutriente(?string $valor, string $unidad): string
{
    if ($valor === null || $valor === '') {
        return '<span class="text-muted">No disponible</span>';
    }
    return e(rtrim(rtrim(number_format((float) $valor, 2, '.', ''), '0'), '.')) . ' ' . $unidad;
}

/** Datos mínimos de los productos para el JavaScript de orientación (sin datos del usuario). */
function productosParaJs(array $productos): string
{
    $out = [];
    foreach ($productos as $p) {
        $out[] = [
            'id'        => (int) $p['id_producto'],
            'nombre'    => $p['nombre'],
            'objetivo'  => $p['objetivo'],
            'porcion'   => $p['porcion_referencia'],
            'proteinas' => $p['proteinas_g'] === null ? null : (float) $p['proteinas_g'],
            'energia'   => $p['energia_kcal'] === null ? null : (float) $p['energia_kcal'],
            'hierro'    => $p['hierro_mg'] === null ? null : (float) $p['hierro_mg'],
            'azucares'  => $p['azucares_g'] === null ? null : (float) $p['azucares_g'],
            'verificado'=> (bool) $p['datos_verificados'],
            'perfil'    => $p['perfil_sugerido'],
            'validada'  => (bool) $p['recomendacion_validada'],
            'alergenos' => $p['alergenos'],
            'fuente'    => $p['fuente_nutricional'],
        ];
    }
    return json_encode($out, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
}
