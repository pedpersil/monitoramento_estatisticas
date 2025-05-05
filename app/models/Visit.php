<?php
require_once __DIR__ . '/../../config/Database.php';

class Visit {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = (new Database())->connect();
    }

    public function store(array $data): void {
        $stmt = $this->pdo->prepare("
            INSERT INTO visits (visit_date, ip_address, browser, os, device_type, country, state, city, referrer, page_url, latitude, longitude)
            VALUES (:visit_date, :ip_address, :browser, :os, :device_type, :country, :state, :city, :referrer, :page_url, :latitude, :longitude)
        ");

        $stmt->execute([
            ':visit_date' => $data['visit_date'],
            ':ip_address' => $data['ip_address'],
            ':browser' => $data['browser'],
            ':os' => $data['os'],
            ':device_type' => $data['device_type'],
            ':country' => $data['country'],
            ':state' => $data['state'],
            ':city' => $data['city'],
            ':referrer' => $data['referrer'],
            ':page_url' => $data['page_url'],
            ':latitude' => $data['latitude'],
            ':longitude' => $data['longitude']
        ]);
    }

    public function countAllVisits(string $period = 'all'): int {
        $where = $this->buildPeriodCondition($period);
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM visits $where");
        return (int) $stmt->fetchColumn();
    }
    
    public function countUniquePages(string $period = 'all'): int {
        $where = $this->buildPeriodCondition($period);
        $stmt = $this->pdo->query("SELECT COUNT(DISTINCT page_url) FROM visits $where");
        return (int) $stmt->fetchColumn();
    }
    
    public function countUniqueCountries(string $period = 'all'): int {
        $where = $this->buildPeriodCondition($period);
        $stmt = $this->pdo->query("SELECT COUNT(DISTINCT country) FROM visits $where");
        return (int) $stmt->fetchColumn();
    }
    
    public function getTopPages(string $period = 'all'): array {
        $where = $this->buildPeriodCondition($period);
        $stmt = $this->pdo->query("
            SELECT page_url, COUNT(*) as total
            FROM visits
            $where
            GROUP BY page_url
            ORDER BY total DESC
            LIMIT 5
        ");
        $results = $stmt->fetchAll();
        return $this->formatChartData($results, 'page_url');
    }
    
    public function getTopBrowsers(string $period = 'all'): array {
        $where = $this->buildPeriodCondition($period);
        $stmt = $this->pdo->query("
            SELECT browser, COUNT(*) as total
            FROM visits
            $where
            GROUP BY browser
            ORDER BY total DESC
            LIMIT 5
        ");
        $results = $stmt->fetchAll();
        return $this->formatChartData($results, 'browser');
    }

    public function getTopCountries(string $period = 'all'): array {
        $where = $this->buildPeriodCondition($period);
        $stmt = $this->pdo->query("
            SELECT country, COUNT(*) as total
            FROM visits
            $where
            GROUP BY country
            ORDER BY total DESC
            LIMIT 5
        ");
        $results = $stmt->fetchAll();
        return $this->formatChartData($results, 'country');
    }
    
    public function getTopCities(string $period = 'all'): array {
        $where = $this->buildPeriodCondition($period);
        $stmt = $this->pdo->query("
            SELECT city, COUNT(*) as total
            FROM visits
            $where
            GROUP BY city
            ORDER BY total DESC
            LIMIT 5
        ");
        $results = $stmt->fetchAll();
        return $this->formatChartData($results, 'city');
    }
    
    private function formatChartData(array $results, string $field): array {
        $labels = [];
        $data = [];
    
        foreach ($results as $row) {
            $labels[] = $row[$field] ?: 'Desconhecido';
            $data[] = $row['total'];
        }
    
        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function buildPeriodCondition(string $period): string {
        switch ($period) {
            case '1h':
                return "WHERE visit_date >= NOW() - INTERVAL 1 HOUR";
            case '6h':
                return "WHERE visit_date >= NOW() - INTERVAL 6 HOUR";
            case '24h':
                return "WHERE visit_date >= NOW() - INTERVAL 1 DAY";
            case '7d':
                return "WHERE visit_date >= NOW() - INTERVAL 7 DAY";
            case 'month':
                return "WHERE MONTH(visit_date) = MONTH(NOW()) AND YEAR(visit_date) = YEAR(NOW())";
            default:
                return "";
        }
    }
    
    public function countVisitorsOnline(): int {
        $stmt = $this->pdo->query("
            SELECT COUNT(*) FROM visits
            WHERE visit_date >= NOW() - INTERVAL 5 MINUTE
        ");
        return (int) $stmt->fetchColumn();
    }    

    public function getVisitsInfo(): array {
        $stmt = $this->pdo->query('
            SELECT 
                id,
                visit_date,
                ip_address,
                os,
                device_type,
                state,
                referrer,
                page_url,
                latitude,
                longitude
            FROM visits
            ORDER BY visit_date DESC
        ');

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOnlineUsers(array $loggedUserIps = []): int {
        $sql = '
            SELECT COUNT(DISTINCT ip_address) as total
            FROM visits
            WHERE visit_date >= (NOW() - INTERVAL 5 MINUTE)
        ';

        if (!empty($loggedUserIps)) {
            // Ignorar IPs dos usuários logados
            $placeholders = rtrim(str_repeat('?,', count($loggedUserIps)), ',');
            $sql .= " AND ip_address NOT IN ($placeholders)";
        }

        $stmt = $this->pdo->prepare($sql);

        if (!empty($loggedUserIps)) {
            $stmt->execute($loggedUserIps);
        } else {
            $stmt->execute();
        }

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) ($result['total'] ?? 0);
    }

    public function getPaginatedVisits(int $page, int $limit, string $period = 'all'): array {
        $offset = ($page - 1) * $limit;
        $where = $this->buildPeriodCondition($period);
        $stmt = $this->pdo->prepare("
            SELECT id, visit_date, ip_address, os, device_type, state, referrer, page_url
            FROM visits
            $where
            ORDER BY visit_date DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getVisitorLocations(string $period = 'all'): array {
        $where = $this->buildPeriodCondition($period);
        
        // Buscar as localizações dos visitantes
        $stmt = $this->pdo->query("
            SELECT DISTINCT ip_address, country, state, city, latitude, longitude
            FROM visits
            $where
        ");
        $results = $stmt->fetchAll();
    
        return $results;
    }    

}
