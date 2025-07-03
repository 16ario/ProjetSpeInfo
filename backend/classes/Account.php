<?php
class Account {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getByUserId(int $userId): array {
        $stmt = $this->pdo->prepare("SELECT * FROM accounts WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $accountId): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM accounts WHERE id = ?");
        $stmt->execute([$accountId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function updateBalance(int $accountId, float $amount): bool {
        $stmt = $this->pdo->prepare("UPDATE accounts SET balance = balance + ? WHERE id = ?");
        return $stmt->execute([$amount, $accountId]);
    }

    public function debit(int $accountId, float $amount): bool {
        return $this->updateBalance($accountId, -abs($amount));
    }
    
    public function credit(int $accountId, float $amount): bool {
        return $this->updateBalance($accountId, abs($amount));
    }
    
    public function getBalance(int $accountId): ?float {
        $stmt = $this->pdo->prepare("SELECT balance FROM accounts WHERE id = ?");
        $stmt->execute([$accountId]);
        return $stmt->fetchColumn();
    }
    
    public function belongsToUser(int $accountId, int $userId): bool {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM accounts WHERE id = ? AND user_id = ?");
        $stmt->execute([$accountId, $userId]);
        return $stmt->fetchColumn() > 0;
    }
    
}
