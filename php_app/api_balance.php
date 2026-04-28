<?php
require 'config.php';
if (!isLoggedIn()) exit('Unauthorized');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $amount = (float) $_POST['amount'];
    $type = $_POST['type'];

    $stmt = $pdo->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE id = ?");
    $stmt->execute([$amount, $user_id]);

    if ($amount > 0 || $type == 'game_loss' || $type == 'game_bet') {
        $abs_amt = abs($amount);
        $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, status) VALUES (?, ?, ?, 'completed')");
        $stmt->execute([$user_id, $type, $abs_amt]);
    }
}
?>
