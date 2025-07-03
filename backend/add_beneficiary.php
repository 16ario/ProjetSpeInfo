<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/classes/Beneficiary.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

$beneficiaryManager = new Beneficiary($pdo);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $iban = strtoupper(trim($_POST['iban'] ?? ''));
    $bank = trim($_POST['bank_name'] ?? '');

    if (empty($name) || empty($iban)) {
        $message = "❌ Nom et IBAN sont obligatoires.";
    } elseif (!preg_match('/^FR\d{12,32}$/', $iban)) {
        $message = "❌ IBAN invalide (doit commencer par FR).";
    } elseif ($beneficiaryManager->existsForUser($_SESSION['user_id'], $iban)) {
        $message = "❌ Ce bénéficiaire existe déjà.";
    } else {
        $success = $beneficiaryManager->add($_SESSION['user_id'], $name, $iban, $bank);
        $message = $success ? "✅ Bénéficiaire ajouté avec succès." : "❌ Une erreur est survenue.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un bénéficiaire</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
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

        input {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            background-color: #fdfdfd;
        }

        button {
            margin-top: 30px;
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #218838;
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
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/includes/navbar.php'; ?>
    <div class="container">
        <h1>➕ Ajouter un bénéficiaire</h1>

        <?php if (!empty($message)): ?>
            <div class="message <?= str_starts_with($message, '✅') ? 'success' : 'error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <label for="name">Nom complet :</label>
            <input type="text" name="name" id="name" required>

            <label for="iban">IBAN :</label>
            <input type="text" name="iban" id="iban" required placeholder="FR...">

            <label for="bank_name">Nom de la banque (facultatif) :</label>
            <input type="text" name="bank_name" id="bank_name">

            <button type="submit">Ajouter</button>
        </form>
    </div>
</body>
</html>
