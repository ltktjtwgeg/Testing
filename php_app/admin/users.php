<?php
require '../config.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_balance'])) {
    $uid = $_POST['user_id'];
    $bal = $_POST['new_balance'];
    $stmt = $pdo->prepare("UPDATE users SET wallet_balance = ? WHERE id = ?");
    $stmt->execute([$bal, $uid]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="navbar">
        <div class="logo">Admin - Users</div>
        <div class="nav-links"><a href="index.php">Back</a></div>
    </div>
    <div class="container">
        <h2>All Users</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th><th>Username</th><th>Balance</th><th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->query("SELECT id, username, wallet_balance FROM users WHERE is_admin = 0");
                while($row = $stmt->fetch()) {
                    echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['username']}</td>
                        <td>
                            <form method='POST' style='display:flex;gap:10px;'>
                                <input type='hidden' name='user_id' value='{$row['id']}'>
                                <input type='number' step='0.01' name='new_balance' value='{$row['wallet_balance']}' style='width:100px'>
                                <button type='submit' name='update_balance' class='btn' style='padding:5px'>Update</button>
                            </form>
                        </td>
                        <td>-</td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
