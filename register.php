<?php
ob_start();
require_once('db_config.php');

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } elseif (strlen($password) < 4) {
        $error = "Password must be at least 4 characters.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "Username already taken.";
        } else {
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $error = "Email already registered.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                try {
                    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
                    $stmt->execute([$username, $email, $hashed_password]);
                    
                    $user_id = $conn->lastInsertId();
                    
                    if ($user_id > 0) {
                        // Auto-login the user with extended session
                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['username'] = $username;
                        $_SESSION['login_time'] = time();
                        $_SESSION['last_activity'] = time();
                        
                        header("Location: account.php");
                        exit();
                    } else {
                        $error = "Registration failed.";
                    }
                } catch (PDOException $e) {
                    $error = "Database error: " . $e->getMessage();
                }
            }
        }
    }
}

include('header.php');
?>

<div class="fc-auth-card">
    <h2>Create Account</h2>
    
    <?php if ($error): ?>
        <div style="background: rgba(220, 53, 69, 0.1); border: 1px solid #dc3545; padding: 12px; border-radius: 4px; margin-bottom: 20px; color: #dc3545;">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <form method="POST">
        <input type="text" name="username" placeholder="Username" class="fc-input" required>
        <input type="email" name="email" placeholder="Email" class="fc-input" required>
        <input type="password" name="password" placeholder="Password (min 4 chars)" class="fc-input" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" class="fc-input" required>
        <button type="submit" class="fc-btn">Register</button>
    </form>
    
    <p style="text-align:center; margin-top:20px; font-size: 12px; color: #888;">
        Already have an account? <a href="login.php" style="color:#dc3545;">Sign In</a>
    </p>
</div>

<?php include('footer.php'); ob_end_flush(); ?>