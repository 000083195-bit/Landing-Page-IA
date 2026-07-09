-- ============================================
-- Base de datos para Pesado y al Fallo
-- ============================================

CREATE DATABASE IF NOT EXISTS pesado_fallo;
USE pesado_fallo;

-- ============================================
-- Tabla de Usuarios
-- ============================================
CREATE TABLE IF NOT EXISTS usuarios (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nombre VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  contraseña VARCHAR(255) NOT NULL,
  rol ENUM('admin', 'user') DEFAULT 'user',
  estado ENUM('activo', 'inactivo') DEFAULT 'activo',
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_email (email),
  INDEX idx_rol (rol)
);

-- ============================================
-- Tabla de Ventas Diarias
-- ============================================
CREATE TABLE IF NOT EXISTS ventas (
  id INT PRIMARY KEY AUTO_INCREMENT,
  fecha DATE NOT NULL,
  producto VARCHAR(150) NOT NULL,
  cantidad INT NOT NULL DEFAULT 1,
  precio_unitario DECIMAL(10, 2) NOT NULL,
  total DECIMAL(10, 2) NOT NULL,
  cliente_email VARCHAR(100),
  metodo_pago VARCHAR(50),
  estado ENUM('completada', 'pendiente', 'cancelada') DEFAULT 'completada',
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_fecha (fecha),
  INDEX idx_estado (estado)
);

-- ============================================
-- Tabla de Categorías de Productos
-- ============================================
CREATE TABLE IF NOT EXISTS categorias (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nombre VARCHAR(100) NOT NULL,
  descripcion TEXT,
  estado ENUM('activo', 'inactivo') DEFAULT 'activo',
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- Tabla de Productos
-- ============================================
CREATE TABLE IF NOT EXISTS productos (
  id INT PRIMARY KEY AUTO_INCREMENT,
  nombre VARCHAR(150) NOT NULL,
  descripcion TEXT,
  categoria_id INT,
  precio DECIMAL(10, 2) NOT NULL,
  stock INT DEFAULT 0,
  estado ENUM('activo', 'inactivo') DEFAULT 'activo',
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

-- ============================================
-- Tabla de Sesiones
-- ============================================
CREATE TABLE IF NOT EXISTS sesiones (
  id INT PRIMARY KEY AUTO_INCREMENT,
  usuario_id INT NOT NULL,
  token VARCHAR(255) NOT NULL UNIQUE,
  ip_address VARCHAR(45),
  user_agent VARCHAR(255),
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  fecha_expiracion DATETIME,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
  INDEX idx_token (token)
);

-- ============================================
-- Inserts de ejemplo
-- ============================================

-- Usuario Admin
INSERT INTO usuarios (nombre, email, contraseña, rol) 
VALUES ('Admin Pesado', 'admin@pesado.com', '$2y$10$92IXUNpkm1BrD3NK3l.Ei.kT6kfEe7j5KPGEOq3N5rEzMZ8BX6pZm', 'admin');

-- Usuario Regular
INSERT INTO usuarios (nombre, email, contraseña, rol) 
VALUES ('Usuario Test', 'user@pesado.com', '$2y$10$92IXUNpkm1BrD3NK3l.Ei.kT6kfEe7j5KPGEOq3N5rEzMZ8BX6pZm', 'user');

-- Categorías
INSERT INTO categorias (nombre, descripcion) 
VALUES 
('Accesorios', 'Guantes, cinturones y accesorios fitness'),
('Suplementos', 'Proteína, pre-entreno y recuperación'),
('Ropa', 'Ropa deportiva de calidad');

-- Productos
INSERT INTO productos (nombre, descripcion, categoria_id, precio, stock) 
VALUES 
('Guantes de Levantamiento', 'Guantes premium para levantamiento de pesas', 1, 29.99, 50),
('Cinturón de Levantamiento', 'Cinturón de cuero para estabilidad', 1, 49.99, 30),
('Proteína Whey 2KG', 'Proteína de suero concentrada', 2, 39.99, 100),
('Pre-Entreno Premium', 'Energía y enfoque para entrenamientos', 2, 34.99, 75);

-- Ventas de ejemplo
INSERT INTO ventas (fecha, producto, cantidad, precio_unitario, total, cliente_email, metodo_pago, estado) 
VALUES 
(CURDATE(), 'Guantes de Levantamiento', 1, 29.99, 29.99, 'cliente@example.com', 'tarjeta', 'completada'),
(CURDATE(), 'Proteína Whey 2KG', 2, 39.99, 79.98, 'cliente2@example.com', 'transferencia', 'completada'),
(DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Cinturón de Levantamiento', 1, 49.99, 49.99, 'cliente3@example.com', 'tarjeta', 'completada');
