<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Digital Wallet System</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body>
    <?php
    session_start();
    include 'config.php';
    
    if(isset($_SESSION['user_id'])) {
        header("Location: dashboard.php");
        exit();
    }
    ?>
    
    <div class="container">
        <div class="header">
            <img src="images/logo.png" alt="Paytm Clone Logo" class="logo">
        </div>
        
        <div class="main-content">
            <div class="auth-container">
                <div class="auth-box">
                    <h2>Welcome to Paytm Clone</h2>
                    <p>India's Most-loved Payments App</p>
                    
                    <div class="auth-options">
                        <a href="login.php" class="btn btn-primary">Login</a>
                        <a href="register.php" class="btn btn-secondary">Register</a>
                    </div>
                </div>
                
                <div class="features">
                    <div class="feature-item">
                        <i class="fas fa-wallet"></i>
                        <h3>Pay Anyone Directly</h3>
                        <p>Pay anyone, everywhere. Make contactless & secure payments in-stores or online.</p>
                    </div>
                    
                    <div class="feature-item">
                        <i class="fas fa-qrcode"></i>
                        <h3>QR Code Payment</h3>
                        <p>Pay using QR code at shops, restaurants, or anywhere.</p>
                    </div>
                    
                    <div class="feature-item">
                        <i class="fas fa-shield-alt"></i>
                        <h3>Secure Payments</h3>
                        <p>100% secure and encrypted payment system.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="js/main.js"></script>
</body>
</html>
