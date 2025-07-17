<?php

// Página para editar cliente
require_once __DIR__ . '/../client-form.php';

$errors = [];
$success = false;
$client = null;

// Obter ID do cliente
$clientId = $_GET['id'] ?? $_POST['id'] ?? null;

if (!$clientId) {
    header('Location: /clientes');
    exit;
}

// Buscar cliente
$client = ClientManager::getClient($clientId);

if (!$client) {
    header('Location: /clientes?error=not_found');
    exit;
}

// Processar formulário se for POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'name' => $_POST['name'] ?? '',
        'email' => $_POST['email'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'company' => $_POST['company'] ?? '',
        'address' => $_POST['address'] ?? '',
        'city' => $_POST['city'] ?? '',
        'state' => $_POST['state'] ?? '',
        'zip_code' => $_POST['zip_code'] ?? '',
        'country' => $_POST['country'] ?? 'Brasil',
        'status' => $_POST['status'] ?? 'active',
        'notes' => $_POST['notes'] ?? '',
        'id' => $clientId // Para validação de email único
    ];
    
    // Validar dados
    $errors = ClientManager::validateClient($data);
    
    if (empty($errors)) {
        $updatedClient = ClientManager::updateClient($clientId, $data);
        
        if ($updatedClient) {
            $success = true;
            $client = $updatedClient; // Atualizar dados do cliente
            // Redirecionar para lista de clientes com mensagem de sucesso
            header('Location: /clientes?updated=1');
            exit;
        } else {
            $errors[] = 'Erro interno ao atualizar cliente. Tente novamente.';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente - CRM</title>
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
                        <h1 class="text-3xl font-bold text-gray-900">Editar Cliente</h1>
                        <p class="mt-2 text-gray-600">Editando: <?php echo htmlspecialchars($client['name']); ?></p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="/clientes" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Voltar
                        </a>
                        <button onclick="deleteClient(<?php echo $client['id']; ?>)" 
                                class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500">
                            <i class="fas fa-trash mr-2"></i>
                            Excluir
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Formulário -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Dados do Cliente</h2>
                    <p class="text-gray-600">Atualize as informações do cliente abaixo</p>
                </div>
                
                <?php if (!empty($errors)): ?>
                    <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Erro ao atualizar cliente</h3>
                                <div class="mt-2 text-sm text-red-700">
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
                
                <form action="/clientes/atualizar/<?php echo $client['id']; ?>" method="POST" class="space-y-6">
                    <!-- Informações Básicas -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nome Completo <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="<?php echo htmlspecialchars($_POST['name'] ?? $client['name']); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   required>
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? $client['email']); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   required>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Telefone
                            </label>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   value="<?php echo htmlspecialchars($_POST['phone'] ?? $client['phone']); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="(11) 99999-9999">
                        </div>
                        
                        <div>
                            <label for="company" class="block text-sm font-medium text-gray-700 mb-2">
                                Empresa
                            </label>
                            <input type="text" 
                                   id="company" 
                                   name="company" 
                                   value="<?php echo htmlspecialchars($_POST['company'] ?? $client['company']); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status
                        </label>
                        <select id="status" 
                                name="status" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="active" <?php echo ($_POST['status'] ?? $client['status']) === 'active' ? 'selected' : ''; ?>>Ativo</option>
                            <option value="inactive" <?php echo ($_POST['status'] ?? $client['status']) === 'inactive' ? 'selected' : ''; ?>>Inativo</option>
                            <option value="prospect" <?php echo ($_POST['status'] ?? $client['status']) === 'prospect' ? 'selected' : ''; ?>>Prospect</option>
                            <option value="lead" <?php echo ($_POST['status'] ?? $client['status']) === 'lead' ? 'selected' : ''; ?>>Lead</option>
                        </select>
                    </div>
                    
                    <!-- Endereço -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900 border-b border-gray-200 pb-2">Endereço</h3>
                        
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                Endereço Completo
                            </label>
                            <textarea id="address" 
                                      name="address" 
                                      rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                      placeholder="Rua, número, bairro..."><?php echo htmlspecialchars($_POST['address'] ?? $client['address']); ?></textarea>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                    Cidade
                                </label>
                                <input type="text" 
                                       id="city" 
                                       name="city" 
                                       value="<?php echo htmlspecialchars($_POST['city'] ?? $client['city']); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            
                            <div>
                                <label for="state" class="block text-sm font-medium text-gray-700 mb-2">
                                    Estado
                                </label>
                                <input type="text" 
                                       id="state" 
                                       name="state" 
                                       value="<?php echo htmlspecialchars($_POST['state'] ?? $client['state']); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            
                            <div>
                                <label for="zip_code" class="block text-sm font-medium text-gray-700 mb-2">
                                    CEP
                                </label>
                                <input type="text" 
                                       id="zip_code" 
                                       name="zip_code" 
                                       value="<?php echo htmlspecialchars($_POST['zip_code'] ?? $client['zip_code']); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="00000-000">
                            </div>
                        </div>
                        
                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 mb-2">
                                País
                            </label>
                            <input type="text" 
                                   id="country" 
                                   name="country" 
                                   value="<?php echo htmlspecialchars($_POST['country'] ?? $client['country']); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <!-- Observações -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Observações
                        </label>
                        <textarea id="notes" 
                                  name="notes" 
                                  rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Informações adicionais sobre o cliente..."><?php echo htmlspecialchars($_POST['notes'] ?? $client['notes']); ?></textarea>
                    </div>
                    
                    <!-- Informações do Sistema -->
                    <div class="bg-gray-50 p-4 rounded-md">
                        <h3 class="text-sm font-medium text-gray-900 mb-2">Informações do Sistema</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                            <div>
                                <span class="font-medium">Criado em:</span> 
                                <?php echo date('d/m/Y H:i', strtotime($client['created_at'])); ?>
                            </div>
                            <div>
                                <span class="font-medium">Atualizado em:</span> 
                                <?php echo date('d/m/Y H:i', strtotime($client['updated_at'])); ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Botões -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="/clientes" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">Cancelar</a>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
