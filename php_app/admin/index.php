<?php
require '../config.php';
requireAdmin();

$users_count = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$pending_deposits = $pdo->query("SELECT COUNT(*) FROM deposits WHERE status = 'pending'")->fetchColumn();
$pending_withdrawals = $pdo->query("SELECT COUNT(*) FROM withdrawals WHERE status = 'pending'")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="navbar">
        <div class="logo">Admin Panel</div>
        <div class="nav-links">
            <a href="index.php">Dashboard</a>
            <a href="users.php">Users</a>
            <a href="deposits.php">Deposits (<?= $pending_deposits ?>)</a>
            <a href="withdrawals.php">Withdrawals (<?= $pending_withdrawals ?>)</a>
            <a href="settings.php">Settings</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>
    <div class="container">
        <h2>Admin Overview</h2>
        <div class="admin-stats">
            <div class="wallet-card"><h3>Total Users</h3><div class="balance"><?= $users_count ?></div></div>
            <div class="wallet-card"><h3>Pending Deposits</h3><div class="balance"><?= $pending_deposits ?></div></div>
            <div class="wallet-card"><h3>Pending Withdrawals</h3><div class="balance"><?= $pending_withdrawals ?></div></div>
        </div>
    </div>
</body>
</html>
