<?php
require 'config.php';

if (isLoggedIn()) {
    if (isAdmin()) header('Location: admin/index.php');
    else header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';

$ref_code = isset($_GET['ref']) ? htmlspecialchars($_GET['ref']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action']) && $_POST['action'] == 'register') {
        $user = trim($_POST['username']);
        $pass = $_POST['password'];
        $ref = trim($_POST['referred_by']);
        
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$user]);
        if ($stmt->fetch()) {
            $error = "Username already exists.";
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $my_ref = strtoupper(substr(md5(uniqid()), 0, 8));
            
            $stmt = $pdo->prepare("INSERT INTO users (username, password, referral_code, referred_by) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$user, $hash, $my_ref, $ref])) {
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Registration failed.";
            }
        }
    } elseif (isset($_POST['action']) && $_POST['action'] == 'login') {
        $user = trim($_POST['username']);
        $pass = $_POST['password'];
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$user]);
        $userData = $stmt->fetch();
        
        if ($userData && password_verify($pass, $userData['password'])) {
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['username'] = $userData['username'];
            $_SESSION['is_admin'] = $userData['is_admin'];
            
            if ($userData['is_admin'] == 1) {
                header('Location: admin/index.php');
            } else {
                header('Location: dashboard.php');
            }
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Aviator Platform - Login / Register</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="auth-container">
        <h2>Aviator Platform</h2>
        <?php if($error): ?><div class="alert error"><?= $error ?></div><?php endif; ?>
        <?php if($success): ?><div class="alert success"><?= $success ?></div><?php endif; ?>
        <div class="forms">
            <div class="login-form">
                <h3>Login</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="login">
                    <div class="form-group"><label>Username</label><input type="text" name="username" required></div>
                    <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
                    <button type="submit" class="btn">Login</button>
                </form>
            </div>
            <div class="register-form mt-2" style="margin-top: 30px;">
                <h3>Register</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="register">
                    <div class="form-group"><label>Username</label><input type="text" name="username" required></div>
                    <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
                    <div class="form-group"><label>Referral Code (Optional)</label><input type="text" name="referred_by" value="<?= $ref_code ?>"></div>
                    <button type="submit" class="btn" style="background:#27ae60">Register</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
