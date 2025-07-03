<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../classes/User.php';

$token = $_GET['token'] ?? '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if ($password !== $confirm) {
        $message = "❌ Les mots de passe ne correspondent pas.";
    } else {
        $stmt = $pdo->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW()");
        $stmt->execute([$token]);
        $email = $stmt->fetchColumn();

        if ($email) {
            $user = new User($pdo);
            if ($user->loadByEmail($email)) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $pdo->prepare("UPDATE users SET password = ? WHERE email = ?")->execute([$hash, $email]);
                $pdo->prepare("DELETE FROM password_resets WHERE email = ?")->execute([$email]);
                $message = "✅ Mot de passe mis à jour. Tu peux te connecter.";
            }
        } else {
            $message = "❌ Lien invalide ou expiré.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Réinitialiser le mot de passe</title></head>
<body>
    <?php require_once __DIR__ . '/../includes/navbar.php'; ?>
    <h1>Réinitialiser le mot de passe</h1>
    <?php if ($message) echo "<p>$message</p>"; ?>
    <?php if (!$message || str_starts_with($message, '❌')): ?>
    <form method="post">
        <label>Nouveau mot de passe :
            <input type="password" name="password" required>
        </label><br><br>
        <label>Confirmer :
            <input type="password" name="confirm" required>
        </label><br><br>
        <button type="submit">Réinitialiser</button>
    </form>
    <?php endif; ?>
</body>
</html>
