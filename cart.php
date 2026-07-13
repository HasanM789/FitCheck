<?php 
// Session is already started in db_config.php
require_once('db_config.php'); 
include('header.php'); 

$session_id = $_SESSION['cart_session_id'];

// Handle remove item
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $product_id = (int)$_GET['remove'];
    $stmt = $conn->prepare("DELETE FROM cart WHERE session_id = ? AND product_id = ?");
    $stmt->execute([$session_id, $product_id]);
    header("Location: cart.php");
    exit();
}

// Handle increment
if (isset($_GET['increment']) && is_numeric($_GET['increment'])) {
    $product_id = (int)$_GET['increment'];
    $stmt = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE session_id = ? AND product_id = ?");
    $stmt->execute([$session_id, $product_id]);
    header("Location: cart.php");
    exit();
}

// Handle decrement
if (isset($_GET['decrement']) && is_numeric($_GET['decrement'])) {
    $product_id = (int)$_GET['decrement'];
    $stmt = $conn->prepare("UPDATE cart SET quantity = quantity - 1 WHERE session_id = ? AND product_id = ?");
    $stmt->execute([$session_id, $product_id]);
    
    // Check if quantity is 0 or less, then delete
    $stmt = $conn->prepare("SELECT quantity FROM cart WHERE session_id = ? AND product_id = ?");
    $stmt->execute([$session_id, $product_id]);
    $result = $stmt->fetch();
    if ($result && $result['quantity'] <= 0) {
        $stmt = $conn->prepare("DELETE FROM cart WHERE session_id = ? AND product_id = ?");
        $stmt->execute([$session_id, $product_id]);
    }
    
    header("Location: cart.php");
    exit();
}

// Clear cart
if (isset($_GET['clear'])) {
    $stmt = $conn->prepare("DELETE FROM cart WHERE session_id = ?");
    $stmt->execute([$session_id]);
    header("Location: cart.php");
    exit();
}

// Get cart items from database
$cart_items = [];
$total = 0;
$cart_count = 0;

$stmt = $conn->prepare("
    SELECT c.product_id, c.quantity, p.name, p.description, p.price 
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.session_id = ?
");
$stmt->execute([$session_id]);
$result = $stmt;

while ($item = $result->fetch()) {
    $item['subtotal'] = $item['price'] * $item['quantity'];
    $total += $item['subtotal'];
    $cart_count += $item['quantity'];
    $cart_items[] = $item;
}
?>

<div class="cart-page">
    <div class="cart-header">
        <h1>Shopping Cart</h1>
        <span class="cart-count"><?php echo $cart_count; ?> items</span>
    </div>

    <?php if (empty($cart_items)): ?>
        <div class="empty-cart">
            <div class="empty-cart-icon">🛒</div>
            <h2>Your cart is empty</h2>
            <p>Looks like you haven't added any items to your cart yet.</p>
            <a href="catalog.php" class="continue-shopping-btn">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="cart-content">
            <div class="cart-items">
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item">
                        <div class="cart-item-image">
                            <div class="image-placeholder">
                                <?php echo strtoupper(substr($item['name'], 0, 2)); ?>
                            </div>
                        </div>
                        <div class="cart-item-details">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p class="item-description"><?php echo htmlspecialchars($item['description']); ?></p>
                            <span class="item-price"><?php echo number_format($item['price'], 2); ?> BD</span>
                        </div>
                        <div class="cart-item-actions">
                            <div class="quantity-control">
                                <a href="cart.php?decrement=<?php echo $item['product_id']; ?>" class="qty-btn">−</a>
                                <span class="qty-display"><?php echo $item['quantity']; ?></span>
                                <a href="cart.php?increment=<?php echo $item['product_id']; ?>" class="qty-btn">+</a>
                            </div>
                            <a href="cart.php?remove=<?php echo $item['product_id']; ?>" class="remove-btn">Remove</a>
                        </div>
                        <div class="cart-item-total">
                            <span class="subtotal-label">SUBTOTAL</span>
                            <span class="subtotal-amount"><?php echo number_format($item['subtotal'], 2); ?> BD</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <h3>Order Summary</h3>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span><?php echo number_format($total, 2); ?> BD</span>
                </div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span class="shipping-free">Free</span>
                </div>
                <div class="summary-divider"></div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span class="total-amount"><?php echo number_format($total, 2); ?> BD</span>
                </div>
                <div class="cart-actions">
                    <a href="catalog.php" class="continue-shopping">← Continue Shopping</a>
                    <div class="action-buttons">
                        <a href="cart.php?clear=1" class="clear-cart-btn" onclick="return confirm('Clear all items from cart?');">Clear Cart</a>
                        <a href="checkout.php" class="checkout-btn">Proceed to Checkout →</a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include('footer.php'); ?>