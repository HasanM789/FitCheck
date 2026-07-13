<?php
// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get the session ID before destroying
$session_id = session_id();

// Clear all session variables
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Delete session from database
if (!empty($session_id)) {
    try {
        require_once('db_config.php');
        $stmt = $conn->prepare("DELETE FROM sessions WHERE session_id = ?");
        $stmt->execute([$session_id]);
    } catch (Exception $e) {
        // Ignore errors
    }
}

// Redirect to home page
header("Location: index.php");
exit();
?>