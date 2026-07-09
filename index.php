<?php
// ============================================
// Index.php - Maneja el flujo de autenticación
// ============================================

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

// Si está logueado
if (isLoggedIn()) {
    if (isAdmin()) {
        // Admin → Dashboard
        redirect('/dashboard.php');
    } else {
        // User → Landing page
        redirect('/landing.php');
    }
} else {
    // No logueado → Login
    redirect('/login.php');
}
?>
