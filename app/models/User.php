<?php
require_once __DIR__ . '/../../config/Database.php';

class User {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = (new Database())->connect();
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch() ?: null;
    }

    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function findAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM users");
        return $stmt->fetchAll();
    }

    public function create(array $data): bool {
        $stmt = $this->pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, :role)");
        return $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':password' => $data['password'],
            ':role' => $data['role']
        ]);
    }

    public function update(int $id, array $data): void {
        $stmt = $this->pdo->prepare("UPDATE users SET name = :name, email = :email, role = :role WHERE id = :id");
        $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':role' => $data['role'],
            ':id' => $id
        ]);
    }

    public function updatePassword(int $id, string $hashedPassword): void {
        $stmt = $this->pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
        $stmt->execute([
            ':password' => $hashedPassword,
            ':id' => $id
        ]);
    }

    public function delete(int $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }

    public function changePassword(int $id, string $newPassword): bool {
        $sql = "UPDATE users SET password = :password, created_at = NOW() WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':password' => password_hash($newPassword, PASSWORD_DEFAULT),
            ':id' => $id
        ]);
    }

    public function verifyPassword(int $id, string $password): bool {
        $stmt = $this->pdo->prepare("SELECT password FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user && password_verify($password, $user['password']);
    }

    public function updateVerificationHash(int $id, ?string $hash): void {
        $stmt = $this->pdo->prepare("UPDATE users SET verification_hash = :hash WHERE id = :id");
        $stmt->execute([
            ':hash' => $hash,
            ':id' => $id
        ]);
    }
    
    
    public function verificateHash(int $id, string $sessionHash): bool {
        $stmt = $this->pdo->prepare("SELECT verification_hash FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // Verifica se ambos são strings válidas
        if (!isset($result['verification_hash']) || !is_string($result['verification_hash'])) {
            return false;
        }
    
        return hash_equals($result['verification_hash'], $sessionHash);
    }
    
    
}
