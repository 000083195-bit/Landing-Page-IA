<?php
require_once __DIR__ . '/config.php';

// Si está logueado como user, mostrar landing con saludo
if (isLoggedIn() && $_SESSION['rol'] === 'user') {
    // Incluir la landing page normal con el saludo
    $nombre_usuario = $_SESSION['nombre'];
} elseif (isLoggedIn() && isAdmin()) {
    // Si es admin pero accede a landing, redirigir a dashboard
    redirect('/dashboard.php');
} else {
    // Si no está logueado, redirigir al login
    redirect('/login.php');
}
?>
