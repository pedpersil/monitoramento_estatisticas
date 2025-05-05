<?php
require_once __DIR__ . '/../models/Visit.php';

class StatisticsController {
    private Visit $visitModel;

    public function __construct() {
        $this->visitModel = new Visit();
    }

    public function showDashboard(): void {
        $period = $_GET['period'] ?? 'all';
    
        $totalVisits = $this->visitModel->countAllVisits($period);
        $totalPages = $this->visitModel->countUniquePages($period);
        $totalCountries = $this->visitModel->countUniqueCountries($period);
    
        $pagesData = $this->visitModel->getTopPages($period);
        $browsersData = $this->visitModel->getTopBrowsers($period);
        $countriesData = $this->visitModel->getTopCountries($period);
        $citiesData = $this->visitModel->getTopCities($period);
        //$visitorsOnline = $this->visitModel->countVisitorsOnline();

        // Capturar IP do usuário logado (se existir)
        $loggedUserIps = [];
        if (isset($_SESSION[SESSION_NAME])) {
            $loggedUserIps[] = $_SERVER['REMOTE_ADDR'];
        }

        $onlineUsers = $this->visitModel->getOnlineUsers($loggedUserIps);

        $visits = $this->visitModel->getVisitsInfo();
    
        include __DIR__ . '/../views/dashboard.php';
    }

    public function getVisitorsOnline(): void {
        $visitorsOnline = $this->visitModel->countVisitorsOnline();
    
        header('Content-Type: application/json');
        echo json_encode(['visitors_online' => $visitorsOnline]);
    }
    
    public function getPaginatedVisits(): void {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 15;
        $period = $_GET['period'] ?? 'all';
        
        $visits = $this->visitModel->getPaginatedVisits($page, $limit, $period);
        
        // Obter o total de registros para a paginação
        $totalVisits = $this->visitModel->countAllVisits($period);
        $totalPages = ceil($totalVisits / $limit);
    
        header('Content-Type: application/json');
        echo json_encode([
            'visits' => $visits,
            'totalPages' => $totalPages
        ]);
    }

    public function getVisitorLocations(): void {
        $period = $_GET['period'] ?? 'all';
    
        // Consultar as localizações dos visitantes com base no período
        $locations = $this->visitModel->getVisitorLocations($period);
    
        header('Content-Type: application/json');
        echo json_encode(['visits' => $locations]);
    }
    
    
}
