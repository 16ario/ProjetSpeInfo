<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../vendor/autoload.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $message = "❌ Veuillez remplir tous les champs.";
    } else {
        $user = new User($pdo);

        if ($user->loadByEmail($email)) {
            // Vérifie si le compte est verrouillé
            $stmt = $pdo->prepare("SELECT failed_attempts, locked_until FROM users WHERE id = ?");
            $stmt->execute([$user->getId()]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $lockedUntil = $row['locked_until'] ?? null;
            $failedAttempts = (int) $row['failed_attempts'];

            if ($lockedUntil && strtotime($lockedUntil) > time()) {
                $message = "⛔ Compte verrouillé jusqu’à " . date('H:i', strtotime($lockedUntil));
            } elseif ($user->verifyPassword($password)) {
                // Réinitialise les tentatives
                $pdo->prepare("UPDATE users SET failed_attempts = 0, locked_until = NULL WHERE id = ?")->execute([$user->getId()]);

                if ($user->is2FAEnabled()) {
                    $_SESSION['2fa_user_id'] = $user->getId();
                    header('Location: verify_2fa.php');
                    exit;
                } else {
                    $_SESSION['user_id'] = $user->getId();
                    $_SESSION['username'] = $user->getUsername();
                    $_SESSION['role'] = $user->getRoleId();
                    header('Location: ../dashboard.php');
                    exit;
                }
            } else {
                // Incrémente les tentatives
                $pdo->prepare("UPDATE users SET failed_attempts = failed_attempts + 1, last_failed_login = NOW() WHERE id = ?")->execute([$user->getId()]);

                $failedAttempts++;

                if ($failedAttempts >= 5) {
                    $lockUntil = date('Y-m-d H:i:s', time() + 900); // 15 minutes
                    $pdo->prepare("UPDATE users SET locked_until = ? WHERE id = ?")->execute([$lockUntil, $user->getId()]);
                    $message = "⛔ Trop de tentatives. Compte verrouillé pendant 15 minutes.";
                } else {
                    $message = "❌ Mot de passe incorrect. Tentative $failedAttempts/5.";
                }
            }
        } else {
            $message = "❌ Aucun compte trouvé avec cet email.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        form { max-width: 400px; margin: auto; }
        label { display: block; margin-bottom: 10px; }
        input { width: 100%; padding: 8px; margin-top: 4px; }
        button { padding: 10px 20px; }
        .message { margin-bottom: 20px; color: red; }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../includes/navbar.php'; ?>

    <h1>Connexion</h1>

    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Email :
            <input type="email" name="email" required>
        </label>

        <label>Mot de passe :
            <input type="password" name="password" required>
        </label>

        <button type="submit">Se connecter</button>
    </form>
</body>
</html>
