<?php
require '../config.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dep_id = $_POST['deposit_id'];
    $action = $_POST['action'];
    
    $stmt = $pdo->prepare("SELECT * FROM deposits WHERE id = ?");
    $stmt->execute([$dep_id]);
    $dep = $stmt->fetch();
    
    if ($dep && $dep['status'] == 'pending') {
        if ($action == 'approve') {
            $pdo->beginTransaction();
            try {
                $stmt = $pdo->prepare("UPDATE deposits SET status = 'approved' WHERE id = ?");
                $stmt->execute([$dep_id]);
                
                $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
                $stmt->execute([$dep['amount'], $dep['user_id']]);
                $pdo->commit();
            } catch (Exception $e) { $pdo->rollBack(); }
        } elseif ($action == 'reject') {
            $stmt = $pdo->prepare("UPDATE deposits SET status = 'rejected' WHERE id = ?");
            $stmt->execute([$dep_id]);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Deposits</title>
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
        <h2>Pending Deposits</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th><th>User ID</th><th>Amount</th><th>UTR</th><th>Screenshot</th><th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("SELECT * FROM deposits WHERE status = 'pending' ORDER BY created_at DESC");
                while($row = $stmt->fetch()) {
                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['user_id']}</td>
                        <td>₹{$row['amount']}</td>
                        <td>{$row['utr_number']}</td>
                        <td><a href='../{$row['screenshot_path']}' target='_blank'>View Image</a></td>
                        <td>
                            <form method='POST' style='display:inline;'>
                                <input type='hidden' name='deposit_id' value='{$row['id']}'>
                                <button type='submit' name='action' value='approve' class='action-btn btn-approve'>Approve</button>
                                <button type='submit' name='action' value='reject' class='action-btn btn-reject'>Reject</button>
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
