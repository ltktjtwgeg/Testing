<?php
require 'config.php';
requireLogin();

$error = '';
$success = '';

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$balance = $stmt->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = (float) $_POST['amount'];
    $upi_id = trim($_POST['upi_id']);
    
    if ($amount <= 0) {
        $error = "Invalid amount.";
    } elseif ($amount > $balance) {
        $error = "Insufficient balance.";
    } else {
        $new_balance = $balance - $amount;
        $stmt = $pdo->prepare("UPDATE users SET wallet_balance = ? WHERE id = ?");
        $stmt->execute([$new_balance, $user_id]);
        
        $stmt = $pdo->prepare("INSERT INTO withdrawals (user_id, amount, upi_id) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $amount, $upi_id]);
        
        $stmt2 = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, status) VALUES (?, 'withdrawal', ?, 'pending')");
        $stmt2->execute([$user_id, $amount]);
        
        $success = "Withdrawal request submitted! Waiting for admin processing.";
        $balance = $new_balance;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Withdraw</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="navbar">
        <div class="logo">Aviator Platform</div>
        <div class="nav-links"><a href="dashboard.php">Dashboard</a></div>
    </div>
    <div class="container">
        <h2>Withdraw Funds</h2>
        <div class="wallet-card">
            <h3>Available Balance</h3>
            <div class="balance">₹<?= number_format($balance, 2) ?></div>
        </div>
        <?php if($error): ?><div class="alert error"><?= $error ?></div><?php endif; ?>
        <?php if($success): ?><div class="alert success"><?= $success ?></div><?php endif; ?>
        <form method="POST" class="action-form">
            <div class="form-group"><label>Amount (₹)</label><input type="number" step="0.01" name="amount" required max="<?= $balance ?>"></div>
            <div class="form-group"><label>Your UPI ID</label><input type="text" name="upi_id" required></div>
            <button type="submit" class="btn">Submit Request</button>
        </form>
    </div>
</body>
</html>
