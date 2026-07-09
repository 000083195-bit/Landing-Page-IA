<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';

$error = '';
$success = '';

// Si ya está logueado, redirigir
if (isLoggedIn()) {
    if (isAdmin()) {
        redirect('/dashboard.php');
    } else {
        redirect('/index.php');
    }
}

// Procesar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $contraseña = $_POST['contraseña'] ?? '';

    $resultado = $auth->login($email, $contraseña);

    if ($resultado['success']) {
        $success = $resultado['message'];
        if ($resultado['rol'] === 'admin') {
            redirect('/dashboard.php');
        } else {
            redirect('/index.php');
        }
    } else {
        $error = $resultado['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Pesado y al Fallo</title>
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
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Montserrat', sans-serif;
            background: radial-gradient(circle at top, #151515 0%, var(--bg) 45%, #050505 100%);
            color: var(--text);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1rem;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
        }

        .login-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 2.5rem;
            box-shadow: var(--shadow);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-mark {
            display: inline-grid;
            place-items: center;
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            color: white;
            font-weight: 800;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .login-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0.5rem 0 0;
        }

        .login-header p {
            color: var(--muted);
            font-size: 0.9rem;
            margin: 0.5rem 0 0;
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .alert-error {
            background: rgba(196, 30, 58, 0.1);
            border: 1px solid rgba(196, 30, 58, 0.3);
            color: #ff9999;
        }

        .alert-success {
            background: rgba(76, 175, 80, 0.1);
            border: 1px solid rgba(76, 175, 80, 0.3);
            color: #81c784;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            font-size: 0.9rem;
        }

        input {
            width: 100%;
            padding: 0.75rem 1rem;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font-family: inherit;
            transition: all 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: var(--accent);
            background: var(--surface);
            box-shadow: 0 0 0 3px rgba(196, 30, 58, 0.1);
        }

        .btn {
            width: 100%;
            padding: 0.85rem;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(196, 30, 58, 0.3);
        }

        .btn:active {
            transform: translateY(0);
        }

        .form-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.9rem;
            color: var(--muted);
        }

        .form-footer a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .form-footer a:hover {
            color: var(--accent-2);
        }

        .divider {
            text-align: center;
            color: var(--muted);
            margin: 1.5rem 0;
            font-size: 0.85rem;
        }

        .demo-creds {
            background: rgba(255, 77, 77, 0.05);
            border: 1px solid rgba(255, 77, 77, 0.2);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1.5rem;
            font-size: 0.85rem;
        }

        .demo-creds strong {
            color: var(--accent);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="brand-mark">P</div>
                <h1>Pesado y al Fallo</h1>
                <p>Acceder a tu cuenta</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required placeholder="tu@email.com">
                </div>

                <div class="form-group">
                    <label for="contraseña">Contraseña</label>
                    <input type="password" id="contraseña" name="contraseña" required placeholder="••••••">
                </div>

                <button type="submit" class="btn">Acceder</button>
            </form>

            <div class="form-footer">
                ¿No tienes cuenta? <a href="#register">Regístrate aquí</a>
            </div>

            <div class="divider">Credenciales de prueba</div>
            <div class="demo-creds">
                <div><strong>Admin:</strong> admin@pesado.com</div>
                <div><strong>Usuario:</strong> user@pesado.com</div>
                <div><strong>Contraseña:</strong> password123</div>
            </div>
        </div>
    </div>
</body>
</html>
