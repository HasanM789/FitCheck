<?php
ob_start();
require_once('db_config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $password]);
        header("Location: login.php?registered=true");
        exit();
    } catch (PDOException $e) {
        $error = "Registration failed: " . $e->getMessage();
    }
}
include('header.php');
?>

<div class="fc-auth-card">
    <h2>Create Account</h2>
    <?php if(isset($error)) echo "<p style='color:red; text-align:center;'>$error</p>"; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" class="fc-input" required>
        <input type="email" name="email" placeholder="Email" class="fc-input" required>
        <input type="password" name="password" placeholder="Password" class="fc-input" required>
        <button type="submit" class="fc-btn">Register</button>
    </form>
</div>

<?php include('footer.php'); ob_end_flush(); ?>