-- Crear base de datos
CREATE DATABASE IF NOT EXISTS pink_fashion_store;
USE pink_fashion_store;

-- Tabla de usuarios
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de productos
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category VARCHAR(50) NOT NULL,
    size VARCHAR(20),
    color VARCHAR(30),
    stock INT DEFAULT 0,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de carrito
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id)
);

-- Tabla de pedidos
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    shipping_address TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabla de detalles de pedidos
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Insertar usuario administrador por defecto
INSERT INTO users (username, email, password, full_name, role) 
VALUES ('admin', 'admin@pinkfashion.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', 'admin');

-- Insertar productos de ejemplo
INSERT INTO products (name, description, price, category, size, color, stock, image_url) VALUES
('Vestido Rosa Elegante', 'Hermoso vestido rosa para ocasiones especiales con detalles únicos', 89.99, 'vestidos', 'M', 'Rosa', 15, '/placeholder.svg?height=400&width=300'),
('Blusa Floral Rosa', 'Blusa con estampado floral en tonos rosas, perfecta para el día', 45.50, 'blusas', 'S', 'Rosa Claro', 20, '/placeholder.svg?height=400&width=300'),
('Falda Plisada Rosa', 'Falda plisada de tela suave con caída perfecta', 35.99, 'faldas', 'L', 'Rosa Pastel', 12, '/placeholder.svg?height=400&width=300'),
('Chaqueta Rosa Vintage', 'Chaqueta estilo vintage en rosa empolvado, ideal para cualquier ocasión', 75.00, 'chaquetas', 'M', 'Rosa Empolvado', 8, '/placeholder.svg?height=400&width=300'),
('Conjunto Rosa Deportivo', 'Conjunto deportivo cómodo y elegante en tonos rosas', 65.99, 'conjuntos', 'S', 'Rosa Fucsia', 25, '/placeholder.svg?height=400&width=300'),
('Zapatos Rosa Tacón', 'Elegantes zapatos de tacón en rosa nude', 120.00, 'zapatos', '38', 'Rosa Nude', 10, '/placeholder.svg?height=400&width=300'),
('Bolso Rosa Cuero', 'Bolso de cuero genuino en rosa clásico', 95.50, 'accesorios', 'Único', 'Rosa', 18, '/placeholder.svg?height=400&width=300'),
('Collar Rosa Perlas', 'Collar de perlas rosas con detalles dorados', 55.00, 'accesorios', 'Único', 'Rosa Perla', 30, '/placeholder.svg?height=400&width=300');
