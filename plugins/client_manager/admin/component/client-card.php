<?php

// Componente para exibir um card de cliente
function renderClientCard($client) {
    $statusColors = [
        'active' => 'bg-green-100 text-green-800',
        'inactive' => 'bg-gray-100 text-gray-800',
        'prospect' => 'bg-blue-100 text-blue-800',
        'lead' => 'bg-yellow-100 text-yellow-800'
    ];
    
    $statusLabels = [
        'active' => 'Ativo',
        'inactive' => 'Inativo',
        'prospect' => 'Prospect',
        'lead' => 'Lead'
    ];
    
    $statusClass = $statusColors[$client['status']] ?? 'bg-gray-100 text-gray-800';
    $statusLabel = $statusLabels[$client['status']] ?? 'Desconhecido';
    
    ob_start();
    ?>
    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-200">
        <div class="flex items-start justify-between mb-4">
            <div class="flex items-center">
                <div class="bg-blue-500 rounded-full w-12 h-12 flex items-center justify-center text-white font-bold text-lg">
                    <?php echo strtoupper(substr($client['name'], 0, 1)); ?>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($client['name']); ?></h3>
                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($client['email']); ?></p>
                </div>
            </div>
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $statusClass; ?>">
                <?php echo $statusLabel; ?>
            </span>
        </div>
        
        <div class="space-y-2 mb-4">
            <?php if ($client['company']): ?>
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-building w-4 h-4 mr-2"></i>
                    <span><?php echo htmlspecialchars($client['company']); ?></span>
                </div>
            <?php endif; ?>
            
            <?php if ($client['phone']): ?>
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-phone w-4 h-4 mr-2"></i>
                    <span><?php echo htmlspecialchars($client['phone']); ?></span>
                </div>
            <?php endif; ?>
            
            <?php if ($client['city'] || $client['state']): ?>
                <div class="flex items-center text-sm text-gray-600">
                    <i class="fas fa-map-marker-alt w-4 h-4 mr-2"></i>
                    <span>
                        <?php 
                        $location = [];
                        if ($client['city']) $location[] = $client['city'];
                        if ($client['state']) $location[] = $client['state'];
                        echo htmlspecialchars(implode(', ', $location));
                        ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if ($client['notes']): ?>
            <div class="mb-4">
                <p class="text-sm text-gray-600 line-clamp-2"><?php echo htmlspecialchars(substr($client['notes'], 0, 100)); ?><?php echo strlen($client['notes']) > 100 ? '...' : ''; ?></p>
            </div>
        <?php endif; ?>
        
        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
            <div class="text-xs text-gray-500">
                Criado em <?php echo date('d/m/Y', strtotime($client['created_at'])); ?>
            </div>
            <div class="flex space-x-2">
                <a href="/clientes/editar/<?php echo $client['id']; ?>" 
                   class="inline-flex items-center px-3 py-1 text-xs font-medium text-blue-600 bg-blue-100 rounded-full hover:bg-blue-200 transition-colors">
                    <i class="fas fa-edit w-3 h-3 mr-1"></i>
                    Editar
                </a>
                <button onclick="deleteClient(<?php echo $client['id']; ?>)" 
                        class="inline-flex items-center px-3 py-1 text-xs font-medium text-red-600 bg-red-100 rounded-full hover:bg-red-200 transition-colors">
                    <i class="fas fa-trash w-3 h-3 mr-1"></i>
                    Excluir
                </button>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// Função para renderizar lista de cards
function renderClientCards($clients) {
    if (empty($clients)) {
        return '<div class="text-center py-12">
                    <i class="fas fa-users text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Nenhum cliente encontrado</h3>
                    <p class="text-gray-500 mb-4">Comece criando seu primeiro cliente</p>
                    <a href="/clientes/novo" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                        <i class="fas fa-plus mr-2"></i>
                        Novo Cliente
                    </a>
                </div>';
    }
    
    $html = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">';
    
    foreach ($clients as $client) {
        $html .= renderClientCard($client);
    }
    
    $html .= '</div>';
    
    return $html;
}

// JavaScript para funcionalidades dos cards
?>
<script>
function deleteClient(clientId) {
    if (confirm('Tem certeza que deseja excluir este cliente? Esta ação não pode ser desfeita.')) {
        fetch('/clientes/deletar/' + clientId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => {
            if (response.ok) {
                location.reload();
            } else {
                alert('Erro ao excluir cliente');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao excluir cliente');
        });
    }
}
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
