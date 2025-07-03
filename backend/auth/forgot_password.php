<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../vendor/autoload.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "❌ Adresse email invalide.";
    } else {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + 3600); // 1h

        $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$email, $token, $expires]);

        // Envoie du lien par email (à adapter avec ton système d'envoi)
        $resetLink = "https://tonsite.com/auth/reset_password.php?token=$token";
        mail($email, "Réinitialisation de mot de passe", "Clique ici pour réinitialiser ton mot de passe : $resetLink");

        $message = "📧 Si l'adresse existe, un lien a été envoyé.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Mot de passe oublié</title></head>
<body>
    <?php require_once __DIR__ . '/../includes/navbar.php'; ?>
    <h1>Mot de passe oublié</h1>
    <?php if ($message) echo "<p>$message</p>"; ?>
    <form method="post">
        <label>Email :
            <input type="email" name="email" required>
        </label><br><br>
        <button type="submit">Envoyer le lien</button>
    </form>
</body>
</html>
