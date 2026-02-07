<?php
// Admin Authentication Check
// Include this file at the top of every admin page

session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Store the requested page to redirect after login
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    
    // Redirect to login page
    header('Location: login.php');
    exit;
}

// Initialize session variables if not set
if (!isset($_SESSION['last_activity'])) {
    $_SESSION['last_activity'] = time();
}

if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
}

if (!isset($_SESSION['login_time'])) {
    $_SESSION['login_time'] = time();
}

// Check session timeout (30 minutes of inactivity)
$timeout_duration = 1800; // 30 minutes in seconds

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    // Session expired
    session_unset();
    session_destroy();
    header('Location: login.php?timeout=1');
    exit;
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Regenerate session ID periodically for security
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} else if (time() - $_SESSION['created'] > 1800) {
    // Regenerate session ID every 30 minutes
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}
?>
