<?php
require_once 'auth.php'; // Authentication check
require_once '../config/database.php';

$database = new Database();
$db = $database->connect();

// Get statistics
$totalProducts = $db->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$revenueResult = $db->query("SELECT SUM(total_amount) FROM orders")->fetchColumn();
$totalRevenue = $revenueResult ? $revenueResult : 0;
$pendingOrders = $db->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Aluth Elegance</title>
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
                <a href="index.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
                <a href="products.php"><i class="fas fa-box"></i> Products</a>
                <a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
                <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </aside>
        
        <main class="main-content">
            <header class="admin-header">
                <h1>Dashboard</h1>
                <div class="user-info">
                    <span><i class="fas fa-user-shield"></i> Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                    <?php if (isset($_SESSION['login_time'])): ?>
                    <span style="margin-left: 15px; font-size: 12px; color: #999;">
                        <i class="fas fa-clock"></i> Session: <?php echo date('H:i', $_SESSION['login_time']); ?>
                    </span>
                    <?php endif; ?>
                </div>
            </header>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-box"></i></div>
                    <div class="stat-info">
                        <h3><?php echo $totalProducts; ?></h3>
                        <p>Total Products</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-shopping-cart"></i></div>
                    <div class="stat-info">
                        <h3><?php echo $totalOrders; ?></h3>
                        <p>Total Orders</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-dollar-sign"></i></div>
                    <div class="stat-info">
                        <h3>$<?php echo number_format((float)$totalRevenue, 2); ?></h3>
                        <p>Total Revenue</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <div class="stat-info">
                        <h3><?php echo $pendingOrders; ?></h3>
                        <p>Pending Orders</p>
                    </div>
                </div>
            </div>
            
            <div class="recent-orders">
                <h2>Recent Orders</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $orders = $db->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
                        if (count($orders) > 0) {
                            foreach ($orders as $order) {
                                echo "<tr>
                                    <td>#{$order['id']}</td>
                                    <td>{$order['customer_name']}</td>
                                    <td>$" . number_format((float)$order['total_amount'], 2) . "</td>
                                    <td><span class='status-{$order['status']}'>" . ucfirst($order['status']) . "</span></td>
                                    <td>" . date('M d, Y', strtotime($order['created_at'])) . "</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' style='text-align:center;'>No orders yet</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
