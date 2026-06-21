-- ============================================
-- ESQUEMA DE BASE DE DATOS - PNK Inmobiliaria
-- Base de datos: pnks
-- ============================================

-- Tabla: propiedades
-- Almacena los inmuebles publicados por los propietarios
CREATE TABLE propiedades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_propietario INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    tipo ENUM('Casa', 'Departamento', 'Terreno') NOT NULL,
    provincia VARCHAR(100),
    comuna VARCHAR(100),
    direccion VARCHAR(255),
    precio DECIMAL(12, 0),
    habitaciones INT DEFAULT 0,
    banos INT DEFAULT 0,
    m2 INT DEFAULT 0,
    estado ENUM('Activa', 'Inactiva', 'Vendida') DEFAULT 'Activa',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_propietario) REFERENCES usuarios(id) ON DELETE CASCADE
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