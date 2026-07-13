<?php
require_once('db_config.php');
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}
include('header.php');

$user_id = $_SESSION['user_id'];

// Get order count
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM orders WHERE user_id = ?");
$stmt->execute([$user_id]);
$order_count = $stmt->fetch()['count'];

// Get total spent
$stmt = $conn->prepare("SELECT COALESCE(SUM(total_price), 0) as total FROM orders WHERE user_id = ?");
$stmt->execute([$user_id]);
$total_spent = $stmt->fetch()['total'];

// Get recent orders
$stmt = $conn->prepare("
    SELECT o.*, COUNT(oi.id) as item_count 
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.user_id = ?
    GROUP BY o.id
    ORDER BY o.order_date DESC
    LIMIT 5
");
$stmt->execute([$user_id]);
$orders_result = $stmt;
?>

<div class="account-container">
    <!-- Sidebar -->
    <aside class="account-sidebar">
        <div class="sidebar-header">
            <h3>My Account</h3>
        </div>
        <nav class="sidebar-nav">
            <a href="account.php" class="active">
                <span class="nav-icon">◆</span> Overview
            </a>
            <a href="orders.php">
                <span class="nav-icon">◈</span> My Orders
            </a>
            <a href="profile.php">
                <span class="nav-icon">◇</span> Profile Settings
            </a>
            <a href="logout.php" class="logout-link">
                <span class="nav-icon">◉</span> Logout
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="account-main">
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">🛍</div>
                <div class="stat-content">
                    <h4>Total Orders</h4>
                    <p class="stat-number"><?php echo $order_count; ?></p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">♡</div>
                <div class="stat-content">
                    <h4>Wishlist</h4>
                    <p class="stat-number">0</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">$</div>
                <div class="stat-content">
                    <h4>Total Spent</h4>
                    <p class="stat-number"><?php echo number_format($total_spent, 2); ?> <span class="currency">BD</span></p>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="orders-section">
            <div class="section-header">
                <h3>Recent Orders</h3>
                <a href="orders.php" class="view-all">View All →</a>
            </div>
            
            <div class="orders-table-wrapper">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Items</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($orders_result && $orders_result->rowCount() > 0): ?>
                            <?php while($order = $orders_result->fetch()): ?>
                                <tr>
                                    <td class="order-id">#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                    <td class="order-total"><?php echo number_format($order['total_price'], 2); ?> BD</td>
                                    <td>
                                        <span class="status-badge status-completed">Completed</span>
                                    </td>
                                    <td><?php echo $order['item_count']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="empty-state">
                                    <div class="empty-state-content">
                                        <div class="empty-icon-line"></div>
                                        <p>No orders yet</p>
                                        <small>Start shopping to see your orders here</small>
                                        <a href="catalog.php" class="shop-now-btn">Shop Now</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<?php include('footer.php'); ?>