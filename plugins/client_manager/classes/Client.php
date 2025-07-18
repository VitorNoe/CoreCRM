<?php

/**
 * Classe Model para manipulação de dados de clientes
 * Esta classe segue o padrão Active Record para interação com o banco de dados
 */
class Client {
    
    private $id;
    private $name;
    private $email;
    private $phone;
    private $company;
    private $address;
    private $city;
    private $state;
    private $zip_code;
    private $country;
    private $status;
    private $notes;
    private $created_by;
    private $created_at;
    private $updated_at;
    private $deleted_at;
    
    // Constantes para status
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_PROSPECT = 'prospect';
    const STATUS_LEAD = 'lead';
    
    // Constantes para validação
    const REQUIRED_FIELDS = ['name', 'email'];
    const SEARCH_FIELDS = ['name', 'email', 'company'];
    
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
        $this->name = $data['name'] ?? null;
        $this->email = $data['email'] ?? null;
        $this->phone = $data['phone'] ?? null;
        $this->company = $data['company'] ?? null;
        $this->address = $data['address'] ?? null;
        $this->city = $data['city'] ?? null;
        $this->state = $data['state'] ?? null;
        $this->zip_code = $data['zip_code'] ?? null;
        $this->country = $data['country'] ?? 'Brasil';
        $this->status = $data['status'] ?? self::STATUS_ACTIVE;
        $this->notes = $data['notes'] ?? null;
        $this->created_by = $data['created_by'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
        $this->deleted_at = $data['deleted_at'] ?? null;
    }
    
    /**
     * Getters
     */
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getEmail() { return $this->email; }
    public function getPhone() { return $this->phone; }
    public function getCompany() { return $this->company; }
    public function getAddress() { return $this->address; }
    public function getCity() { return $this->city; }
    public function getState() { return $this->state; }
    public function getZipCode() { return $this->zip_code; }
    public function getCountry() { return $this->country; }
    public function getStatus() { return $this->status; }
    public function getNotes() { return $this->notes; }
    public function getCreatedBy() { return $this->created_by; }
    public function getCreatedAt() { return $this->created_at; }
    public function getUpdatedAt() { return $this->updated_at; }
    public function getDeletedAt() { return $this->deleted_at; }
    
    /**
     * Setters
     */
    public function setName($name) { $this->name = $name; }
    public function setEmail($email) { $this->email = $email; }
    public function setPhone($phone) { $this->phone = $phone; }
    public function setCompany($company) { $this->company = $company; }
    public function setAddress($address) { $this->address = $address; }
    public function setCity($city) { $this->city = $city; }
    public function setState($state) { $this->state = $state; }
    public function setZipCode($zip_code) { $this->zip_code = $zip_code; }
    public function setCountry($country) { $this->country = $country; }
    public function setStatus($status) { $this->status = $status; }
    public function setNotes($notes) { $this->notes = $notes; }
    public function setCreatedBy($created_by) { $this->created_by = $created_by; }
    
    /**
     * Retorna todos os atributos como array
     */
    public function toArray() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'company' => $this->company,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'zip_code' => $this->zip_code,
            'country' => $this->country,
            'status' => $this->status,
            'notes' => $this->notes,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
    
    /**
     * Salva o cliente no banco de dados
     */
    public function save() {
        global $db;
        
        // Validar antes de salvar
        $errors = $this->validate();
        if (!empty($errors)) {
            throw new Exception('Dados inválidos: ' . implode(', ', $errors));
        }
        
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
        global $db;
        
        $sql = "INSERT INTO clients (name, email, phone, company, address, city, state, zip_code, country, status, notes, created_by) 
                VALUES (:name, :email, :phone, :company, :address, :city, :state, :zip_code, :country, :status, :notes, :created_by)";
        
        $stmt = $db->prepare($sql);
        $result = $stmt->execute([
            ':name' => $this->name,
            ':email' => $this->email,
            ':phone' => $this->phone,
            ':company' => $this->company,
            ':address' => $this->address,
            ':city' => $this->city,
            ':state' => $this->state,
            ':zip_code' => $this->zip_code,
            ':country' => $this->country,
            ':status' => $this->status,
            ':notes' => $this->notes,
            ':created_by' => $this->created_by
        ]);
        
        if ($result) {
            $this->id = $db->lastInsertId();
            
            // Chamar hook após criação
            if (class_exists('HookHandler') && method_exists('HookHandler', 'call_hook')) {
                HookHandler::call_hook("after_client_created", $this->toArray());
            }
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Atualiza um cliente existente
     */
    private function update() {
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
            ':id' => $this->id,
            ':name' => $this->name,
            ':email' => $this->email,
            ':phone' => $this->phone,
            ':company' => $this->company,
            ':address' => $this->address,
            ':city' => $this->city,
            ':state' => $this->state,
            ':zip_code' => $this->zip_code,
            ':country' => $this->country,
            ':status' => $this->status,
            ':notes' => $this->notes
        ]);
        
        if ($result) {
            // Chamar hook após atualização
            if (class_exists('HookHandler') && method_exists('HookHandler', 'call_hook')) {
                HookHandler::call_hook("after_client_updated", $this->toArray());
            }
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Realiza soft delete do cliente
     */
    public function delete() {
        global $db;
        
        if (!$this->id) {
            return false;
        }
        
        $stmt = $db->prepare("UPDATE clients SET deleted_at = CURRENT_TIMESTAMP WHERE id = :id");
        $result = $stmt->execute([':id' => $this->id]);
        
        if ($result) {
            $this->deleted_at = date('Y-m-d H:i:s');
            
            // Chamar hook após delete
            if (class_exists('HookHandler') && method_exists('HookHandler', 'call_hook')) {
                HookHandler::call_hook("after_client_deleted", $this->id);
            }
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Valida os dados do cliente
     */
    public function validate() {
        $errors = [];
        
        // Validar campos obrigatórios
        foreach (self::REQUIRED_FIELDS as $field) {
            $value = $this->$field;
            if (empty($value)) {
                $errors[] = ucfirst($field) . ' é obrigatório';
            }
        }
        
        // Validar email
        if (!empty($this->email) && !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email inválido';
        }
        
        // Validar se email já existe
        if (!empty($this->email) && $this->emailExists()) {
            $errors[] = 'Este email já está cadastrado';
        }
        
        // Validar status
        $validStatuses = [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_PROSPECT, self::STATUS_LEAD];
        if (!in_array($this->status, $validStatuses)) {
            $errors[] = 'Status inválido';
        }
        
        return $errors;
    }
    
    /**
     * Verifica se o email já existe no banco
     */
    private function emailExists() {
        global $db;
        
        $sql = "SELECT id FROM clients WHERE email = :email AND deleted_at IS NULL";
        if ($this->id) {
            $sql .= " AND id != :id";
        }
        
        $stmt = $db->prepare($sql);
        $params = [':email' => $this->email];
        if ($this->id) {
            $params[':id'] = $this->id;
        }
        
        $stmt->execute($params);
        return $stmt->fetch() !== false;
    }
    
    /**
     * Busca cliente por ID
     */
    public static function find($id) {
        global $db;
        
        $stmt = $db->prepare("SELECT * FROM clients WHERE id = :id AND deleted_at IS NULL");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($data) {
            return new self($data);
        }
        
        return null;
    }
    
    /**
     * Lista todos os clientes
     */
    public static function all($limit = null, $offset = 0, $search = null) {
        global $db;
        
        $sql = "SELECT * FROM clients WHERE deleted_at IS NULL";
        $params = [];
        
        // Adicionar busca se fornecida
        if ($search) {
            $searchConditions = [];
            foreach (self::SEARCH_FIELDS as $field) {
                $searchConditions[] = "$field LIKE :search";
            }
            $sql .= " AND (" . implode(" OR ", $searchConditions) . ")";
            $params[':search'] = '%' . $search . '%';
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        // Adicionar paginação se fornecida
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $params[':limit'] = $limit;
            $params[':offset'] = $offset;
        }
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $clients = [];
        foreach ($results as $data) {
            $clients[] = new self($data);
        }
        
        return $clients;
    }
    
    /**
     * Conta o total de clientes
     */
    public static function count($search = null) {
        global $db;
        
        $sql = "SELECT COUNT(*) FROM clients WHERE deleted_at IS NULL";
        $params = [];
        
        // Adicionar busca se fornecida
        if ($search) {
            $searchConditions = [];
            foreach (self::SEARCH_FIELDS as $field) {
                $searchConditions[] = "$field LIKE :search";
            }
            $sql .= " AND (" . implode(" OR ", $searchConditions) . ")";
            $params[':search'] = '%' . $search . '%';
        }
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }
    
    /**
     * Busca clientes por status
     */
    public static function findByStatus($status) {
        global $db;
        
        $stmt = $db->prepare("SELECT * FROM clients WHERE status = :status AND deleted_at IS NULL ORDER BY created_at DESC");
        $stmt->execute([':status' => $status]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $clients = [];
        foreach ($results as $data) {
            $clients[] = new self($data);
        }
        
        return $clients;
    }
    
    /**
     * Busca clientes por email
     */
    public static function findByEmail($email) {
        global $db;
        
        $stmt = $db->prepare("SELECT * FROM clients WHERE email = :email AND deleted_at IS NULL");
        $stmt->execute([':email' => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($data) {
            return new self($data);
        }
        
        return null;
    }
    
    /**
     * Restaura um cliente que foi deletado (soft delete)
     */
    public function restore() {
        global $db;
        
        if (!$this->id) {
            return false;
        }
        
        $stmt = $db->prepare("UPDATE clients SET deleted_at = NULL WHERE id = :id");
        $result = $stmt->execute([':id' => $this->id]);
        
        if ($result) {
            $this->deleted_at = null;
            return true;
        }
        
        return false;
    }
    
    /**
     * Verifica se o cliente está deletado
     */
    public function isDeleted() {
        return $this->deleted_at !== null;
    }
    
    /**
     * Verifica se o cliente está ativo
     */
    public function isActive() {
        return $this->status === self::STATUS_ACTIVE;
    }
    
    /**
     * Retorna o nome completo formatado
     */
    public function getFullName() {
        $name = $this->name;
        if ($this->company) {
            $name .= ' (' . $this->company . ')';
        }
        return $name;
    }
    
    /**
     * Retorna o endereço completo formatado
     */
    public function getFullAddress() {
        $address = [];
        
        if ($this->address) $address[] = $this->address;
        if ($this->city) $address[] = $this->city;
        if ($this->state) $address[] = $this->state;
        if ($this->zip_code) $address[] = $this->zip_code;
        if ($this->country) $address[] = $this->country;
        
        return implode(', ', $address);
    }
    
    /**
     * Retorna array com todos os status possíveis
     */
    public static function getStatuses() {
        return [
            self::STATUS_ACTIVE => 'Ativo',
            self::STATUS_INACTIVE => 'Inativo', 
            self::STATUS_PROSPECT => 'Prospect',
            self::STATUS_LEAD => 'Lead'
        ];
    }
}