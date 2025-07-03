<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/classes/Beneficiary.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

$beneficiaryManager = new Beneficiary($pdo);
$beneficiaries = $beneficiaryManager->getByUserId($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes bénéficiaires</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 14px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
            color: #333;
        }

        .add-button {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 16px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .add-button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/includes/navbar.php'; ?>
    <div class="container">
        <h1>👥 Mes bénéficiaires</h1>

        <a href="add_beneficiary.php" class="add-button">➕ Ajouter un bénéficiaire</a>

        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>IBAN</th>
                    <th>Banque</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($beneficiaries as $b): ?>
                    <tr>
                        <td><?= htmlspecialchars($b['name']) ?></td>
                        <td><?= $b['iban'] ?></td>
                        <td><?= htmlspecialchars($b['bank_name']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
