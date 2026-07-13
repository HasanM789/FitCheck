<?php
require_once('db_config.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

// Check if user is admin
$stmt = $conn->prepare("SELECT is_admin FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user || $user['is_admin'] != 1) {
    die("⛔ Access denied. Admin only. <a href='account.php'>Back to Account</a>");
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
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px; }
        .btn { background: #dc3545; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; }
        .btn:hover { background: #b02a37; }
        .btn-secondary { background: transparent; border: 1px solid #333; color: #fff; }
        .btn-secondary:hover { border-color: #dc3545; color: #dc3545; }
        table { width: 100%; border-collapse: collapse; background: #1a1c22; border-radius: 8px; overflow: hidden; }
        th { background: #0f1115; padding: 15px; text-align: left; color: #94a3b8; }
        td { padding: 15px; border-bottom: 1px solid #2d2d2d; }
        .delete-btn { color: #dc3545; text-decoration: none; padding: 5px 15px; border: 1px solid #dc3545; border-radius: 4px; }
        .delete-btn:hover { background: #dc3545; color: #fff; }
        .success { background: rgba(40, 167, 69, 0.1); padding: 15px; border-radius: 4px; margin-bottom: 20px; color: #28a745; }
        .empty { text-align: center; color: #94a3b8; padding: 40px; }
        .admin-badge { background: #dc3545; color: #fff; padding: 2px 10px; border-radius: 12px; font-size: 11px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🛍️ Admin Panel <span class="admin-badge">ADMIN</span></h1>
            <div>
                <a href="add_product.php" class="btn">➕ Add New Product</a>
                <a href="catalog.php" class="btn btn-secondary">← Catalog</a>
                <a href="account.php" class="btn btn-secondary">Account</a>
            </div>
        </div>
        
        <?php if (isset($_GET['deleted'])): ?>
            <div class="success">✅ Product deleted successfully!</div>
        <?php endif; ?>
        
        <?php if (count($products) > 0): ?>
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
                                   onclick="return confirm('Delete <?php echo htmlspecialchars($product['name']); ?>?')">
                                   🗑️ Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p style="color: #94a3b8; margin-top: 20px;">Total: <?php echo count($products); ?> products</p>
        <?php else: ?>
            <div class="empty">
                <p>No products found.</p>
                <a href="add_product.php" class="btn">Add your first product</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>