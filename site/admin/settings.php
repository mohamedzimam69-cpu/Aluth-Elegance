<?php
require_once 'auth.php'; // Authentication check
require_once '../config/database.php';

$database = new Database();
$db = $database->connect();

$message = '';

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    $stmt = $db->prepare("SELECT password FROM admin_users WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (password_verify($currentPassword, $admin['password'])) {
        if ($newPassword === $confirmPassword) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
            $stmt->execute([$hashedPassword, $_SESSION['admin_id']]);
            $message = "Password changed successfully!";
        } else {
            $message = "New passwords do not match!";
        }
    } else {
        $message = "Current password is incorrect!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin Panel</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <aside class="sidebar">
            <div class="logo">
                <h2>Aluth<span>Elegance</span></h2>
                <p>Admin Panel</p>
            </div>
            <nav class="admin-nav">
                <a href="index.php"><i class="fas fa-home"></i> Dashboard</a>
                <a href="products.php"><i class="fas fa-box"></i> Products</a>
                <a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
                <a href="categories.php"><i class="fas fa-tags"></i> Categories</a>
                <a href="settings.php" class="active"><i class="fas fa-cog"></i> Settings</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </aside>
        
        <main class="main-content">
            <header class="admin-header">
                <h1>Settings</h1>
            </header>
            
            <?php if ($message): ?>
                <div class="alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <div class="table-container">
                <h2>Change Password</h2>
                <form method="POST" style="max-width: 500px;">
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                    <button type="submit" name="change_password" class="btn-primary">
                        <i class="fas fa-key"></i> Change Password
                    </button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
