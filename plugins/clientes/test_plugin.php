<?php
/**
 * Script de Teste Básico - Plugin de Clientes
 * 
 * Execute este script para testar as funcionalidades básicas do plugin
 */

// Inclui o bootstrap do CoreCRM
require_once __DIR__ . '/../../bootstrap.php';

echo "=== TESTE DO PLUGIN DE CLIENTES ===\n\n";

// Teste 1: Verificar se as classes estão carregadas
echo "1. Testando carregamento de classes...\n";
try {
    require_once __DIR__ . '/models/Cliente.php';
    require_once __DIR__ . '/controllers/ClienteController.php';
    echo "✓ Classes carregadas com sucesso\n\n";
} catch (Exception $e) {
    echo "✗ Erro ao carregar classes: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Teste 2: Verificar conexão com banco de dados
echo "2. Testando conexão com banco de dados...\n";
try {
    $connection = DatabaseHandler::getConnection();
    if ($connection) {
        echo "✓ Conexão com banco estabelecida\n\n";
    } else {
        echo "✗ Falha na conexão com banco\n\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "✗ Erro na conexão: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Teste 3: Criar tabela de clientes
echo "3. Testando criação da tabela clientes...\n";
try {
    Cliente::createTable();
    echo "✓ Tabela clientes criada/verificada\n\n";
} catch (Exception $e) {
    echo "✗ Erro ao criar tabela: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Teste 4: Testar CRUD básico
echo "4. Testando operações CRUD...\n";

// Criar cliente de teste
try {
    $cliente = new Cliente();
    $cliente->nome = 'Cliente Teste';
    $cliente->email = 'teste@exemplo.com';
    $cliente->telefone = '(11) 99999-9999';
    $cliente->status = Cliente::STATUS_PROSPECTO;
    $cliente->observacoes = 'Cliente criado durante teste automatizado';
    
    if ($cliente->save()) {
        echo "✓ Cliente criado com sucesso (ID: {$cliente->id})\n";
        
        // Buscar cliente
        $clienteEncontrado = Cliente::find($cliente->id);
        if ($clienteEncontrado && $clienteEncontrado->nome === 'Cliente Teste') {
            echo "✓ Cliente encontrado com sucesso\n";
            
            // Atualizar cliente
            $clienteEncontrado->nome = 'Cliente Teste Atualizado';
            if ($clienteEncontrado->save()) {
                echo "✓ Cliente atualizado com sucesso\n";
                
                // Excluir cliente
                if ($clienteEncontrado->delete()) {
                    echo "✓ Cliente excluído com sucesso\n";
                } else {
                    echo "✗ Erro ao excluir cliente\n";
                }
            } else {
                echo "✗ Erro ao atualizar cliente\n";
            }
        } else {
            echo "✗ Erro ao buscar cliente\n";
        }
    } else {
        echo "✗ Erro ao criar cliente\n";
    }
} catch (Exception $e) {
    echo "✗ Erro durante teste CRUD: " . $e->getMessage() . "\n";
}

echo "\n";

// Teste 5: Testar validações
echo "5. Testando validações...\n";
try {
    $clienteInvalido = new Cliente();
    $clienteInvalido->nome = ''; // Nome vazio (inválido)
    $clienteInvalido->email = 'email-invalido'; // Email inválido
    
    $errors = $clienteInvalido->validate();
    if (!empty($errors)) {
        echo "✓ Validações funcionando corretamente\n";
        echo "  Erros encontrados: " . implode(', ', $errors) . "\n";
    } else {
        echo "✗ Validações não estão funcionando\n";
    }
} catch (Exception $e) {
    echo "✗ Erro durante teste de validação: " . $e->getMessage() . "\n";
}

echo "\n";

// Teste 6: Testar métodos estáticos
echo "6. Testando métodos estáticos...\n";
try {
    $total = Cliente::count();
    echo "✓ Total de clientes: {$total}\n";
    
    $statusOptions = Cliente::getStatusOptions();
    echo "✓ Opções de status: " . implode(', ', array_keys($statusOptions)) . "\n";
    
    $clientesAtivos = Cliente::countByStatus(Cliente::STATUS_ATIVO);
    echo "✓ Clientes ativos: {$clientesAtivos}\n";
} catch (Exception $e) {
    echo "✗ Erro durante teste de métodos estáticos: " . $e->getMessage() . "\n";
}

echo "\n";

// Teste 7: Testar busca e filtros
echo "7. Testando busca e filtros...\n";
try {
    // Criar alguns clientes de teste para busca
    $clientes_teste = [
        ['nome' => 'João Silva', 'email' => 'joao@teste.com', 'status' => Cliente::STATUS_ATIVO],
        ['nome' => 'Maria Santos', 'email' => 'maria@teste.com', 'status' => Cliente::STATUS_PROSPECTO],
        ['nome' => 'Pedro Oliveira', 'email' => 'pedro@teste.com', 'status' => Cliente::STATUS_INATIVO]
    ];
    
    $ids_criados = [];
    foreach ($clientes_teste as $dados) {
        $cliente = new Cliente();
        $cliente->fill($dados);
        if ($cliente->save()) {
            $ids_criados[] = $cliente->id;
        }
    }
    
    // Testar busca
    $resultados = Cliente::all(1, 10, 'João');
    if (count($resultados) > 0 && $resultados[0]->nome === 'João Silva') {
        echo "✓ Busca por nome funcionando\n";
    } else {
        echo "✗ Busca por nome não funcionou\n";
    }
    
    // Testar filtro por status
    $resultados = Cliente::all(1, 10, '', Cliente::STATUS_ATIVO);
    $encontrou_ativo = false;
    foreach ($resultados as $cliente) {
        if ($cliente->status === Cliente::STATUS_ATIVO) {
            $encontrou_ativo = true;
            break;
        }
    }
    
    if ($encontrou_ativo) {
        echo "✓ Filtro por status funcionando\n";
    } else {
        echo "✗ Filtro por status não funcionou\n";
    }
    
    // Limpar clientes de teste
    foreach ($ids_criados as $id) {
        Cliente::destroy($id);
    }
    echo "✓ Clientes de teste removidos\n";
    
} catch (Exception $e) {
    echo "✗ Erro durante teste de busca: " . $e->getMessage() . "\n";
}

echo "\n=== TESTE CONCLUÍDO ===\n";
echo "Execute este script sempre que fizer alterações no plugin.\n";
