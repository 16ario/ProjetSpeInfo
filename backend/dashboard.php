<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/classes/User.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

$user = new User($pdo);
$user->loadById($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 700px;
            margin: auto;
        }
        h1 {
            margin-top: 0;
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/includes/navbar.php'; ?>

    <div class="container">
        <h1>Bienvenue, <?= htmlspecialchars($user->getUsername()) ?> 👋</h1>

        <p><strong>Email :</strong> <?= htmlspecialchars($user->getEmail()) ?></p>
        <p><strong>Rôle :</strong> <?= $user->getRoleId() === 2 ? 'Admin' : 'Utilisateur' ?></p>
        <p><strong>2FA :</strong> <?= $user->is2FAEnabled() ? '✅ Activée' : '❌ Non activée' ?></p>

        <?php if (!$user->is2FAEnabled()): ?>
            <p><a href="auth/enable_2fa.php">🔐 Activer la double authentification</a></p>
        <?php else: ?>
            <p><a href="auth/disable_2fa.php">❌ Désactiver la double authentification</a></p>
        <?php endif; ?>
    </div>
</body>
</html>
