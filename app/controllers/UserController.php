<?php
require_once __DIR__ . '/../models/User.php';

class UserController {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index(): void {
        $users = $this->userModel->findAll();
        include __DIR__ . '/../views/users/index.php';
    }

    public function createForm(): void {

        $error = $_SESSION['login_error'] ?? null;
        unset($_SESSION['login_error']);

        $success = $_SESSION['login_success'] ?? null;
        unset($_SESSION['login_success']);

        include __DIR__ . '/../views/users/create.php';
    }

    public function store(): void {
      
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'user';

        if (empty($name) || empty($email) || empty($password) || empty($role)) {
            $_SESSION['login_error'] = 'Por favor, preencha todos os campos.';
            header('Location: ' . BASE_URL . '/users/create');
            exit;
        }

        if ($this->userModel->findByEmail($email)) {
            $_SESSION['login_error'] = 'Email já cadastrado. Tente novamente.';
            header('Location: ' . BASE_URL . '/users/create');
            exit;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        if ($this->userModel->create([
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'role' => $role
        ])) {
            $_SESSION['login_success'] = 'Usuário criado com sucesso!';
            header('Location: ' . BASE_URL . '/users/create');
            exit;
        } else {
            $_SESSION['login_error'] = 'Erro ao criar o usuário. Tente novamente.';
            header('Location: ' . BASE_URL . '/users/create');
            exit;
        }

    }

    public function changePasswordForm(): void {
        $error = $_SESSION['change_password_error'] ?? null;
        unset($_SESSION['change_password_error']);

        $success = $_SESSION['change_password_success'] ?? null;
        unset($_SESSION['change_password_success']);

        include __DIR__ . '/../views/users/change_password.php';
    }

    public function changePassword(): void {
        $id = $_SESSION[SESSION_NAME]['id'];
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $_SESSION['change_password_error'] = 'Por favor, preencha todos os campos.';
            header('Location: ' . BASE_URL . '/users/change-password');
            exit;
        }

        if ($newPassword != $confirmPassword) {
            $_SESSION['change_password_error'] = 'Nova senha e confirmação de nova senha não coincidem.';
            header('Location: ' . BASE_URL . '/users/change-password');
            exit;
        }

        if ($this->userModel->verifyPassword($id, $currentPassword)) {
            $this->userModel->changePassword($id, $newPassword);
            $_SESSION['change_password_success'] = 'Senha alterada com sucesso!';
            header('Location: ' . BASE_URL . '/users/change-password');
            exit;
        } else {
            $_SESSION['change_password_error'] = "Senha atual incorreta.";
            header('Location: ' . BASE_URL . '/users/change-password');
            exit;
        }
    }

    
}
