<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html>
<head>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/spin/assets/css/style.css">
    
    <style>
        .nav-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .home-link {
            color: var(--text-dark, #1a2b41);
            font-size: 20px;
            text-decoration: none;
            transition: color 0.3s;
        }

        .home-link:hover {
            color: var(--primary-blue, #4facfe);
        }
        .nav-right {
    display: flex;
    align-items: center;
    gap: 20px;
}

.nav-icon-btn {
    text-decoration: none;
    color: var(--text-dark); /* Matches your dashboard text color */
    font-size: 20px;
    width: 45px;
    height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    background: #f8fbff; /* Soft blue background to match cards */
    transition: all 0.3s ease;
}

.nav-icon-btn:hover {
    background: #eef6ff;
    color: var(--primary-blue);
    transform: translateY(-2px);
}

.logout-btn:hover {
    color: #ff4757; /* Subtle red hint for logout */
    background: #fff0f0;
}
    </style>
</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
    
    <!-- LEFT -->
    <div class="nav-left">
        <img src="/spin/assets/img/logo.png">
        <span>S.P.I.N.</span>
    </div>

    <!-- RIGHT -->
    <div class="nav-right">
        
        <!-- HOME ICON -->
        <a href="dashboard.php" class="nav-icon-btn" title="Dashboard">
            <i class="fa-solid fa-house"></i>
        </a>

        <!-- LOGOUT ICON BUTTON -->
        <a href="#" class="nav-icon-btn logout-btn" onclick="openModal()" title="Logout">
            <i class="fa-solid fa-right-from-bracket"></i>
        </a>

    </div>
</div>

<!-- LOGOUT MODAL -->
<div id="logoutModal" class="modal">
    <div class="modal-content">
        <h3>Are you sure you want to logout?</h3>
        <button class="btn btn-yes" onclick="window.location.href='/spin/logout.php'">Yes</button>
        <button class="btn btn-no" onclick="closeModal()">No</button>
    </div>
</div>

<script>
function toggleDropdown() {
    let menu = document.getElementById("dropdownMenu");
    menu.style.display = (menu.style.display === "block") ? "none" : "block";
}

function openModal() {
    document.getElementById('logoutModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('logoutModal').style.display = 'none';
}

/* CLICK OUTSIDE CLOSE */
window.onclick = function(event) {
    if (!event.target.closest('.profile')) {
        let dropdown = document.getElementById("dropdownMenu");
        if (dropdown) dropdown.style.display = "none";
    }
}
</script>

</body>
</html>