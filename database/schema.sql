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

-- -------------------------------------------------------------------
-- Tabla: horarios_disponibles
-- Permite al admin definir rangos de horarios por dia de la semana
-- dia_semana: 0 = Domingo, 1 = Lunes, ..., 6 = Sábado
-- -------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS horarios_disponibles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dia_semana TINYINT NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_dia (dia_semana)
) ENGINE=InnoDB;

-- -------------------------------------------------------------------
-- Tabla: trabajos (Galería)
-- -------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS trabajos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    imagen VARCHAR(255) NOT NULL,
    titulo VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Administrador por defecto: usuario=admin / clave=admin123
-- Hash generado con password_hash('admin123', PASSWORD_BCRYPT)
INSERT IGNORE INTO admins (usuario, clave) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Maxi', '$2y$10$87JZPy/naO75U3f4kzwfeu0D0veioTeiCGsDPB8E10FPrDPEQWKQ2');

-- -------------------------------------------------------------------
-- Datos iniciales: servicios
-- -------------------------------------------------------------------
INSERT INTO servicios (nombre, precio, duracion_minutos, descripcion) VALUES
('Semipermanentes', 6500.00, 60, 'Esmaltado semipermanente con acabado brillante de larga duración.'),
('Capping gel', 7500.00, 75, 'Refuerzo de uña natural con gel nivelador para mayor resistencia.'),
('Softgel', 8500.00, 90, 'Extensiones livianas con tips soft gel para un aspecto natural.')

ON DUPLICATE KEY UPDATE precio = VALUES(precio), duracion_minutos = VALUES(duracion_minutos);

-- -------------------------------------------------------------------
-- Datos iniciales: horarios (Lunes a Viernes 9-19, Sábado 9-14)
-- -------------------------------------------------------------------
INSERT IGNORE INTO horarios_disponibles (dia_semana, hora_inicio, hora_fin, activo) VALUES
(0, '00:00', '00:00', 0),
(1, '09:00', '19:00', 1),
(2, '09:00', '19:00', 1),
(3, '09:00', '19:00', 1),
(4, '09:00', '19:00', 1),
(5, '09:00', '19:00', 1),
(6, '09:00', '14:00', 1);
