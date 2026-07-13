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
    ");
    
    // Check if products exist
    $stmt = $conn->query("SELECT COUNT(*) as count FROM products");
    $count = $stmt->fetch()['count'];
    
    // Insert sample products if table is empty
    if ($count == 0) {
        $conn->exec("
            INSERT INTO products (name, description, price, image_url, category) VALUES
            ('Classic White Tee', 'Comfortable 100% cotton everyday essential t-shirt.', 4.50, 'white_tee.jpg', 'Tops'),
            ('Relaxed Fit Denim', 'Affordable and stylish light-wash denim jeans.', 12.00, 'denim_jeans.jpg', 'Bottoms'),
            ('Oversized Varsity Hoodie', 'Cozy fleece-lined hoodie perfect for college classes.', 15.00, 'hoodie.jpg', 'Outerwear'),
            ('Casual Summer Dress', 'Lightweight, breathable floral dress for daily wear.', 9.99, 'dress.jpg', 'Dresses')
        ");
    }
    
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

// ============================================
// SESSION CONFIGURATION - FIX FOR RENDER
// ============================================

// Set session cookie parameters BEFORE starting session
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',  // Auto-detect
    'secure' => false,  // Set to true if using HTTPS
    'httponly' => true,
    'samesite' => 'Lax'
]);

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Regenerate session ID periodically for security (optional)
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} elseif (time() - $_SESSION['created'] > 1800) {
    // Session older than 30 minutes
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Debug: Log session info (remove after testing)
error_log("Session ID: " . session_id());
error_log("Session Cart: " . print_r($_SESSION['cart'], true));
?>