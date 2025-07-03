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

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newEmail = trim($_POST['email'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (!$user->verifyPassword($currentPassword)) {
        $message = "❌ Mot de passe actuel incorrect.";
    } else {
        $updates = [];

        if (!empty($newEmail) && $newEmail !== $user->getEmail()) {
            if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                $message = "❌ Adresse email invalide.";
            } else {
                $updates['email'] = $newEmail;
            }
        }

        if (!empty($newPassword)) {
            if ($newPassword !== $confirmPassword) {
                $message = "❌ Les mots de passe ne correspondent pas.";
            } else {
                $updates['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
            }
        }

        if (empty($message) && !empty($updates)) {
            $success = $user->update($updates);
            $message = $success ? "✅ Profil mis à jour." : "❌ Une erreur est survenue.";
        } elseif (empty($updates)) {
            $message = "ℹ️ Aucun changement détecté.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon profil</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        form { max-width: 500px; margin: auto; }
        label { display: block; margin-top: 15px; }
        input { width: 100%; padding: 8px; margin-top: 5px; }
        button { margin-top: 20px; padding: 10px 20px; }
        .message { margin-top: 20px; color: #d00; font-weight: bold; }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/includes/navbar.php'; ?>

    <h1>👤 Mon profil</h1>

    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Adresse email :
            <input type="email" name="email" value="<?= htmlspecialchars($user->getEmail()) ?>" required>
        </label>

        <label>Nouveau mot de passe :
            <input type="password" name="new_password" placeholder="Laisse vide pour ne pas changer">
        </label>

        <label>Confirmer le nouveau mot de passe :
            <input type="password" name="confirm_password">
        </label>

        <label>Mot de passe actuel (obligatoire) :
            <input type="password" name="current_password" required>
        </label>

        <button type="submit">Mettre à jour</button>
    </form>
</body>
</html>
