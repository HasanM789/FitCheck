<?php
require_once('db_config.php');
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}
include('header.php');

$session_id = $_SESSION['cart_session_id'];

// Check if cart is empty
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM cart WHERE session_id = ?");
$stmt->execute([$session_id]);
$cart_count = $stmt->fetch()['count'];

if ($cart_count == 0) {
    header("Location: cart.php");
    exit();
}

// Process checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    $user_id = $_SESSION['user_id'];
    $total = 0;
    
    // Get cart items
    $stmt = $conn->prepare("
        SELECT c.product_id, c.quantity, p.price 
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.session_id = ?
    ");
    $stmt->execute([$session_id]);
    $cart_items = $stmt->fetchAll();
    
    foreach ($cart_items as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    
    // Create order
    $conn->beginTransaction();
    try {
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price) VALUES (?, ?)");
        $stmt->execute([$user_id, $total]);
        $order_id = $conn->lastInsertId();
        
        // Add order items
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, selected_size, selected_color) VALUES (?, ?, ?, ?, ?)");
        $default_size = 'M';
        $default_color = 'Black';
        
        foreach ($cart_items as $item) {
            $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $default_size, $default_color]);
        }
        
        // Clear cart
        $stmt = $conn->prepare("DELETE FROM cart WHERE session_id = ?");
        $stmt->execute([$session_id]);
        
        $conn->commit();
        
        header("Location: account.php?order=success");
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        $error = "Order failed: " . $e->getMessage();
    }
}

// Get cart items for display
$stmt = $conn->prepare("
    SELECT c.product_id, c.quantity, p.name, p.description, p.price 
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.session_id = ?
");
$stmt->execute([$session_id]);
$cart_items = $stmt->fetchAll();

$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<div class="checkout-container">
    <div class="checkout-header">
        <h1>Checkout</h1>
        <p class="checkout-subtitle">Review your order and confirm to complete your purchase</p>
    </div>

    <?php if (isset($error)): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="checkout-grid">
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
                            <span class="item-price"><?php echo number_format($item['price'] * $item['quantity'], 2); ?> BD</span>
                        </div>
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
                    <button type="submit" name="place_order" class="place-order-btn">Place Order</button>
                </form>
                <a href="cart.php" class="back-to-cart">← Back to Cart</a>
            </div>
        </div>
    </div>
</div>

<?php include('footer.php'); ?>