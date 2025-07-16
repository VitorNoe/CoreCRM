<?php
// Migration para garantir a existência e integridade da tabela api_tokens
class SystemManagerApiTokensMigration {
    public static function migrate($pdo) {
        try {
            $pdo->exec("CREATE TABLE IF NOT EXISTS api_tokens (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                token VARCHAR(128) NOT NULL,
                created_at DATETIME NOT NULL,
                expires_at DATETIME NOT NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

            // Adiciona os campos se não existirem
            $stmt = $pdo->query("SHOW COLUMNS FROM api_tokens LIKE 'created_at'");
            if ($stmt->rowCount() === 0) {
                $pdo->exec("ALTER TABLE api_tokens ADD COLUMN created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;");
            }
            $stmt = $pdo->query("SHOW COLUMNS FROM api_tokens LIKE 'expires_at'");
            if ($stmt->rowCount() === 0) {
                $pdo->exec("ALTER TABLE api_tokens ADD COLUMN expires_at DATETIME NOT NULL DEFAULT (CURRENT_TIMESTAMP + INTERVAL 30 DAY);");
            }
        } catch (Exception $e) {
            System::log('Erro ao executar migration de tokens: ' . $e->getMessage(), 'error');
        }
    }
}
