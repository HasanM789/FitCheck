<?php
require_once('db_config.php');

// Force delete all products and re-add them
try {
    $conn->exec("DELETE FROM products");
    
    $conn->exec("
        INSERT INTO products (name, description, price, image_url, category) VALUES
        ('Classic White Tee', 'Comfortable 100% cotton everyday essential t-shirt.', 4.50, 'white_tee.jpg', 'Tops'),
        ('Relaxed Fit Denim', 'Affordable and stylish light-wash denim jeans.', 12.00, 'denim_jeans.jpg', 'Bottoms'),
        ('Oversized Varsity Hoodie', 'Cozy fleece-lined hoodie perfect for college classes.', 15.00, 'hoodie.jpg', 'Outerwear'),
        ('Casual Summer Dress', 'Lightweight, breathable floral dress for daily wear.', 9.99, 'dress.jpg', 'Dresses')
    ");
    
    echo "✅ Products added successfully!<br><br>";
    
    // Show what's in the database
    $result = $conn->query("SELECT * FROM products");
    echo "<h3>Products in database:</h3>";
    while ($row = $result->fetch()) {
        echo "- " . $row['name'] . " - " . $row['price'] . " BD<br>";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>