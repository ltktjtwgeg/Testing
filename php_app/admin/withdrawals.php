<?php
require '../config.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $wd_id = $_POST['withdrawal_id'];
    $action = $_POST['action'];
    
    $stmt = $pdo->prepare("SELECT * FROM withdrawals WHERE id = ?");
    $stmt->execute([$wd_id]);
    $wd = $stmt->fetch();
    
    if ($wd && $wd['status'] == 'pending') {
        if ($action == 'approve') {
            $stmt = $pdo->prepare("UPDATE withdrawals SET status = 'completed' WHERE id = ?");
            $stmt->execute([$wd_id]);
        } elseif ($action == 'reject') {
            $pdo->beginTransaction();
            try {
                $stmt = $pdo->prepare("UPDATE withdrawals SET status = 'rejected' WHERE id = ?");
                $stmt->execute([$wd_id]);
                
                $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
                $stmt->execute([$wd['amount'], $wd['user_id']]);
                $pdo->commit();
            } catch (Exception $e) { $pdo->rollBack(); }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Withdrawals</title>
    <link rel="stylesheet" href="../style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="navbar">
        <div class="logo">Admin Panel</div>
        <div class="nav-links">
            <a href="index.php">Dashboard</a>
            <a href="deposits.php">Deposits</a>
            <a href="withdrawals.php">Withdrawals</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>
    <div class="container">
        <h2>Pending Withdrawals</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th><th>User ID</th><th>Amount</th><th>UPI ID</th><th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("SELECT * FROM withdrawals WHERE status = 'pending' ORDER BY created_at DESC");
                while($row = $stmt->fetch()) {
                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['user_id']}</td>
                        <td>₹{$row['amount']}</td>
                        <td>{$row['upi_id']}</td>
                        <td>
                            <form method='POST' style='display:inline;'>
                                <input type='hidden' name='withdrawal_id' value='{$row['id']}'>
                                <button type='submit' name='action' value='approve' class='action-btn btn-approve'>Approve (Paid)</button>
                                <button type='submit' name='action' value='reject' class='action-btn btn-reject'>Reject (Refund)</button>
                            </form>
                        </td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
