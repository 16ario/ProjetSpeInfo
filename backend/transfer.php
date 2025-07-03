<?php
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/classes/Account.php';
require_once __DIR__ . '/classes/Beneficiary.php';
require_once __DIR__ . '/classes/Transaction.php';
require_once __DIR__ . '/classes/Device.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$accountManager = new Account($pdo);
$beneficiaryManager = new Beneficiary($pdo);
$transactionManager = new Transaction($pdo);
$deviceManager = new Device($pdo);

$accounts = $accountManager->getByUserId($userId);
$beneficiaries = $beneficiaryManager->getByUserId($userId);
$devices = $deviceManager->getAll();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accountId = (int) ($_POST['account_id'] ?? 0);
    $beneficiaryId = (int) ($_POST['beneficiary_id'] ?? 0);
    $deviceId = (int) ($_POST['device_id'] ?? 0);
    $amount = (float) ($_POST['amount'] ?? 0);
    $description = trim($_POST['description'] ?? '');

    if (!$accountManager->belongsToUser($accountId, $userId)) {
        $message = "❌ Compte invalide.";
    } elseif ($amount <= 0) {
        $message = "❌ Montant invalide.";
    } elseif ($accountManager->getBalance($accountId) < $amount) {
        $message = "❌ Solde insuffisant.";
    } else {
        $reference = 'VIR' . strtoupper(uniqid());
        $pdo->beginTransaction();

        try {
            $accountManager->debit($accountId, $amount);

            $transactionManager->create([
                'account_id' => $accountId,
                'type' => 'transfer',
                'amount' => $amount,
                'description' => $description,
                'beneficiary_id' => $beneficiaryId,
                'device_id' => $deviceId,
                'reference' => $reference
            ]);

            $pdo->commit();
            $message = "✅ Virement effectué avec succès. Référence : $reference";
        } catch (Exception $e) {
            $pdo->rollBack();
            $message = "❌ Erreur lors du virement : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Effectuer un virement</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 700px;
            margin: 40px auto;
            background: #fff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-top: 20px;
            font-weight: bold;
            color: #444;
        }

        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
            background-color: #fdfdfd;
        }

        textarea {
            resize: vertical;
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
            transition: background-color 0.3s ease;
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
            animation: fadeIn 0.4s ease-in-out;
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

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .amount-field {
            position: relative;
            margin-top: 8px;
        }

        .amount-field input {
            width: 100%;
            padding: 12px 16px;
            padding-right: 40px;
            font-size: 22px;
            font-weight: 600;
            border: 2px solid #ccc;
            border-radius: 8px;
            background-color: #fff;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .amount-field input:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.15);
            outline: none;
        }

        .amount-field .euro {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
            color: #666;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/includes/navbar.php'; ?>
    <div class="container">
        <h1>💸 Effectuer un virement</h1>

        <?php if (!empty($message)): ?>
            <div class="message <?= str_starts_with($message, '✅') ? 'success' : 'error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <label for="account_id">Compte source :</label>
            <select name="account_id" id="account_id" required>
                <?php foreach ($accounts as $a): ?>
                    <option value="<?= $a['id'] ?>">
                        <?= ucfirst($a['account_type']) ?> – <?= number_format($a['balance'], 2, ',', ' ') ?> €
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="beneficiary_id">Bénéficiaire :</label>
            <select name="beneficiary_id" id="beneficiary_id" required>
                <?php foreach ($beneficiaries as $b): ?>
                    <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['name']) ?> – <?= $b['iban'] ?></option>
                <?php endforeach; ?>
            </select>

            <label for="device_id">Moyen de transaction :</label>
            <select name="device_id" id="device_id" required>
                <?php foreach ($devices as $d): ?>
                    <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['label']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="amount">Montant :</label>
            <div class="amount-field">
                <input type="number" name="amount" id="amount" step="0.01" min="0.01" required placeholder="0.00">
                <span class="euro">€</span>
            </div>

            <label for="description">Description :</label>
            <textarea name="description" id="description" rows="3" placeholder="Ex : Loyer, remboursement, cadeau..."></textarea>

            <button type="submit">💸 Envoyer le virement</button>
        </form>
    </div>
</body>
</html>
