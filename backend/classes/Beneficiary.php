<?php
class Beneficiary {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getByUserId(int $userId): array {
        $stmt = $this->pdo->prepare("SELECT * FROM beneficiaries WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add(int $userId, string $name, string $iban, ?string $bankName): bool {
        $stmt = $this->pdo->prepare("INSERT INTO beneficiaries (user_id, name, iban, bank_name) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$userId, $name, $iban, $bankName]);
    }

    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM beneficiaries WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function existsForUser(int $userId, string $iban): bool {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM beneficiaries WHERE user_id = ? AND iban = ?");
        $stmt->execute([$userId, $iban]);
        return $stmt->fetchColumn() > 0;
    }
    
    public function delete(int $beneficiaryId, int $userId): bool {
        $stmt = $this->pdo->prepare("DELETE FROM beneficiaries WHERE id = ? AND user_id = ?");
        return $stmt->execute([$beneficiaryId, $userId]);
    }
    
}
