<?php
require_once __DIR__ . '/../../config/Database.php';

class Statistics {
    private PDO $pdo;

    public function __construct() {
        $database = new Database();
        $this->pdo = $database->connect();  // Usando a conexão da classe Database
    }

    public function storeVisit(array $data): void {
        $sql = "INSERT INTO visits (visit_date, ip_address, browser, os, device_type, country, state, city, referrer) 
                VALUES (:visit_date, :ip_address, :browser, :os, :device_type, :country, :state, :city, :referrer)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':visit_date' => date('Y-m-d H:i:s'),
            ':ip_address' => $data['ip_address'],
            ':browser' => $data['browser'],
            ':os' => $data['os'],
            ':device_type' => $data['device_type'],
            ':country' => $data['country'],
            ':state' => $data['state'],
            ':city' => $data['city'],
            ':referrer' => $data['referrer']
        ]);
    }
    

    public function getVisitStats(): array {
        $sql = "SELECT COUNT(*) AS total_visits, 
                       SUM(CASE WHEN DATE(visit_date) = CURDATE() THEN 1 ELSE 0 END) AS today_visits
                FROM visits";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
