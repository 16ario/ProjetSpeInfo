<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<style>
    nav {
        background-color: #1e1e2f;
        color: #fff;
        padding: 12px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-family: Arial, sans-serif;
    }

    .nav-left a,
    .nav-right a,
    .nav-right form button {
        color: #fff;
        text-decoration: none;
        margin-right: 15px;
        font-weight: bold;
    }

    .nav-left a:hover,
    .nav-right a:hover {
        text-decoration: underline;
    }

    .nav-right {
        display: flex;
        align-items: center;
    }

    .nav-right form {
        margin: 0;
    }

    .nav-right form button {
        background: none;
        border: none;
        cursor: pointer;
        font-weight: bold;
        color: #ff6b6b;
    }

    .nav-user {
        margin-left: 15px;
        font-style: italic;
        color: #ccc;
    }
</style>

<nav>
    <div class="nav-left">
        <a href="/dashboard.php">🏠 Accueil</a>
        <a href="/accounts.php">🏦 Comptes</a>
        <a href="/beneficiaries.php">👥 Bénéficiaires</a>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 2): ?>
            <a href="/admin/users.php">👑 Admin</a>
        <?php endif; ?>
    </div>

    <div class="nav-right">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="/transfer.php">💸 Virement</a>
            <a href="/profile.php">👤 Mon profil</a>
            <form action="/auth/logout.php" method="post">
                <button type="submit">🚪 Déconnexion</button>
            </form>
            <span class="nav-user">Connecté en tant que <?= htmlspecialchars($_SESSION['username']) ?></span>
        <?php else: ?>
            <a href="/auth/login.php">🔑 Connexion</a>
            <a href="/auth/register.php">📝 Inscription</a>
        <?php endif; ?>
    </div>
</nav>
