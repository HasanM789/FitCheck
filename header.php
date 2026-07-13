<?php
// Start session - but ONLY if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include db_config for cart count
require_once('db_config.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FitCheck — Premium Apparel</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header class="fc-navbar-main">
    <a href="index.php" class="fc-logo-container">
        <svg class="fc-logo-svg" viewBox="0 0 100 100" width="38" height="38" xmlns="http://www.w3.org/2000/svg">
            <rect class="fc-box" x="5" y="5" width="90" height="90" fill="#dc3545"/>
            <polyline class="fc-check" points="20,50 40,70 80,30" fill="none" stroke="white" stroke-width="12" stroke-linecap="square"/>
        </svg>
        <div class="fc-logo-text-group">
            <span class="fc-logo-main">Fit Check.</span>
            <span class="fc-logo-sub">YOUR DAILY ESSENTIALS.</span>
        </div>
    </a>
    
    <nav class="fc-nav-links-group">
        <a href="index.php" class="fc-nav-item">Home</a>
        <a href="catalog.php" class="fc-nav-item">Catalog</a>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="fc-account-wrapper">
                <a href="account.php" class="fc-nav-item">Account</a>
                <div class="fc-account-dropdown">
                    <div class="fc-dropdown-user">
                        <span class="dropdown-username"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </div>
                    <div class="fc-dropdown-divider"></div>
                    <a href="account.php" class="fc-dropdown-item">Dashboard</a>
                    <a href="orders.php" class="fc-dropdown-item">My Orders</a>
                    <a href="profile.php" class="fc-dropdown-item">Profile Settings</a>
                    <div class="fc-dropdown-divider"></div>
                    <a href="logout.php" class="fc-dropdown-item fc-dropdown-logout">Logout</a>
                </div>
            </div>
        <?php else: ?>
            <div class="fc-account-wrapper">
                <a href="#" class="fc-nav-item">Account</a>
                <div class="fc-account-dropdown">
                    <div class="fc-dropdown-header">Welcome to FitCheck</div>
                    <div class="fc-dropdown-actions">
                        <a href="login.php" class="fc-btn-auth fc-btn-signin">Sign In</a>
                        <a href="register.php" class="fc-btn-auth fc-btn-join">Join</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Cart Button - Icon Only -->
        <a href="cart.php" class="fc-nav-item fc-nav-cart-icon">
            <div class="cart-icon-wrapper">
                <svg class="cart-icon" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1"/>
                    <circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                </svg>
                <span class="cart-badge">
                    <?php 
                    // Get cart count from database
                    $total_items = 0;
                    if (isset($_SESSION['cart_session_id'])) {
                        try {
                            $stmt = $conn->prepare("SELECT SUM(quantity) as total FROM cart WHERE session_id = ?");
                            $stmt->execute([$_SESSION['cart_session_id']]);
                            $result = $stmt->fetch();
                            $total_items = $result ? (int)$result['total'] : 0;
                        } catch (Exception $e) {
                            $total_items = 0;
                        }
                    }
                    echo $total_items; 
                    ?>
                </span>
            </div>
        </a>
    </nav>
</header>