<?php
require_once('db_config.php');

echo "<h1>Test Page - Showing All Products</h1>";

// Get all products
$result = $conn->query("SELECT * FROM products");

// Use fetchAll to count (works in SQLite)
$products = $result->fetchAll();
$count = count($products);

echo "<p>Found $count products in database</p>";

echo "<div style='display: flex; flex-wrap: wrap; gap: 20px;'>";
foreach ($products as $item) {
    echo "<div style='border: 1px solid #333; padding: 15px; border-radius: 8px; width: 200px;'>";
    echo "<h3>" . $item['name'] . "</h3>";
    echo "<p>" . $item['description'] . "</p>";
    echo "<p><strong>" . $item['price'] . " BD</strong></p>";
    echo "<p>Category: " . $item['category'] . "</p>";
    echo "</div>";
}
echo "</div>";
?>