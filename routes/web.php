<?php
// Definindo fuso horário padrão
date_default_timezone_set('America/Sao_Paulo');

require_once __DIR__ . '/../config/Database.php';

// Controllers
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/StatisticsController.php';
require_once __DIR__ . '/../app/controllers/TrackController.php';
require_once __DIR__ . '/../app/controllers/UserController.php';

// Conexão com o banco de dados
$db = (new Database())->connect();

// Inicializar Controllers
$authController = new AuthController();
$statisticsController = new StatisticsController();
$trackController = new TrackController();
$userController = new UserController();

// URI e método
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$basePath = BASE_PATH; // ajuste conforme seu ambiente
$uri = str_replace($basePath, '', $uri);
$method = $_SERVER['REQUEST_METHOD'];

// Página inicial / dashboard (rota protegida)
if ($uri === '/') {
    require __DIR__ . '/../middleware/auth.php';
    $statisticsController->showDashboard();
}

// Dashboard de Estatísticas
elseif ($uri === '/dashboard') {
    require __DIR__ . '/../middleware/auth.php';
    $statisticsController->showDashboard();
}

// Autenticação
elseif ($uri === '/login' && $method === 'GET') {
    $authController->showLogin();
} elseif ($uri === '/login' && $method === 'POST') {
    $authController->login();
} elseif ($uri === '/logout') {
    $authController->logout();
}

// Usuários
elseif ($uri == '/users/create') {
    require __DIR__ . '/../middleware/auth.php';
    if ($_SESSION[SESSION_NAME]['role'] != 'admin') {
        header("Location: " . BASE_URL . "/");
        exit;
    }
    $userController->createForm();
} elseif ($uri == '/users/store' && $method == 'POST') {
    require __DIR__ . '/../middleware/auth.php';
    if ($_SESSION[SESSION_NAME]['role'] != 'admin') {
        header("Location: " . BASE_URL . "/");
        exit;
    }
    $userController->store();
} elseif ($uri == '/users/change-password' && $method == 'GET') {
    require __DIR__ . '/../middleware/auth.php';
    $userController->changePasswordForm();
} elseif ($uri == '/users/change-password' && $method == 'POST') {
    require __DIR__ . '/../middleware/auth.php';
    $userController->changePassword();
}


// Visitas (Monitoramento)
elseif ($uri === '/track' && $method === 'POST') {
    $trackController->store();
}

// Visitantes online
elseif ($uri === '/visitors-online' && $method === 'GET') {
    require __DIR__ . '/../middleware/auth.php';
    $statisticsController->getVisitorsOnline();
}

// Rota para carregar as visitas paginadas
elseif ($uri === '/statistics/getPaginatedVisits' && $method === 'GET') {
    require __DIR__ . '/../middleware/auth.php';
    $statisticsController->getPaginatedVisits();
}

// Rota para obter as localizações dos visitantes
elseif ($uri === '/statistics/getVisitorLocations' && $method === 'GET') {
    require __DIR__ . '/../middleware/auth.php';
    $statisticsController->getVisitorLocations();
}


// Página de erro 404
else {
    http_response_code(404);
    echo "Página não encontrada.";
}
