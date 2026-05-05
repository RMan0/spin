<?php
session_start();
include 'conn/config.php';

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields!";
    } else {
        $stmt = $conn->prepare("SELECT id, full_name, password_hash FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Wrong password!";
            }
        } else {
            $error = "User not found!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laundry System Login</title>
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
            max-width: 380px;
            background: #ffffff;
            padding: 40px 30px;
            border-radius: 40px;
            box-shadow: 0 20px 50px rgba(180, 200, 220, 0.4);
            text-align: center;
        }

        .icon-box {
            width: 65px;
            height: 65px;
            background: #eef6ff;
            border-radius: 18px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #d9e9ff;
        }

        .icon-box img {
            width: 70%;
            height: 70%;
            object-fit: contain;
        }

        h2 { margin: 0; font-size: 26px; font-weight: 700; color: #1a2b41; }
        .subtitle {
            font-size: 10px;
            font-weight: 700;
            color: #8fa1b4;
            letter-spacing: 1.5px;
            margin: 8px 0 30px;
            text-transform: uppercase;
        }

        .input-group { text-align: left; margin-bottom: 20px; }
        .input-group label {
            font-size: 10px;
            font-weight: 700;
            color: #8fa1b4;
            display: block;
            margin-bottom: 8px;
            margin-left: 5px;
        }

        .input-group input {
            width: 100%;
            padding: 14px 20px;
            border-radius: 20px;
            border: none;
            background: #f8fbff;
            outline: none;
            font-size: 14px;
            transition: 0.3s;
        }

        .input-group input:focus { box-shadow: inset 0 0 0 1px #4facfe; }

        .login-btn {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 20px;
            background: linear-gradient(to right, #56ccf2, #4facfe);
            color: white;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(86, 204, 242, 0.3);
            transition: 0.3s;
        }

        .login-btn:active { transform: scale(0.98); }
        .error { color: #ff5e5e; font-size: 12px; margin-bottom: 15px; }
        .footer-text { margin-top: 25px; font-size: 11px; font-weight: 700; color: #8fa1b4; }
        .footer-text a { color: #4facfe; text-decoration: none; }

        @media (max-width: 400px) {
            .login-card { padding: 35px 20px; border-radius: 30px; }
            h2 { font-size: 22px; }
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
        <h2>Welcome Back</h2>
        <div class="subtitle">USER ACCESS PANEL</div>

        <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>

        <form method="POST">
            <div class="input-group">
                <label>GMAIL</label>
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="input-group">
                <label>PASSWORD</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" name="login" class="login-btn">SIGN IN →</button>
        </form>

        <div class="footer-text">
            NO ACCOUNT YET? <a href="register.php">REGISTER</a>
        </div>
    </div>
</body>
</html>