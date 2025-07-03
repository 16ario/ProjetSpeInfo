<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../vendor/autoload.php';

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if (empty($username) || empty($email) || empty($password) || empty($confirm)) {
        $message = "❌ Veuillez remplir tous les champs.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "❌ Adresse email invalide.";
    } elseif ($password !== $confirm) {
        $message = "❌ Les mots de passe ne correspondent pas.";
    } else {
        $user = new User($pdo);

        if ($user->loadByEmail($email)) {
            $message = "⚠️ Un compte avec cet email existe déjà.";
        } else {
            $success = $user->create($username, $email, $password);
            if ($success) {
                $_SESSION['user_id'] = $user->getId();
                $_SESSION['username'] = $user->getUsername();
                $_SESSION['role'] = $user->getRoleId();
                header('Location: ../dashboard.php');
                exit;
            } else {
                $message = "❌ Une erreur est survenue lors de l'inscription.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
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

    <h1>Créer un compte</h1>

    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Nom d'utilisateur :
            <input type="text" name="username" required>
        </label>

        <label>Email :
            <input type="email" name="email" required>
        </label>

        <label>Mot de passe :
            <input type="password" name="password" required>
        </label>

        <label>Confirmer le mot de passe :
            <input type="password" name="confirm" required>
        </label>

        <button type="submit">S'inscrire</button>
    </form>
</body>
</html>
