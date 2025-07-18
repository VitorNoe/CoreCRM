<?php

require_once 'Client.php';

/**
 * Controlador para gerenciar as operações de clientes
 * Esta classe segue o padrão MVC e atua como intermediário entre as views e o model
 */
class ClientController {
    
    private $client;
    
    /**
     * Construtor
     */
    public function __construct() {
        $this->client = new Client();
    }
    
    /**
     * Lista todos os clientes com paginação e busca
     */
    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
        $search = isset($_GET['search']) ? trim($_GET['search']) : null;
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        
        $offset = ($page - 1) * $limit;
        
        // Buscar clientes
        if ($status) {
            $clients = Client::findByStatus($status);
            $total = count($clients);
            $clients = array_slice($clients, $offset, $limit);
        } else {
            $clients = Client::all($limit, $offset, $search);
            $total = Client::count($search);
        }
        
        // Calcular paginação
        $totalPages = ceil($total / $limit);
        
        $data = [
            'clients' => $clients,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_clients' => $total,
            'search' => $search,
            'status' => $status,
            'limit' => $limit,
            'statuses' => Client::getStatuses()
        ];
        
        return $this->view('list', $data);
    }
    
    /**
     * Exibe formulário para criar novo cliente
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->store();
        }
        
        $data = [
            'statuses' => Client::getStatuses(),
            'client' => new Client()
        ];
        
        return $this->view('create', $data);
    }
    
    /**
     * Processa a criação de um novo cliente
     */
    public function store() {
        try {
            $clientData = $this->getClientDataFromPost();
            $clientData['created_by'] = $this->getCurrentUserId();
            
            $client = new Client($clientData);
            
            if ($client->save()) {
                $this->setFlashMessage('success', 'Cliente criado com sucesso!');
                return $this->redirect('/clientes');
            } else {
                $this->setFlashMessage('error', 'Erro ao criar cliente. Tente novamente.');
            }
            
        } catch (Exception $e) {
            $this->setFlashMessage('error', $e->getMessage());
        }
        
        // Em caso de erro, retorna ao formulário
        $data = [
            'statuses' => Client::getStatuses(),
            'client' => new Client($_POST),
            'errors' => isset($e) ? [$e->getMessage()] : []
        ];
        
        return $this->view('create', $data);
    }
    
    /**
     * Exibe formulário para editar cliente
     */
    public function edit($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->update($id);
        }
        
        $client = Client::find($id);
        
        if (!$client) {
            $this->setFlashMessage('error', 'Cliente não encontrado.');
            return $this->redirect('/clientes');
        }
        
        $data = [
            'client' => $client,
            'statuses' => Client::getStatuses()
        ];
        
        return $this->view('edit', $data);
    }
    
    /**
     * Processa a atualização de um cliente
     */
    public function update($id) {
        try {
            $client = Client::find($id);
            
            if (!$client) {
                $this->setFlashMessage('error', 'Cliente não encontrado.');
                return $this->redirect('/clientes');
            }
            
            $clientData = $this->getClientDataFromPost();
            $client->fill($clientData);
            
            if ($client->save()) {
                $this->setFlashMessage('success', 'Cliente atualizado com sucesso!');
                return $this->redirect('/clientes');
            } else {
                $this->setFlashMessage('error', 'Erro ao atualizar cliente. Tente novamente.');
            }
            
        } catch (Exception $e) {
            $this->setFlashMessage('error', $e->getMessage());
        }
        
        // Em caso de erro, retorna ao formulário
        $data = [
            'client' => $client ?? new Client($_POST),
            'statuses' => Client::getStatuses(),
            'errors' => isset($e) ? [$e->getMessage()] : []
        ];
        
        return $this->view('edit', $data);
    }
    
    /**
     * Exibe detalhes de um cliente específico
     */
    public function show($id) {
        $client = Client::find($id);
        
        if (!$client) {
            $this->setFlashMessage('error', 'Cliente não encontrado.');
            return $this->redirect('/clientes');
        }
        
        $data = [
            'client' => $client,
            'statuses' => Client::getStatuses()
        ];
        
        return $this->view('show', $data);
    }
    
    /**
     * Remove um cliente (soft delete)
     */
    public function destroy($id) {
        try {
            $client = Client::find($id);
            
            if (!$client) {
                $this->setFlashMessage('error', 'Cliente não encontrado.');
                return $this->redirect('/clientes');
            }
            
            if ($client->delete()) {
                $this->setFlashMessage('success', 'Cliente removido com sucesso!');
            } else {
                $this->setFlashMessage('error', 'Erro ao remover cliente. Tente novamente.');
            }
            
        } catch (Exception $e) {
            $this->setFlashMessage('error', $e->getMessage());
        }
        
        return $this->redirect('/clientes');
    }
    
    /**
     * Restaura um cliente deletado
     */
    public function restore($id) {
        try {
            $client = Client::find($id);
            
            if (!$client) {
                $this->setFlashMessage('error', 'Cliente não encontrado.');
                return $this->redirect('/clientes');
            }
            
            if ($client->restore()) {
                $this->setFlashMessage('success', 'Cliente restaurado com sucesso!');
            } else {
                $this->setFlashMessage('error', 'Erro ao restaurar cliente. Tente novamente.');
            }
            
        } catch (Exception $e) {
            $this->setFlashMessage('error', $e->getMessage());
        }
        
        return $this->redirect('/clientes');
    }
    
    /**
     * API: Retorna lista de clientes em formato JSON
     */
    public function apiIndex() {
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
        $search = isset($_GET['search']) ? trim($_GET['search']) : null;
        
        $clients = Client::all($limit, $offset, $search);
        $total = Client::count($search);
        
        $response = [
            'data' => array_map(function($client) {
                return $client->toArray();
            }, $clients),
            'total' => $total,
            'limit' => $limit,
            'offset' => $offset,
            'count' => count($clients)
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    /**
     * API: Retorna um cliente específico em formato JSON
     */
    public function apiShow($id) {
        $client = Client::find($id);
        
        if (!$client) {
            http_response_code(404);
            echo json_encode(['error' => 'Cliente não encontrado']);
            exit;
        }
        
        header('Content-Type: application/json');
        echo json_encode($client->toArray());
        exit;
    }
    
    /**
     * API: Cria um novo cliente via API
     */
    public function apiStore() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                http_response_code(400);
                echo json_encode(['error' => 'Dados inválidos']);
                exit;
            }
            
            $input['created_by'] = $this->getCurrentUserId();
            $client = new Client($input);
            
            if ($client->save()) {
                http_response_code(201);
                echo json_encode($client->toArray());
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Erro ao criar cliente']);
            }
            
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
        
        exit;
    }
    
    /**
     * API: Atualiza um cliente via API
     */
    public function apiUpdate($id) {
        try {
            $client = Client::find($id);
            
            if (!$client) {
                http_response_code(404);
                echo json_encode(['error' => 'Cliente não encontrado']);
                exit;
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                http_response_code(400);
                echo json_encode(['error' => 'Dados inválidos']);
                exit;
            }
            
            $client->fill($input);
            
            if ($client->save()) {
                echo json_encode($client->toArray());
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Erro ao atualizar cliente']);
            }
            
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
        
        exit;
    }
    
    /**
     * API: Remove um cliente via API
     */
    public function apiDestroy($id) {
        try {
            $client = Client::find($id);
            
            if (!$client) {
                http_response_code(404);
                echo json_encode(['error' => 'Cliente não encontrado']);
                exit;
            }
            
            if ($client->delete()) {
                echo json_encode(['message' => 'Cliente removido com sucesso']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Erro ao remover cliente']);
            }
            
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
        
        exit;
    }
    
    /**
     * Exporta clientes para CSV
     */
    public function exportCsv() {
        $clients = Client::all();
        
        $filename = 'clientes_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Cabeçalho do CSV
        fputcsv($output, [
            'ID', 'Nome', 'Email', 'Telefone', 'Empresa', 
            'Endereço', 'Cidade', 'Estado', 'CEP', 'País', 
            'Status', 'Observações', 'Criado em'
        ]);
        
        // Dados dos clientes
        foreach ($clients as $client) {
            fputcsv($output, [
                $client->getId(),
                $client->getName(),
                $client->getEmail(),
                $client->getPhone(),
                $client->getCompany(),
                $client->getAddress(),
                $client->getCity(),
                $client->getState(),
                $client->getZipCode(),
                $client->getCountry(),
                $client->getStatus(),
                $client->getNotes(),
                $client->getCreatedAt()
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Busca clientes (AJAX)
     */
    public function search() {
        $term = isset($_GET['term']) ? trim($_GET['term']) : '';
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        
        if (empty($term)) {
            header('Content-Type: application/json');
            echo json_encode([]);
            exit;
        }
        
        $clients = Client::all($limit, 0, $term);
        
        $results = array_map(function($client) {
            return [
                'id' => $client->getId(),
                'name' => $client->getName(),
                'email' => $client->getEmail(),
                'company' => $client->getCompany(),
                'full_name' => $client->getFullName()
            ];
        }, $clients);
        
        header('Content-Type: application/json');
        echo json_encode($results);
        exit;
    }
    
    /**
     * Método auxiliar para extrair dados do cliente do POST
     */
    private function getClientDataFromPost() {
        return [
            'name' => isset($_POST['name']) ? trim($_POST['name']) : '',
            'email' => isset($_POST['email']) ? trim($_POST['email']) : '',
            'phone' => isset($_POST['phone']) ? trim($_POST['phone']) : '',
            'company' => isset($_POST['company']) ? trim($_POST['company']) : '',
            'address' => isset($_POST['address']) ? trim($_POST['address']) : '',
            'city' => isset($_POST['city']) ? trim($_POST['city']) : '',
            'state' => isset($_POST['state']) ? trim($_POST['state']) : '',
            'zip_code' => isset($_POST['zip_code']) ? trim($_POST['zip_code']) : '',
            'country' => isset($_POST['country']) ? trim($_POST['country']) : 'Brasil',
            'status' => isset($_POST['status']) ? $_POST['status'] : Client::STATUS_ACTIVE,
            'notes' => isset($_POST['notes']) ? trim($_POST['notes']) : ''
        ];
    }
    
    /**
     * Método auxiliar para renderizar views
     */
    private function view($view, $data = []) {
        extract($data);
        
        $viewPath = "views/clients/{$view}.php";
        
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            throw new Exception("View não encontrada: {$viewPath}");
        }
    }
    
    /**
     * Método auxiliar para redirecionar
     */
    private function redirect($url) {
        header("Location: {$url}");
        exit;
    }
    
    /**
     * Método auxiliar para definir mensagens flash
     */
    private function setFlashMessage($type, $message) {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        $_SESSION['flash_message'] = [
            'type' => $type,
            'message' => $message
        ];
    }
    
    /**
     * Método auxiliar para obter o ID do usuário atual
     */
    private function getCurrentUserId() {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    }
    
    /**
     * Importa clientes de um arquivo CSV
     */
    public function importCsv() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->processImportCsv();
        }
        
        return $this->view('import', []);
    }
    
    /**
     * Processa a importação de clientes via CSV
     */
    private function processImportCsv() {
        try {
            if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Erro no upload do arquivo');
            }
            
            $filename = $_FILES['csv_file']['tmp_name'];
            $handle = fopen($filename, 'r');
            
            if (!$handle) {
                throw new Exception('Não foi possível abrir o arquivo');
            }
            
            $imported = 0;
            $errors = [];
            $line = 0;
            
            // Pular cabeçalho
            fgetcsv($handle);
            
            while (($data = fgetcsv($handle)) !== FALSE) {
                $line++;
                
                try {
                    $clientData = [
                        'name' => isset($data[1]) ? trim($data[1]) : '',
                        'email' => isset($data[2]) ? trim($data[2]) : '',
                        'phone' => isset($data[3]) ? trim($data[3]) : '',
                        'company' => isset($data[4]) ? trim($data[4]) : '',
                        'address' => isset($data[5]) ? trim($data[5]) : '',
                        'city' => isset($data[6]) ? trim($data[6]) : '',
                        'state' => isset($data[7]) ? trim($data[7]) : '',
                        'zip_code' => isset($data[8]) ? trim($data[8]) : '',
                        'country' => isset($data[9]) ? trim($data[9]) : 'Brasil',
                        'status' => isset($data[10]) ? trim($data[10]) : Client::STATUS_ACTIVE,
                        'notes' => isset($data[11]) ? trim($data[11]) : '',
                        'created_by' => $this->getCurrentUserId()
                    ];
                    
                    $client = new Client($clientData);
                    
                    if ($client->save()) {
                        $imported++;
                    } else {
                        $errors[] = "Linha {$line}: Erro ao salvar cliente";
                    }
                    
                } catch (Exception $e) {
                    $errors[] = "Linha {$line}: " . $e->getMessage();
                }
            }
            
            fclose($handle);
            
            $message = "Importação concluída! {$imported} clientes importados.";
            if (!empty($errors)) {
                $message .= " Erros: " . implode(', ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $message .= " e mais " . (count($errors) - 5) . " erros.";
                }
            }
            
            $this->setFlashMessage($imported > 0 ? 'success' : 'warning', $message);
            
        } catch (Exception $e) {
            $this->setFlashMessage('error', 'Erro na importação: ' . $e->getMessage());
        }
        
        return $this->redirect('/clientes');
    }
    
    /**
     * Retorna estatísticas dos clientes
     */
    public function stats() {
        $stats = [
            'total' => Client::count(),
            'active' => count(Client::findByStatus(Client::STATUS_ACTIVE)),
            'inactive' => count(Client::findByStatus(Client::STATUS_INACTIVE)),
            'prospects' => count(Client::findByStatus(Client::STATUS_PROSPECT)),
            'leads' => count(Client::findByStatus(Client::STATUS_LEAD))
        ];
        
        header('Content-Type: application/json');
        echo json_encode($stats);
        exit;
    }
}