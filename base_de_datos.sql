
-- Tabla de administrador
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    correo VARCHAR(100) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL
);

-- Insertar administrador por defecto
INSERT INTO admin (correo, contrasena)
VALUES ('admin@cafeteria.com', MD5('admin123'))
ON DUPLICATE KEY UPDATE correo = correo;

-- Tabla del menu
CREATE TABLE IF NOT EXISTS menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10,2) NOT NULL,
    categoria VARCHAR(50) NOT NULL,
    imagen LONGTEXT,
    disponible TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Productos de ejemplo
INSERT INTO menu (nombre, descripcion, precio, categoria, disponible) VALUES
('Cafe Americano', 'Cafe negro suave y aromatico', 35.00, 'Bebidas', 1),
('Capuchino', 'Espresso con leche vaporizada y espuma', 45.00, 'Bebidas', 1),
('Sandwich de Jamon', 'Pan artesanal, jamon, queso y verduras frescas', 65.00, 'Comida', 1),
('Pay de Manzana', 'Pay casero con canela, servido tibio', 50.00, 'Postres', 1);

-- Tabla de noticias
CREATE TABLE IF NOT EXISTS noticias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    texto TEXT NOT NULL,
    fecha DATE NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Noticias de ejemplo
INSERT INTO noticias (titulo, texto, fecha) VALUES
('Bienvenidos a nuestra cafeteria', 'Estamos felices de atenderte. Ven y disfruta de nuestros productos frescos cada dia.', CURDATE()),
('Nuevo menu de temporada', 'Prueba nuestras nuevas bebidas frias. Disponibles por tiempo limitado.', CURDATE());

-- Tabla de pedidos
CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente VARCHAR(100) NOT NULL,
    productos TEXT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    estado VARCHAR(50) DEFAULT 'Pendiente',
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
