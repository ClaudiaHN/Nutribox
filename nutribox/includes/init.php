<?php
/**
 * Nutribox — Arranque común de todas las páginas.
 * Carga configuración, abre la conexión PDO, inicia sesión y define helpers.
 */
declare(strict_types=1);

define('NB_ROOT', dirname(__DIR__));

$configFile = NB_ROOT . '/config/config.php';
if (!is_file($configFile)) {
    http_response_code(500);
    exit('Falta config/config.php. Copia config/config.example.php y completa los datos.');
}
$CONFIG = require $configFile;

/* ---------- Sesión (cookies seguras razonables para entorno local) ---------- */
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

/* ---------- Conexión PDO (consultas preparadas, RNF03) ---------- */
function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        global $CONFIG;
        $c   = $CONFIG['db'];
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $c['host'], $c['port'], $c['name'], $c['charset']);
        try {
            $pdo = new PDO($dsn, $c['user'], $c['pass'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            exit('No se pudo conectar con la base de datos. Revisa config/config.php e importa sql/nutribox.sql en phpMyAdmin.');
        }
    }
    return $pdo;
}

/* ---------- Helpers de salida ---------- */
function e(?string $v): string
{
    return htmlspecialchars((string) $v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function soles(float|string $n): string
{
    return 'S/ ' . number_format((float) $n, 2, '.', ',');
}

function codigoPedido(int $id): string
{
    return 'NB-' . str_pad((string) $id, 4, '0', STR_PAD_LEFT);
}

function redirigir(string $url): never
{
    header('Location: ' . $url);
    exit;
}

/* ---------- CSRF (RNF03) ---------- */
function csrfToken(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrfCampo(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrfToken()) . '">';
}

function csrfValido(): bool
{
    $t = $_POST['csrf'] ?? '';
    return is_string($t) && $t !== '' && hash_equals(csrfToken(), $t);
}

/* ---------- Estados de pedido (RN04, RN05) ---------- */
const ESTADOS = ['Pendiente', 'Confirmado', 'Entregado', 'Cancelado'];

const TRANSICIONES = [
    'Pendiente'  => ['Confirmado', 'Cancelado'],
    'Confirmado' => ['Entregado', 'Cancelado'],
    'Entregado'  => [],
    'Cancelado'  => [],
];

function transicionValida(string $actual, string $nuevo): bool
{
    return in_array($nuevo, TRANSICIONES[$actual] ?? [], true);
}

/* ---------- Validaciones del pedido (RNF02, RN01) ---------- */
function telefonoValido(string $t): bool
{
    return (bool) preg_match('/^9[0-9]{8}$/', $t);
}

function correoValido(string $c): bool
{
    return filter_var($c, FILTER_VALIDATE_EMAIL) !== false && strlen($c) <= 120;
}

function cantidadValida(mixed $q): bool
{
    return is_string($q) && preg_match('/^(10|[1-9])$/', $q) === 1;
}
