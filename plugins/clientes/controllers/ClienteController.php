<?php

/**
 * Controlador de Clientes
 * 
 * Classe responsável por gerenciar as operações CRUD de clientes
 * e renderizar as views correspondentes
 * 
 * @version 1.0.0
 */

class ClienteController {
    
    /**
     * Lista todos os clientes com paginação, busca e filtros
     */
    public static function index() {
        // Parâmetros da requisição
        $page = (int) ($_GET['page'] ?? 1);
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $perPage = 20;
        
        // Busca os clientes
        $clientes = Cliente::all($page, $perPage, $search, $status);
        $totalClientes = Cliente::count($search, $status);
        $totalPages = ceil($totalClientes / $perPage);
        
        // Dados para a view
        $data = [
            'clientes' => $clientes,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalClientes' => $totalClientes,
            'search' => $search,
            'status' => $status,
            'statusOptions' => Cliente::getStatusOptions(),
            'perPage' => $perPage
        ];
        
        self::renderView('index', $data);
    }
    
    /**
     * Exibe o formulário para criar um novo cliente
     */
    public static function create() {
        $data = [
            'cliente' => new Cliente(),
            'statusOptions' => Cliente::getStatusOptions(),
            'action' => 'create'
        ];
        
        self::renderView('form', $data);
    }
    
    /**
     * Salva um novo cliente
     */
    public static function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/clientes');
            exit;
        }
        
        $cliente = new Cliente();
        $cliente->nome = $_POST['nome'] ?? '';
        $cliente->email = $_POST['email'] ?? '';
        $cliente->telefone = $_POST['telefone'] ?? '';
        $cliente->status = $_POST['status'] ?? Cliente::STATUS_PROSPECTO;
        $cliente->observacoes = $_POST['observacoes'] ?? '';
        
        // Valida os dados
        $errors = $cliente->validate();
        
        if (empty($errors)) {
            if ($cliente->save()) {
                self::setFlashMessage('Cliente criado com sucesso!', 'success');
                header('Location: /admin/clientes');
                exit;
            } else {
                $errors[] = 'Erro ao salvar cliente. Tente novamente.';
            }
        }
        
        // Se há erros, volta para o formulário
        $data = [
            'cliente' => $cliente,
            'statusOptions' => Cliente::getStatusOptions(),
            'errors' => $errors,
            'action' => 'create'
        ];
        
        self::renderView('form', $data);
    }
    
    /**
     * Exibe o formulário para editar um cliente
     */
    public static function edit($id) {
        $cliente = Cliente::find($id);
        
        if (!$cliente) {
            self::setFlashMessage('Cliente não encontrado.', 'error');
            header('Location: /admin/clientes');
            exit;
        }
        
        $data = [
            'cliente' => $cliente,
            'statusOptions' => Cliente::getStatusOptions(),
            'action' => 'edit'
        ];
        
        self::renderView('form', $data);
    }
    
    /**
     * Atualiza um cliente existente
     */
    public static function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/clientes');
            exit;
        }
        
        $cliente = Cliente::find($id);
        
        if (!$cliente) {
            self::setFlashMessage('Cliente não encontrado.', 'error');
            header('Location: /admin/clientes');
            exit;
        }
        
        $cliente->nome = $_POST['nome'] ?? '';
        $cliente->email = $_POST['email'] ?? '';
        $cliente->telefone = $_POST['telefone'] ?? '';
        $cliente->status = $_POST['status'] ?? Cliente::STATUS_PROSPECTO;
        $cliente->observacoes = $_POST['observacoes'] ?? '';
        
        // Valida os dados
        $errors = $cliente->validate();
        
        if (empty($errors)) {
            if ($cliente->save()) {
                self::setFlashMessage('Cliente atualizado com sucesso!', 'success');
                header('Location: /admin/clientes');
                exit;
            } else {
                $errors[] = 'Erro ao atualizar cliente. Tente novamente.';
            }
        }
        
        // Se há erros, volta para o formulário
        $data = [
            'cliente' => $cliente,
            'statusOptions' => Cliente::getStatusOptions(),
            'errors' => $errors,
            'action' => 'edit'
        ];
        
        self::renderView('form', $data);
    }
    
    /**
     * Exclui um cliente
     */
    public static function destroy($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/clientes');
            exit;
        }
        
        $cliente = Cliente::find($id);
        
        if (!$cliente) {
            self::setFlashMessage('Cliente não encontrado.', 'error');
        } else {
            if ($cliente->delete()) {
                self::setFlashMessage('Cliente excluído com sucesso!', 'success');
            } else {
                self::setFlashMessage('Erro ao excluir cliente.', 'error');
            }
        }
        
        header('Location: /admin/clientes');
        exit;
    }
    
    /**
     * Busca clientes via AJAX
     */
    public static function search() {
        header('Content-Type: application/json');
        
        $search = $_GET['q'] ?? '';
        $status = $_GET['status'] ?? '';
        $page = (int) ($_GET['page'] ?? 1);
        $perPage = 20;
        
        $clientes = Cliente::all($page, $perPage, $search, $status);
        $totalClientes = Cliente::count($search, $status);
        
        $result = [
            'clientes' => array_map(function($cliente) {
                return $cliente->toArray();
            }, $clientes),
            'total' => $totalClientes,
            'page' => $page,
            'totalPages' => ceil($totalClientes / $perPage)
        ];
        
        echo json_encode($result);
        exit;
    }
    
    /**
     * API para listar clientes (formato JSON)
     */
    public static function apiIndex() {
        header('Content-Type: application/json');
        
        $page = (int) ($_GET['page'] ?? 1);
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $perPage = (int) ($_GET['per_page'] ?? 20);
        
        $clientes = Cliente::all($page, $perPage, $search, $status);
        $totalClientes = Cliente::count($search, $status);
        
        $result = [
            'data' => array_map(function($cliente) {
                return $cliente->toArray();
            }, $clientes),
            'meta' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $totalClientes,
                'total_pages' => ceil($totalClientes / $perPage)
            ],
            'filters' => [
                'search' => $search,
                'status' => $status
            ]
        ];
        
        echo json_encode($result);
        exit;
    }
    
    /**
     * Renderiza uma view do plugin
     */
    private static function renderView($view, $data = []) {
        // Extrai as variáveis para o escopo da view
        extract($data);
        
        // Inclui o header do tema
        require_once __DIR__ . '/../../../themes/default/blocks/BlockRenderer.php';
        
        // Renderiza o header
        echo BlockRenderer::render('Header', [
            'title' => 'Gerenciamento de Clientes',
            'logo' => '<a href="/" class="text-xl font-bold tracking-tight hover:underline">CoreCRM</a>',
            'user' => ['name' => 'Admin'], // TODO: Pegar usuário logado
            'actions' => [
                ['label' => 'Dashboard', 'href' => '/', 'class' => 'bg-gray-500 hover:bg-gray-600'],
                ['label' => 'Novo Cliente', 'href' => '/admin/clientes/novo', 'class' => 'bg-green-500 hover:bg-green-600']
            ]
        ]);
        
        // Renderiza breadcrumbs
        $breadcrumbs = [
            ['label' => 'Home', 'icon' => 'fa-home', 'href' => '/'],
            ['label' => 'Clientes', 'icon' => 'fa-users', 'href' => '/admin/clientes']
        ];
        
        if ($view === 'form') {
            $breadcrumbs[] = [
                'label' => isset($cliente->id) ? 'Editar Cliente' : 'Novo Cliente',
                'icon' => 'fa-edit'
            ];
        }
        
        echo BlockRenderer::render('Breadcrumb', ['items' => $breadcrumbs]);
        
        // Exibe mensagens flash
        self::displayFlashMessages();
        
        // Inclui a view específica
        $viewFile = __DIR__ . "/../views/{$view}.php";
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo '<div class="container mx-auto my-8">';
            echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">';
            echo "View '{$view}' não encontrada.";
            echo '</div>';
            echo '</div>';
        }
        
        // Renderiza o footer
        echo BlockRenderer::render('Footer', [
            'breadcrumbs' => true,
            'clock' => true,
            'status' => 'Online',
            'content' => '&copy; ' . date('Y') . ' CoreCRM - Plugin de Clientes'
        ]);
    }
    
    /**
     * Define uma mensagem flash
     */
    private static function setFlashMessage($message, $type = 'info') {
        if (!isset($_SESSION)) {
            session_start();
        }
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
    
    /**
     * Exibe mensagens flash
     */
    private static function displayFlashMessages() {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        if (isset($_SESSION['flash_message'])) {
            $message = $_SESSION['flash_message'];
            $type = $_SESSION['flash_type'] ?? 'info';
            
            $colorClass = [
                'success' => 'bg-green-100 border-green-400 text-green-700',
                'error' => 'bg-red-100 border-red-400 text-red-700',
                'warning' => 'bg-yellow-100 border-yellow-400 text-yellow-700',
                'info' => 'bg-blue-100 border-blue-400 text-blue-700'
            ][$type] ?? 'bg-blue-100 border-blue-400 text-blue-700';
            
            echo '<div class="container mx-auto mt-4">';
            echo "<div class=\"{$colorClass} px-4 py-3 rounded border mb-4\">";
            echo htmlspecialchars($message);
            echo '</div>';
            echo '</div>';
            
            // Remove a mensagem da sessão
            unset($_SESSION['flash_message']);
            unset($_SESSION['flash_type']);
        }
    }
}

?>

