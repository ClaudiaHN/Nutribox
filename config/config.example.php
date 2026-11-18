<?php
/**
 * Nutribox — Configuración local (PLANTILLA).
 *
 * Copia este archivo como config.php y ajusta los valores.
 * config.php está en .gitignore: las credenciales NO se suben al repositorio (RNF07).
 * La carpeta config/ está protegida con .htaccess para que Apache no la sirva.
 */

return [
    // Conexión MySQL (XAMPP por defecto: usuario root sin contraseña)
    'db' => [
        'host'    => '127.0.0.1',
        'port'    => 3306,
        'name'    => 'nutribox',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf8mb4',
    ],

    // Único administrador (RF06). El hash se genera con:
    //   php -r "echo password_hash('TU_CLAVE', PASSWORD_DEFAULT);"
    'admin' => [
        'usuario' => 'admin',
        'hash'    => '$2y$10$REEMPLAZA_CON_TU_HASH',
    ],

    // Número de WhatsApp de Nutribox (solo dígitos, con código de país) para la confirmación.
    'whatsapp' => '51999999999',
];
