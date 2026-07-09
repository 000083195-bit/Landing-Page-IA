<?php
// ============================================
// Funciones de Autenticación
// ============================================

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

class Auth {
    private $db;

    public function __construct() {
        global $db;
        $this->db = $db;
    }

    /**
     * Registrar nuevo usuario
     */
    public function register($nombre, $email, $contraseña) {
        // Validar datos
        if (empty($nombre) || empty($email) || empty($contraseña)) {
            return ['success' => false, 'message' => 'Todos los campos son requeridos'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Email inválido'];
        }

        if (strlen($contraseña) < 6) {
            return ['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres'];
        }

        // Verificar si el email ya existe
        $result = $this->db->query(
            "SELECT id FROM usuarios WHERE email = ?",
            [$email]
        );

        if ($result->num_rows > 0) {
            return ['success' => false, 'message' => 'El email ya está registrado'];
        }

        // Hash de la contraseña
        $contraseña_hash = password_hash($contraseña, PASSWORD_HASH_ALGO, ['cost' => PASSWORD_HASH_COST]);

        // Insertar usuario
        $sql = "INSERT INTO usuarios (nombre, email, contraseña, rol) VALUES (?, ?, ?, ?)";
        if ($this->db->execute($sql, [$nombre, $email, $contraseña_hash, 'user'])) {
            return ['success' => true, 'message' => 'Usuario registrado exitosamente'];
        } else {
            return ['success' => false, 'message' => 'Error al registrar el usuario'];
        }
    }

    /**
     * Login de usuario
     */
    public function login($email, $contraseña) {
        if (empty($email) || empty($contraseña)) {
            return ['success' => false, 'message' => 'Email y contraseña requeridos'];
        }

        // Buscar usuario
        $result = $this->db->query(
            "SELECT id, nombre, email, contraseña, rol FROM usuarios WHERE email = ?",
            [$email]
        );

        if ($result->num_rows === 0) {
            return ['success' => false, 'message' => 'Credenciales inválidas'];
        }

        $usuario = $result->fetch_assoc();

        // Verificar contraseña
        if (!password_verify($contraseña, $usuario['contraseña'])) {
            return ['success' => false, 'message' => 'Credenciales inválidas'];
        }

        // Crear sesión
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nombre'] = $usuario['nombre'];
        $_SESSION['email'] = $usuario['email'];
        $_SESSION['rol'] = $usuario['rol'];
        $_SESSION['usuario'] = [
            'id' => $usuario['id'],
            'nombre' => $usuario['nombre'],
            'email' => $usuario['email'],
            'rol' => $usuario['rol']
        ];

        return [
            'success' => true,
            'message' => 'Login exitoso',
            'rol' => $usuario['rol']
        ];
    }

    /**
     * Logout
     */
    public function logout() {
        session_destroy();
        return true;
    }

    /**
     * Obtener usuario por ID
     */
    public function getUserById($id) {
        $result = $this->db->query(
            "SELECT id, nombre, email, rol FROM usuarios WHERE id = ?",
            [$id]
        );

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return null;
    }

    /**
     * Actualizar usuario
     */
    public function updateUser($id, $nombre, $email) {
        $sql = "UPDATE usuarios SET nombre = ?, email = ? WHERE id = ?";
        return $this->db->execute($sql, [$nombre, $email, $id]);
    }

    /**
     * Cambiar contraseña
     */
    public function changePassword($id, $contraseña_actual, $contraseña_nueva) {
        // Obtener usuario
        $result = $this->db->query(
            "SELECT contraseña FROM usuarios WHERE id = ?",
            [$id]
        );

        if ($result->num_rows === 0) {
            return ['success' => false, 'message' => 'Usuario no encontrado'];
        }

        $usuario = $result->fetch_assoc();

        // Verificar contraseña actual
        if (!password_verify($contraseña_actual, $usuario['contraseña'])) {
            return ['success' => false, 'message' => 'Contraseña actual incorrecta'];
        }

        // Hash de nueva contraseña
        $contraseña_hash = password_hash($contraseña_nueva, PASSWORD_HASH_ALGO, ['cost' => PASSWORD_HASH_COST]);

        // Actualizar
        $sql = "UPDATE usuarios SET contraseña = ? WHERE id = ?";
        if ($this->db->execute($sql, [$contraseña_hash, $id])) {
            return ['success' => true, 'message' => 'Contraseña actualizada'];
        }

        return ['success' => false, 'message' => 'Error al actualizar contraseña'];
    }
}

$auth = new Auth();
?>
