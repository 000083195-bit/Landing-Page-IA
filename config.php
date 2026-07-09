<?php
// ============================================
// Configuración de la aplicación
// ============================================

session_start();

// Detectar si estamos en producción (Railway) o local
define('ENVIRONMENT', getenv('ENVIRONMENT') ?: 'development');

// Configuración de base de datos
if (ENVIRONMENT === 'production') {
    // Configuración para Railway
    define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
    define('DB_USER', getenv('DB_USER') ?: 'root');
    define('DB_PASS', getenv('DB_PASS') ?: '');
    define('DB_NAME', getenv('DB_NAME') ?: 'pesado_fallo');
    define('DB_PORT', getenv('DB_PORT') ?: 3306);
} else {
    // Configuración local
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'pesado_fallo');
    define('DB_PORT', 3306);
}

// Configuración general
define('APP_NAME', 'Pesado y al Fallo');
define('APP_URL', getenv('APP_URL') ?: 'http://localhost');
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
    return rtrim(APP_URL, '/');
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
