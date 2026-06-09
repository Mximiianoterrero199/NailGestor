CREATE DATABASE IF NOT EXISTS nailgestor CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nailgestor;

-- -------------------------------------------------------------------
-- Tabla: servicios
-- -------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    precio DECIMAL(10, 2) NOT NULL,
    duracion_minutos INT NOT NULL,
    descripcion TEXT,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -------------------------------------------------------------------
-- Tabla: clientes
-- -------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -------------------------------------------------------------------
-- Tabla: turnos
-- -------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS turnos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT,
    servicio_id INT,
    fecha_hora DATETIME NOT NULL,
    estado ENUM('pendiente', 'confirmado', 'cancelado', 'completado') DEFAULT 'pendiente',
    notas TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
    FOREIGN KEY (servicio_id) REFERENCES servicios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------------------
-- Tabla: admins
-- -------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(60) NOT NULL UNIQUE,
    clave VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Administrador por defecto: usuario=admin / clave=admin123
-- Hash generado con password_hash('admin123', PASSWORD_BCRYPT)
INSERT IGNORE INTO admins (usuario, clave) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- -------------------------------------------------------------------
-- Datos iniciales: servicios
-- -------------------------------------------------------------------
INSERT INTO servicios (nombre, precio, duracion_minutos, descripcion) VALUES
('Manicura Clásica', 6500.00, 45, 'Limpieza, limado y esmaltado tradicional con acabado impecable.'),
('Semipermanente', 9500.00, 60, 'Esmaltado semipermanente con acabado brillante de larga duración.'),
('Kapping Gel', 12500.00, 75, 'Refuerzo de uña natural con gel nivelador para mayor resistencia.'),
('Soft Gel', 16000.00, 90, 'Extensiones livianas con tips soft gel, aspecto natural y elegante.'),
('Nail Art', 8000.00, 60, 'Diseños personalizados con técnicas artísticas sobre cualquier base.'),
('Spa de Manos', 5000.00, 40, 'Exfoliación, hidratación profunda y masaje relajante de manos.')
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);
