<?php
require_once __DIR__ . '/../includes/admin_only.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = (int) ($_POST['user_id'] ?? 0);
    $currentRole = (int) ($_POST['current_role'] ?? 1);

    if ($userId > 0) {
        $newRole = $currentRole === 2 ? 1 : 2;

        // Empêche un admin de se rétrograder lui-même
        if ($userId === $_SESSION['user_id'] && $newRole === 1) {
            header('Location: users.php?error=self-demotion');
            exit;
        }

        $stmt = $pdo->prepare("UPDATE users SET role_id = ? WHERE id = ?");
        $stmt->execute([$newRole, $userId]);
    }
}

header('Location: users.php');
exit;
