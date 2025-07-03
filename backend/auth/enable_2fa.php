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

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user = new User($pdo);
$user->loadById($_SESSION['user_id']);

// Empêche d'activer la 2FA si elle est déjà activée
if ($user->is2FAEnabled()) {
    header('Location: ../dashboard.php');
    exit;
}

$qrProvider = new EndroidQrCodeProvider();
$tfa = new TwoFactorAuth($qrProvider, 'BanqueApp', 6, 30, Algorithm::Sha1);

// Génère une clé une seule fois et la stocke en session
if (!isset($_SESSION['pending_2fa_secret'])) {
    $_SESSION['pending_2fa_secret'] = $tfa->createSecret();
}

$secret = $_SESSION['pending_2fa_secret'];
$qrCode = $tfa->getQRCodeImageAsDataUri($user->getEmail(), $secret);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'] ?? '';
    if ($tfa->verifyCode($secret, $code)) {
        $user->enable2FA($secret);
        unset($_SESSION['pending_2fa_secret']);
        header('Location: ../dashboard.php');
        exit;
    } else {
        $error = "❌ Code incorrect. Veuillez réessayer.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Activer la 2FA</title>
</head>
<body>
    <h1>Activer la double authentification pour <?= htmlspecialchars($user->getUsername()) ?></h1>

    <?php if (!empty($error)) echo "<p>$error</p>"; ?>

    <p>Scanne ce QR code avec ton application d’authentification :</p>
    <img src="<?= $qrCode ?>" alt="QR Code"><br><br>

    <form method="post">
        <label>Code à 6 chiffres :
            <input type="text" name="code" required>
        </label><br><br>
        <button type="submit">Activer la 2FA</button>
    </form>
</body>
</html>
