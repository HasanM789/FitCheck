<?php
// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get the session ID before destroying
$session_id = session_id();

// Clear session data
$_SESSION = array();

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