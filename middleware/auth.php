<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/models/User.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION[SESSION_NAME])) {
    header('Location: ' . BASE_URL . '/login');
    exit;
}

$userModel = new User();
$isValid = $userModel->verificateHash(
    $_SESSION[SESSION_NAME]['id'],
    $_SESSION[SESSION_NAME]['verification_hash'] ?? ''
);

if (!$isValid) {
    session_destroy();
    header('Location: ' . BASE_URL . '/login');
    exit;
}
