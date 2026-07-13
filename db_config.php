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

        -- NEW: Session table for database-based sessions
        CREATE TABLE IF NOT EXISTS sessions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            session_id VARCHAR(255) NOT NULL UNIQUE,
            data TEXT NOT NULL,
            last_accessed TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
    ");
    
    // Check if products exist with IDs 17-20
    $stmt = $conn->query("SELECT COUNT(*) as count FROM products WHERE id >= 17 AND id <= 20");
    $count = $stmt->fetch()['count'];
    
    if ($count < 4) {
        $conn->exec("DELETE FROM products");
        $conn->exec("DELETE FROM sqlite_sequence WHERE name='products'");
        $conn->exec("
            INSERT INTO products (id, name, description, price, image_url, category) VALUES
            (17, 'Classic White Tee', 'Comfortable 100% cotton everyday essential t-shirt.', 4.50, 'white_tee.jpg', 'Tops'),
            (18, 'Relaxed Fit Denim', 'Affordable and stylish light-wash denim jeans.', 12.00, 'denim_jeans.jpg', 'Bottoms'),
            (19, 'Oversized Varsity Hoodie', 'Cozy fleece-lined hoodie perfect for college classes.', 15.00, 'hoodie.jpg', 'Outerwear'),
            (20, 'Casual Summer Dress', 'Lightweight, breathable floral dress for daily wear.', 9.99, 'dress.jpg', 'Dresses')
        ");
    }
    
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

// ============================================
// DATABASE-BASED SESSION HANDLER
// ============================================

// Custom session handler using database
class DatabaseSessionHandler {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function open($savePath, $sessionName) {
        return true;
    }
    
    public function close() {
        return true;
    }
    
    public function read($sessionId) {
        $stmt = $this->conn->prepare("SELECT data FROM sessions WHERE session_id = ?");
        $stmt->execute([$sessionId]);
        $result = $stmt->fetch();
        if ($result) {
            // Update last_accessed
            $stmt = $this->conn->prepare("UPDATE sessions SET last_accessed = CURRENT_TIMESTAMP WHERE session_id = ?");
            $stmt->execute([$sessionId]);
            return $result['data'];
        }
        return '';
    }
    
    public function write($sessionId, $data) {
        // Check if session exists
        $stmt = $this->conn->prepare("SELECT id FROM sessions WHERE session_id = ?");
        $stmt->execute([$sessionId]);
        if ($stmt->fetch()) {
            // Update existing session
            $stmt = $this->conn->prepare("UPDATE sessions SET data = ?, last_accessed = CURRENT_TIMESTAMP WHERE session_id = ?");
            $stmt->execute([$data, $sessionId]);
        } else {
            // Insert new session
            $stmt = $this->conn->prepare("INSERT INTO sessions (session_id, data, last_accessed) VALUES (?, ?, CURRENT_TIMESTAMP)");
            $stmt->execute([$sessionId, $data]);
        }
        return true;
    }
    
    public function destroy($sessionId) {
        $stmt = $this->conn->prepare("DELETE FROM sessions WHERE session_id = ?");
        $stmt->execute([$sessionId]);
        return true;
    }
    
    public function gc($maxlifetime) {
        // Delete sessions older than maxlifetime
        $stmt = $this->conn->prepare("DELETE FROM sessions WHERE datetime(last_accessed) < datetime('now', '-' || ? || ' seconds')");
        $stmt->execute([$maxlifetime]);
        return true;
    }
}

// Register the custom session handler
$handler = new DatabaseSessionHandler($conn);
session_set_save_handler($handler, true);

// Set session parameters
ini_set('session.gc_maxlifetime', 604800); // 7 days
ini_set('session.cookie_lifetime', 604800); // 7 days
session_set_cookie_params([
    'lifetime' => 604800,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get or create session ID for cart
if (!isset($_SESSION['cart_session_id'])) {
    $_SESSION['cart_session_id'] = session_id() . '_' . time();
}

// Update last activity time
if (isset($_SESSION['user_id'])) {
    $_SESSION['last_activity'] = time();
}
?>