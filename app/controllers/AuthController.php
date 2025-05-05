<?php
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function showLogin(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
        if (!isset($_SESSION[SESSION_NAME]) && isset($_COOKIE[SESSION_NAME . '_remember'])) {
            $cookieData = json_decode($_COOKIE[SESSION_NAME . '_remember'], true);
            $user = $this->userModel->findById($cookieData['id'] ?? 0);
    
            if (
                $user &&
                isset($user['verification_hash'], $cookieData['hash']) &&
                is_string($user['verification_hash']) &&
                hash_equals($user['verification_hash'], $cookieData['hash'])
            ) {
                // login automático por cookie
                $_SESSION[SESSION_NAME] = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                    'verification_hash' => $user['verification_hash']
                ];
                header('Location: ' . BASE_URL . '/dashboard');
                exit;
            }
            
        }
    
        if (isset($_SESSION[SESSION_NAME])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
    
        $error = $_SESSION['login_error'] ?? null;
        unset($_SESSION['login_error']);
    
        include __DIR__ . '/../views/auth/login.php';
    }
    

    public function login(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
    
        if (empty($email) || empty($password)) {
            $_SESSION['login_error'] = 'Por favor, preencha todos os campos.';
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    
        $user = $this->userModel->findByEmail($email);
    
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
    
            $hash = bin2hex(random_bytes(32));
            $this->userModel->updateVerificationHash($user['id'], $hash);
    
            $_SESSION[SESSION_NAME] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role'],
                'verification_hash' => $hash
            ];
    
            // Se "lembrar-me" estiver marcado, criar cookie por 24h
            if ($remember) {
                setcookie(SESSION_NAME . '_remember', json_encode([
                    'id' => $user['id'],
                    'hash' => $hash
                ]), time() + 86400, "/");
            }
    
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        } else {
            $_SESSION['login_error'] = 'Email ou senha inválidos.';
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }
    

    public function logout(): void {
        if (isset($_SESSION[SESSION_NAME]['id'])) {
            $this->userModel->updateVerificationHash($_SESSION[SESSION_NAME]['id'], null);
        }
    
        // Remover cookie de lembrar
        setcookie(SESSION_NAME . '_remember', '', time() - 3600, "/");
    
        unset($_SESSION[SESSION_NAME]);
        session_destroy();
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
    
    
}
