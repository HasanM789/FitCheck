<?php
require_once('db_config.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $image_url = $_POST['image_url'] ?: 'product.jpg';
    
    try {
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, image_url, category) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $description, $price, $image_url, $category]);
        $success = "✅ Product added successfully!";
        
        // Redirect to catalog after 2 seconds
        header("refresh:2;url=catalog.php");
    } catch (Exception $e) {
        $error = "❌ Error: " . $e->getMessage();
    }
}

// Get categories for dropdown
$categories = ['Tops', 'Bottoms', 'Hoodies', 'Shoes', 'Outerwear', 'Dresses', 'Accessories'];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add New Product - FitCheck</title>
    <style>
        body { background: #0f1115; color: #fff; font-family: Arial, sans-serif; padding: 40px; }
        .container { max-width: 600px; margin: 0 auto; background: #1a1c22; padding: 40px; border-radius: 8px; border: 1px solid #2d2d2d; }
        h1 { color: #dc3545; margin-bottom: 30px; }
        label { display: block; color: #94a3b8; margin-bottom: 8px; font-size: 14px; }
        input, select, textarea { width: 100%; padding: 12px; margin-bottom: 20px; background: #0f1115; border: 1px solid #2d2d2d; color: #fff; border-radius: 4px; }
        button { background: #dc3545; color: #fff; border: none; padding: 14px 40px; font-weight: bold; cursor: pointer; border-radius: 4px; }
        button:hover { background: #b02a37; }
        .success { color: #28a745; background: rgba(40, 167, 69, 0.1); padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .error { color: #dc3545; background: rgba(220, 53, 69, 0.1); padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        a { color: #dc3545; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .back-link { display: inline-block; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>➕ Add New Product</h1>
        
        <?php if (isset($success)): ?>
            <div class="success"><?php echo $success; ?> Redirecting to catalog...</div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <label>Product Name *</label>
            <input type="text" name="name" required placeholder="e.g., Premium Leather Jacket">
            
            <label>Description *</label>
            <textarea name="description" required rows="3" placeholder="Describe your product..."></textarea>
            
            <label>Price (BD) *</label>
            <input type="number" name="price" step="0.01" required placeholder="e.g., 24.99">
            
            <label>Category *</label>
            <select name="category" required>
                <option value="">Select Category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
                <?php endforeach; ?>
            </select>
            
            <label>Image URL (optional)</label>
            <input type="text" name="image_url" placeholder="e.g., jacket.jpg (leave empty for default)">
            
            <button type="submit" name="add_product">Add Product</button>
        </form>
        
        <a href="catalog.php" class="back-link">← Back to Catalog</a>
    </div>
</body>
</html>