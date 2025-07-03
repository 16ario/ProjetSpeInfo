<?php
require_once __DIR__ . '/../includes/admin_only.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = (int) ($_POST['user_id'] ?? 0);
    if ($userId > 0) {
        $stmt = $pdo->prepare("UPDATE users SET failed_attempts = 0, locked_until = NULL WHERE id = ?");
        $stmt->execute([$userId]);
    }
}

header('Location: users.php');
exit;
