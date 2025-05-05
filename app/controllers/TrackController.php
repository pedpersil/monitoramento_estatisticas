<?php
require_once __DIR__ . '/../models/Visit.php';

class TrackController {
    private Visit $visitModel;

    public function __construct() {
        $this->visitModel = new Visit();
    }

    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') === false) {
            http_response_code(403);
            exit('Acesso proibido.');
        }
        
        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input) {
            http_response_code(400);
            echo json_encode(['error' => 'Dados inválidos']);
            exit;
        }

        $userAgent = $input['user_agent'] ?? '';
        $referrer = $input['referrer'] ?? '';
        $pageUrl = $input['page_url'] ?? '';
        $deviceType = $input['device_type'] ?? '';

        $ip = $this->getIpAddress();
        $browser = $this->detectBrowser($userAgent);
        $os = $this->detectOs($userAgent);
        $location = $this->getLocationByIp($ip);

        $this->visitModel->store([
            'visit_date' => date('Y-m-d H:i:s'),
            'ip_address' => $ip,
            'browser' => $browser,
            'os' => $os,
            'device_type' => $deviceType,
            'country' => $location['country'] ?? 'Desconhecido',
            'state' => $location['regionName'] ?? 'Desconhecido',
            'city' => $location['city'] ?? 'Desconhecido',
            'referrer' => $referrer ?: 'Acesso Direto',
            'page_url' => $pageUrl,
            'latitude' => $location['lat'] ?? null,
            'longitude' => $location['lon'] ?? null
        ]);

        http_response_code(204); // sucesso sem resposta
    }

    private function getIpAddress(): string {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) return $_SERVER['HTTP_CLIENT_IP'];
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    private function detectBrowser(string $userAgent): string {
        if (stripos($userAgent, 'Firefox') !== false) return 'Firefox';
        if (stripos($userAgent, 'Chrome') !== false) return 'Chrome';
        if (stripos($userAgent, 'Safari') !== false) return 'Safari';
        if (stripos($userAgent, 'Opera') !== false || stripos($userAgent, 'OPR') !== false) return 'Opera';
        if (stripos($userAgent, 'MSIE') !== false || stripos($userAgent, 'Trident') !== false) return 'Internet Explorer';
        return 'Desconhecido';
    }

    private function detectOs(string $userAgent): string {
        if (stripos($userAgent, 'Windows') !== false) return 'Windows';
        if (stripos($userAgent, 'Mac') !== false) return 'MacOS';
        if (stripos($userAgent, 'Linux') !== false) return 'Linux';
        if (stripos($userAgent, 'Android') !== false) return 'Android';
        if (stripos($userAgent, 'iPhone') !== false) return 'iOS';
        return 'Desconhecido';
    }

    private function getLocationByIp(string $ip): array {
        $url = "http://ip-api.com/json/{$ip}?fields=status,country,regionName,city,lat,lon";
        $response = @file_get_contents($url);
        if ($response) {
            $data = json_decode($response, true);
            if ($data && $data['status'] === 'success') {
                return $data;
            }
        }
        return [];
    }

    
    
}
