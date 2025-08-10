<?php
session_start();
include 'config.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Get recent transactions
$transactions_sql = "SELECT t.*, 
    u1.name as sender_name, 
    u2.name as receiver_name 
    FROM transactions t 
    LEFT JOIN users u1 ON t.sender_id = u1.id 
    LEFT JOIN users u2 ON t.receiver_id = u2.id 
    WHERE t.sender_id = $user_id OR t.receiver_id = $user_id 
    ORDER BY t.created_at DESC LIMIT 10";
$transactions = $conn->query($transactions_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Paytm Clone</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <style>
        :root {
            --primary-color: #00BAF2;
            --secondary-color: #002E6E;
            --text-color: #333;
            --light-gray: #f5f5f5;
            --border-color: #ddd;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--light-gray);
            color: var(--text-color);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 20px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .site-title {
            font-size: 24px;
            margin: 0;
            color: white;
        }

        .dashboard {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 30px;
        }

        .sidebar {
            background: white;
            padding: 20px;
            border-radius: 10px;
            height: calc(100vh - 100px);
            position: sticky;
            top: 20px;
        }

        .sidebar-nav {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: var(--text-color);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .sidebar-nav a i {
            margin-right: 15px;
            width: 20px;
            text-align: center;
            font-size: 18px;
            color: var(--primary-color);
        }

        .sidebar-nav a:hover {
            background: #f8f9fa;
            border-left-color: var(--primary-color);
            color: var(--primary-color);
        }

        .sidebar-nav a.active {
            background: #f0f7ff;
            border-left-color: var(--primary-color);
            color: var(--primary-color);
            font-weight: 600;
        }

        .balance-card {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .balance-card h3 {
            margin: 0;
            font-size: 16px;
            opacity: 0.9;
        }

        .balance-card h2 {
            margin: 10px 0 0;
            font-size: 28px;
            font-weight: 600;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .action-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .action-card i {
            font-size: 24px;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .action-card h3 {
            margin: 0;
            font-size: 16px;
            color: var(--text-color);
        }

        .transaction-history {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .transaction-history h3 {
            margin: 0 0 20px;
            color: var(--text-color);
        }

        .transactions-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .transaction-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .transaction-item:hover {
            background: #f0f0f0;
        }

        .transaction-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }

        .transaction-icon i {
            font-size: 18px;
        }

        .text-success {
            color: #28a745 !important;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        .transaction-details {
            flex: 1;
        }

        .transaction-details h4 {
            margin: 0;
            font-size: 16px;
            color: var(--text-color);
        }

        .transaction-details .date {
            font-size: 14px;
            color: #6c757d;
        }

        .transaction-amount {
            font-weight: 600;
            font-size: 16px;
        }

        .transaction-amount.received {
            color: #28a745;
        }

        .transaction-amount.sent {
            color: #dc3545;
        }

        .bg-success-light {
            background-color: rgba(40, 167, 69, 0.1);
        }

        .bg-danger-light {
            background-color: rgba(220, 53, 69, 0.1);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
        }

        .modal-content {
            background: #fff;
            width: 90%;
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            border-radius: 10px;
        }

        .btn {
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-secondary {
            background: #fff;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .btn-logout-text {
            display: inline-block;
        }
        
        .btn-logout-icon {
            display: none;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-left: auto;
        }

        .welcome-text {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .btn-logout {
            margin-left: auto;
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            .dashboard {
                grid-template-columns: 1fr;
            }
            
            .sidebar {
                height: auto;
                position: static;
            }
            
            .sidebar-nav {
                margin-bottom: 20px;
            }

            .sidebar-nav a {
                padding: 12px 15px;
            }

            .sidebar-nav a i {
                font-size: 16px;
            }
            
            .container {
                padding: 10px;
            }

            .btn-logout-text {
                display: none;
            }
            
            .btn-logout-icon {
                display: inline-block;
            }

            .btn {
                padding: 8px 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="site-title">Digital Wallet</h1>
            <div class="user-info">
                <span>Welcome, <?php echo $_SESSION['name']; ?></span>
                <a href="logout.php" class="btn btn-secondary">
                    <span class="btn-logout-text">Logout</span>
                    <i class="fas fa-sign-out-alt btn-logout-icon"></i>
                </a>
            </div>
        </div>
        
        <div class="dashboard">
            <div class="sidebar">
                <div class="balance-card">
                    <h3>Wallet Balance</h3>
                    <h2>₹<?php echo number_format($user['balance'], 2); ?></h2>
                </div>
                
                <nav class="sidebar-nav">
                    <a href="#" class="active"><i class="fas fa-home"></i> Dashboard</a>
                    <a href="send_money.php"><i class="fas fa-paper-plane"></i> Send Money</a>
                    <a href="scan_pay.php"><i class="fas fa-qrcode"></i> Scan & Pay</a>
                    <a href="add_money.php"><i class="fas fa-plus-circle"></i> Add Money</a>
                    <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                </nav>
            </div>
            
            <div class="main-dashboard">
                <div class="quick-actions">
                    <div class="action-card" onclick="location.href='send_money.php'">
                        <i class="fas fa-paper-plane"></i>
                        <h3>Send Money</h3>
                    </div>
                    
                    <div class="action-card" onclick="location.href='scan_pay.php'">
                        <i class="fas fa-qrcode"></i>
                        <h3>Scan & Pay</h3>
                    </div>
                    
                    <div class="action-card" onclick="location.href='add_money.php'">
                        <i class="fas fa-plus-circle"></i>
                        <h3>Add Money</h3>
                    </div>
                    
                    <div class="action-card" onclick="showQR()">
                        <i class="fas fa-qrcode"></i>
                        <h3>My QR Code</h3>
                    </div>
                </div>
                
                <div class="transaction-history">
                    <h3>Recent Transactions</h3>
                    
                    <?php if($transactions->num_rows > 0): ?>
                        <div class="transactions-list">
                            <?php while($transaction = $transactions->fetch_assoc()): ?>
                                <?php 
                                $isReceived = $transaction['receiver_id'] == $user_id;
                                $bgClass = $isReceived ? 'bg-success-light' : 'bg-danger-light';
                                $iconClass = $isReceived ? 'text-success' : 'text-danger';
                                $icon = $isReceived ? 'fa-arrow-down' : 'fa-arrow-up';
                                ?>
                                <div class="transaction-item <?php echo $bgClass; ?>">
                                    <div class="transaction-icon">
                                        <i class="fas <?php echo $icon; ?> <?php echo $iconClass; ?>"></i>
                                    </div>
                                    
                                    <div class="transaction-details">
                                        <h4>
                                            <?php
                                            if($transaction['sender_id'] == $user_id) {
                                                echo "Sent to " . $transaction['receiver_name'];
                                            } else {
                                                echo "Received from " . $transaction['sender_name'];
                                            }
                                            ?>
                                        </h4>
                                        <span class="date"><?php echo date('d M Y, h:i A', strtotime($transaction['created_at'])); ?></span>
                                    </div>
                                    
                                    <div class="transaction-amount <?php echo $isReceived ? 'received' : 'sent'; ?>">
                                        <span>
                                            <?php echo $isReceived ? '+' : '-'; ?>₹<?php echo number_format($transaction['amount'], 2); ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p>No transactions found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- QR Code Modal -->
    <div id="qrModal" class="modal">
        <div class="modal-content">
            <h3>Your Payment QR Code</h3>
            <div id="qrcode"></div>
            <p>Scan this code to receive payment</p>
            <button class="btn btn-secondary" onclick="closeQR()">Close</button>
        </div>
    </div>
    
    <script>
        function showQR() {
            document.getElementById('qrModal').style.display = 'block';
            document.getElementById('qrcode').innerHTML = ''; // Clear previous QR code
            new QRCode(document.getElementById("qrcode"), {
                text: "<?php echo $user['phone']; ?>", // Using phone number as QR code
                width: 256,
                height: 256,
                colorDark : "#000000",
                colorLight : "#ffffff",
                correctLevel : QRCode.CorrectLevel.H
            });
        }
        
        function closeQR() {
            document.getElementById('qrModal').style.display = 'none';
            document.getElementById('qrcode').innerHTML = '';
        }
    </script>
</body>
</html>
