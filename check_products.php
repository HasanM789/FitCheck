<?php
require_once('db_config.php');

echo "<h1>Products in Database</h1>";

$result = $conn->query("SELECT id, name, price FROM products");
$products = $result->fetchAll();

if (count($products) > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Name</th><th>Price</th></tr>";
    foreach ($products as $p) {
        echo "<tr>";
        echo "<td>" . $p['id'] . "</td>";
        echo "<td>" . $p['name'] . "</td>";
        echo "<td>" . $p['price'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<br><strong>Your product IDs are: ";
    foreach ($products as $p) {
        echo $p['id'] . " ";
    }
    echo "</strong>";
} else {
    echo "❌ No products found!";
}
?>