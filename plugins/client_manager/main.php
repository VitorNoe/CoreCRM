<?php

// Arquivo principal do plugin Client Manager
// Este arquivo é carregado pelo PluginHandler quando o plugin está ativo.

// Classe principal do plugin
class ClientManager {
    
    public static function init() {
        self::registerRoutes();
        self::registerHooks();
        self::addAdminContent();
        self::addSidebarMenuItems();
        
        System::log("Plugin Client Manager carregado com sucesso.", "info");
    }
    
    private static function registerRoutes() {
        // Listagem de clientes
        RoutesHandler::addRoute("GET", "/clientes", function() {
            require_once __DIR__ . '/admin/list.php';
        });
        
        // Página de criação de cliente
        RoutesHandler::addRoute("GET", "/clientes/novo", function() {
            require_once __DIR__ . '/admin/create.php';
        });
        
        // Processar criação de cliente
        RoutesHandler::addRoute("POST", "/clientes/criar", function() {
            require_once __DIR__ . '/admin/create.php';
        });
        
        // Página de edição de cliente
        RoutesHandler::addRoute("GET", "/clientes/editar/{id}", function($id) {
            $_GET['id'] = $id;
            require_once __DIR__ . '/admin/edit.php';
        });
        
        // Processar edição de cliente
        RoutesHandler::addRoute("POST", "/clientes/atualizar/{id}", function($id) {
            $_POST['id'] = $id;
            require_once __DIR__ . '/admin/edit.php';
        });
        
        // Deletar cliente
        RoutesHandler::addRoute("POST", "/clientes/deletar/{id}", function($id) {
            self::deleteClient($id);
        });
        
        // API endpoints
        RoutesHandler::addRoute("GET", "/api/clientes", function() {
            header('Content-Type: application/json');
            echo json_encode(self::getClients());
        });
        
        RoutesHandler::addRoute("GET", "/api/clientes/{id}", function($id) {
            header('Content-Type: application/json');
            echo json_encode(self::getClient($id));
        });
    }
    
    private static function registerHooks() {
        // Hook após criar cliente
        HookHandler::register_hook("after_client_created", function($client) {
            System::log("Cliente criado: " . $client['name'], "info");
        });
        
        // Hook após atualizar cliente
        HookHandler::register_hook("after_client_updated", function($client) {
            System::log("Cliente atualizado: " . $client['name'], "info");
        });
        
        // Hook após deletar cliente
        HookHandler::register_hook("after_client_deleted", function($client_id) {
            System::log("Cliente deletado: ID " . $client_id, "info");
        });
    }
    
    private static function addAdminContent() {
        System::addAdminContent(function() {
            $clientsCount = self::getClientsCount();
            echo '<div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded shadow mb-4">';
            echo '<h2 class="text-lg font-bold text-blue-700 mb-1">Gerenciamento de Clientes</h2>';
            echo '<p class="text-gray-700">Total de clientes cadastrados: <span class="font-bold">' . $clientsCount . '</span></p>';
            echo '<div class="mt-2">';
            echo '<a href="/clientes" class="inline-block bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">Ver Clientes</a>';
            echo '<a href="/clientes/novo" class="inline-block bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600 ml-2">Novo Cliente</a>';
            echo '</div>';
            echo '</div>';
        });
    }
    
    private static function addSidebarMenuItems() {
        System::addAdminSidebarMenuItem([
            'name' => 'Clientes',
            'icon' => 'people',
            'url' => '/clientes',
            'badge' => self::getClientsCount()
        ]);
        
        System::addAdminSidebarMenuItem([
            'name' => 'Novo Cliente',
            'icon' => 'person_add',
            'url' => '/clientes/novo'
        ]);
    }
    
    // Métodos auxiliares
    public static function getClients($limit = null, $offset = 0, $search = null) {
        global $db;
        
        $sql = "SELECT * FROM clients WHERE deleted_at IS NULL";
        $params = [];
        
        if ($search) {
            $sql .= " AND (name LIKE :search OR email LIKE :search OR company LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $params[':limit'] = $limit;
            $params[':offset'] = $offset;
        }
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public static function getClient($id) {
        global $db;
        
        $stmt = $db->prepare("SELECT * FROM clients WHERE id = :id AND deleted_at IS NULL");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public static function getClientsCount() {
        global $db;
        
        $stmt = $db->prepare("SELECT COUNT(*) FROM clients WHERE deleted_at IS NULL");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    public static function createClient($data) {
        global $db;
        
        $sql = "INSERT INTO clients (name, email, phone, company, address, city, state, zip_code, country, status, notes, created_by) 
                VALUES (:name, :email, :phone, :company, :address, :city, :state, :zip_code, :country, :status, :notes, :created_by)";
        
        $stmt = $db->prepare($sql);
        $result = $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':phone' => $data['phone'] ?? null,
            ':company' => $data['company'] ?? null,
            ':address' => $data['address'] ?? null,
            ':city' => $data['city'] ?? null,
            ':state' => $data['state'] ?? null,
            ':zip_code' => $data['zip_code'] ?? null,
            ':country' => $data['country'] ?? 'Brasil',
            ':status' => $data['status'] ?? 'active',
            ':notes' => $data['notes'] ?? null,
            ':created_by' => 1 // Pegar ID do usuário logado
        ]);
        
        if ($result) {
            $clientId = $db->lastInsertId();
            $client = self::getClient($clientId);
            if (method_exists('HookHandler', 'call_hook')) {
                HookHandler::call_hook("after_client_created", $client);
            }
            return $client;
        }
        
        return false;
    }
    
    public static function updateClient($id, $data) {
        global $db;
        
        $sql = "UPDATE clients SET 
                name = :name, 
                email = :email, 
                phone = :phone, 
                company = :company, 
                address = :address, 
                city = :city, 
                state = :state, 
                zip_code = :zip_code, 
                country = :country, 
                status = :status, 
                notes = :notes,
                updated_at = CURRENT_TIMESTAMP
                WHERE id = :id AND deleted_at IS NULL";
        
        $stmt = $db->prepare($sql);
        $result = $stmt->execute([
            ':id' => $id,
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':phone' => $data['phone'] ?? null,
            ':company' => $data['company'] ?? null,
            ':address' => $data['address'] ?? null,
            ':city' => $data['city'] ?? null,
            ':state' => $data['state'] ?? null,
            ':zip_code' => $data['zip_code'] ?? null,
            ':country' => $data['country'] ?? 'Brasil',
            ':status' => $data['status'] ?? 'active',
            ':notes' => $data['notes'] ?? null
        ]);
        
        if ($result) {
            $client = self::getClient($id);
            if (method_exists('HookHandler', 'call_hook')) {
                HookHandler::call_hook("after_client_updated", $client);
            }
            return $client;
        }
        
        return false;
    }
    
    public static function deleteClient($id) {
        global $db;
        
        // Soft delete
        $stmt = $db->prepare("UPDATE clients SET deleted_at = CURRENT_TIMESTAMP WHERE id = :id");
        $result = $stmt->execute([':id' => $id]);
        
        if ($result) {
            if (class_exists('HookHandler') && method_exists('HookHandler', 'call_hook')) {
                HookHandler::call_hook("after_client_deleted", $id);
            }
            header('Location: /clientes?deleted=1');
            exit;
        }
        
        return false;
    }
    
    public static function validateClient($data) {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors[] = 'Nome é obrigatório';
        }
        
        if (empty($data['email'])) {
            $errors[] = 'Email é obrigatório';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email inválido';
        }
        
        // Verificar se email já existe
        global $db;
        $stmt = $db->prepare("SELECT id FROM clients WHERE email = :email AND deleted_at IS NULL");
        $stmt->execute([':email' => $data['email']]);
        if ($stmt->fetch() && (!isset($data['id']) || $stmt->fetchColumn() != $data['id'])) {
            $errors[] = 'Este email já está cadastrado';
        }
        
        return $errors;
    }
}

// Inicializar o plugin
ClientManager::init();
