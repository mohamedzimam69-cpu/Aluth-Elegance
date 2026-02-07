<?php
// Database Setup Script
// Run this file once to create the database and tables

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'aluth_elegance';

try {
    // Connect to MySQL
    $conn = new PDO("mysql:host=$host", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $conn->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    echo "✓ Database created successfully<br>";
    
    // Use the database
    $conn->exec("USE $dbname");
    
    // Create products table
    $conn->exec("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10, 2) NOT NULL,
        category VARCHAR(100),
        image VARCHAR(255),
        featured BOOLEAN DEFAULT FALSE,
        stock INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✓ Products table created<br>";
    
    // Create categories table
    $conn->exec("CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        slug VARCHAR(100) NOT NULL,
        description TEXT,
        image VARCHAR(255)
    )");
    echo "✓ Categories table created<br>";
    
    // Create orders table
    $conn->exec("CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_name VARCHAR(255) NOT NULL,
        customer_email VARCHAR(255) NOT NULL,
        customer_phone VARCHAR(50),
        shipping_address TEXT,
        total_amount DECIMAL(10, 2) NOT NULL,
        status VARCHAR(50) DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✓ Orders table created<br>";
    
    // Create order_items table
    $conn->exec("CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT,
        product_id INT,
        quantity INT NOT NULL,
        price DECIMAL(10, 2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id),
        FOREIGN KEY (product_id) REFERENCES products(id)
    )");
    echo "✓ Order items table created<br>";
    
    // Create admin_login_log table for security (optional)
    try {
        $conn->exec("CREATE TABLE IF NOT EXISTS admin_login_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            admin_id INT,
            ip_address VARCHAR(45),
            status VARCHAR(20),
            login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (admin_id) REFERENCES admin_users(id)
        )");
        echo "✓ Admin login log table created<br>";
    } catch (Exception $e) {
        echo "⚠ Login log table skipped (optional feature)<br>";
    }
    
    // Create admin_users table
    $conn->exec("CREATE TABLE IF NOT EXISTS admin_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✓ Admin users table created<br>";
    
    // Insert default admin (password: admin123)
    $stmt = $conn->query("SELECT COUNT(*) FROM admin_users WHERE username = 'admin'");
    if ($stmt->fetchColumn() == 0) {
        // Insert default admin (password: admin123)
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $conn->exec("INSERT INTO admin_users (username, password, email) VALUES 
            ('admin', '$hashedPassword', 'aluthelegance@gmail.com')");
        echo "✓ Default admin user created (username: admin, password: admin123)<br>";
    } else {
        echo "✓ Admin user already exists<br>";
    }
    
    // Insert sample categories
    $stmt = $conn->query("SELECT COUNT(*) FROM categories");
    if ($stmt->fetchColumn() == 0) {
        $conn->exec("INSERT INTO categories (name, slug, description, image) VALUES
            ('Tile Basins', 'tile-basins', 'Handcrafted ceramic masterpieces', 'images/tile-basins.jpg'),
            ('Bathroom Accessories', 'bathroom-accessories', 'Elegant finishing touches', 'images/bathroom-accessories.jpg'),
            ('Home Accents', 'home-accents', 'Sophisticated decor pieces', 'images/home-accents.jpg')");
        echo "✓ Sample categories inserted<br>";
    }
    
    // Insert sample products
    $stmt = $conn->query("SELECT COUNT(*) FROM products");
    if ($stmt->fetchColumn() == 0) {
        $conn->exec("INSERT INTO products (name, description, price, category, image, featured, stock) VALUES
            ('Venetian Marble Basin', 'Luxurious handcrafted marble basin with elegant finish', 129900.00, 'Tile Basins', 'images/product1.jpg', TRUE, 10),
            ('Azure Mosaic Sink', 'Beautiful mosaic design with azure blue tiles', 89900.00, 'Tile Basins', 'images/product2.jpg', TRUE, 15),
            ('Golden Soap Dispenser', 'Premium brass soap dispenser with gold finish', 14900.00, 'Accessories', 'images/product3.jpg', TRUE, 25),
            ('Terracotta Vanity Set', 'Complete vanity set with terracotta finish', 34900.00, 'Home Accents', 'images/product4.jpg', TRUE, 8)");
        echo "✓ Sample products inserted<br>";
    }
    
    echo "<br><strong style='color: green;'>✓ Setup completed successfully!</strong><br>";
    echo "<br>You can now:<br>";
    echo "- Visit the website: <a href='index.html'>index.html</a><br>";
    echo "- Login to admin: <a href='admin/login.php'>admin/login.php</a><br>";
    echo "- Username: admin<br>";
    echo "- Password: admin123<br>";
    echo "<br><strong>IMPORTANT: Delete this setup.php file after setup!</strong>";
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
