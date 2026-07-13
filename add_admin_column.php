<?php
require_once('db_config.php');

try {
    // Check if column already exists
    $result = $conn->query("PRAGMA table_info(users)");
    $columns = $result->fetchAll();
    $hasAdminColumn = false;
    
    foreach ($columns as $col) {
        if ($col['name'] == 'is_admin') {
            $hasAdminColumn = true;
            break;
        }
    }
    
    if (!$hasAdminColumn) {
        $conn->exec("ALTER TABLE users ADD COLUMN is_admin INTEGER DEFAULT 0");
        echo "✅ Admin column added to users table!<br>";
    } else {
        echo "✅ Admin column already exists.<br>";
    }
    
    // Show all users with their admin status
    $users = $conn->query("SELECT id, username, is_admin FROM users")->fetchAll();
    echo "<br><h3>Current Users:</h3>";
    foreach ($users as $user) {
        echo "- " . $user['username'] . " (ID: " . $user['id'] . ") - " . ($user['is_admin'] ? '✅ Admin' : '👤 User') . "<br>";
    }
    
    echo "<br><strong>To make yourself admin, visit:</strong><br>";
    echo "<a href='make_admin.php?id=1'>Make User ID 1 Admin</a> (change the ID to match your user ID)";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>