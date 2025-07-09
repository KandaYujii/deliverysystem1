<?php
session_start();
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'rider') {
    header('Location: rider_login.php');
    exit;
}

$page_title = "Rider Dashboard - Rimbunan Cafe";
include 'config/database.php';

// Handle delivery status update
if (isset($_POST['update_delivery'])) {
    $order_id = $_POST['order_id'];
    $action = $_POST['action'];
    
    if ($action === 'accept') {
        $stmt = $pdo->prepare("UPDATE orders SET delivery_status = 1 WHERE order_id = ?");
        $stmt->execute([$order_id]);
    } elseif ($action === 'delivered') {
        $stmt = $pdo->prepare("UPDATE orders SET order_status = 'Delivered', delivery_status = 2 WHERE order_id = ?");
        $stmt->execute([$order_id]);
    }
}

// Handle profile update
if (isset($_POST['update_profile'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $vehicle = $_POST['vehicle'];
    $password = $_POST['password'];
    
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE rider SET rider_username = ?, rider_email = ?, rider_phonenumber = ?, rider_vehicleinfo = ?, rider_password = ? WHERE rider_id = ?");
        $stmt->execute([$username, $email, $phone, $vehicle, $hashed_password, $_SESSION['user_id']]);
    } else {
        $stmt = $pdo->prepare("UPDATE rider SET rider_username = ?, rider_email = ?, rider_phonenumber = ?, rider_vehicleinfo = ? WHERE rider_id = ?");
        $stmt->execute([$username, $email, $phone, $vehicle, $_SESSION['user_id']]);
    }
    
    $_SESSION['username'] = $username;
}

// Get rider info
$stmt = $pdo->prepare("SELECT * FROM rider WHERE rider_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$rider = $stmt->fetch();

// Get assigned orders with payment method
$stmt = $pdo->prepare("SELECT o.*, c.cust_username, c.cust_address, c.cust_phonenumber, o.payment_method,
                      GROUP_CONCAT(CONCAT(p.product_name, ' (', od.qty, ')') SEPARATOR ', ') as items
                      FROM orders o 
                      LEFT JOIN customer c ON o.cust_id = c.cust_id
                      LEFT JOIN order_details od ON o.order_id = od.orders_id 
                      LEFT JOIN product p ON od.product_id = p.product_id 
                      WHERE o.rider_id = ? AND o.order_status IN ('In Delivery', 'Preparing')
                      GROUP BY o.order_id 
                      ORDER BY o.order_date DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();

// Calculate earnings
$today = date('Y-m-d');
$week_start = date('Y-m-d', strtotime('monday this week'));
$month_start = date('Y-m-01');

// Today's earnings
$stmt = $pdo->prepare("SELECT SUM(total_price * 0.1) as total FROM orders WHERE rider_id = ? AND DATE(order_date) = ? AND order_status = 'Delivered'");
$stmt->execute([$_SESSION['user_id'], $today]);
$today_earnings = $stmt->fetch()['total'] ?? 0;

// This week's earnings
$stmt = $pdo->prepare("SELECT SUM(total_price * 0.1) as total FROM orders WHERE rider_id = ? AND DATE(order_date) >= ? AND order_status = 'Delivered'");
$stmt->execute([$_SESSION['user_id'], $week_start]);
$week_earnings = $stmt->fetch()['total'] ?? 0;

// This month's earnings
$stmt = $pdo->prepare("SELECT SUM(total_price * 0.1) as total FROM orders WHERE rider_id = ? AND DATE(order_date) >= ? AND order_status = 'Delivered'");
$stmt->execute([$_SESSION['user_id'], $month_start]);
$month_earnings = $stmt->fetch()['total'] ?? 0;

// Payment method breakdown for today
$stmt = $pdo->prepare("SELECT 
    SUM(CASE WHEN payment_method = 'cod' THEN total_price * 0.1 ELSE 0 END) as cod_total,
    SUM(CASE WHEN payment_method = 'qr' THEN total_price * 0.1 ELSE 0 END) as qr_total,
    COUNT(CASE WHEN payment_method = 'cod' THEN 1 END) as cod_count,
    COUNT(CASE WHEN payment_method = 'qr' THEN 1 END) as qr_count
    FROM orders 
    WHERE rider_id = ? AND DATE(order_date) = ? AND order_status = 'Delivered'");
$stmt->execute([$_SESSION['user_id'], $today]);
$payment_breakdown = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<div class="dashboard">
    <div class="dashboard-header">
        <div class="container">
            <div class="dashboard-nav">
                <div class="logo">🛵 Rider Dashboard</div>
                <div class="nav-actions">
                    <button class="btn btn-secondary" onclick="refreshPage()" style="margin-right: 1rem;">🔄 Refresh</button>
                    <a href="rider_reports.php" class="btn btn-secondary" style="margin-right: 1rem;">📊 Reports</a>
                    <button class="btn btn-secondary" onclick="showTab('profile')" style="margin-right: 1rem;">👤 Profile</button>
                    <span>Welcome, <?php echo $_SESSION['username']; ?>!</span>
                    <a href="logout.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="dashboard-content">
            <div class="dashboard-tabs">
                <button class="tab-btn active" onclick="showTab('orders')">📦 My Deliveries</button>
                <button class="tab-btn" onclick="showTab('profile')">👤 Profile</button>
                <button class="tab-btn" onclick="showTab('earnings')">💰 Earnings</button>
                <button class="tab-btn" onclick="showTab('statistics')">📊 Statistics</button>
            </div>
            
            <!-- Orders Tab -->
            <div id="orders" class="tab-content active">
                <h2>Assigned Deliveries</h2>
                
                <?php if (empty($orders)): ?>
                    <div style="background: white; padding: 2rem; border-radius: 15px; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                        <h3>No deliveries assigned</h3>
                        <p>You will see your assigned deliveries here when staff assigns them to you.</p>
                    </div>
                <?php else: ?>
                    <div style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Items</th>
                                    <th>Payment</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>#<?php echo $order['order_id']; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($order['cust_username']); ?></strong><br>
                                            <small>📞 <?php echo htmlspecialchars($order['cust_phonenumber']); ?></small><br>
                                            <small>📍 <?php echo htmlspecialchars($order['cust_address']); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($order['items']); ?></td>
                                        <td>
                                            <span class="status-badge <?php echo ($order['payment_method'] ?? 'cod') === 'cod' ? 'status-pending' : 'status-completed'; ?>">
                                                <?php echo ($order['payment_method'] ?? 'cod') === 'cod' ? '💵 COD' : '📱 QR Paid'; ?>
                                            </span>
                                        </td>
                                        <td>RM <?php echo number_format($order['total_price'], 2); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo strtolower(str_replace(' ', '', $order['order_status'])); ?>">
                                                <?php echo $order['order_status']; ?>
                                            </span>
                                            <?php if ($order['delivery_status'] == 1): ?>
                                                <br><small style="color: #28a745;">✓ Accepted</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <form method="POST" style="display: inline-block;">
                                                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                                
                                                <?php if ($order['delivery_status'] == 0): ?>
                                                    <button type="submit" name="update_delivery" value="accept" class="btn btn-primary" style="font-size: 0.875rem; margin-bottom: 0.5rem;">
                                                        ✅ Accept Delivery
                                                    </button>
                                                    <input type="hidden" name="action" value="accept">
                                                <?php elseif ($order['delivery_status'] == 1 && $order['order_status'] !== 'Delivered'): ?>
                                                    <button type="submit" name="update_delivery" value="delivered" class="btn btn-primary" style="font-size: 0.875rem;">
                                                        📦 Mark as Delivered
                                                    </button>
                                                    <input type="hidden" name="action" value="delivered">
                                                <?php else: ?>
                                                    <span style="color: #28a745; font-weight: 500;">✓ Completed</span>
                                                <?php endif; ?>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Profile Tab -->
            <div id="profile" class="tab-content">
                <h2>Profile Settings</h2>
                <div style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                    <form method="POST">
                        <div class="form-group">
                            <label>Rider ID (Read Only)</label>
                            <input type="text" value="<?php echo $rider['rider_id']; ?>" class="form-control" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($rider['rider_username']); ?>" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($rider['rider_email']); ?>" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($rider['rider_phonenumber']); ?>" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="vehicle">Vehicle Information</label>
                            <input type="text" id="vehicle" name="vehicle" value="<?php echo htmlspecialchars($rider['rider_vehicleinfo']); ?>" class="form-control" placeholder="e.g., Honda Wave 125, ABC1234" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">New Password (leave blank to keep current)</label>
                            <input type="password" id="password" name="password" class="form-control">
                        </div>
                        
                        <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>
            
            <!-- Earnings Tab -->
            <div id="earnings" class="tab-content">
                <h2>💰 Earnings Overview</h2>
                
                <!-- Earnings Cards -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                    <div style="background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 2rem; border-radius: 15px; text-align: center; box-shadow: 0 5px 15px rgba(40,167,69,0.3);">
                        <div style="font-size: 2.5rem; margin-bottom: 1rem;">📅</div>
                        <h3 style="margin-bottom: 1rem;">Today's Earnings</h3>
                        <p style="font-size: 1.5rem; font-weight: 600; margin: 0;">
                            RM <?php echo number_format($today_earnings, 2); ?>
                        </p>
                    </div>
                    <div style="background: linear-gradient(135deg, #17a2b8, #138496); color: white; padding: 2rem; border-radius: 15px; text-align: center; box-shadow: 0 5px 15px rgba(23,162,184,0.3);">
                        <div style="font-size: 2.5rem; margin-bottom: 1rem;">📊</div>
                        <h3 style="margin-bottom: 1rem;">This Week</h3>
                        <p style="font-size: 1.5rem; font-weight: 600; margin: 0;">
                            RM <?php echo number_format($week_earnings, 2); ?>
                        </p>
                    </div>
                    <div style="background: linear-gradient(135deg, #8B4513, #A0522D); color: white; padding: 2rem; border-radius: 15px; text-align: center; box-shadow: 0 5px 15px rgba(139,69,19,0.3);">
                        <div style="font-size: 2.5rem; margin-bottom: 1rem;">📈</div>
                        <h3 style="margin-bottom: 1rem;">This Month</h3>
                        <p style="font-size: 1.5rem; font-weight: 600; margin: 0;">
                            RM <?php echo number_format($month_earnings, 2); ?>
                        </p>
                    </div>
                </div>
                
                <!-- Payment Method Breakdown -->
                <div style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                    <h3 style="color: #8B4513; margin-bottom: 2rem; text-align: center;">💳 Today's Payment Breakdown</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                        <div style="text-align: center; padding: 1.5rem; background: #fff3cd; border-radius: 10px; border: 2px solid #ffeaa7;">
                            <div style="font-size: 2rem; margin-bottom: 0.5rem;">💵</div>
                            <h4 style="color: #856404; margin-bottom: 1rem;">Cash on Delivery</h4>
                            <p style="font-size: 1.25rem; font-weight: 600; color: #856404; margin-bottom: 0.5rem;">
                                RM <?php echo number_format($payment_breakdown['cod_total'] ?? 0, 2); ?>
                            </p>
                            <small style="color: #856404;"><?php echo $payment_breakdown['cod_count'] ?? 0; ?> orders</small>
                        </div>
                        <div style="text-align: center; padding: 1.5rem; background: #d1ecf1; border-radius: 10px; border: 2px solid #bee5eb;">
                            <div style="font-size: 2rem; margin-bottom: 0.5rem;">📱</div>
                            <h4 style="color: #0c5460; margin-bottom: 1rem;">QR Payment</h4>
                            <p style="font-size: 1.25rem; font-weight: 600; color: #0c5460; margin-bottom: 0.5rem;">
                                RM <?php echo number_format($payment_breakdown['qr_total'] ?? 0, 2); ?>
                            </p>
                            <small style="color: #0c5460;"><?php echo $payment_breakdown['qr_count'] ?? 0; ?> orders</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Statistics Tab -->
            <div id="statistics" class="tab-content">
                <h2>📊 Delivery Statistics & History</h2>
                
                <?php
                // Get delivery statistics
                $stmt = $pdo->prepare("SELECT 
                    COUNT(*) as total_deliveries,
                    COUNT(CASE WHEN order_status = 'Delivered' THEN 1 END) as completed_deliveries,
                    COUNT(CASE WHEN order_status = 'In Delivery' THEN 1 END) as in_progress_deliveries,
                    AVG(total_price) as avg_order_value,
                    SUM(total_price * 0.1) as total_earnings
                    FROM orders 
                    WHERE rider_id = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $stats = $stmt->fetch();
                
                // Get delivery history
                $stmt = $pdo->prepare("SELECT o.*, c.cust_username, c.cust_address, c.cust_phonenumber, o.payment_method,
                                      GROUP_CONCAT(CONCAT(p.product_name, ' (', od.qty, ')') SEPARATOR ', ') as items
                                      FROM orders o 
                                      LEFT JOIN customer c ON o.cust_id = c.cust_id
                                      LEFT JOIN order_details od ON o.order_id = od.orders_id 
                                      LEFT JOIN product p ON od.product_id = p.product_id 
                                      WHERE o.rider_id = ?
                                      GROUP BY o.order_id 
                                      ORDER BY o.order_date DESC
                                      LIMIT 20");
                $stmt->execute([$_SESSION['user_id']]);
                $delivery_history = $stmt->fetchAll();
                ?>
                
                <!-- Statistics Cards -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                    <div style="background: linear-gradient(135deg, #17a2b8, #138496); color: white; padding: 1.5rem; border-radius: 15px; text-align: center; box-shadow: 0 5px 15px rgba(23,162,184,0.3);">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">📦</div>
                        <h4 style="margin-bottom: 0.5rem;">Total Deliveries</h4>
                        <p style="font-size: 1.5rem; font-weight: 600; margin: 0;"><?php echo $stats['total_deliveries'] ?? 0; ?></p>
                    </div>
                    <div style="background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 1.5rem; border-radius: 15px; text-align: center; box-shadow: 0 5px 15px rgba(40,167,69,0.3);">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">✅</div>
                        <h4 style="margin-bottom: 0.5rem;">Completed</h4>
                        <p style="font-size: 1.5rem; font-weight: 600; margin: 0;"><?php echo $stats['completed_deliveries'] ?? 0; ?></p>
                    </div>
                    <div style="background: linear-gradient(135deg, #ffc107, #e0a800); color: white; padding: 1.5rem; border-radius: 15px; text-align: center; box-shadow: 0 5px 15px rgba(255,193,7,0.3);">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">🚚</div>
                        <h4 style="margin-bottom: 0.5rem;">In Progress</h4>
                        <p style="font-size: 1.5rem; font-weight: 600; margin: 0;"><?php echo $stats['in_progress_deliveries'] ?? 0; ?></p>
                    </div>
                    <div style="background: linear-gradient(135deg, #8B4513, #A0522D); color: white; padding: 1.5rem; border-radius: 15px; text-align: center; box-shadow: 0 5px 15px rgba(139,69,19,0.3);">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">💰</div>
                        <h4 style="margin-bottom: 0.5rem;">Total Earned</h4>
                        <p style="font-size: 1.5rem; font-weight: 600; margin: 0;">RM <?php echo number_format($stats['total_earnings'] ?? 0, 2); ?></p>
                    </div>
                </div>
                
                <!-- Average Order Value -->
                <div style="background: white; padding: 1.5rem; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 2rem; text-align: center;">
                    <h4 style="color: #8B4513; margin-bottom: 1rem;">📈 Average Order Value</h4>
                    <p style="font-size: 1.25rem; font-weight: 600; color: #28a745; margin: 0;">
                        RM <?php echo number_format($stats['avg_order_value'] ?? 0, 2); ?>
                    </p>
                </div>
                
                <!-- Delivery History -->
                <div style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
                    <div style="background: #8B4513; color: white; padding: 1rem;">
                        <h3 style="margin: 0;">📋 Recent Delivery History</h3>
                    </div>
                    
                    <?php if (empty($delivery_history)): ?>
                        <div style="padding: 2rem; text-align: center; color: #666;">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">📦</div>
                            <h4>No delivery history yet</h4>
                            <p>Your completed deliveries will appear here</p>
                        </div>
                    <?php else: ?>
                        <div style="max-height: 400px; overflow-y: auto;">
                            <table class="data-table" style="margin: 0;">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Items</th>
                                        <th>Payment</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($delivery_history as $order): ?>
                                        <tr>
                                            <td>#<?php echo $order['order_id']; ?></td>
                                            <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($order['cust_username']); ?></strong><br>
                                                <small>📍 <?php echo htmlspecialchars(substr($order['cust_address'], 0, 30)) . (strlen($order['cust_address']) > 30 ? '...' : ''); ?></small>
                                            </td>
                                            <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                <?php echo htmlspecialchars($order['items']); ?>
                                            </td>
                                            <td>
                                                <span class="status-badge <?php echo ($order['payment_method'] ?? 'cod') === 'cod' ? 'status-pending' : 'status-completed'; ?>">
                                                    <?php echo ($order['payment_method'] ?? 'cod') === 'cod' ? '💵 COD' : '📱 QR'; ?>
                                                </span>
                                            </td>
                                            <td>RM <?php echo number_format($order['total_price'], 2); ?></td>
                                            <td>
                                                <span class="status-badge status-<?php echo strtolower(str_replace(' ', '', $order['order_status'])); ?>">
                                                    <?php echo $order['order_status']; ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="js/main.js"></script>

</body>
</html>