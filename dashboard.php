<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

// Requiere que sea admin
requireAdmin();

global $db;

// Obtener estadísticas
$totalVentas = $db->query("SELECT SUM(total) as total, COUNT(*) as cantidad FROM ventas WHERE fecha = CURDATE()")->fetch_assoc();
$ventasRecientes = $db->query("SELECT * FROM ventas ORDER BY fecha_creacion DESC LIMIT 5");
$usuarios = $db->query("SELECT * FROM usuarios");
$ventasHoy = $totalVentas['total'] ?? 0;
$cantidadVentas = $totalVentas['cantidad'] ?? 0;

// Procesar acciones
$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accion'])) {
        if ($_POST['accion'] === 'agregar_usuario') {
            $nombre = $_POST['nombre'] ?? '';
            $email = $_POST['email'] ?? '';
            $contraseña = password_hash($_POST['contraseña'] ?? '', PASSWORD_BCRYPT, ['cost' => 10]);
            $rol = $_POST['rol'] ?? 'user';

            if (!empty($nombre) && !empty($email)) {
                if ($db->execute("INSERT INTO usuarios (nombre, email, contraseña, rol) VALUES (?, ?, ?, ?)", 
                    [$nombre, $email, $contraseña, $rol])) {
                    $mensaje = 'Usuario agregado exitosamente';
                    $tipo_mensaje = 'success';
                } else {
                    $mensaje = 'Error al agregar usuario';
                    $tipo_mensaje = 'error';
                }
            }
        } elseif ($_POST['accion'] === 'actualizar_usuario') {
            $usuario_id = $_POST['usuario_id'] ?? '';
            $nombre = $_POST['nombre'] ?? '';
            $email = $_POST['email'] ?? '';
            $rol = $_POST['rol'] ?? 'user';

            if (!empty($usuario_id) && !empty($nombre) && !empty($email)) {
                if ($db->execute("UPDATE usuarios SET nombre = ?, email = ?, rol = ? WHERE id = ?", 
                    [$nombre, $email, $rol, $usuario_id])) {
                    $mensaje = 'Usuario actualizado exitosamente';
                    $tipo_mensaje = 'success';
                } else {
                    $mensaje = 'Error al actualizar usuario';
                    $tipo_mensaje = 'error';
                }
            }
        } elseif ($_POST['accion'] === 'eliminar_usuario') {
            $usuario_id = $_POST['usuario_id'] ?? '';
            if ($usuario_id !== $_SESSION['usuario_id']) { // No eliminar admin actual
                if ($db->execute("DELETE FROM usuarios WHERE id = ?", [$usuario_id])) {
                    $mensaje = 'Usuario eliminado';
                    $tipo_mensaje = 'success';
                }
            }
        }
        
        // Recargar datos
        $usuarios = $db->query("SELECT * FROM usuarios");
        $ventasRecientes = $db->query("SELECT * FROM ventas ORDER BY fecha_creacion DESC LIMIT 5");
        $totalVentas = $db->query("SELECT SUM(total) as total, COUNT(*) as cantidad FROM ventas WHERE fecha = CURDATE()")->fetch_assoc();
        $ventasHoy = $totalVentas['total'] ?? 0;
        $cantidadVentas = $totalVentas['cantidad'] ?? 0;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Pesado y al Fallo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #050505;
            --bg-soft: #121212;
            --surface: #171717;
            --text: #f5f5f5;
            --muted: #b7b7b7;
            --accent: #c41e3a;
            --accent-2: #ff4d4d;
            --border: rgba(255,255,255,0.12);
            --shadow: 0 20px 60px rgba(0,0,0,0.35);
            --success: #4caf50;
            --warning: #ff9800;
            --danger: #f44336;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Montserrat', sans-serif;
            background: radial-gradient(circle at top, #151515 0%, var(--bg) 45%, #050505 100%);
            color: var(--text);
        }

        header {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand {
            font-weight: 800;
            font-size: 1.2rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logout-btn {
            background: var(--accent);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background: var(--accent-2);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            border-color: var(--accent);
            transform: translateY(-4px);
        }

        .stat-label {
            color: var(--muted);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--accent);
        }

        .section {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .section h2 {
            font-size: 1.5rem;
            margin-top: 0;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border);
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .alert-success {
            background: rgba(76, 175, 80, 0.1);
            border: 1px solid rgba(76, 175, 80, 0.3);
            color: #81c784;
        }

        .alert-error {
            background: rgba(244, 67, 54, 0.1);
            border: 1px solid rgba(244, 67, 54, 0.3);
            color: #e57373;
        }

        .close-alert {
            background: none;
            border: none;
            color: inherit;
            cursor: pointer;
            font-size: 1.2rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            font-size: 0.9rem;
        }

        input, select {
            width: 100%;
            padding: 0.75rem;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 6px;
            color: var(--text);
            font-family: inherit;
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--accent);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn:hover {
            background: var(--accent-2);
        }

        .btn-small {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: var(--bg);
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid var(--border);
        }

        tr:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: var(--surface);
            padding: 2rem;
            border-radius: 12px;
            max-width: 500px;
            width: 90%;
            border: 1px solid var(--border);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .modal-header h3 {
            margin: 0;
        }

        .close-btn {
            background: none;
            border: none;
            color: var(--text);
            font-size: 1.5rem;
            cursor: pointer;
        }

        .modal-footer {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border);
        }

        .btn-secondary {
            background: var(--bg);
            color: var(--text);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            background: var(--bg-soft);
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn-edit, .btn-delete {
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
        }

        .btn-delete {
            background: var(--danger);
        }

        .btn-delete:hover {
            background: #d32f2f;
        }
    </style>
</head>
<body>
    <header>
        <div class="brand">Pesado y al Fallo - Dashboard</div>
        <div class="user-info">
            <span>Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
            <form action="" method="POST" style="margin: 0;">
                <input type="hidden" name="logout" value="1">
                <a href="?logout=1" class="logout-btn">Cerrar sesión</a>
            </form>
        </div>
    </header>

    <div class="container">
        <h1>Dashboard de Administración</h1>

        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-<?php echo $tipo_mensaje; ?>">
                <span><?php echo htmlspecialchars($mensaje); ?></span>
                <button class="close-alert" onclick="this.parentElement.style.display='none';">×</button>
            </div>
        <?php endif; ?>

        <!-- Estadísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Ventas Hoy</div>
                <div class="stat-value">$<?php echo number_format($ventasHoy, 2); ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Transacciones Hoy</div>
                <div class="stat-value"><?php echo $cantidadVentas; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Total de Usuarios</div>
                <div class="stat-value"><?php echo $usuarios->num_rows; ?></div>
            </div>
        </div>

        <!-- Gestión de Usuarios -->
        <div class="section">
            <h2>Gestión de Usuarios</h2>
            <button class="btn" onclick="openModal('addUserModal')">+ Agregar Usuario</button>

            <div class="table-container" style="margin-top: 1.5rem;">
                <table>
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($usuario = $usuarios->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($usuario['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                            <td><?php echo ucfirst($usuario['rol']); ?></td>
                            <td><?php echo ucfirst($usuario['estado']); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-edit btn-secondary btn-small" 
                                        onclick="openEditModal(<?php echo htmlspecialchars(json_encode($usuario)); ?>)">Editar</button>
                                    <?php if ($usuario['id'] !== $_SESSION['usuario_id']): ?>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('¿Eliminar este usuario?');">
                                        <input type="hidden" name="accion" value="eliminar_usuario">
                                        <input type="hidden" name="usuario_id" value="<?php echo $usuario['id']; ?>">
                                        <button type="submit" class="btn btn-delete btn-small">Eliminar</button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Ventas Recientes -->
        <div class="section">
            <h2>Ventas Recientes</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Total</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($venta = $ventasRecientes->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i', strtotime($venta['fecha_creacion'])); ?></td>
                            <td><?php echo htmlspecialchars($venta['producto']); ?></td>
                            <td><?php echo $venta['cantidad']; ?></td>
                            <td>$<?php echo number_format($venta['total'], 2); ?></td>
                            <td><?php echo ucfirst($venta['estado']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal: Agregar Usuario -->
    <div id="addUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Agregar Nuevo Usuario</h3>
                <button class="close-btn" onclick="closeModal('addUserModal')">×</button>
            </div>
            <form method="POST">
                <input type="hidden" name="accion" value="agregar_usuario">
                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" name="nombre" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Contraseña</label>
                    <input type="password" name="contraseña" required>
                </div>
                <div class="form-group">
                    <label>Rol</label>
                    <select name="rol" required>
                        <option value="user">Usuario</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('addUserModal')">Cancelar</button>
                    <button type="submit" class="btn">Agregar Usuario</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal: Editar Usuario -->
    <div id="editUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Editar Usuario</h3>
                <button class="close-btn" onclick="closeModal('editUserModal')">×</button>
            </div>
            <form method="POST">
                <input type="hidden" name="accion" value="actualizar_usuario">
                <input type="hidden" name="usuario_id" id="editUserId">
                <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" name="nombre" id="editNombre" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" id="editEmail" required>
                </div>
                <div class="form-group">
                    <label>Rol</label>
                    <select name="rol" id="editRol" required>
                        <option value="user">Usuario</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editUserModal')">Cancelar</button>
                    <button type="submit" class="btn">Actualizar Usuario</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
        }

        function openEditModal(usuario) {
            document.getElementById('editUserId').value = usuario.id;
            document.getElementById('editNombre').value = usuario.nombre;
            document.getElementById('editEmail').value = usuario.email;
            document.getElementById('editRol').value = usuario.rol;
            openModal('editUserModal');
        }

        // Cerrar modal al hacer click fuera
        window.onclick = function(event) {
            let modal = event.target;
            if (modal.classList.contains('modal')) {
                modal.classList.remove('active');
            }
        }
    </script>
</body>
</html>

<?php
// Logout
if (isset($_GET['logout'])) {
    $auth->logout();
    redirect('/login.php');
}
?>
