<?php
session_start();
require_once __DIR__ . '/includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['account_type'] ?? '';
    $validTypes = ['courant', 'epargne'];

    if (!in_array($type, $validTypes)) {
        $message = "❌ Type de compte invalide.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO accounts (user_id, account_type, balance) VALUES (?, ?, 0.00)");
        $success = $stmt->execute([$_SESSION['user_id'], $type]);
        if ($success) {
            header('Location: accounts.php?created=1');
            exit;
        } else {
            $message = "❌ Une erreur est survenue lors de la création du compte.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un compte</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 500px;
            margin: 40px auto;
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-top: 20px;
            font-weight: bold;
            color: #444;
        }

        select {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        button {
            margin-top: 30px;
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .message {
            margin-top: 20px;
            padding: 12px;
            border-radius: 6px;
            font-weight: bold;
            text-align: center;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #007bff;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/includes/navbar.php'; ?>
    <div class="container">
        <h1>➕ Ajouter un compte</h1>

        <?php if (!empty($message)): ?>
            <div class="message <?= str_starts_with($message, '✅') ? 'success' : 'error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <label for="account_type">Type de compte :</label>
            <select name="account_type" id="account_type" required>
                <option value="">-- Choisir --</option>
                <option value="courant">Compte courant</option>
                <option value="epargne">Compte épargne</option>
            </select>

            <button type="submit">Créer le compte</button>
        </form>

        <a href="accounts.php" class="back-link">← Retour à mes comptes</a>
    </div>
</body>
</html>
