<?php
// SQLite Database Configuration
$db_file = __DIR__ . '/fitcheck.db';

try {
    $conn = new PDO("sqlite:$db_file");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Create tables automatically
    $conn->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            is_admin INTEGER DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            price DECIMAL(10, 2) NOT NULL,
            image_url VARCHAR(255) NOT NULL,
            category VARCHAR(50) NOT NULL
        );

        CREATE TABLE IF NOT EXISTS orders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER,
            total_price DECIMAL(10, 2) NOT NULL,
            coupon_used VARCHAR(20) DEFAULT NULL,
            order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS order_items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            order_id INTEGER,
            product_id INTEGER,
            quantity INTEGER NOT NULL,
            selected_size VARCHAR(10) NOT NULL,
            selected_color VARCHAR(20) NOT NULL,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS cart (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            session_id VARCHAR(255) NOT NULL,
            product_id INTEGER NOT NULL,
            quantity INTEGER NOT NULL DEFAULT 1,
            added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        );
    ");
    
    // FORCE PRODUCTS TO HAVE IDs 17-20
    // First, delete existing products
    $conn->exec("DELETE FROM products");
    
    // Reset auto-increment
    $conn->exec("DELETE FROM sqlite_sequence WHERE name='products'");
    
    // Insert products with specific IDs 17-20
    $conn->exec("
        INSERT INTO products (id, name, description, price, image_url, category) VALUES
        (17, 'Classic White Tee', 'Comfortable 100% cotton everyday essential t-shirt.', 4.50, 'white_tee.jpg', 'Tops'),
        (18, 'Relaxed Fit Denim', 'Affordable and stylish light-wash denim jeans.', 12.00, 'denim_jeans.jpg', 'Bottoms'),
        (19, 'Oversized Varsity Hoodie', 'Cozy fleece-lined hoodie perfect for college classes.', 15.00, 'hoodie.jpg', 'Outerwear'),
        (20, 'Casual Summer Dress', 'Lightweight, breathable floral dress for daily wear.', 9.99, 'dress.jpg', 'Dresses')
    ");
    
    echo "Products inserted with IDs 17-20!<br>";
    
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get or create session ID for cart
if (!isset($_SESSION['cart_session_id'])) {
    $_SESSION['cart_session_id'] = session_id() . '_' . time();
}
?>