<?php
require_once('db_config.php');

echo "<h1>User List</h1>";

$users = $conn->query("SELECT id, username, email, is_admin FROM users")->fetchAll();

if (count($users) > 0) {
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Status</th></tr>";
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td><strong>" . $user['id'] . "</strong></td>";
        echo "<td>" . htmlspecialchars($user['username']) . "</td>";
        echo "<td>" . htmlspecialchars($user['email']) . "</td>";
        echo "<td>" . ($user['is_admin'] ? '✅ Admin' : '👤 User') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<br><strong>Your User ID is in the first column.</strong>";
    echo "<br>Example: If your ID is 1, go to: <strong>make_admin.php?id=1</strong>";
} else {
    echo "❌ No users found. Please register first.";
}
?>