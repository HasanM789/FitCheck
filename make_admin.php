<?php
require_once('db_config.php');

// Check if user ID is provided
$user_id = $_GET['id'] ?? 1;

try {
    // Update user to admin
    $stmt = $conn->prepare("UPDATE users SET is_admin = 1 WHERE id = ?");
    $stmt->execute([$user_id]);
    
    // Check if it worked
    $stmt = $conn->prepare("SELECT username, is_admin FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "✅ User '" . $user['username'] . "' (ID: $user_id) is now an ADMIN!<br>";
        echo "<a href='admin.php'>Go to Admin Panel</a><br>";
        echo "<a href='account.php'>Back to Account</a>";
    } else {
        echo "❌ User not found. Try a different ID.<br>";
        echo "Check your user ID in the database.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>