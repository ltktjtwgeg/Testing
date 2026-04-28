<?php
require 'config.php';
requireLogin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = $_POST['amount'];
    $utr = $_POST['utr_number'];
    
    // File upload
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_ext = strtolower(pathinfo($_FILES["screenshot"]["name"], PATHINFO_EXTENSION));
    if ($file_ext != "jpg" && $file_ext != "png" && $file_ext != "jpeg") {
        $error = "Only JPG, JPEG, PNG files are allowed.";
    } else {
        $new_filename = uniqid() . '.' . $file_ext;
        $target_file = $target_dir . $new_filename;
        
        if (move_uploaded_file($_FILES["screenshot"]["tmp_name"], $target_file)) {
            $stmt = $pdo->prepare("INSERT INTO deposits (user_id, amount, utr_number, screenshot_path) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $amount, $utr, $target_file]);
            
            $stmt2 = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, status) VALUES (?, 'deposit', ?, 'pending')");
            $stmt2->execute([$_SESSION['user_id'], $amount]);
            
            $success = "Deposit request submitted successfully! Awaiting admin approval.";
        } else {
            $error = "Sorry, there was an error uploading your file.";
        }
    }
}

$stmt = $pdo->prepare("SELECT setting_value FROM admin_settings WHERE setting_key = 'admin_upi'");
$stmt->execute();
$admin_upi = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT setting_value FROM admin_settings WHERE setting_key = 'admin_qr'");
$stmt->execute();
$admin_qr = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Deposit</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="navbar">
        <div class="logo">Aviator Platform</div>
        <div class="nav-links"><a href="dashboard.php">Dashboard</a></div>
    </div>
    <div class="container">
        <h2>Deposit Funds</h2>
        <?php if($error): ?><div class="alert error"><?= $error ?></div><?php endif; ?>
        <?php if($success): ?><div class="alert success"><?= $success ?></div><?php endif; ?>
        <div class="deposit-info">
            <p>Please send money to the following UPI ID and upload the screenshot with UTR number.</p>
            <h3>Admin UPI ID: <strong><?= htmlspecialchars($admin_upi) ?></strong></h3>
            <?php if($admin_qr): ?><img src="<?= htmlspecialchars($admin_qr) ?>" alt="Admin QR" class="qr-code"><?php endif; ?>
        </div>
        <form method="POST" enctype="multipart/form-data" class="action-form">
            <div class="form-group"><label>Amount (₹)</label><input type="number" step="0.01" name="amount" required></div>
            <div class="form-group"><label>UTR Number (12 digits)</label><input type="text" name="utr_number" required></div>
            <div class="form-group"><label>Payment Screenshot</label><input type="file" name="screenshot" accept="image/png, image/jpeg" required></div>
            <button type="submit" class="btn">Submit Deposit Request</button>
        </form>
    </div>
</body>
</html>
