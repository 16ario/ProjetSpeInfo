<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/classes/Account.php';
require_once __DIR__ . '/classes/Transaction.php';
require_once __DIR__ . '/classes/Beneficiary.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

$accountId = (int) ($_GET['account_id'] ?? 0);
$accountManager = new Account($pdo);
$transactionManager = new Transaction($pdo);
$beneficiaryManager = new Beneficiary($pdo);

if (!$accountManager->belongsToUser($accountId, $_SESSION['user_id'])) {
    die("❌ Accès non autorisé.");
}

$account = $accountManager->getById($accountId);
$transactions = $transactionManager->getByAccountId($accountId);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Transactions du compte</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 900px;
            margin: 40px auto;
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 14px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        .amount {
            font-weight: bold;
        }

        .amount.positive {
            color: #28a745;
        }

        .amount.negative {
            color: #dc3545;
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
        <h1>📄 Transactions</h1>
        <div class="subtitle">
            Compte <?= ucfirst($account['account_type']) ?> – Solde : <strong><?= number_format($account['balance'], 2, ',', ' ') ?> €</strong>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Montant</th>
                    <th>Bénéficiaire</th>
                    <th>Description</th>
                    <th>Référence</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $t): ?>
                    <tr>
                        <td><?= date('d/m/Y H:i', strtotime($t['created_at'])) ?></td>
                        <td><?= ucfirst($t['type']) ?></td>
                        <td class="amount <?= in_array($t['type'], ['deposit', 'refund', 'interest']) ? 'positive' : 'negative' ?>">
                            <?= number_format($t['amount'], 2, ',', ' ') ?> €
                        </td>
                        <td>
                            <?php
                            if ($t['beneficiary_id']) {
                                $b = $beneficiaryManager->getById($t['beneficiary_id']);
                                echo htmlspecialchars($b['name'] ?? '—');
                            } else {
                                echo '—';
                            }
                            ?>
                        </td>
                        <td><?= htmlspecialchars($t['description'] ?? '—') ?></td>
                        <td><?= htmlspecialchars($t['reference'] ?? '—') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="accounts.php" class="back-link">← Retour à mes comptes</a>
    </div>
</body>
</html>
