<?php
require_once('db_config.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin.php?deleted=1");
    exit();
}

// Get all products
$products = $conn->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin - FitCheck</title>
    <style>
        body { background: #0f1115; color: #fff; font-family: Arial, sans-serif; padding: 40px; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #dc3545; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .btn { background: #dc3545; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; }
        .btn:hover { background: #b02a37; }
        table { width: 100%; border-collapse: collapse; background: #1a1c22; border-radius: 8px; overflow: hidden; }
        th { background: #0f1115; padding: 15px; text-align: left; color: #94a3b8; }
        td { padding: 15px; border-bottom: 1px solid #2d2d2d; }
        .delete-btn { color: #dc3545; text-decoration: none; padding: 5px 15px; border: 1px solid #dc3545; border-radius: 4px; }
        .delete-btn:hover { background: #dc3545; color: #fff; }
        .success { background: rgba(40, 167, 69, 0.1); padding: 15px; border-radius: 4px; margin-bottom: 20px; color: #28a745; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛍️ Product Management</h1>
            <div>
                <a href="add_product.php" class="btn">➕ Add New Product</a>
                <a href="catalog.php" class="btn" style="background: transparent; border: 1px solid #333;">← Catalog</a>
            </div>
        </div>
        
        <?php if (isset($_GET['deleted'])): ?>
            <div class="success">✅ Product deleted successfully!</div>
        <?php endif; ?>
        
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td>#<?php echo $product['id']; ?></td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo number_format($product['price'], 2); ?> BD</td>
                        <td><?php echo $product['category']; ?></td>
                        <td>
                            <a href="admin.php?delete=<?php echo $product['id']; ?>" 
                               class="delete-btn" 
                               onclick="return confirm('Delete <?php echo $product['name']; ?>?')">
                               🗑️ Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <p style="color: #94a3b8; margin-top: 20px;">Total: <?php echo count($products); ?> products</p>
    </div>
</body>
</html>