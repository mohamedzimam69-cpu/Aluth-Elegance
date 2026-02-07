<?php
session_start();

// Check if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

$error = '';
$timeout_message = '';

if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    $timeout_message = 'You have been logged out successfully.';
}

// Check if session timed out
if (isset($_GET['timeout']) && $_GET['timeout'] == 1) {
    $timeout_message = 'Your session has expired. Please login again.';
}

// Rate limiting - prevent brute force attacks
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt_time'] = time();
}

// Reset attempts after 15 minutes
if (time() - $_SESSION['last_attempt_time'] > 900) {
    $_SESSION['login_attempts'] = 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if too many failed attempts
    if ($_SESSION['login_attempts'] >= 5) {
        $wait_time = 900 - (time() - $_SESSION['last_attempt_time']);
        if ($wait_time > 0) {
            $error = 'Too many failed attempts. Please try again in ' . ceil($wait_time / 60) . ' minutes.';
        } else {
            $_SESSION['login_attempts'] = 0;
        }
    }
    
    if (empty($error)) {
        require_once '../config/database.php';
        $database = new Database();
        $db = $database->connect();
        
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        
        // Validate input
        if (empty($username) || empty($password)) {
            $error = 'Please enter both username and password';
        } else {
            $stmt = $db->prepare("SELECT * FROM admin_users WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($admin && password_verify($password, $admin['password'])) {
                // Successful login
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['login_time'] = time();
                $_SESSION['last_activity'] = time();
                $_SESSION['created'] = time();
                
                // Reset login attempts
                $_SESSION['login_attempts'] = 0;
                
                // Log successful login (optional - skip if table doesn't exist)
                try {
                    $ip_address = $_SERVER['REMOTE_ADDR'];
                    $stmt = $db->prepare("INSERT INTO admin_login_log (admin_id, ip_address, status) VALUES (?, ?, 'success')");
                    $stmt->execute([$admin['id'], $ip_address]);
                } catch (Exception $e) {
                    // Ignore if table doesn't exist
                }
                
                // Redirect to requested page or dashboard
                if (isset($_SESSION['redirect_after_login'])) {
                    $redirect = $_SESSION['redirect_after_login'];
                    unset($_SESSION['redirect_after_login']);
                    header('Location: ' . $redirect);
                } else {
                    header('Location: index.php');
                }
                exit;
            } else {
                // Failed login
                $_SESSION['login_attempts']++;
                $_SESSION['last_attempt_time'] = time();
                
                // Log failed attempt (optional - skip if table doesn't exist)
                try {
                    if ($admin) {
                        $ip_address = $_SERVER['REMOTE_ADDR'];
                        $stmt = $db->prepare("INSERT INTO admin_login_log (admin_id, ip_address, status) VALUES (?, ?, 'failed')");
                        $stmt->execute([$admin['id'], $ip_address]);
                    }
                } catch (Exception $e) {
                    // Ignore if table doesn't exist
                }
                
                $remaining_attempts = 5 - $_SESSION['login_attempts'];
                if ($remaining_attempts > 0) {
                    $error = 'Invalid username or password. ' . $remaining_attempts . ' attempts remaining.';
                } else {
                    $error = 'Too many failed attempts. Please try again in 15 minutes.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Aluth Elegance</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <div style="text-align: center; margin-bottom: 20px;">
                <i class="fas fa-shield-alt" style="font-size: 48px; color: #D4AF37;"></i>
            </div>
            <h1>Aluth<span>Elegance</span></h1>
            <h2>Admin Login</h2>
            <p style="color: #666; font-size: 14px; margin-bottom: 20px;">Secure access for authorized personnel only</p>
            
            <?php if ($timeout_message): ?>
                <div class="error-message" style="background: #f39c12;">
                    <i class="fas fa-clock"></i> <?php echo $timeout_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <div style="position: relative;">
                        <i class="fas fa-user" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #999;"></i>
                        <input type="text" name="username" placeholder="Username" required style="padding-left: 45px;" autocomplete="username">
                    </div>
                </div>
                <div class="form-group">
                    <div style="position: relative;">
                        <i class="fas fa-lock" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #999;"></i>
                        <input type="password" name="password" id="password" placeholder="Password" required style="padding-left: 45px;" autocomplete="current-password">
                        <i class="fas fa-eye" id="togglePassword" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: #999; cursor: pointer;"></i>
                    </div>
                </div>
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Login to Dashboard
                </button>
            </form>
            <p class="login-note">
                <i class="fas fa-info-circle"></i> Default credentials: admin / admin123<br>
                <strong>Change password after first login!</strong>
            </p>
            <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-radius: 5px; font-size: 12px;">
                <i class="fas fa-lock" style="color: #856404;"></i> 
                <strong>Security Notice:</strong> This area is restricted to authorized administrators only. 
                All access attempts are logged and monitored.
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>
