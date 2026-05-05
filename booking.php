<?php
session_start();
require_once 'conn/config.php'; 

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_booking'])) {
    $user_id = $_SESSION['user_id'] ?? 1; 
    
    $service_id    = $_POST['service_id'];
    $service_name  = $_POST['service_name'];
    $price_per_kg  = $_POST['price_per_kg'];
    $weight        = $_POST['weight'];
    $logistics     = $_POST['logistics'];
    $pickup_date   = $_POST['pickup_date'];
    $pickup_time   = $_POST['pickup_time'];
    $delivery_date = $_POST['delivery_date'];
    $customer_message = $_POST['customer_message']; 

    $delivery_fee  = ($logistics == 'delivery') ? 50.00 : 0.00;
    $total_price   = ($price_per_kg * $weight) + $delivery_fee;
    $status        = "Pending";

    // Updated Query: Added 'customer_message' and an extra 's' in bind_param
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, service_id, service_name, price_per_kg, weight, pickup_date, pickup_time, delivery_date, logistics, status, total_price, customer_message) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("iisddsssssds", 
        $user_id, $service_id, $service_name, $price_per_kg, $weight, 
        $pickup_date, $pickup_time, $delivery_date, $logistics, $status, $total_price, $customer_message
    );

    if ($stmt->execute()) {
        $message = "<div style='background: #e8f5e9; color: #2e7d32; padding: 15px; border-radius: 10px; margin: 20px auto; max-width: 1100px; text-align: center; font-weight: bold;'><i class='fa-solid fa-circle-check'></i> Booking confirmed! Total: ₱" . number_format($total_price, 2) . "</div>";
    } else {
        $message = "<div style='background: #ffebee; color: #c62828; padding: 15px; border-radius: 10px; margin: 20px auto; max-width: 1100px; text-align: center;'><i class='fa-solid fa-circle-xmark'></i> Error: " . $conn->error . "</div>";
    }
    $stmt->close();
}

include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Booking</title>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        /* ... Keep your existing CSS variables and base styles ... */
        :root {
            --bg-color: #f0f7ff;
            --card-white: #ffffff;
            --primary-blue: #4facfe;
            --text-dark: #1a2b41;
            --text-muted: #8fa1b4;
            --dark-card: #0d1b2a; 
            --accent-green: #2ecc71;
            --active-service-bg: #eef6ff;
            --active-service-border: rgba(79, 172, 254, 0.5);
        }

        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Fredoka', sans-serif; background-color: var(--bg-color); color: var(--text-dark); }
        .main-wrapper { display: flex; flex-direction: column; align-items: center; width: 100%; padding: 60px 40px; }
        .booking-layout { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; max-width: 1100px; width: 100%; }
        .form-container { background: var(--card-white); border-radius: 40px; padding: 50px; box-shadow: 0 20px 50px rgba(180, 200, 220, 0.3); }
        .section-separator { margin-bottom: 40px; }
        .service-option { background: #f8fbff; padding: 20px 30px; border-radius: 25px; border: 2px solid transparent; cursor: pointer; display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px; transition: 0.3s; }
        .service-option.active { background: var(--active-service-bg); border-color: var(--active-service-border); }
        .service-left { display: flex; align-items: center; gap: 20px; }
        .service-icon { width: 50px; height: 50px; background: white; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 20px; color: var(--text-muted); border: 1px solid #eee; }
        .service-option.active .service-icon { color: var(--primary-blue); }
        .schedule-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .input-group { display: flex; flex-direction: column; gap: 8px; }
        .styled-input { padding: 12px 20px; border-radius: 15px; border: 2px solid #f0f7ff; background: #f8fbff; font-family: 'Fredoka'; font-size: 14px; outline: none; }
        .weight-input { display: flex; align-items: center; justify-content: space-between; background: #f8fbff; padding: 18px 30px; border-radius: 20px; }
        .weight-input input { border: none; background: transparent; outline: none; font-family: 'Fredoka'; font-weight: 700; font-size: 20px; width: 80px; }
        .logistics-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .logistics-option { background: #f8fbff; padding: 20px; border-radius: 20px; text-align: center; border: 2px solid transparent; cursor: pointer; transition: 0.3s; }
        .logistics-option.active { background: var(--active-service-bg); border-color: var(--active-service-border); }
        .confirm-btn { width: 100%; padding: 20px; border: none; border-radius: 20px; background: linear-gradient(to right, #81d4fa, #4facfe); color: white; font-size: 16px; font-weight: 700; cursor: pointer; margin-top: 20px; }
        .sidebar { display: flex; flex-direction: column; gap: 20px; }
        .summary-card { background: var(--dark-card); border-radius: 35px; padding: 40px; color: white; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 13px; color: #a5b1c2; }
        .summary-row .value { color: white; font-weight: 700; }
        .total-estimate { border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px; margin-top: 10px; }
        .total-estimate h1 { margin: 10px 0 0; font-size: 42px; }
        .note-bubble {
        position: relative;
        background: #eef6ff; /* Light blue bubble */
        color: #006ebf;      /* Primary blue text */
        padding: 15px;
        border-radius: 20px 20px 20px 5px; 
        font-weight: 500;
        display: block;      /* Changed to block for sidebar fit */
        max-width: 100%;
        margin-top: 15px;
        font-size: 13px;
        line-height: 1.4;
        border: 1px solid rgb(0, 55, 104);
    }

    .note-bubble::before {
        content: "\f10d"; 
        font-family: "Font Awesome 6 Free";
        font-weight: 900;
        opacity: 0.3;
        margin-right: 8px;
    }

    /* Style for the textarea in the form */
    .message-input {
        width: 100%;
        padding: 15px;
        border-radius: 15px;
        border: 2px solid #f0f7ff;
        background: #f8fbff;
        font-family: 'Fredoka', sans-serif;
        outline: none;
        resize: none;
        transition: 0.3s;
    }

    .message-input:focus {
        border-color: var(--primary-blue);
        background: #fff;
    }
    
        </style>
</head>
<body>

<div class="main-wrapper">
    <?php echo $message; ?>
    
    <form action="" method="POST" class="booking-layout">
        <!-- Hidden Inputs for DB -->
        <input type="hidden" name="service_id" id="db_service_id" value="1">
        <input type="hidden" name="service_name" id="db_service_name" value="Wash Only">
        <input type="hidden" name="price_per_kg" id="db_price_per_kg" value="50.00">
        <input type="hidden" name="logistics" id="db_logistics" value="pickup">

        <div class="form-container">
            <h2><i class="fa-solid fa-wand-magic-sparkles"></i> New Booking</h2>

            <!-- 1. Services -->
            <div class="section-separator">
                <h4>1. Choose Service</h4>
                <div class="service-option active" data-id="1" data-name="Wash Only" data-price="50.00" onclick="selectService(this)">
                    <div class="service-left">
                        <div class="service-icon"><i class="fa-solid fa-basket-shopping"></i></div>
                        <div class="service-info"><h5>Wash Only</h5><p>₱50.00 PER KG</p></div>
                    </div>
                </div>
                <div class="service-option" data-id="2" data-name="Wash and Fold" data-price="75.00" onclick="selectService(this)">
                    <div class="service-left">
                        <div class="service-icon"><i class="fa-solid fa-shirt"></i></div>
                        <div class="service-info"><h5>Wash and Fold</h5><p>₱75.00 PER KG</p></div>
                    </div>
                </div>
                <!-- New Service Added Below -->
                <div class="service-option" data-id="3" data-name="Wash, Fold with FabCon" data-price="95.00" onclick="selectService(this)">
                    <div class="service-left">
                        <div class="service-icon"><i class="fa-solid fa-star"></i></div>
                        <div class="service-info"><h5>Wash, Fold with FabCon</h5><p>₱95.00 PER KG</p></div>
                    </div>
                </div>
            </div>

            <!-- 2. Schedule & Weight -->
            <div class="section-separator">
                <h4>2. Schedule & Weight</h4>
                <div class="schedule-grid">
                    <div class="input-group">
                        <label>Pickup Date</label>
                        <input type="date" name="pickup_date" class="styled-input" required>
                    </div>
                    <div class="input-group">
                        <label>Pickup Time</label>
                        <input type="time" name="pickup_time" class="styled-input" required>
                    </div>
                </div>
                <br>
                <div class="input-group">
                    <label>Estimated Weight (kg)</label>
                    <div class="weight-input">
                        <input type="number" name="weight" id="weight" value="1.0" min="1" step="0.5" oninput="updateTotal()">
                        <span class="unit">Kilograms</span>
                    </div>
                </div>
            </div>

            <div class="section-separator">
                <h4>3. Target Delivery Date</h4>
                <input type="date" name="delivery_date" class="styled-input" style="width: 100%;" required>
            </div>

            <div class="section-separator">
                <h4>4. Logistics</h4>
                <div class="logistics-grid">
                    <div class="logistics-option active" onclick="selectLogistics(this, 'pickup')">
                        <i class="fa-solid fa-house-chimney" style="color: #38bdf8"></i><br>Store Pick-up
                    </div>
                    <div class="logistics-option" onclick="selectLogistics(this, 'delivery')">
                        <i class="fa-solid fa-truck-fast" style="color: #ff9f43"></i><br>Delivery (₱50)
                    </div>
                </div>
            </div>
            
            <div class="section-separator">
                <h4>5. Special Instructions</h4>
                <div class="input-group">
                    <textarea name="customer_message" id="customer_message" class="message-input" 
                            placeholder="Any special requests for our team?" rows="3" 
                            oninput="updateSummaryMessage()"></textarea>
                </div>
            </div>
            <button type="submit" name="confirm_booking" class="confirm-btn">Confirm Booking</button>
        </div>

        <!-- Sidebar Summary -->
        <div class="sidebar">
            <div class="summary-card">
                <div class="summary-header" style="font-weight: 700; margin-bottom: 20px; font-size: 18px;">Summary</div>
                <div class="summary-row"><span>Service</span><span class="value" id="summaryService">Wash Only</span></div>
                <div class="summary-row"><span>Rate</span><span class="value" id="summaryRate">₱50.00/kg</span></div>
                <div class="summary-row"><span>Weight</span><span class="value" id="summaryWeight">1 kg</span></div>
                
                <!-- Delivery Fee Row (Hidden by default) -->
                <div class="summary-row" id="deliveryFeeRow" style="display: none;">
                    <span>Delivery Fee</span><span class="value">₱50.00</span>
                </div>

                <div class="total-estimate">
                    <label style="font-size: 11px; opacity: 0.6; text-transform: uppercase;">Total Estimate</label>
                    <h1 id="summaryTotal">₱50.00</h1>
                </div>
            </div>
           
            <!-- The Bubble Note -->
            <div id="noteBubbleContainer" style="display: none;">
                <div class="note-bubble" id="summaryNote">
                    No instructions.
                </div>
            </div>
        </div>
        
    </form>
</div>

<script>
    function selectService(element) {
        document.querySelectorAll('.service-option').forEach(opt => opt.classList.remove('active'));
        element.classList.add('active');
        
        document.getElementById('db_service_id').value = element.getAttribute('data-id');
        document.getElementById('db_service_name').value = element.getAttribute('data-name');
        document.getElementById('db_price_per_kg').value = element.getAttribute('data-price');
        
        updateTotal();
    }

    function selectLogistics(element, type) {
        document.querySelectorAll('.logistics-option').forEach(opt => opt.classList.remove('active'));
        element.classList.add('active');
        document.getElementById('db_logistics').value = type;
        
        // Show/Hide delivery fee in summary
        const feeRow = document.getElementById('deliveryFeeRow');
        feeRow.style.display = (type === 'delivery') ? 'flex' : 'none';
        
        updateTotal();
    }

    function updateTotal() {
        const weight = parseFloat(document.getElementById('weight').value) || 0;
        const price = parseFloat(document.getElementById('db_price_per_kg').value);
        const name = document.getElementById('db_service_name').value;
        const logistics = document.getElementById('db_logistics').value;
        
        let deliveryFee = (logistics === 'delivery') ? 50.00 : 0.00;
        const total = (weight * price) + deliveryFee;
        
        document.getElementById('summaryService').innerText = name;
        document.getElementById('summaryRate').innerText = `₱${price.toFixed(2)}/kg`;
        document.getElementById('summaryWeight').innerText = `${weight} kg`;
        document.getElementById('summaryTotal').innerText = `₱${total.toFixed(2)}`;
    }
    function updateSummaryMessage() {
        const msg = document.getElementById('customer_message').value;
        const bubbleContainer = document.getElementById('noteBubbleContainer');
        const summaryNote = document.getElementById('summaryNote');
        
        if (msg.trim().length > 0) {
            bubbleContainer.style.display = 'block';
            summaryNote.innerText = msg;
        } else {
            bubbleContainer.style.display = 'none';
        }
    }
</script>

</body>
</html>