CREATE DATABASE IF NOT EXISTS aluth_elegance;
USE aluth_elegance;

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category VARCHAR(100),
    image VARCHAR(255),
    featured BOOLEAN DEFAULT FALSE,
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories Table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL,
    description TEXT,
    image VARCHAR(255)
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(50),
    shipping_address TEXT,
    total_amount DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Admin Users Table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin (password: admin123)
INSERT INTO admin_users (username, password, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'aluthelegance@gmail.com');

-- Insert sample categories
INSERT INTO categories (name, slug, description, image) VALUES
('Tile Basins', 'tile-basins', 'Handcrafted ceramic masterpieces', 'images/tile-basins.jpg'),
('Bathroom Accessories', 'bathroom-accessories', 'Elegant finishing touches', 'images/bathroom-accessories.jpg'),
('Home Accents', 'home-accents', 'Sophisticated decor pieces', 'images/home-accents.jpg');

-- Insert sample products
INSERT INTO products (name, description, price, category, image, featured, stock) VALUES
('Venetian Marble Basin', 'Luxurious handcrafted marble basin with elegant finish', 1299.00, 'Tile Basins', 'images/product1.jpg', TRUE, 10),
('Azure Mosaic Sink', 'Beautiful mosaic design with azure blue tiles', 899.00, 'Tile Basins', 'images/product2.jpg', TRUE, 15),
('Golden Soap Dispenser', 'Premium brass soap dispenser with gold finish', 149.00, 'Accessories', 'images/product3.jpg', TRUE, 25),
('Terracotta Vanity Set', 'Complete vanity set with terracotta finish', 349.00, 'Home Accents', 'images/product4.jpg', TRUE, 8);
