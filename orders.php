<?php
require_once('db_config.php');

// Restrict access to logged-in users only
if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}
include('header.php');

$user_id = $_SESSION['user_id'];

// Fetch all orders for the logged-in user with their total item counts
$orders_result = $conn->query("
    SELECT o.*, COUNT(oi.id) as item_count 
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.user_id = $user_id
    GROUP BY o.id
    ORDER BY o.order_date DESC
");
?>

<div class="account-container">
    <!-- Sidebar (Matches account.php layout) -->
    <aside class="account-sidebar">
        <div class="sidebar-header">
            <h3>My Account</h3>
        </div>
        <nav class="sidebar-nav">
            <a href="account.php">
                <span class="nav-icon">◆</span> Overview
            </a>
            <a href="orders.php" class="active">
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

    <!-- Main Content Area -->
    <main class="account-main">
        <div class="orders-section">
            <div class="section-header">
                <h3>Order History</h3>
            </div>
            
            <div class="orders-table-wrapper">
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date Purchased</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Items Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($orders_result && $orders_result->num_rows > 0): ?>
                            <?php while($order = $orders_result->fetch_assoc()): ?>
                                <tr>
                                    <!-- Formats Order IDs to a clean 6-digit zero-padded format -->
                                    <td class="order-id">#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                    <td><?php echo date('M d, Y — h:i A', strtotime($order['order_date'])); ?></td>
                                    <td class="order-total"><?php echo number_format($order['total_price'], 2); ?> BD</td>
                                    <td>
                                        <span class="status-badge status-completed">Completed</span>
                                    </td>
                                    <td><?php echo $order['item_count']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <!-- Empty State View matching the global theme structure -->
                            <tr>
                                <td colspan="5" class="empty-state">
                                    <div class="empty-state-content">
                                        <div class="empty-icon-line"></div>
                                        <p>You haven't placed any orders yet</p>
                                        <small>Explore our high-quality essentials to make your first fit check!</small>
                                        <a href="catalog.php" class="shop-now-btn">Explore Catalog</a>
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