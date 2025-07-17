<?php

// Migração para criar a tabela de clientes
class CreateClientsTable {
    
    public static function up() {
        global $db;
        
        $sql = "CREATE TABLE IF NOT EXISTS clients (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            phone VARCHAR(20),
            company VARCHAR(255),
            address TEXT,
            city VARCHAR(100),
            state VARCHAR(50),
            zip_code VARCHAR(20),
            country VARCHAR(100) DEFAULT 'Brasil',
            status ENUM('active', 'inactive', 'prospect', 'lead') DEFAULT 'active',
            notes TEXT,
            created_by INT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            deleted_at TIMESTAMP NULL DEFAULT NULL,
            
            INDEX idx_email (email),
            INDEX idx_status (status),
            INDEX idx_created_at (created_at),
            INDEX idx_deleted_at (deleted_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        try {
            $db->exec($sql);
            System::log("Tabela 'clients' criada com sucesso.", "success");
            return true;
        } catch (PDOException $e) {
            System::log("Erro ao criar tabela 'clients': " . $e->getMessage(), "error");
            return false;
        }
    }
    
    public static function down() {
        global $db;
        
        try {
            $db->exec("DROP TABLE IF EXISTS clients");
            System::log("Tabela 'clients' removida com sucesso.", "info");
            return true;
        } catch (PDOException $e) {
            System::log("Erro ao remover tabela 'clients': " . $e->getMessage(), "error");
            return false;
        }
    }
}

// Executar migração automaticamente
if (class_exists('System')) {
    CreateClientsTable::up();
}
