<?php
ob_start();
require_once('db_config.php');

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['login_time'] = time();
            $_SESSION['last_activity'] = time();
            
            // Check if user is admin
            if (isset($user['is_admin']) && $user['is_admin'] == 1) {
                $_SESSION['is_admin'] = 1;
            }
            
            header("Location: account.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    }
}
include('header.php');
?>

<div class="fc-auth-card">
    <h2>Sign In</h2>
    
    <?php if ($error): ?>
        <div style="background: rgba(220, 53, 69, 0.1); border: 1px solid #dc3545; padding: 12px; border-radius: 4px; margin-bottom: 20px; color: #dc3545;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <form method="POST">
        <input type="text" name="username" placeholder="Username" class="fc-input" required>
        <input type="password" name="password" placeholder="Password" class="fc-input" required>
        <button type="submit" class="fc-btn">Login</button>
    </form>
    
    <p style="text-align:center; margin-top:20px; font-size: 12px; color: #888;">
        Don't have an account? <a href="register.php" style="color:#dc3545;">Join now</a>
    </p>
</div>

<?php include('footer.php'); ob_end_flush(); ?>