<?php
// ============================================
// Configuración de la aplicación
// ============================================

session_start();

// Detectar si estamos en producción (Railway) o local
define('ENVIRONMENT', getenv('ENVIRONMENT') ?: 'development');

// Función helper para leer variables de entorno
function env($key, $default = null) {
    $value = getenv($key);
    if ($value !== false && $value !== '') {
        return $value;
    }
    return $default;
}

// Configuración de base de datos
define('DB_HOST', env('DB_HOST', env('MYSQL_HOST', env('RAILWAY_MYSQL_HOST', 'localhost'))));
define('DB_USER', env('DB_USER', env('MYSQL_USER', env('DB_USERNAME', 'root'))));
define('DB_PASS', env('DB_PASS', env('MYSQL_PASSWORD', env('DB_PASSWORD', ''))));
define('DB_NAME', env('DB_NAME', env('MYSQL_DATABASE', env('DATABASE_NAME', 'pesado_fallo'))));
define('DB_PORT', env('DB_PORT', env('MYSQL_PORT', env('DB_PORT', 3306))));

// Configuración general
define('APP_NAME', 'Pesado y al Fallo');
define('APP_URL', getenv('APP_URL') ?: '');
define('SESSION_TIMEOUT', 3600); // 1 hora en segundos

// Configuración de seguridad
define('PASSWORD_HASH_ALGO', PASSWORD_BCRYPT);
define('PASSWORD_HASH_COST', 10);

// Headers de seguridad
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');

// Funciones útiles
function isProduction() {
    return ENVIRONMENT === 'production';
}

function getBaseUrl() {
    if (!empty(APP_URL)) {
        return rtrim(APP_URL, '/');
    }

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $scheme . '://' . $host;
}

function redirect($path) {
    header('Location: ' . getBaseUrl() . '/' . ltrim($path, '/'));
    exit;
}

function isLoggedIn() {
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}

function isAdmin() {
    return isLoggedIn() && isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
}

function getCurrentUser() {
    return $_SESSION['usuario'] ?? null;
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('/login.php');
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('HTTP/1.1 403 Forbidden');
        die('Acceso denegado. Solo administradores.');
    }
}
?>
