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

if(isset($_POST['pay'])) {
    $qr_code = mysqli_real_escape_string($conn, $_POST['qr_code']);
    $amount = floatval($_POST['amount']);
    
    if($amount <= 0) {
        $error = "Invalid amount!";
    } elseif($amount > $user['balance']) {
        $error = "Insufficient balance!";
    } else {
        // Find receiver by QR code
        $receiver_sql = "SELECT * FROM users WHERE qr_code = '$qr_code' OR phone = '$qr_code'";
        $receiver_result = $conn->query($receiver_sql);
        
        if($receiver_result->num_rows > 0) {
            $receiver = $receiver_result->fetch_assoc();
            
            if($receiver['id'] == $user_id) {
                $error = "You cannot pay to yourself!";
            } else {
                // Start transaction
                $conn->begin_transaction();
                
                try {
                    // Deduct from sender
                    $update_sender = "UPDATE users SET balance = balance - $amount WHERE id = $user_id";
                    $conn->query($update_sender);
                    
                    // Add to receiver
                    $update_receiver = "UPDATE users SET balance = balance + $amount WHERE id = " . $receiver['id'];
                    $conn->query($update_receiver);
                    
                    // Create transaction record
                    $transaction_sql = "INSERT INTO transactions (sender_id, receiver_id, amount, type, status) 
                                      VALUES ($user_id, " . $receiver['id'] . ", $amount, 'send', 'completed')";
                    $conn->query($transaction_sql);
                    
                    $conn->commit();
                    $_SESSION['success'] = "Payment successful!";
                    header("Location: dashboard.php");
                    exit();
                } catch(Exception $e) {
                    $conn->rollback();
                    $error = "Transaction failed! Please try again.";
                }
            }
        } else {
            $error = "Invalid QR code or phone number!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan & Pay - Paytm Clone</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="images/logo.png" alt="Paytm Clone Logo" class="logo">
            <div class="user-info">
                <span>Balance: ₹<?php echo number_format($user['balance'], 2); ?></span>
            </div>
        </div>
        
        <div class="main-content">
            <div class="scanner-container">
                <h2>Scan QR Code</h2>
                
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <div id="qr-reader"></div>
                
                <form method="POST" action="" id="payment-form" style="display: none;">
                    <input type="hidden" name="qr_code" id="qr-code">
                    
                    <div class="form-group">
                        <input type="number" name="amount" class="form-control" placeholder="Enter Amount" step="0.01" required>
                    </div>
                    
                    <button type="submit" name="pay" class="btn btn-primary">Pay Now</button>
                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        function onScanSuccess(decodedText, decodedResult) {
            // Validate QR code format
            try {
                let qrData = decodedText;
                // Check if it's a phone number or QR code
                if(qrData.length > 0) {
                    html5QrcodeScanner.clear();
                    document.getElementById('qr-code').value = qrData;
                    document.getElementById('payment-form').style.display = 'block';
                    document.getElementById('qr-reader').style.display = 'none';
                } else {
                    alert('Invalid QR Code format');
                }
            } catch(e) {
                alert('Invalid QR Code format');
            }
        }

        function onScanFailure(error) {
            // handle scan failure, usually better to ignore and keep scanning
            console.warn(`QR error = ${error}`);
        }

        let html5QrcodeScanner = new Html5QrcodeScanner(
            "qr-reader", { fps: 10, qrbox: {width: 250, height: 250} }
        );
        html5QrcodeScanner.render(onScanSuccess, onScanFailure);
    </script>
</body>
</html>
