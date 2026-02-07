<?php
// Simple test to check if login works
session_start();

echo "<h1>Admin Login Test</h1>";

// Test database connection
try {
    require_once '../config/database.php';
    $database = new Database();
    $db = $database->connect();
    echo "<p style='color: green;'>✓ Database connection successful</p>";
    
    // Check if admin user exists
    $stmt = $db->query("SELECT * FROM admin_users WHERE username = 'admin'");
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "<p style='color: green;'>✓ Admin user exists</p>";
        echo "<p>Username: " . $admin['username'] . "</p>";
        echo "<p>Email: " . $admin['email'] . "</p>";
        
        // Test password
        if (password_verify('admin123', $admin['password'])) {
            echo "<p style='color: green;'>✓ Password verification works</p>";
        } else {
            echo "<p style='color: red;'>✗ Password verification failed</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ Admin user not found</p>";
        echo "<p>Please run setup.php first</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='login.php'>Go to Login Page</a></p>";
echo "<p><a href='../setup.php'>Run Setup</a></p>";
?>
