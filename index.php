<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Laundry Dashboard</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/spin/assets/css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background: #eef3f7;
            color: #1c1c1c;
            padding: 50px;
        }

        /* ================= HERO ================= */

        .hero {
            text-align: center;
            padding: 80px 20px 40px;
        }

        .hero-icon {
            width: 80px;
            height: 80px;
            margin: auto;
            margin-bottom: 20px;
            background: white;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            font-size: 40px;
        }

        .hero h1 {
            font-size: 80px;
            font-weight: bold;
            color: rgb(0, 29, 71);
            font-family: 'Fredoka', sans-serif;
        }

        .hero span {
            font-size: 80px;
            color: #4facfe;
            font-family: 'Fredoka', sans-serif;
        }

        .hero p {
            margin-top: 10px;
            color: #666;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .hero img {
            height: 80px;
            width: 120px;
        }
        .hero-btn {
            margin-top: 25px;
            display: inline-block;
            padding: 14px 28px;
            background: linear-gradient(to right, #4facfe, #00c6ff);
            color: white;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            position: relative;
            overflow: hidden;
            transition: 0.3s;
        }

/* hover lift */
.hero-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

/* bubbles container */
.hero-btn::before {
    content: "";
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle, rgba(255,255,255,0.6) 10%, transparent 11%) 0 0,
        radial-gradient(circle, rgba(255,255,255,0.4) 10%, transparent 11%) 10px 10px,
        radial-gradient(circle, rgba(255,255,255,0.3) 10%, transparent 11%) 20px 5px;
    background-size: 30px 30px;
    opacity: 0;
    transform: translateY(20px);
    transition: 0.3s ease;
}

/* activate bubbles on hover */
.hero-btn:hover::before {
    opacity: 1;
    animation: floatBubbles 1s linear infinite;
}

@keyframes floatBubbles {
    0% {
        transform: translateY(20px);
    }
    100% {
        transform: translateY(-40px);
    }
}
        /* ================= FEATURES ================= */

        .features {
            max-width: 1000px;
            margin: 50px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .feature-card {
            background: #f8fbff;
            border-radius: 15px;
            padding: 20px;
            text-align: left;
            box-shadow: 0 6px 15px rgba(0,0,0,0.05);
            transition: 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .feature-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .feature-desc {
            font-size: 13px;
            color: #666;
        }

        /* ================= RESPONSIVE ================= */

        @media (max-width: 768px) {

            .hero span{
                font-size: 48px;
            }
            .hero h1 {
                font-size: 48px;
            }
            .hero p {
            margin-top: 10px;
            color: #666;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 1px;
            }
            .features {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<!-- HERO -->
<div class="hero">

    <div class="hero-icon"><img src="/spin/assets/img/logo.png"></div>

    <h1>Freshness, <span>Simplified.</span></h1>

    <p>SERVICE PROCESSING & INSTANT NOTIFICATION</p>

    <a href="login.php" class="hero-btn">Member Login</a>

</div>

<!-- FEATURES -->
<div class="features">

    <div class="feature-card">
        <div class="feature-icon">⚡</div>
        <div class="feature-title">Instant</div>
        <div class="feature-desc">
            Real-time updates on your laundry status.
        </div>
    </div>

    <div class="feature-card">
        <div class="feature-icon">🧼</div>
        <div class="feature-title">Premium</div>
        <div class="feature-desc">
            Eco-friendly detergents and expert care.
        </div>
    </div>

    <div class="feature-card">
        <div class="feature-icon">🚚</div>
        <div class="feature-title">Doorstep</div>
        <div class="feature-desc">
            Hassle-free pickup and delivery service.
        </div>
    </div>

</div>

</body>
</html>