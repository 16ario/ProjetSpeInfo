<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../classes/User.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /auth/login.php');
    exit;
}

$user = new User($pdo);
$user->loadById($_SESSION['user_id']);

if (!$user->isAdmin()) {
    http_response_code(403);
    echo "⛔ Accès réservé aux administrateurs.";
    exit;
}
