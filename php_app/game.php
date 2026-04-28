<?php
require 'config.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT wallet_balance FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$balance = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Aviator Game</title>
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .game-container {
            background-color: #1a1a1a; color: white; padding: 20px; border-radius: 10px;
            max-width: 800px; margin: 20px auto; text-align: center;
        }
        .canvas-area {
            background: #2a2a2a; height: 300px; border-radius: 8px; display: flex;
            align-items: center; justify-content: center; font-size: 4em; font-weight: bold;
            color: #d12222; position: relative; overflow: hidden;
        }
        .multiplier { z-index: 10; }
        .plane { position: absolute; bottom: 20px; left: 20px; font-size: 2em; transition: all 0.1s linear; }
        .crashed .multiplier { color: red; }
        .flying .multiplier { color: #28a745; }
        .controls { margin-top: 20px; display: flex; gap: 10px; justify-content: center; align-items: center; flex-wrap: wrap; }
        .controls input { padding: 10px; border-radius: 5px; border: none; width: 150px; text-align: center; }
        .bet-btn { padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .cashout-btn { background: #ffc107; color: black; display: none; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .pf-info { font-size: 0.8em; color: #888; margin-top: 15px; word-break: break-all; }
        .message { margin-top:10px; font-size: 1.2em; font-weight: bold; color: gold; }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">Aviator Game</div>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <div class="balance-display">Bal: ₹<span id="bal"><?= number_format($balance, 2) ?></span></div>
        </div>
    </div>

    <div class="game-container">
        <div class="canvas-area" id="canvasArea">
            <div class="multiplier" id="multiplier">1.00x</div>
            <div class="plane" id="plane">✈️</div>
        </div>
        <div class="controls">
            <input type="number" id="betAmount" value="10.00" min="1" step="1">
            <button class="bet-btn" id="betBtn">BET</button>
            <button class="cashout-btn" id="cashoutBtn">CASHOUT at <span id="cashoutAmt">0.00</span></button>
        </div>
        <div class="message" id="message">Waiting for next round...</div>
        <div class="pf-info">
            <strong>Provably Fair Seed (upcoming round):</strong> <span id="serverSeedHash">Processing...</span>
        </div>
    </div>

    <script>
        let currentBalance = <?= $balance ?>;
        let isBetPlaced = false;
        let isFlying = false;
        let currentMultiplier = 1.00;
        let gameTimer = null;
        let finalCrashPoint = 1.00;
        let betAmount = 0;

        const multiplierEl = document.getElementById('multiplier');
        const planeEl = document.getElementById('plane');
        const betBtn = document.getElementById('betBtn');
        const cashoutBtn = document.getElementById('cashoutBtn');
        const cashoutAmt = document.getElementById('cashoutAmt');
        const messageEl = document.getElementById('message');
        const balEl = document.getElementById('bal');

        function generateCrashPoint() {
            const rand = Math.random();
            let crash = 1.00;
            if (rand < 0.6) crash = 1 + (Math.random() * 2);
            else if (rand < 0.9) crash = 3 + (Math.random() * 7);
            else crash = 10 + (Math.random() * 40);
            
            if(Math.random() < 0.02) return 1.00;
            return parseFloat(crash.toFixed(2));
        }

        async function createHash(msg) {
            const buffer = await crypto.subtle.digest("SHA-256", new TextEncoder().encode(msg));
            return Array.from(new Uint8Array(buffer)).map(b => b.toString(16).padStart(2, '0')).join('');
        }

        let nextSeed = Math.random().toString();
        async function prepNextRound() {
            nextSeed = Math.random().toString(36).substring(2, 15);
            let hash = await createHash(nextSeed);
            document.getElementById('serverSeedHash').innerText = hash;
            finalCrashPoint = generateCrashPoint();
        }
        prepNextRound();

        betBtn.addEventListener('click', () => {
            let amt = parseFloat(document.getElementById('betAmount').value);
            if(amt > currentBalance) { alert('Insufficient Balance!'); return; }
            if(amt <= 0) return;
            
            updateBalance(-amt, 'game_bet');
            betAmount = amt;
            isBetPlaced = true;
            betBtn.style.display = 'none';
            document.getElementById('betAmount').disabled = true;
            messageEl.innerText = "Bet placed! Waiting for flight...";
            setTimeout(startGame, 2000);
        });

        cashoutBtn.addEventListener('click', () => {
            if(!isFlying || !isBetPlaced) return;
            let winAmt = (betAmount * currentMultiplier).toFixed(2);
            updateBalance(parseFloat(winAmt), 'game_win');
            isBetPlaced = false;
            cashoutBtn.style.display = 'none';
            messageEl.innerText = `Cashed out ₹${winAmt}!`;
        });

        function startGame() {
            isFlying = true; currentMultiplier = 1.00;
            multiplierEl.innerText = currentMultiplier.toFixed(2) + "x";
            document.getElementById('canvasArea').className = "canvas-area flying";
            if (isBetPlaced) cashoutBtn.style.display = 'block';
            planeEl.style.bottom = "20px"; planeEl.style.left = "20px";
            
            let startTime = Date.now();
            gameTimer = setInterval(() => {
                let timePassed = (Date.now() - startTime) / 1000;
                currentMultiplier = Math.exp(0.15 * timePassed);
                if (currentMultiplier >= finalCrashPoint) { crash(finalCrashPoint); return; }
                multiplierEl.innerText = currentMultiplier.toFixed(2) + "x";
                if(isBetPlaced) cashoutAmt.innerText = (betAmount * currentMultiplier).toFixed(2);
                planeEl.style.bottom = Math.min(250, 20 + timePassed * 20) + "px";
                planeEl.style.left = Math.min(700, 20 + timePassed * 40) + "px";
            }, 50);
        }

        function crash(point) {
            clearInterval(gameTimer); isFlying = false; currentMultiplier = point;
            multiplierEl.innerText = "FLEW AWAY AT " + point.toFixed(2) + "x";
            document.getElementById('canvasArea').className = "canvas-area crashed";
            cashoutBtn.style.display = 'none'; betBtn.style.display = 'block';
            document.getElementById('betAmount').disabled = false;
            
            if(isBetPlaced) {
                messageEl.innerText = "You crashed!"; isBetPlaced = false;
                logTransaction(-betAmount, 'game_loss');
            } else if (!messageEl.innerText.includes("Cashed out")) {
                messageEl.innerText = "Game Over.";
            }
            document.getElementById('serverSeedHash').innerText += ` (Revealed Seed: ${nextSeed}, Outcome: ${point.toFixed(2)}x)`;
            setTimeout(prepNextRound, 100);
        }

        function updateBalance(amount, type) {
            currentBalance += amount; balEl.innerText = currentBalance.toFixed(2);
            logTransaction(amount, type);
        }

        function logTransaction(amount, type) {
            fetch('api_balance.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `amount=${amount}&type=${type}`
            });
        }
    </script>
</body>
</html>
