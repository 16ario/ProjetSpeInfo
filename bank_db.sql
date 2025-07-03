-- Création de la base
CREATE DATABASE IF NOT EXISTS banque_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE banque_app;
-- Table des rôles
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
);
-- Table des utilisateurs
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role_id INT NOT NULL,
    is_2fa_enabled BOOLEAN NOT NULL DEFAULT FALSE,
    twofa_secret VARCHAR(255) DEFAULT NULL,
    failed_attempts INT DEFAULT 0,
    last_failed_login DATETIME DEFAULT NULL,
    locked_until DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);
-- Table reset password par mail
CREATE TABLE password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Table des comptes bancaires
CREATE TABLE IF NOT EXISTS accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    account_type ENUM('courant', 'epargne') NOT NULL,
    balance DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
-- Table des bénéficiaires
CREATE TABLE IF NOT EXISTS beneficiaries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    iban VARCHAR(34) NOT NULL,
    bank_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
-- Table des moyens de transaction
CREATE TABLE IF NOT EXISTS devices (
    id INT AUTO_INCREMENT PRIMARY KEY,
    label VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Table des transactions enrichie
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    account_id INT NOT NULL,
    type ENUM(
        'deposit',
        'withdrawal',
        'transfer',
        'payment',
        'fee',
        'refund',
        'interest',
        'chargeback',
        'loan_disbursement',
        'loan_repayment'
    ) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL CHECK (amount > 0),
    description TEXT,
    beneficiary_id INT DEFAULT NULL,
    device_id INT DEFAULT NULL,
    reference VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE CASCADE,
    FOREIGN KEY (beneficiary_id) REFERENCES beneficiaries(id) ON DELETE
    SET NULL,
        FOREIGN KEY (device_id) REFERENCES devices(id) ON DELETE
    SET NULL
);
-- Index pour les performances
CREATE INDEX idx_user_id ON accounts(user_id);
CREATE INDEX idx_account_id ON transactions(account_id);
CREATE INDEX idx_beneficiary_id ON transactions(beneficiary_id);
CREATE INDEX idx_device_id ON transactions(device_id);
-- Insertion des rôles
INSERT INTO roles (name)
VALUES ('user'),
    ('admin');
-- Insertion d'utilisateurs
INSERT INTO users (
        username,
        password,
        email,
        role_id,
        is_2fa_enabled,
        twofa_secret
    )
VALUES (
        'cesario',
        '$2y$10$examplehash1',
        'cesario@example.com',
        1,
        TRUE,
        'JBSWY3DPEHPK3PXP'
    ),
    (
        'admin',
        '$2y$10$examplehash2',
        'admin@banque.com',
        2,
        FALSE,
        NULL
    );
-- Insertion de comptes
INSERT INTO accounts (user_id, account_type, balance)
VALUES (1, 'courant', 1500.00),
    (1, 'epargne', 3000.00),
    (2, 'courant', 10000.00);
-- Insertion de bénéficiaires
INSERT INTO beneficiaries (user_id, name, iban, bank_name)
VALUES (
        1,
        'Jean Dupont',
        'FR7630006000011234567890189',
        'BNP Paribas'
    ),
    (
        1,
        'Marie Curie',
        'FR1420041010050500013M02606',
        'Société Générale'
    );
-- Insertion de moyens de transaction
INSERT INTO devices (label, description)
VALUES ('Carte Visa', 'Carte bancaire Visa classique'),
    ('Chéquier', 'Chéquier personnel'),
    (
        'Virement tiers',
        'Virement effectué par un tiers'
    );
-- Insertion de transactions
INSERT INTO transactions (
        account_id,
        type,
        amount,
        description,
        beneficiary_id,
        device_id,
        reference
    )
VALUES (
        1,
        'deposit',
        500.00,
        'Dépôt initial',
        NULL,
        1,
        'DEP123456'
    ),
    (
        1,
        'withdrawal',
        100.00,
        'Retrait distributeur',
        NULL,
        1,
        'ATM987654'
    ),
    (
        1,
        'transfer',
        250.00,
        'Virement à Jean Dupont',
        1,
        3,
        'VIR20250701A'
    ),
    (
        2,
        'transfer',
        1000.00,
        'Virement à Marie Curie',
        2,
        3,
        'VIR20250701B'
    );