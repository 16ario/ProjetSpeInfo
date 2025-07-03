<?php
class User {
    private PDO $pdo;

    private ?int $id = null;
    private ?string $username = null;
    private ?string $email = null;
    private ?int $role_id = null;
    private ?bool $is_2fa_enabled = null;
    private ?string $twofa_secret = null;
    private ?string $created_at = null;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function create(string $username, string $email, string $password, int $role_id = 1): bool {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("
            INSERT INTO users (username, email, password, role_id, is_2fa_enabled, twofa_secret)
            VALUES (?, ?, ?, ?, 0, NULL)
        ");
        $success = $stmt->execute([$username, $email, $hashedPassword, $role_id]);

        if ($success) {
            $this->loadById($this->pdo->lastInsertId());
        }

        return $success;
    }

    public function loadById(int $id): bool {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? $this->hydrate($data) : false;
    }

    public function loadByEmail(string $email): bool {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? $this->hydrate($data) : false;
    }

    public function verifyPassword(string $password): bool {
        $stmt = $this->pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$this->id]);
        $hash = $stmt->fetchColumn();
        return $hash && password_verify($password, $hash);
    }

    public function update(array $fields): bool {
        if (!$this->id) return false;

        $updates = [];
        $values = [];

        foreach ($fields as $key => $value) {
            $updates[] = "$key = ?";
            $values[] = $value;
        }

        $values[] = $this->id;

        $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $success = $stmt->execute($values);

        if ($success) {
            $this->loadById($this->id);
        }

        return $success;
    }

    public function delete(): bool {
        if (!$this->id) return false;
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$this->id]);
    }

    public function enable2FA(string $secret): void {
        $encrypted = Crypto::encrypt($secret);
        $stmt = $this->pdo->prepare("UPDATE users SET twofa_secret = ?, is_2fa_enabled = 1 WHERE id = ?");
        $stmt->execute([$encrypted, $this->id]);
        $this->loadById($this->id);
    }

    public function disable2FA(): bool {
        return $this->update([
            'is_2fa_enabled' => 0,
            'twofa_secret' => null
        ]);
    }

    public function getRoleName(): ?string {
        $stmt = $this->pdo->prepare("SELECT name FROM roles WHERE id = ?");
        $stmt->execute([$this->role_id]);
        return $stmt->fetchColumn() ?: null;
    }

    public function isAdmin(): bool {
        return $this->role_id === 2;
    }

    private function hydrate(array $data): bool {
        $this->id = (int) $data['id'];
        $this->username = $data['username'];
        $this->email = $data['email'];
        $this->role_id = (int) $data['role_id'];
        $this->is_2fa_enabled = (bool) $data['is_2fa_enabled'];
        $this->twofa_secret = $data['twofa_secret'];
        $this->created_at = $data['created_at'];
        return true;
    }

    public function getId(): ?int { return $this->id; }
    public function getUsername(): ?string { return $this->username; }
    public function getEmail(): ?string { return $this->email; }
    public function getRoleId(): ?int { return $this->role_id; }
    public function is2FAEnabled(): bool { return $this->is_2fa_enabled; }
    public function getCreatedAt(): ?string { return $this->created_at; }

    public function getTwoFASecret(): ?string {
        if (!$this->twofa_secret) return null;
        return Crypto::decrypt($this->twofa_secret);
    }
}
