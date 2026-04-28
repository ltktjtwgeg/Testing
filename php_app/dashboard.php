<?php
require 'config.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="navbar">
        <div class="logo">Aviator Platform</div>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="game.php" class="btn-game">Play Aviator</a>
            <a href="deposit.php">Deposit</a>
            <a href="withdraw.php">Withdraw</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <h2>Welcome, <?= htmlspecialchars($user['username']) ?>!</h2>
        
        <div class="wallet-card">
            <h3>Wallet Balance</h3>
            <div class="balance">₹<?= number_format($user['wallet_balance'], 2) ?></div>
        </div>

        <div class="referral-card mt-2" style="background:#f9f9f9; padding:15px; border-radius:5px;">
            <h3>Your Referral System</h3>
            <p>Share your code to earn bonuses!</p>
            <div class="ref-code">Your Code: <strong><?= $user['referral_code'] ?></strong></div>
            <p>Your Link: <?= (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']==='on'?"https":"http")."://".$_SERVER['HTTP_HOST']."/php_app/index.php?ref=".$user['referral_code'] ?></p>
        </div>

        <h3 class="mt-2">Recent Transactions</h3>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->prepare("SELECT * FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 10");
                $stmt->execute([$user_id]);
                while($row = $stmt->fetch()) {
                    $statusClass = 'status-'.$row['status'];
                    echo "<tr>
                        <td>".ucfirst($row['type'])."</td>
                        <td>₹{$row['amount']}</td>
                        <td><span class='{$statusClass}'>".ucfirst($row['status'])."</span></td>
                        <td>{$row['created_at']}</td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
