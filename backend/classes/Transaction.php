<?php
class Transaction {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function create(array $data): bool {
        $stmt = $this->pdo->prepare("
            INSERT INTO transactions (account_id, type, amount, description, beneficiary_id, device_id, reference)
            VALUES (:account_id, :type, :amount, :description, :beneficiary_id, :device_id, :reference)
        ");
        return $stmt->execute([
            'account_id' => $data['account_id'],
            'type' => $data['type'],
            'amount' => $data['amount'],
            'description' => $data['description'],
            'beneficiary_id' => $data['beneficiary_id'] ?? null,
            'device_id' => $data['device_id'] ?? null,
            'reference' => $data['reference'] ?? null
        ]);
    }

    public function getByAccountId(int $accountId): array {
        $stmt = $this->pdo->prepare("SELECT * FROM transactions WHERE account_id = ? ORDER BY created_at DESC");
        $stmt->execute([$accountId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecentByUser(int $userId, int $limit = 10): array {
        $stmt = $this->pdo->prepare("
            SELECT t.*, b.name AS beneficiary_name, a.account_type
            FROM transactions t
            JOIN accounts a ON t.account_id = a.id
            LEFT JOIN beneficiaries b ON t.beneficiary_id = b.id
            WHERE a.user_id = ?
            ORDER BY t.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getByReference(string $ref): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM transactions WHERE reference = ?");
        $stmt->execute([$ref]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    
}
