<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/classes/Account.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

$accountManager = new Account($pdo);
$accounts = $accountManager->getByUserId($_SESSION['user_id']);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_account'])) {
    $accountId = (int) $_POST['delete_account'];
    if ($accountManager->belongsToUser($accountId, $_SESSION['user_id'])) {
        $stmt = $pdo->prepare("DELETE FROM accounts WHERE id = ?");
        $stmt->execute([$accountId]);
        $message = "✅ Compte supprimé.";
        $accounts = $accountManager->getByUserId($_SESSION['user_id']); // refresh
    } else {
        $message = "❌ Action non autorisée.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes comptes</title>
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
            margin-bottom: 30px;
        }

        .actions {
            text-align: right;
            margin-bottom: 20px;
        }

        .actions a {
            background-color: #28a745;
            color: white;
            padding: 10px 16px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
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

        .balance {
            font-weight: bold;
            color: #007bff;
        }

        .btn {
            padding: 6px 12px;
            font-size: 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
        }

        .btn-view {
            background-color: #007bff;
            color: white;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
        }

        .message {
            margin-bottom: 20px;
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
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/includes/navbar.php'; ?>
    <div class="container">
        <h1>🏦 Mes comptes bancaires</h1>

        <?php if (!empty($message)): ?>
            <div class="message <?= str_starts_with($message, '✅') ? 'success' : 'error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="actions">
            <a href="add_account.php">➕ Ajouter un compte</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Solde</th>
                    <th>Créé le</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($accounts as $account): ?>
                    <tr>
                        <td><?= ucfirst($account['account_type']) ?></td>
                        <td class="balance"><?= number_format($account['balance'], 2, ',', ' ') ?> €</td>
                        <td><?= date('d/m/Y', strtotime($account['created_at'])) ?></td>
                        <td>
                            <a class="btn btn-view" href="transactions.php?account_id=<?= $account['id'] ?>">📄 Transactions</a>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="delete_account" value="<?= $account['id'] ?>">
                                <button type="submit" class="btn btn-delete" onclick="return confirm('Supprimer ce compte ?')">🗑️ Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
