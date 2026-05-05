<?php
include 'conn/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// GET CURRENT USER DATA
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// UPDATE PROFILE
if (isset($_POST['update'])) {

    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $profile_image = $user['profile_image'];

    // IMAGE UPLOAD
    if (!empty($_FILES['profile_image']['name'])) {

        $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/ats_system/assets/uploads/";

        $file_name = time() . "_" . basename($_FILES["profile_image"]["name"]);
        $target_file = $target_dir . $file_name;

        // create folder if not exists
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file);

        $profile_image = $file_name;
    }

    $update = $conn->prepare("
        UPDATE users 
        SET full_name=?, email=?, phone=?, profile_image=? 
        WHERE id=?
    ");

    $update->bind_param("ssssi", $full_name, $email, $phone, $profile_image, $user_id);

    if ($update->execute()) {

        // ✅ SYNC SESSION (IMPORTANT FIX)
        $_SESSION['full_name'] = $full_name;
        $_SESSION['profile_image'] = $profile_image;

        header("Location: main.php?updated=1");
        exit();
    } else {
        $error = "Update failed!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="container">

    <div class="card">
        <h2>Edit Profile</h2>

        <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

        <form method="POST" enctype="multipart/form-data">

            <label>Full Name</label>
            <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" class="input">

            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="input">

            <label>Phone</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" class="input">

            <label>Profile Image</label><br>

            <img src="/ats_system/assets/uploads/<?php echo !empty($user['profile_image']) ? $user['profile_image'] : 'default.png'; ?>"
                 width="80"
                 style="border-radius:50%; margin:10px 0;">

            <br>

            <input type="file" name="profile_image" class="input">

            <button type="submit" name="update" class="btn btn-primary" style="margin-top:10px;">
                Update Profile
            </button>

        </form>
    </div>

</div>

</body>
</html>