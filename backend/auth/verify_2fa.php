<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Crypto.php';
require_once __DIR__ . '/../vendor/autoload.php';

use RobThree\Auth\TwoFactorAuth;
use RobThree\Auth\Providers\Qr\EndroidQrCodeProvider;
use RobThree\Auth\Algorithm;

// Chargement de la clé de chiffrement
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
Crypto::init($_ENV['TOTP_ENCRYPTION_KEY']);

if (!isset($_SESSION['2fa_user_id'])) {
    header('Location: login.php');
    exit;
}

$user = new User($pdo);
$user->loadById($_SESSION['2fa_user_id']);

$qrProvider = new EndroidQrCodeProvider();
$tfa = new TwoFactorAuth($qrProvider, 'BanqueApp', 6, 30, Algorithm::Sha1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'] ?? '';
    if ($tfa->verifyCode($user->getTwoFASecret(), $code)) {
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['username'] = $user->getUsername();
        $_SESSION['role'] = $user->getRoleId();
        unset($_SESSION['2fa_user_id']);
        header('Location: ../dashboard.php');
        exit;
    } else {
        $error = "❌ Code incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vérification 2FA</title>
</head>
<body>
    <h1>Vérification de la double authentification</h1>

    <?php if (!empty($error)) echo "<p>$error</p>"; ?>

    <form method="post">
        <label>Code à 6 chiffres :
            <input type="text" name="code" required>
        </label><br><br>
        <button type="submit">Valider</button>
    </form>
</body>
</html>
