<?php
require_once __DIR__ . '/../includes/admin_only.php';

$stmt = $pdo->query("SELECT * FROM users ORDER BY id DESC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des utilisateurs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        h1 {
            margin-bottom: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .actions form {
            display: inline;
            margin-right: 5px;
        }

        .actions button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 4px;
        }

        .actions button:hover {
            background-color: #0056b3;
        }

        .promote {
            background-color: #28a745;
        }

        .promote:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../includes/navbar.php'; ?>
    <h1>👑 Interface administrateur – Utilisateurs</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>2FA</th>
                <th>Verrouillé</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['username']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= $u['role_id'] == 2 ? 'Admin' : 'User' ?></td>
                    <td><?= $u['is_2fa_enabled'] ? '✅' : '❌' ?></td>
                    <td>
                        <?= $u['locked_until'] && strtotime($u['locked_until']) > time()
                            ? '⛔ Jusqu’à ' . date('H:i', strtotime($u['locked_until']))
                            : '—' ?>
                    </td>
                    <td class="actions">
                        <?php if ($u['locked_until'] && strtotime($u['locked_until']) > time()): ?>
                            <form method="post" action="unlock_user.php">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <button type="submit">🔓 Débloquer</button>
                            </form>
                        <?php endif; ?>

                        <?php if ($u['id'] !== $_SESSION['user_id']): ?>
                            <form method="post" action="toggle_role.php">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <input type="hidden" name="current_role" value="<?= $u['role_id'] ?>">
                                <button type="submit" class="promote">
                                    <?= $u['role_id'] == 2 ? '⬇️ Rétrograder' : '⬆️ Promouvoir' ?>
                                </button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
