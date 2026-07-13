<?php 
require_once('db_config.php'); 
include('header.php'); 

$selected_category = $_GET['category'] ?? 'All';
$price_filter = $_GET['price_filter'] ?? 'All';
?>

<div class="filter-menu-bar" style="display: flex; flex-direction: column; align-items: center; gap: 25px; margin-bottom: 40px;">
    
    <!-- Category Capsules -->
    <div style="display: flex; gap: 12px; flex-wrap: wrap; justify-content: center;">
        <?php $categories = ['All', 'Tops', 'Bottoms', 'Hoodies', 'Shoes']; ?>
        <?php foreach ($categories as $cat): ?>
            <a href="catalog.php?category=<?php echo $cat; ?>&price_filter=<?php echo urlencode($price_filter); ?>" 
               class="category-capsule <?php echo $selected_category == $cat ? 'active' : ''; ?>">
               <?php echo $cat; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Updated Price Filter with visibility fix -->
    <form action="catalog.php" method="GET" style="display: flex; gap: 15px; align-items: center; background: #0f0f0f; padding: 10px 20px; border: 1px solid #222; border-radius: 50px;">
        <input type="hidden" name="category" value="<?php echo htmlspecialchars($selected_category); ?>">
        <label style="color: #666; font-size: 12px; font-weight: bold; text-transform: uppercase;">Price Range:</label>
        <select name="price_filter" onchange="this.form.submit()" style="background: transparent; color: #fff; border: none; outline: none; cursor: pointer; font-size: 14px;">
            <option value="All" style="background: #111; color: #fff;" <?php if($price_filter == 'All') echo 'selected'; ?>>All Prices</option>
            <option value="0-5" style="background: #111; color: #fff;" <?php if($price_filter == '0-5') echo 'selected'; ?>>0 - 5 BD</option>
            <option value="5-10" style="background: #111; color: #fff;" <?php if($price_filter == '5-10') echo 'selected'; ?>>5 - 10 BD</option>
            <option value="10-20" style="background: #111; color: #fff;" <?php if($price_filter == '10-20') echo 'selected'; ?>>10 - 20 BD</option>
            <option value="20+" style="background: #111; color: #fff;" <?php if($price_filter == '20+') echo 'selected'; ?>>20 BD & Above</option>
        </select>
    </form>
</div>

<div class="modern-storefront-grid">
    <?php
    $sql = "SELECT * FROM products WHERE 1=1";
    $params = [];
    
    if ($selected_category !== 'All') {
        $sql .= " AND category = ?";
        $params[] = $selected_category;
    }
    
    if ($price_filter == '0-5') { 
        $sql .= " AND price <= 5"; 
    } elseif ($price_filter == '5-10') { 
        $sql .= " AND price > 5 AND price <= 10"; 
    } elseif ($price_filter == '10-20') { 
        $sql .= " AND price > 10 AND price <= 20"; 
    } elseif ($price_filter == '20+') { 
        $sql .= " AND price > 20"; 
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $result = $stmt;

    if ($result->rowCount() > 0) {
        while($item = $result->fetch()) {
            ?>
            <div class="premium-item-card">
                <div class="product-image-container">
                    <?php 
                    $image_path = !empty($item['image_url']) ? $item['image_url'] : '';
                    if (!empty($image_path) && file_exists($image_path)): ?>
                        <img src="<?php echo htmlspecialchars($image_path); ?>" class="product-image" loading="lazy">
                    <?php else: ?>
                        <div class="image-placeholder">
                            <span class="placeholder-icon">👕</span>
                            <span class="placeholder-code"><?php echo strtoupper(substr($item['name'], 0, 2)); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <div class="app-canvas-container">PREMIUM OVERVIEW</div>
                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                    <p><?php echo htmlspecialchars($item['description']); ?></p>
                    <div class="product-price"><?php echo number_format($item['price'], 2); ?> BD</div>
                </div>
                <form action="cart_action.php" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                    <button type="submit" class="premium-purchase-btn">Add to Cart</button>
                </form>
            </div>
            <?php
        }
    } else {
        echo "<p style='text-align: center; width: 100%; color: #94a3b8; padding: 40px 0;'>No items match your filters.</p>";
    }
    ?>
</div>

<?php include('footer.php'); ?>