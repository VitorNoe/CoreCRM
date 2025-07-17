<?php

// Página de listagem de clientes
$errors = [];
$success = '';

// Verificar mensagens de sucesso
if (isset($_GET['created'])) {
    $success = 'Cliente criado com sucesso!';
}
if (isset($_GET['updated'])) {
    $success = 'Cliente atualizado com sucesso!';
}
if (isset($_GET['deleted'])) {
    $success = 'Cliente excluído com sucesso!';
}
if (isset($_GET['error']) && $_GET['error'] === 'not_found') {
    $errors[] = 'Cliente não encontrado.';
}

// Parâmetros de filtro e busca
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

// Buscar clientes
$clients = ClientManager::getClients($limit, $offset, $search);

// Contar total de clientes para paginação
global $db;
$countSql = "SELECT COUNT(*) FROM clients WHERE deleted_at IS NULL";
$countParams = [];

if ($search) {
    $countSql .= " AND (name LIKE :search OR email LIKE :search OR company LIKE :search)";
    $countParams[':search'] = '%' . $search . '%';
}

if ($status) {
    $countSql .= " AND status = :status";
    $countParams[':status'] = $status;
}

$countStmt = $db->prepare($countSql);
$countStmt->execute($countParams);
$totalClients = $countStmt->fetchColumn();
$totalPages = ceil($totalClients / $limit);

// Função para obter badge de status
function getStatusBadge($status) {
    $badges = [
        'active' => 'bg-green-100 text-green-800',
        'inactive' => 'bg-red-100 text-red-800',
        'prospect' => 'bg-yellow-100 text-yellow-800',
        'lead' => 'bg-blue-100 text-blue-800'
    ];
    
    $labels = [
        'active' => 'Ativo',
        'inactive' => 'Inativo',
        'prospect' => 'Prospect',
        'lead' => 'Lead'
    ];
    
    $class = $badges[$status] ?? 'bg-gray-100 text-gray-800';
    $label = $labels[$status] ?? $status;
    
    return "<span class='px-2 py-1 text-xs font-medium rounded-full {$class}'>{$label}</span>";
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes - CRM</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Clientes</h1>
                        <p class="mt-2 text-gray-600">Gerencie seus clientes de forma eficiente</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="/clientes/novo" 
                           class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <i class="fas fa-plus mr-2"></i>
                            Novo Cliente
                        </a>
                    </div>
                </div>
            </div>

            <!-- Alertas -->
            <?php if ($success): ?>
                <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800"><?php echo htmlspecialchars($success); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <div class="text-sm text-red-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?php echo htmlspecialchars($error); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Filtros e Busca -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <form method="GET" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                                Buscar
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       id="search" 
                                       name="search" 
                                       value="<?php echo htmlspecialchars($search); ?>"
                                       placeholder="Nome, email ou empresa..."
                                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status
                            </label>
                            <select id="status" 
                                    name="status" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Todos os status</option>
                                <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Ativo</option>
                                <option value="inactive" <?php echo $status === 'inactive' ? 'selected' : ''; ?>>Inativo</option>
                                <option value="prospect" <?php echo $status === 'prospect' ? 'selected' : ''; ?>>Prospect</option>
                                <option value="lead" <?php echo $status === 'lead' ? 'selected' : ''; ?>>Lead</option>
                            </select>
                        </div>
                        
                        <div class="flex items-end space-x-2">
                            <button type="submit" 
                                    class="flex-1 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <i class="fas fa-search mr-2"></i>
                                Buscar
                            </button>
                            <a href="/clientes" 
                               class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Estatísticas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-full">
                            <i class="fas fa-users text-blue-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total</p>
                            <p class="text-2xl font-semibold text-gray-900"><?php echo $totalClients; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-full">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Ativos</p>
                            <p class="text-2xl font-semibold text-gray-900">
                                <?php
                                $activeStmt = $db->prepare("SELECT COUNT(*) FROM clients WHERE status = 'active' AND deleted_at IS NULL");
                                $activeStmt->execute();
                                echo $activeStmt->fetchColumn();
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-100 rounded-full">
                            <i class="fas fa-eye text-yellow-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Prospects</p>
                            <p class="text-2xl font-semibold text-gray-900">
                                <?php
                                $prospectStmt = $db->prepare("SELECT COUNT(*) FROM clients WHERE status = 'prospect' AND deleted_at IS NULL");
                                $prospectStmt->execute();
                                echo $prospectStmt->fetchColumn();
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-full">
                            <i class="fas fa-star text-blue-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Leads</p>
                            <p class="text-2xl font-semibold text-gray-900">
                                <?php
                                $leadStmt = $db->prepare("SELECT COUNT(*) FROM clients WHERE status = 'lead' AND deleted_at IS NULL");
                                $leadStmt->execute();
                                echo $leadStmt->fetchColumn();
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabela de Clientes -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Lista de Clientes</h2>
                        <div class="text-sm text-gray-600">
                            <?php echo count($clients); ?> de <?php echo $totalClients; ?> clientes
                        </div>
                    </div>
                </div>

                <?php if (empty($clients)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-users text-gray-400 text-6xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum cliente encontrado</h3>
                        <p class="text-gray-600 mb-4">
                            <?php if ($search): ?>
                                Nenhum cliente encontrado para "<?php echo htmlspecialchars($search); ?>"
                            <?php else: ?>
                                Comece adicionando seu primeiro cliente
                            <?php endif; ?>
                        </p>
                        <a href="/clientes/novo" 
                           class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                            <i class="fas fa-plus mr-2"></i>
                            Novo Cliente
                        </a>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Cliente
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Contato
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Empresa
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Criado em
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($clients as $client): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                                        <span class="text-white font-medium">
                                                            <?php echo strtoupper(substr($client['name'], 0, 1)); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        <?php echo htmlspecialchars($client['name']); ?>
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        ID: <?php echo $client['id']; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                <div class="flex items-center mb-1">
                                                    <i class="fas fa-envelope text-gray-400 mr-2"></i>
                                                    <?php echo htmlspecialchars($client['email']); ?>
                                                </div>
                                                <?php if ($client['phone']): ?>
                                                    <div class="flex items-center">
                                                        <i class="fas fa-phone text-gray-400 mr-2"></i>
                                                        <?php echo htmlspecialchars($client['phone']); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                <?php echo $client['company'] ? htmlspecialchars($client['company']) : '-'; ?>
                                            </div>
                                            <?php if ($client['city']): ?>
                                                <div class="text-sm text-gray-500">
                                                    <?php echo htmlspecialchars($client['city']); ?>
                                                    <?php if ($client['state']): ?>
                                                        , <?php echo htmlspecialchars($client['state']); ?>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php echo getStatusBadge($client['status']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo date('d/m/Y', strtotime($client['created_at'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end space-x-2">
                                                <a href="/clientes/editar/<?php echo $client['id']; ?>" 
                                                   class="text-blue-600 hover:text-blue-900"
                                                   title="Editar cliente">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button onclick="deleteClient(<?php echo $client['id']; ?>)" 
                                                        class="text-red-600 hover:text-red-900"
                                                        title="Excluir cliente">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginação -->
                    <?php if ($totalPages > 1): ?>
                        <div class="bg-white px-6 py-3 border-t border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-700">
                                    Mostrando <span class="font-medium"><?php echo ($offset + 1); ?></span> a 
                                    <span class="font-medium"><?php echo min($offset + $limit, $totalClients); ?></span> de 
                                    <span class="font-medium"><?php echo $totalClients; ?></span> resultados
                                </div>
                                <nav class="flex items-center space-x-2">
                                    <?php if ($page > 1): ?>
                                        <a href="?page=<?php echo ($page - 1); ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>" 
                                           class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                            Anterior
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>" 
                                           class="px-3 py-2 text-sm font-medium <?php echo $i === $page ? 'text-blue-600 bg-blue-50 border-blue-300' : 'text-gray-500 bg-white border-gray-300 hover:bg-gray-50'; ?> border rounded-md">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $totalPages): ?>
                                        <a href="?page=<?php echo ($page + 1); ?>&search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status); ?>" 
                                           class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                                            Próxima
                                        </a>
                                    <?php endif; ?>
                                </nav>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-2">Confirmar Exclusão</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Tem certeza que deseja excluir este cliente? Esta ação não pode ser desfeita.
                    </p>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="confirmDelete" 
                            class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300 mr-2">
                        Excluir
                    </button>
                    <button id="cancelDelete" 
                            class="px-4 py-2 bg-gray-300 text-gray-800 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let clientToDelete = null;
        
        function deleteClient(clientId) {
            clientToDelete = clientId;
            document.getElementById('deleteModal').classList.remove('hidden');
        }
        
        document.getElementById('confirmDelete').addEventListener('click', function() {
            if (clientToDelete) {
                // Criar formulário para enviar POST
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/clientes/deletar/' + clientToDelete;
                document.body.appendChild(form);
                form.submit();
            }
        });
        
        document.getElementById('cancelDelete').addEventListener('click', function() {
            document.getElementById('deleteModal').classList.add('hidden');
            clientToDelete = null;
        });
        
        // Fechar modal ao clicar fora
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
                clientToDelete = null;
            }
        });
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.bg-green-50, .bg-red-50');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            });
        }, 5000);
    </script>
</body>
</html>
