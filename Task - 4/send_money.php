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

if(isset($_POST['send'])) {
    $receiver_phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $amount = floatval($_POST['amount']);
    
    if($amount <= 0) {
        $error = "Invalid amount!";
    } elseif($amount > $user['balance']) {
        $error = "Insufficient balance!";
    } else {
        // Find receiver
        $receiver_sql = "SELECT * FROM users WHERE phone = '$receiver_phone'";
        $receiver_result = $conn->query($receiver_sql);
        
        if($receiver_result->num_rows > 0) {
            $receiver = $receiver_result->fetch_assoc();
            
            if($receiver['id'] == $user_id) {
                $error = "You cannot send money to yourself!";
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
                    $_SESSION['success'] = "Money sent successfully!";
                    header("Location: dashboard.php");
                    exit();
                } catch(Exception $e) {
                    $conn->rollback();
                    $error = "Transaction failed! Please try again.";
                }
            }
        } else {
            $error = "Receiver not found!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Money - Paytm Clone</title>
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
            <div class="auth-container">
                <div class="auth-box">
                    <h2>Send Money</h2>
                    
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="form-group">
                            <input type="tel" name="phone" class="form-control" placeholder="Receiver's Phone Number" required>
                        </div>
                        
                        <div class="form-group">
                            <input type="number" name="amount" class="form-control" placeholder="Amount" step="0.01" required>
                        </div>
                        
                        <button type="submit" name="send" class="btn btn-primary">Send Money</button>
                        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
