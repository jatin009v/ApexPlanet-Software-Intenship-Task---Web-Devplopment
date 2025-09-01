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

if(isset($_POST['add_money'])) {
    $amount = floatval($_POST['amount']);
    
    if($amount <= 0) {
        $error = "Invalid amount!";
    } else {
        // In a real application, you would integrate with a payment gateway here
        // For demo purposes, we'll directly add the money
        $conn->begin_transaction();
        
        try {
            // Add money to user's wallet
            $update_balance = "UPDATE users SET balance = balance + $amount WHERE id = $user_id";
            $conn->query($update_balance);
            
            // Create transaction record
            $transaction_sql = "INSERT INTO transactions (sender_id, receiver_id, amount, type, status) 
                              VALUES ($user_id, $user_id, $amount, 'add_money', 'completed')";
            $conn->query($transaction_sql);
            
            $conn->commit();
            $_SESSION['success'] = "Money added successfully!";
            header("Location: dashboard.php");
            exit();
        } catch(Exception $e) {
            $conn->rollback();
            $error = "Transaction failed! Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Money - Paytm Clone</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="images/logo.png" alt="Paytm Clone Logo" class="logo">
            <div class="user-info">
                <span>Current Balance: ₹<?php echo number_format($user['balance'], 2); ?></span>
            </div>
        </div>
        
        <div class="main-content">
            <div class="auth-container">
                <div class="auth-box">
                    <h2>Add Money to Wallet</h2>
                    
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="form-group">
                            <input type="number" name="amount" class="form-control" placeholder="Enter Amount" step="0.01" required>
                        </div>
                        
                        <div class="payment-options">
                            <h3>Select Payment Method</h3>
                            <div class="payment-method">
                                <input type="radio" name="payment_method" value="upi" checked> UPI
                            </div>
                            <div class="payment-method">
                                <input type="radio" name="payment_method" value="card"> Debit/Credit Card
                            </div>
                            <div class="payment-method">
                                <input type="radio" name="payment_method" value="netbanking"> Net Banking
                            </div>
                        </div>
                        
                        <button type="submit" name="add_money" class="btn btn-primary">Proceed to Add Money</button>
                        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
