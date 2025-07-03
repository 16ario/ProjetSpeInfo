<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../classes/User.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user = new User($pdo);
$user->loadById($_SESSION['user_id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user->disable2FA();
    header('Location: ../dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Désactiver la 2FA</title>
</head>
<body>
    <h1>Désactiver la double authentification</h1>

    <p>Es-tu sûr de vouloir désactiver la 2FA ?</p>

    <form method="post">
        <button type="submit">✅ Oui, désactiver la 2FA</button>
        <a href="../dashboard.php">❌ Annuler</a>
    </form>
</body>
</html>
