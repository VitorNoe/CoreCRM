<?php

/**
 * Modelo Cliente
 * 
 * Classe responsável pela manipulação de dados da tabela clientes
 * 
 * @version 1.0.0
 */

class Cliente {
    
    // Propriedades da classe
    public $id;
    public $nome;
    public $email;
    public $telefone;
    public $status;
    public $observacoes;
    public $created_at;
    public $updated_at;
    
    // Constantes para status
    const STATUS_ATIVO = 'ativo';
    const STATUS_INATIVO = 'inativo';
    const STATUS_PROSPECTO = 'prospecto';
    const STATUS_BLOQUEADO = 'bloqueado';
    
    /**
     * Construtor da classe
     */
    public function __construct($data = []) {
        if (!empty($data)) {
            $this->fill($data);
        }
    }
    
    /**
     * Preenche os atributos do objeto com dados do array
     */
    public function fill($data) {
        $this->id = $data['id'] ?? null;
        $this->nome = $data['nome'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->telefone = $data['telefone'] ?? '';
        $this->status = $data['status'] ?? self::STATUS_PROSPECTO;
        $this->observacoes = $data['observacoes'] ?? '';
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }
    
    /**
     * Cria a tabela clientes no banco de dados
     */
    public static function createTable() {
        $sql = "CREATE TABLE IF NOT EXISTS clientes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE,
            telefone VARCHAR(20),
            status ENUM('ativo', 'inativo', 'prospecto', 'bloqueado') DEFAULT 'prospecto',
            observacoes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_nome (nome),
            INDEX idx_email (email),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        DatabaseHandler::query($sql);
        System::log("Tabela 'clientes' criada/verificada com sucesso.", "info");
    }
    
    /**
     * Salva o cliente no banco de dados
     */
    public function save() {
        if ($this->id) {
            return $this->update();
        } else {
            return $this->create();
        }
    }
    
    /**
     * Cria um novo cliente
     */
    private function create() {
        $sql = "INSERT INTO clientes (nome, email, telefone, status, observacoes) 
                VALUES (:nome, :email, :telefone, :status, :observacoes)";
        
        $params = [
            ':nome' => $this->nome,
            ':email' => $this->email,
            ':telefone' => $this->telefone,
            ':status' => $this->status,
            ':observacoes' => $this->observacoes
        ];
        
        try {
            $stmt = DatabaseHandler::query($sql, $params);
            $this->id = DatabaseHandler::getConnection()->lastInsertId();
            System::log("Cliente '{$this->nome}' criado com sucesso. ID: {$this->id}", "info");
            return true;
        } catch (Exception $e) {
            System::log("Erro ao criar cliente: " . $e->getMessage(), "error");
            return false;
        }
    }
    
    /**
     * Atualiza um cliente existente
     */
    private function update() {
        $sql = "UPDATE clientes SET 
                nome = :nome, 
                email = :email, 
                telefone = :telefone, 
                status = :status, 
                observacoes = :observacoes,
                updated_at = CURRENT_TIMESTAMP
                WHERE id = :id";
        
        $params = [
            ':id' => $this->id,
            ':nome' => $this->nome,
            ':email' => $this->email,
            ':telefone' => $this->telefone,
            ':status' => $this->status,
            ':observacoes' => $this->observacoes
        ];
        
        try {
            DatabaseHandler::query($sql, $params);
            System::log("Cliente ID {$this->id} atualizado com sucesso.", "info");
            return true;
        } catch (Exception $e) {
            System::log("Erro ao atualizar cliente: " . $e->getMessage(), "error");
            return false;
        }
    }
    
    /**
     * Busca um cliente por ID
     */
    public static function find($id) {
        $sql = "SELECT * FROM clientes WHERE id = :id";
        $stmt = DatabaseHandler::query($sql, [':id' => $id]);
        $data = $stmt->fetch();
        
        if ($data) {
            return new self($data);
        }
        
        return null;
    }
    
    /**
     * Busca todos os clientes com paginação
     */
    public static function all($page = 1, $perPage = 20, $search = '', $status = '') {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM clientes WHERE 1=1";
        $params = [];
        
        // Adiciona filtro de busca
        if (!empty($search)) {
            $sql .= " AND (nome LIKE :search OR email LIKE :search OR telefone LIKE :search)";
            $params[':search'] = "%{$search}%";
        }
        
        // Adiciona filtro de status
        if (!empty($status)) {
            $sql .= " AND status = :status";
            $params[':status'] = $status;
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $params[':limit'] = $perPage;
        $params[':offset'] = $offset;
        
        $stmt = DatabaseHandler::query($sql, $params);
        $results = $stmt->fetchAll();
        
        $clientes = [];
        foreach ($results as $data) {
            $clientes[] = new self($data);
        }
        
        return $clientes;
    }
    
    /**
     * Conta o total de clientes
     */
    public static function count($search = '', $status = '') {
        $sql = "SELECT COUNT(*) as total FROM clientes WHERE 1=1";
        $params = [];
        
        // Adiciona filtro de busca
        if (!empty($search)) {
            $sql .= " AND (nome LIKE :search OR email LIKE :search OR telefone LIKE :search)";
            $params[':search'] = "%{$search}%";
        }
        
        // Adiciona filtro de status
        if (!empty($status)) {
            $sql .= " AND status = :status";
            $params[':status'] = $status;
        }
        
        $stmt = DatabaseHandler::query($sql, $params);
        $result = $stmt->fetch();
        
        return (int) $result['total'];
    }
    
    /**
     * Conta clientes por status
     */
    public static function countByStatus($status) {
        $sql = "SELECT COUNT(*) as total FROM clientes WHERE status = :status";
        $stmt = DatabaseHandler::query($sql, [':status' => $status]);
        $result = $stmt->fetch();
        
        return (int) $result['total'];
    }
    
    /**
     * Exclui um cliente
     */
    public function delete() {
        if (!$this->id) {
            return false;
        }
        
        $sql = "DELETE FROM clientes WHERE id = :id";
        
        try {
            DatabaseHandler::query($sql, [':id' => $this->id]);
            System::log("Cliente ID {$this->id} excluído com sucesso.", "info");
            return true;
        } catch (Exception $e) {
            System::log("Erro ao excluir cliente: " . $e->getMessage(), "error");
            return false;
        }
    }
    
    /**
     * Exclui um cliente por ID (método estático)
     */
    public static function destroy($id) {
        $cliente = self::find($id);
        if ($cliente) {
            return $cliente->delete();
        }
        return false;
    }
    
    /**
     * Valida os dados do cliente
     */
    public function validate() {
        $errors = [];
        
        // Nome é obrigatório
        if (empty($this->nome)) {
            $errors[] = "Nome é obrigatório";
        }
        
        // Email deve ser válido se fornecido
        if (!empty($this->email) && !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Email deve ter um formato válido";
        }
        
        // Status deve ser válido
        $statusValidos = [self::STATUS_ATIVO, self::STATUS_INATIVO, self::STATUS_PROSPECTO, self::STATUS_BLOQUEADO];
        if (!in_array($this->status, $statusValidos)) {
            $errors[] = "Status deve ser um dos valores válidos";
        }
        
        return $errors;
    }
    
    /**
     * Retorna array com os status disponíveis
     */
    public static function getStatusOptions() {
        return [
            self::STATUS_PROSPECTO => 'Prospecto',
            self::STATUS_ATIVO => 'Ativo',
            self::STATUS_INATIVO => 'Inativo',
            self::STATUS_BLOQUEADO => 'Bloqueado'
        ];
    }
    
    /**
     * Retorna o nome do status formatado
     */
    public function getStatusLabel() {
        $options = self::getStatusOptions();
        return $options[$this->status] ?? $this->status;
    }
    
    /**
     * Retorna a cor CSS para o status
     */
    public function getStatusColor() {
        switch ($this->status) {
            case self::STATUS_ATIVO:
                return 'green';
            case self::STATUS_INATIVO:
                return 'gray';
            case self::STATUS_PROSPECTO:
                return 'blue';
            case self::STATUS_BLOQUEADO:
                return 'red';
            default:
                return 'gray';
        }
    }
    
    /**
     * Converte o objeto para array
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'email' => $this->email,
            'telefone' => $this->telefone,
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'status_color' => $this->getStatusColor(),
            'observacoes' => $this->observacoes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}

?>

