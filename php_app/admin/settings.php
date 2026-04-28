<?php
require '../config.php';
requireAdmin();

$msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['admin_upi'])) {
        $stmt = $pdo->prepare("UPDATE admin_settings SET setting_value = ? WHERE setting_key = 'admin_upi'");
        $stmt->execute([$_POST['admin_upi']]);
        $msg = "UPI Updated.";
    }
}

$stmt = $pdo->query("SELECT setting_value FROM admin_settings WHERE setting_key = 'admin_upi'");
$upi = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="navbar">
        <div class="logo">Admin - Settings</div>
        <div class="nav-links"><a href="index.php">Back</a></div>
    </div>
    <div class="container">
        <h2>Platform Settings</h2>
        <?php if($msg) echo "<div class='alert success'>$msg</div>"; ?>
        <form method="POST" class="action-form">
            <div class="form-group">
                <label>Admin UPI ID</label>
                <input type="text" name="admin_upi" value="<?= htmlspecialchars($upi) ?>" required>
            </div>
            <button type="submit" class="btn">Save</button>
        </form>
    </div>
</body>
</html>
