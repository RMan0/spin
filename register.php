<?php
session_start();
include 'conn/config.php';

if (isset($_POST['register'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "Email already exists!";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone, password_hash) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $full_name, $email, $phone, $password_hash);

            if ($stmt->execute()) {
                $success = "Registration successful! Redirecting to login...";
                header("refresh:2;url=login.php");
            } else {
                $error = "Insert failed: " . $stmt->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laundry Register</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Fredoka', sans-serif;
            background: #f0f7ff;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .login-card {
            position: relative;
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            padding: 40px 25px;
            border-radius: 40px;
            box-shadow: 0 20px 50px rgba(180, 200, 220, 0.4);
            text-align: center;
        }
        .login-card::after {
            content: '';
            position: absolute;
            top: -20px;
            right: -20px;
            width: 80px;
            height: 80px;
            background: rgba(240, 247, 255, 0.38);
            border-radius: 50%;
        }
        .icon-box {
            width: 60px; height: 60px; background: #eef6ff;
            border-radius: 15px; margin: 0 auto 15px;
            display: flex; align-items: center; justify-content: center;
            border: 1px solid #d9e9ff;
        }
        .icon-box img { width: 70%; height: 70%; object-fit: contain; }
        h2 { margin: 0; font-size: 24px; color: #1a2b41; font-weight: 700; }
        .subtitle { font-size: 10px; font-weight: 700; color: #8fa1b4; letter-spacing: 1.5px; margin: 5px 0 25px; }
        
        .input-group { text-align: left; margin-bottom: 12px; }
        .input-group label { font-size: 10px; font-weight: 700; color: #8fa1b4; display: block; margin-bottom: 5px; margin-left: 5px; }
        .input-group input { width: 100%; padding: 12px 18px; border-radius: 18px; border: none; background: #f8fbff; outline: none; font-size: 13px; }
        
        .login-btn {
            width: 100%; margin-top: 15px; padding: 16px;
            border: none; border-radius: 18px;
            background: linear-gradient(to right, #56ccf2, #4facfe);
            color: white; font-size: 14px; font-weight: 700; cursor: pointer;
            box-shadow: 0 10px 20px rgba(86, 204, 242, 0.3);
        }
        .error { color: #ff5e5e; font-size: 12px; margin-bottom: 10px; }
        .footer-text { margin-top: 20px; font-size: 11px; font-weight: 700; color: #8fa1b4; }
        .footer-text a { color: #4facfe; text-decoration: none; }

        @media (max-width: 480px) {
            .login-card { border-radius: 30px; padding: 30px 20px; }
            .input-group input { padding: 11px 15px; }
        }
        .success {
            background: #e6f9f0;
            color: #1bbf73;
            font-size: 12px;
            padding: 10px;
            border-radius: 12px;
            margin-bottom: 10px;
            text-align: center;
            font-weight: 700;
        }
        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 22px;
            text-decoration: none;
            color: #4facfe;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.3s;
        }

        .back-btn:hover {
            transform: translateX(-3px);
            background: #d9e9ff;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <a href="index.php" class="back-btn">←</a>
        <div class="icon-box">
            <img src="/spin/assets/img/logo.png" alt="Logo">
        </div>
        <h2>Create Account</h2>
        <div class="subtitle">JOIN OUR COMMUNITY</div>

        <?php 
        if(isset($error)) echo "<div class='error'>$error</div>"; 
        if(isset($success)) echo "<div class='success'>$success</div>"; 
        ?>

        <form method="POST">
            <div class="input-group">
                <label>FULL NAME</label>
                <input type="text" name="full_name" placeholder="Enter your full name" required>
            </div>
            <div class="input-group">
                <label>GMAIL</label>
                <input type="email" name="email" placeholder="example@gmail.com" required>
            </div>
            <div class="input-group">
                <label>PHONE NUMBER</label>
                <input type="text" name="phone" placeholder="+63..." required>
            </div>
            <div class="input-group">
                <label>PASSWORD</label>
                <input type="password" name="password" placeholder="Create password" required>
            </div>
            <div class="input-group">
                <label>CONFIRM PASSWORD</label>
                <input type="password" name="confirm_password" placeholder="Repeat password" required>
            </div>
            <button type="submit" name="register" class="login-btn">REGISTER NOW</button>
        </form>

        <div class="footer-text">
            ALREADY REGISTERED? <a href="login.php">LOGIN</a>
        </div>
    </div>
</body>
</html>