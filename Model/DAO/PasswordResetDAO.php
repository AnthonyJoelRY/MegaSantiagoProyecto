<?php
class PasswordResetDAO {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function create(string $email, string $token, string $expiresAt): bool {
        $stmt = $this->pdo->prepare("
            INSERT INTO password_resets (email, token, expires_at)
            VALUES (?, ?, ?)
        ");
        return $stmt->execute([$email, $token, $expiresAt]);
    }

    public function deleteByEmail(string $email): void {
        $stmt = $this->pdo->prepare("DELETE FROM password_resets WHERE email = ?");
        $stmt->execute([$email]);
    }

    public function cleanupExpired(): void {
        // No dependemos de NOW() del servidor MySQL (puede estar en UTC/SYSTEM).
        // Comparamos contra el "now" calculado en PHP para evitar desfases.
        $now = date("Y-m-d H:i:s");
        $stmt = $this->pdo->prepare("DELETE FROM password_resets WHERE expires_at < ?");
        $stmt->execute([$now]);
    }

    public function findValidByToken(string $token): ?array {
        $now = date("Y-m-d H:i:s");
        $stmt = $this->pdo->prepare("
            SELECT id, email, token, expires_at
            FROM password_resets
            WHERE token = ?
              AND expires_at >= ?
            ORDER BY id DESC
            LIMIT 1
        ");
        $stmt->execute([$token, $now]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function deleteById(int $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM password_resets WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
    }
}
