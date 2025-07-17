<?php

/**
 * Plugin de Clientes - CoreCRM
 * 
 * Plugin completo para gerenciamento de clientes com funcionalidades de CRUD,
 * busca, filtros e paginação.
 * 
 * @version 1.0.0
 * @author CoreCRM Team
 */

// Carrega as dependências do plugin
require_once __DIR__ . '/models/Cliente.php';
require_once __DIR__ . '/controllers/ClienteController.php';

// Inicialização do plugin
class ClientesPlugin {
    
    public static function init() {
        // Registra as rotas do plugin
        self::registerRoutes();
        
        // Adiciona item ao menu administrativo
        self::addAdminMenu();
        
        // Registra hooks necessários
        self::registerHooks();
        
        // Cria as tabelas necessárias se não existirem
        self::createTables();
        
        System::log("Plugin de Clientes carregado com sucesso.", "info");
    }
    
    /**
     * Registra as rotas do plugin
     */
    private static function registerRoutes() {
        // Rota principal - Lista de clientes
        RoutesHandler::addRoute("GET", "/admin/clientes", function() {
            ClienteController::index();
        });
        
        // Rota para exibir formulário de novo cliente
        RoutesHandler::addRoute("GET", "/admin/clientes/novo", function() {
            ClienteController::create();
        });
        
        // Rota para salvar novo cliente
        RoutesHandler::addRoute("POST", "/admin/clientes/salvar", function() {
            ClienteController::store();
        });
        
        // Rota para exibir formulário de edição
        RoutesHandler::addRoute("GET", "/admin/clientes/editar/{id}", function($id) {
            ClienteController::edit($id);
        });
        
        // Rota para atualizar cliente
        RoutesHandler::addRoute("POST", "/admin/clientes/atualizar/{id}", function($id) {
            ClienteController::update($id);
        });
        
        // Rota para excluir cliente
        RoutesHandler::addRoute("POST", "/admin/clientes/excluir/{id}", function($id) {
            ClienteController::destroy($id);
        });
        
        // Rota para busca AJAX
        RoutesHandler::addRoute("GET", "/admin/clientes/buscar", function() {
            ClienteController::search();
        });
        
        // Rota para API JSON (opcional)
        RoutesHandler::addRoute("GET", "/api/clientes", function() {
            ClienteController::apiIndex();
        });
    }
    
    /**
     * Adiciona item ao menu administrativo
     */
    private static function addAdminMenu() {
        System::addAdminSidebarMenuItem([
            'name' => 'Clientes',
            'icon' => 'users',
            'url'  => '/admin/clientes',
            'badge' => Cliente::count()
        ]);
    }
    
    /**
     * Registra hooks do sistema
     */
    private static function registerHooks() {
        // Hook executado após login do usuário
        HookHandler::register_hook("after_user_login", function($user) {
            System::log("Usuário {$user['name']} acessou o sistema - Plugin Clientes ativo.", "info");
        });
        
        // Hook para adicionar estatísticas ao dashboard
        HookHandler::register_hook("dashboard_stats", function() {
            $totalClientes = Cliente::count();
            $clientesAtivos = Cliente::countByStatus('ativo');
            
            echo '<div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded shadow mb-4">';
            echo '<h3 class="text-lg font-bold text-blue-700 mb-2">Estatísticas de Clientes</h3>';
            echo '<div class="grid grid-cols-2 gap-4">';
            echo '<div class="text-center">';
            echo '<div class="text-2xl font-bold text-blue-600">' . $totalClientes . '</div>';
            echo '<div class="text-sm text-gray-600">Total de Clientes</div>';
            echo '</div>';
            echo '<div class="text-center">';
            echo '<div class="text-2xl font-bold text-green-600">' . $clientesAtivos . '</div>';
            echo '<div class="text-sm text-gray-600">Clientes Ativos</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        });
    }
    
    /**
     * Cria as tabelas necessárias
     */
    private static function createTables() {
        try {
            Cliente::createTable();
        } catch (Exception $e) {
            System::log("Erro ao criar tabela de clientes: " . $e->getMessage(), "error");
        }
    }
}

// Inicializa o plugin
ClientesPlugin::init();

?>

