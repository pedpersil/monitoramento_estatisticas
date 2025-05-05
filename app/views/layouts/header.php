<?php
$isLoggedIn = isset($_SESSION[SESSION_NAME]);
$role = $_SESSION[SESSION_NAME]['role'] ?? null;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InsightTrack - Sistema de Monitoramento e Estatísticas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>


    <style>
        .navbar-custom .navbar-toggler {
            border-color: rgba(255, 255, 255, 0.5);
        }

        .navbar-custom .navbar-toggler-icon {
            background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='rgba%28 255, 255, 255, 0.7 %29)' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
        }

        .brand-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #ffffff;
        }
        .brand-subtitle {
            font-size: 0.8rem;
            color: #adb5bd;
        }
        .navbar-custom {
            background-color:rgb(13, 14, 14); /* Cor escura elegante */
        }
        .navbar-custom .nav-link {
            color: #ffffff;
            transition: color 0.3s;
        }
        .navbar-custom .nav-link:hover {
            color: #adb5bd;
        }
        .navbar-custom .dropdown-menu {
            background-color: #343a40;
        }
        .navbar-custom .dropdown-item {
            color: #ffffff;
            transition: background-color 0.3s, color 0.3s;
        }
        .navbar-custom .dropdown-item:hover {
            background-color: #495057;
            color: #ffffff;
        }

        footer a {
            position: relative;
            color: #212529;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        footer a::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -2px;
            width: 0%;
            height: 2px;
            background-color: #dc3545; /* vermelho */
            transition: width 0.3s ease;
        }

        footer a:hover {
            color: #dc3545; /* vermelho */
        }

        footer a:hover::after {
            width: 100%;
        }

    </style>
</head>
<body>
<!-- Loader -->
<div id="pageLoader" style="position: fixed; z-index: 9999; background: #121212; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
    <img src="<?= BASE_URL ?>/images/loading.gif" alt="Carregando..." style="width: 80px;">
</div>

<script>
// Remove o loader após o carregamento da página
window.addEventListener('load', function () {
    const loader = document.getElementById('pageLoader');
    if (loader) {
        loader.style.transition = "opacity 0.5s ease";
        loader.style.opacity = "0";
        setTimeout(() => loader.remove(), 500);
    }
});
</script>

<nav class="navbar navbar-expand-lg navbar-custom shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="<?= BASE_URL ?>/dashboard">
            <img src="<?= BASE_URL ?>/images/insightTrack.png" alt="InsightTrack Logo" style="height: 80px; margin-right: 10px;">
        </a>

        <?php if ($isLoggedIn): ?>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>/dashboard">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="configDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-gear"></i> Config
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="configDropdown">
                            <?php if ($role === 'admin'): ?>    
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/users/create">Novo Usuário</a></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/users/change-password">Mudar Senha</a></li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link text-danger" href="<?= BASE_URL ?>/logout">
                            <i class="bi bi-box-arrow-right"></i> Sair
                        </a>
                    </li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</nav>


<main class="py-4">
