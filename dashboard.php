<?php
session_start();
require_once 'conn/config.php'; 

$user_id = $_SESSION['user_id'] ?? 1;

// 1. Stats Query
$stats_query = $conn->query("SELECT 
    COUNT(CASE WHEN status NOT IN ('Completed', 'Cancelled') THEN 1 END) as active_count,
    COUNT(CASE WHEN status = 'Completed' THEN 1 END) as completed_count,
    COUNT(CASE WHEN status = 'Picked Up' THEN 1 END) as ready_count
    FROM bookings WHERE user_id = $user_id");
$stats = $stats_query->fetch_assoc();

// 2. Fetch ALL Active Orders (Instead of LIMIT 1)
$active_orders_query = $conn->query("SELECT * FROM bookings 
    WHERE user_id = $user_id AND status != 'Completed' AND status != 'Cancelled'
    ORDER BY created_at DESC");

// 3. Recent Activity Query
$recent_activity_query = $conn->query("SELECT * FROM bookings 
    WHERE user_id = $user_id AND status = 'Completed' 
    ORDER BY created_at DESC");

// Progress Helper
function getProgress($status) {
    switch ($status) {
        case 'Pending': return 25;
        case 'Processing': return 50;
        case 'Picked Up': return 75;
        case 'Completed': return 100;
        default: return 10;
    }
}

// Color Helper
function getStatusColor($status) {
    switch ($status) {
        case 'Pending': return '#ff9f43';
        case 'Processing': return '#4facfe';
        case 'Picked Up': return '#a55eea';
        case 'Completed': return '#2ecc71';
        default: return '#8fa1b4';
    }
}

include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --bg-color: #f0f7ff;
            --card-white: #ffffff;
            --primary-blue: #4facfe;
            --text-dark: #1a2b41;
            --text-muted: #8fa1b4;
            --accent-orange: #ff9f43;
            --accent-green: #2ecc71;
            --dark-card: #0d1b2a; 
        }

        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Fredoka', sans-serif; background-color: var(--bg-color); color: var(--text-dark); }

        .main-wrapper { width: 100%; display: flex; justify-content: center; padding: 60px 40px; }
        .dashboard-container { max-width: 1100px; width: 100%; }

        header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
        .header-title h1 { margin: 0; font-size: 32px; font-weight: 700; }
        .header-title p { margin: 5px 0 0; font-size: 10px; font-weight: 700; color: var(--text-muted); letter-spacing: 1px; text-transform: uppercase; }

        .new-booking-btn {
            text-decoration: none;
            background: linear-gradient(to right, #56ccf2, #4facfe);
            color: white;
            padding: 12px 25px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 14px;
            box-shadow: 0 10px 20px rgba(79, 172, 254, 0.3);
            transition: 0.3s;
        }
        .new-booking-btn:hover { transform: translateY(-2px); box-shadow: 0 15px 25px rgba(79, 172, 254, 0.4); }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px; }
        .stat-card { background: var(--card-white); padding: 25px; border-radius: 30px; box-shadow: 0 10px 30px rgba(180, 200, 220, 0.2); }
        .stat-card span { font-size: 10px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; display: block; margin-bottom: 10px; }
        .stat-card h2 { margin: 0; font-size: 32px; font-weight: 700; }

        .main-layout { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; }
        h3 { font-size: 18px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        h3 i { color: var(--primary-blue); }

        .content-card { background: var(--card-white); border-radius: 30px; padding: 25px; margin-bottom: 20px; box-shadow: 0 10px 30px rgba(180, 200, 220, 0.2); }
        .status-card-dynamic { transition: all 0.4s ease; border-left: 8px solid var(--primary-blue); }

        .order-row { display: flex; align-items: center; gap: 15px; }
        .order-icon { width: 50px; height: 50px; background: #f8fbff; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        .order-info { flex-grow: 1; }
        .order-info h4 { margin: 0; font-size: 16px; }
        .order-info p { margin: 5px 0 0; font-size: 12px; color: var(--text-muted); font-weight: 600; }
        
        .badge { font-size: 9px; font-weight: 800; padding: 4px 8px; border-radius: 8px; text-transform: uppercase; }
        .badge.pending { background: #fff4e5; color: var(--accent-orange); }
        .badge.ready { background: #f3ebff; color: #a55eea; }
        .badge.completed { background: #e8f8f0; color: var(--accent-green); }
        .date-text { font-size: 10px; color: var(--text-muted); font-weight: 700; display: block; margin-top: 5px; }

        .progress-container { margin-top: 20px; }
        .progress-header { display: flex; justify-content: space-between; font-size: 9px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px; }
        .progress-bar { width: 100%; height: 6px; background: #f0f7ff; border-radius: 10px; overflow: hidden; }
        .progress-fill { height: 100%; border-radius: 10px; transition: width 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275); }

        .sidebar-card { background: var(--card-white); border-radius: 30px; padding: 30px; box-shadow: 0 10px 30px rgba(180, 200, 220, 0.2); position: sticky; top: 20px; height: fit-content; }
        .guide-item { display: flex; gap: 15px; margin-bottom: 25px; position: relative; opacity: 0.4; }
        .guide-item.active { opacity: 1; }
        .guide-item:not(:last-child)::after { content: ''; position: absolute; left: 4px; top: 20px; width: 2px; height: 25px; background: #eee; }
        .guide-dot { width: 10px; height: 10px; border-radius: 50%; border: 2px solid #ddd; background: white; margin-top: 5px; z-index: 2; }
        
        .guide-item.active.pending .guide-dot { background: #ff9f43; border-color: #ff9f43; }
        .guide-item.active.processing .guide-dot { background: #4facfe; border-color: #4facfe; }
        .guide-item.active.picked-up .guide-dot { background: #a55eea; border-color: #a55eea; }
        .guide-item.active.completed .guide-dot { background: #2ecc71; border-color: #2ecc71; }

        .guide-info h5 { margin: 0; font-size: 14px; }
        .guide-info p { margin: 5px 0 0; font-size: 11px; color: var(--text-muted); }

        @media (max-width: 850px) {
            .main-wrapper { padding: 30px 20px; }
            .main-layout { grid-template-columns: 1fr; }
            header { flex-direction: column; align-items: flex-start; gap: 20px; }
            .new-booking-btn { width: 100%; text-align: center; }
        }
        .activity-scroll-area {
            max-height: 450px; /* Fixed height for the activity section */
            overflow-y: auto;  /* Enables vertical scrolling */
            padding-right: 10px;
        }

        /* Optional: Make the scrollbar look nicer */
        .activity-scroll-area::-webkit-scrollbar {
            width: 6px;
        }
        .activity-scroll-area::-webkit-scrollbar-thumb {
            background: #d0e4ff;
            border-radius: 10px;
        }
    </style>
</head>
<body>

<div class="main-wrapper">
    <div class="dashboard-container">
        <header>
            <div class="header-title">
                <h1>Your Dashboard</h1>
                <p>Welcome back, User #<?php echo $user_id; ?></p>
            </div>
            <a href="booking.php" class="new-booking-btn">+ NEW BOOKING</a>
        </header>

        <div class="stats-grid">
            <div class="stat-card">
                <span>Active Orders</span>
                <h2><?php echo str_pad($stats['active_count'], 2, "0", STR_PAD_LEFT); ?></h2>
            </div>
            <div class="stat-card">
                <span>Completed</span>
                <h2><?php echo str_pad($stats['completed_count'], 2, "0", STR_PAD_LEFT); ?></h2>
            </div>
            <div class="stat-card">
                <span>Ready for Pickup</span>
                <h2><?php echo str_pad($stats['ready_count'], 2, "0", STR_PAD_LEFT); ?></h2>
            </div>
        </div>

        <div class="main-layout">
            <div class="main-content">
                <h3><i class="fa-regular fa-clock"></i> Current Order Status (<?php echo $stats['active_count']; ?>)</h3>
                
                <?php if ($active_orders_query->num_rows > 0): ?>
                    <?php while($order = $active_orders_query->fetch_assoc()): 
                        $status = $order['status'];
                        $color = getStatusColor($status);
                        $perc = getProgress($status);
                    ?>
                    <div class="content-card status-card-dynamic" style="border-left-color: <?php echo $color; ?>">
                        <div class="order-row">
                            <div class="order-icon" style="color: <?php echo $color; ?>;"><i class="fa-solid fa-soap"></i></div>
                            <div class="order-info">
                                <h4 style="color: <?php echo $color; ?>;"><?php echo $order['service_name']; ?></h4>
                                <p>ID: #<?php echo $order['id']; ?> • <?php echo $order['weight']; ?> KG • ₱<?php echo number_format($order['total_price'], 2); ?></p>
                            </div>
                            <div style="text-align: right;">
                                <span class="badge <?php echo ($status == 'Picked Up') ? 'ready' : 'pending'; ?>">
                                    <?php echo ($status == 'Picked Up') ? 'Ready for Pickup' : $status; ?>
                                </span>
                                <span class="date-text"><?php echo date('M d, Y', strtotime($order['pickup_date'])); ?></span>
                            </div>
                        </div>
                        
                        <div class="progress-container">
                            <div class="progress-header">
                                <span>Status Progress</span>
                                <span style="color: <?php echo $color; ?>"><?php echo $perc; ?>%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $perc; ?>%; background: <?php echo $color; ?>;"></div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="content-card" style="text-align: center; color: var(--text-muted); padding: 40px;">
                        <i class="fa-solid fa-layer-group" style="font-size: 30px; margin-bottom: 10px;"></i>
                        <p>No active orders at the moment.</p>
                    </div>
                <?php endif; ?>

                <h3><i class="fa-regular fa-circle-check"></i> Recent Activity</h3>
                
                <?php while($row = $recent_activity_query->fetch_assoc()): ?>
                <div class="content-card">
                    <div class="order-row">
                        <div class="order-icon" style="color: var(--accent-green);"><i class="fa-solid fa-circle-check"></i></div>
                        <div class="order-info">
                            <h4><?php echo $row['service_name']; ?></h4>
                            <p>ID: #<?php echo $row['id']; ?> • ₱<?php echo number_format($row['total_price'], 2); ?></p>
                        </div>
                        <div style="text-align: right;">
                            <span class="badge completed">Completed</span>
                            <span class="date-text"><?php echo date('M d', strtotime($row['created_at'])); ?></span>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <div class="sidebar-card">
                <h3><i class="fa-solid fa-wave-square"></i> Process Guide</h3>
                <!-- The guide will highlight based on the LATEST active order if available -->
                <?php 
                $latest_status = '';
                // Rewind and check first item for the guide
                if ($active_orders_query->num_rows > 0) {
                    $active_orders_query->data_seek(0);
                    $first = $active_orders_query->fetch_assoc();
                    $latest_status = $first['status'];
                    $active_orders_query->data_seek(0); // reset for the loop above
                }
                ?>

                <div class="guide-item <?php echo ($latest_status == 'Pending') ? 'active pending' : ''; ?>">
                    <div class="guide-dot"></div>
                    <div class="guide-info">
                        <h5>Pending Approval</h5>
                        <p>We're checking your order</p>
                    </div>
                </div>

                <div class="guide-item <?php echo ($latest_status == 'Processing') ? 'active processing' : ''; ?>">
                    <div class="guide-dot"></div>
                    <div class="guide-info">
                        <h5>In Process</h5>
                        <p>Washing with premium care</p>
                    </div>
                </div>

                <div class="guide-item <?php echo ($latest_status == 'Picked Up') ? 'active picked-up' : ''; ?>">
                    <div class="guide-dot"></div>
                    <div class="guide-info">
                        <h5>Ready for Pick-up</h5>
                        <p>Fresh, folded, and waiting</p>
                    </div>
                </div>

                <div class="guide-item <?php echo ($latest_status == 'Completed') ? 'active completed' : ''; ?>">
                    <div class="guide-dot"></div>
                    <div class="guide-info">
                        <h5>Handed Over</h5>
                        <p>Service completed</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>