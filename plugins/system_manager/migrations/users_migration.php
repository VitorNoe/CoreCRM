<?php
// Migration para garantir a existência e integridade da tabela users
class SystemManagerUsersMigration {
    public static function migrate($pdo) {
        try {
            $pdo->exec("CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                role ENUM('admin', 'user', 'moderator') NOT NULL DEFAULT 'user',
                active TINYINT(1) DEFAULT 1,
                username VARCHAR(255) UNIQUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_email (email),
                INDEX idx_username (username),
                INDEX idx_role (role),
                INDEX idx_active (active)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");

            // ALTER TABLE para renomear campos do português para inglês, se existirem
            $pdo->exec("ALTER TABLE users 
                CHANGE COLUMN nome name VARCHAR(255) NOT NULL,
                CHANGE COLUMN senha password VARCHAR(255) NOT NULL,
                CHANGE COLUMN tipo role ENUM('admin', 'user', 'moderator') NOT NULL DEFAULT 'user',
                CHANGE COLUMN ativo active TINYINT(1) DEFAULT 1;");
        } catch (Exception $e) {
            System::log('Erro ao executar migration de usuários: ' . $e->getMessage(), 'error');
        }
        // Insere usuário administrador padrão (apenas se não existir)
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'admin'");
        $stmt->execute();
        if ($stmt->fetchColumn() == 0) {
            $adminHash = password_hash('admin123', PASSWORD_ARGON2ID);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, username, active) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                'Administrator',
                'admin@corecrm.com',
                $adminHash,
                'admin',
                'admin',
                1
            ]);
        }
        // Insere usuário comum padrão (apenas se não existir)
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'user'");
        $stmt->execute();
        if ($stmt->fetchColumn() == 0) {
            $userHash = password_hash('user123', PASSWORD_ARGON2ID);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, username, active) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                'User',
                'user@corecrm.com',
                $userHash,
                'user',
                'user',
                1
            ]);
        }
    }
}
