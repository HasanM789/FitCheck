<?php
require_once('db_config.php');

echo "<h1>Database Debugging</h1>";

// Check if products table exists
try {
    $result = $conn->query("SELECT name FROM sqlite_master WHERE type='table' AND name='products'");
    $tableExists = $result->fetch();
    
    if ($tableExists) {
        echo "✅ Products table exists<br>";
    } else {
        echo "❌ Products table does NOT exist<br>";
    }
    
    // Count products
    $stmt = $conn->query("SELECT COUNT(*) as count FROM products");
    $count = $stmt->fetch()['count'];
    echo "📊 Products in database: " . $count . "<br><br>";
    
    // Show all products
    if ($count > 0) {
        echo "<h3>Products:</h3>";
        $result = $conn->query("SELECT * FROM products");
        while ($row = $result->fetch()) {
            echo "- ID: " . $row['id'] . " | " . $row['name'] . " | " . $row['price'] . " BD | " . $row['category'] . "<br>";
        }
    } else {
        echo "❌ No products found<br>";
        
        // Try to insert products directly
        echo "<br><h3>Attempting to insert products...</h3>";
        try {
            $conn->exec("DELETE FROM products");
            $conn->exec("
                INSERT INTO products (name, description, price, image_url, category) VALUES
                ('Classic White Tee', 'Comfortable 100% cotton everyday essential t-shirt.', 4.50, 'white_tee.jpg', 'Tops'),
                ('Relaxed Fit Denim', 'Affordable and stylish light-wash denim jeans.', 12.00, 'denim_jeans.jpg', 'Bottoms'),
                ('Oversized Varsity Hoodie', 'Cozy fleece-lined hoodie perfect for college classes.', 15.00, 'hoodie.jpg', 'Outerwear'),
                ('Casual Summer Dress', 'Lightweight, breathable floral dress for daily wear.', 9.99, 'dress.jpg', 'Dresses')
            ");
            echo "✅ Products inserted successfully!<br>";
            
            // Verify insertion
            $stmt = $conn->query("SELECT COUNT(*) as count FROM products");
            $newCount = $stmt->fetch()['count'];
            echo "📊 Now products in database: " . $newCount . "<br>";
            
        } catch (Exception $e) {
            echo "❌ Insert failed: " . $e->getMessage() . "<br>";
        }
    }
    
    // Check database file location
    echo "<br><h3>Database Info:</h3>";
    echo "Database file: " . __DIR__ . '/fitcheck.db<br>';
    echo "File exists: " . (file_exists(__DIR__ . '/fitcheck.db') ? '✅ Yes' : '❌ No') . "<br>";
    if (file_exists(__DIR__ . '/fitcheck.db')) {
        echo "File size: " . filesize(__DIR__ . '/fitcheck.db') . " bytes<br>";
        echo "Is writable: " . (is_writable(__DIR__ . '/fitcheck.db') ? '✅ Yes' : '❌ No') . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}
?>