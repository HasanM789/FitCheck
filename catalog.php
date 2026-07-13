<?php 
require_once('db_config.php'); 
include('header.php'); 
?>

<div class="filter-menu-bar" style="display: flex; flex-direction: column; align-items: center; gap: 25px; margin-bottom: 40px;">
    
    <!-- Category Capsules -->
    <div style="display: flex; gap: 12px; flex-wrap: wrap; justify-content: center;">
        <?php $categories = ['All', 'Tops', 'Bottoms', 'Hoodies', 'Shoes']; ?>
        <?php foreach ($categories as $cat): ?>
            <a href="catalog.php?category=<?php echo $cat; ?>" 
               class="category-capsule <?php echo ($_GET['category'] ?? 'All') == $cat ? 'active' : ''; ?>">
               <?php echo $cat; ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<div class="modern-storefront-grid">
    <?php
    // Build the query
    $sql = "SELECT * FROM products";
    $category = $_GET['category'] ?? 'All';
    
    if ($category !== 'All') {
        $sql = "SELECT * FROM products WHERE category = '" . $category . "'";
    }
    
    // Execute query
    try {
        $result = $conn->query($sql);
        
        // Count products using fetchAll (works in SQLite)
        $products = $result->fetchAll();
        $count = count($products);
        
        if ($count > 0) {
            foreach ($products as $item) {
                ?>
                <div class="premium-item-card">
                    <div class="product-image-container">
                        <div class="image-placeholder">
                            <span class="placeholder-icon">👕</span>
                            <span class="placeholder-code"><?php echo strtoupper(substr($item['name'], 0, 2)); ?></span>
                        </div>
                    </div>
                    <div class="product-info">
                        <div class="app-canvas-container">PREMIUM OVERVIEW</div>
                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p><?php echo htmlspecialchars($item['description']); ?></p>
                        <div class="product-price"><?php echo number_format($item['price'], 2); ?> BD</div>
                        <!-- DEBUG: Show product ID (remove after testing) -->
                        <div style="font-size: 10px; color: #666; margin-top: 5px;">ID: <?php echo $item['id']; ?></div>
                    </div>
                    <form action="cart_action.php" method="POST">
                        <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                        <button type="submit" class="premium-purchase-btn">Add to Cart</button>
                    </form>
                </div>
                <?php
            }
        } else {
            echo "<p style='text-align: center; width: 100%; color: #94a3b8; padding: 40px 0;'>No items found in this category.</p>";
        }
    } catch (Exception $e) {
        echo "<p style='text-align: center; width: 100%; color: #dc3545; padding: 40px 0;'>Error loading products: " . $e->getMessage() . "</p>";
    }
    ?>
</div>

<?php include('footer.php'); ?>