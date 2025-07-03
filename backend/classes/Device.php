<?php
class Device {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM devices ORDER BY label ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM devices WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getLabel(int $id): ?string {
        $stmt = $this->pdo->prepare("SELECT label FROM devices WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() ?: null;
    }
    
}
