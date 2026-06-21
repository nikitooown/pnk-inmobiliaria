-- ============================================
-- ESQUEMA DE BASE DE DATOS - PNK Inmobiliaria
-- Base de datos: pnks
-- ============================================

-- Tabla: usuarios
-- Almacena los usuarios del sistema (Administradores, Propietarios, Gestores)
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rut VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    fecha_nacimiento DATE DEFAULT NULL,
    genero VARCHAR(20) DEFAULT NULL,
    telefono VARCHAR(20) DEFAULT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    clave VARCHAR(255) NOT NULL,
    estado TINYINT(1) NOT NULL DEFAULT 0,
    fecha_hora DATETIME DEFAULT CURRENT_TIMESTAMP,
    foto VARCHAR(255) DEFAULT 'default.png',
    idperfil INT NOT NULL DEFAULT 2,
    certificado VARCHAR(500) DEFAULT NULL
);

-- Tabla: propiedades
-- Almacena los inmuebles publicados por los propietarios
CREATE TABLE propiedades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    tipo ENUM('Casa', 'Departamento', 'Terreno') NOT NULL,
    descripcion TEXT,
    provincia VARCHAR(100),
    comuna VARCHAR(100),
    sector VARCHAR(100),
    direccion VARCHAR(200),
    precio DECIMAL(12,2) NOT NULL,
    uf DECIMAL(10,2) DEFAULT 0.00,
    m2_terreno INT DEFAULT 0,
    m2_construido INT DEFAULT 0,
    habitaciones INT DEFAULT 0,
    banos INT DEFAULT 0,
    bodega TINYINT(1) DEFAULT 0,
    estacionamiento TINYINT(1) DEFAULT 0,
    logia TINYINT(1) DEFAULT 0,
    cocina_amoblada TINYINT(1) DEFAULT 0,
    antejardin TINYINT(1) DEFAULT 0,
    patio_trasero TINYINT(1) DEFAULT 0,
    piscina TINYINT(1) DEFAULT 0,
    latitud VARCHAR(50) DEFAULT NULL,
    longitud VARCHAR(50) DEFAULT NULL,
    estado ENUM('Publicada', 'Pendiente', 'Vendida', 'Arrendada', 'Inactiva') DEFAULT 'Publicada',
    idpropietario INT NOT NULL,
    fecha_publicacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idpropietario) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Tabla: fotografias
-- Almacena las imágenes asociadas a cada propiedad
CREATE TABLE fotografias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_propiedad INT NOT NULL,
    ruta VARCHAR(500) NOT NULL,
    nombre_original VARCHAR(255),
    es_principal TINYINT(1) DEFAULT 0,
    fecha_subida DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_propiedad) REFERENCES propiedades(id) ON DELETE CASCADE
);

-- Tabla: mensajes_contacto
-- Almacena los mensajes enviados desde el formulario de contacto
CREATE TABLE mensajes_contacto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    mensaje TEXT NOT NULL,
    fecha_envio DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabla: visitas
-- Almacena las solicitudes de visita a propiedades
CREATE TABLE IF NOT EXISTS visitas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_propiedad INT NOT NULL,
    nombre VARCHAR(100),
    telefono VARCHAR(20),
    fecha_solicitud TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- DATOS SEMILLA (Seed data)
-- ============================================

-- Usuario Administrador por defecto
-- Clave hasheada con password_hash('Admin123!', PASSWORD_DEFAULT)
-- Hash generado: $2y$12$1/H5cRzjfC0TfVDRPcmGBeYo3JoPfMB1/L3LVjxn3biqOgHPX5bdK
-- Contraseña: Admin123!
-- IMPORTANTE: Si necesitas regenerar el hash, usa: php -r "echo password_hash('Admin123!', PASSWORD_DEFAULT);"
INSERT INTO usuarios (rut, nombre, apellido, fecha_nacimiento, genero, telefono, email, clave, estado, fecha_hora, foto, idperfil, certificado)
VALUES ('11111111-1', 'Admin', 'PNK', '1990-01-01', 'Masculino', '+56911111111', 'admin@pnk.cl', '$2y$12$1/H5cRzjfC0TfVDRPcmGBeYo3JoPfMB1/L3LVjxn3biqOgHPX5bdK', 1, NOW(), 'default.png', 1, NULL);