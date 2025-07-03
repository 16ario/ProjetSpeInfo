<?php
session_start();

// Si l'utilisateur est déjà connecté, on le redirige vers le tableau de bord
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Sinon, on le redirige vers la page de connexion
header('Location: auth/login.php');
exit;
