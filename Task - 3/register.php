<?php
session_start();
include 'config.php';

if(isset($_POST['register'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Generate unique QR code for user
    $qr_code = uniqid('PAY_');
    
    $check_email = "SELECT * FROM users WHERE email = '$email'";
    $check_phone = "SELECT * FROM users WHERE phone = '$phone'";
    
    $email_result = $conn->query($check_email);
    $phone_result = $conn->query($check_phone);
    
    if($email_result->num_rows > 0) {
        $error = "Email already exists!";
    } elseif($phone_result->num_rows > 0) {
        $error = "Phone number already exists!";
    } else {
        $sql = "INSERT INTO users (name, email, phone, password, qr_code) VALUES ('$name', '$email', '$phone', '$password', '$qr_code')";
        
        if($conn->query($sql)) {
            $_SESSION['success'] = "Registration successful! Please login.";
            header("Location: login.php");
            exit();
        } else {
            $error = "Registration failed! Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Paytm Clone</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="images/logo.png" alt="Paytm Clone Logo" class="logo">
        </div>
        
        <div class="main-content">
            <div class="auth-container">
                <div class="auth-box">
                    <h2>Create Your Account</h2>
                    
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="form-group">
                            <input type="text" name="name" class="form-control" placeholder="Full Name" required>
                        </div>
                        
                        <div class="form-group">
                            <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                        </div>
                        
                        <div class="form-group">
                            <input type="tel" name="phone" class="form-control" placeholder="Phone Number" required>
                        </div>
                        
                        <div class="form-group">
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>
                        
                        <button type="submit" name="register" class="btn btn-primary">Register</button>
                    </form>
                    
                    <p class="mt-3">Already have an account? <a href="login.php">Login here</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
