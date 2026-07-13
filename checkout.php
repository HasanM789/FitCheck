<?php
require_once('db_config.php');
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}
include('header.php');

// Check if cart is empty
if (empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// Process checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    $user_id = $_SESSION['user_id'];
    $total = 0;
    
    // Get cart items
    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $types = str_repeat('i', count($ids));
    $stmt->bind_param($types, ...$ids);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $cart_items = [];
    while ($item = $result->fetch_assoc()) {
        $item['quantity'] = $_SESSION['cart'][$item['id']];
        $item['subtotal'] = $item['price'] * $item['quantity'];
        $total += $item['subtotal'];
        $cart_items[] = $item;
    }
    
    // Create order
    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price) VALUES (?, ?)");
        $stmt->bind_param("id", $user_id, $total);
        $stmt->execute();
        $order_id = $conn->insert_id;
        
        // Add order items with default size/color
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, selected_size, selected_color) VALUES (?, ?, ?, ?, ?)");
        $default_size = 'M';
        $default_color = 'Black';
        
        foreach ($cart_items as $item) {
            $stmt->bind_param("iiiss", $order_id, $item['id'], $item['quantity'], $default_size, $default_color);
            $stmt->execute();
        }
        
        $conn->commit();
        
        // Clear cart
        $_SESSION['cart'] = [];
        
        header("Location: account.php?order=success");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Order failed: " . $e->getMessage();
    }
}

// Get cart items for display
$ids = array_keys($_SESSION['cart']);
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = $conn->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
$types = str_repeat('i', count($ids));
$stmt->bind_param($types, ...$ids);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$total = 0;
while ($item = $result->fetch_assoc()) {
    $item['quantity'] = $_SESSION['cart'][$item['id']];
    $item['subtotal'] = $item['price'] * $item['quantity'];
    $total += $item['subtotal'];
    $cart_items[] = $item;
}
?>

<div class="checkout-container">
    <div class="checkout-header">
        <h1>Checkout</h1>
        <p class="checkout-subtitle">Review your order and confirm to complete your purchase</p>
    </div>

    <?php if (isset($error)): ?>
        <div class="error-message">
            <span class="error-icon">⚠</span>
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <div class="checkout-grid">
        <!-- Left Column: Order Summary -->
        <div class="order-summary-card">
            <div class="card-header">
                <h2>Order Summary</h2>
                <span class="item-count"><?php echo count($cart_items); ?> items</span>
            </div>
            
            <div class="order-items-list">
                <?php foreach ($cart_items as $item): ?>
                    <div class="order-item">
                        <div class="order-item-info">
                            <div class="item-details">
                                <span class="item-name"><?php echo htmlspecialchars($item['name']); ?></span>
                                <span class="item-quantity">× <?php echo $item['quantity']; ?></span>
                            </div>
                            <span class="item-price"><?php echo number_format($item['subtotal'], 2); ?> BD</span>
                        </div>
                        <?php if (!empty($item['description'])): ?>
                            <div class="item-description-small"><?php echo htmlspecialchars(substr($item['description'], 0, 60)); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="order-divider"></div>

            <div class="order-totals">
                <div class="total-row">
                    <span>Subtotal</span>
                    <span><?php echo number_format($total, 2); ?> BD</span>
                </div>
                <div class="total-row shipping-row">
                    <span>Shipping</span>
                    <span class="shipping-free">Free</span>
                </div>
                <div class="total-divider"></div>
                <div class="total-row grand-total">
                    <span><strong>Total</strong></span>
                    <span class="grand-total-amount"><strong><?php echo number_format($total, 2); ?> BD</strong></span>
                </div>
            </div>
        </div>

        <!-- Right Column: Confirm Order -->
        <div class="confirm-order-card">
            <div class="card-header">
                <h2>Confirm Order</h2>
            </div>
            
            <div class="customer-info-section">
                <div class="info-group">
                    <span class="info-label">Customer</span>
                    <span class="info-value"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </div>
                <div class="info-group">
                    <span class="info-label">Items</span>
                    <span class="info-value"><?php echo count($cart_items); ?> product(s)</span>
                </div>
                <div class="info-group highlight-group">
                    <span class="info-label">Total Amount</span>
                    <span class="info-value highlight"><?php echo number_format($total, 2); ?> BD</span>
                </div>
            </div>

            <div class="order-actions">
                <form method="POST" class="place-order-form">
                    <button type="submit" name="place_order" class="place-order-btn">
                        <span class="btn-icon">✓</span> Place Order
                    </button>
                </form>
                <a href="cart.php" class="back-to-cart">← Back to Cart</a>
            </div>

            <div class="order-note">
                <p>By placing your order, you agree to our <a href="#" class="terms-link">Terms & Conditions</a>.</p>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>