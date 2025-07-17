<?php

// Componente para formulário de cliente
function renderClientForm($client = null, $errors = []) {
    $isEdit = !empty($client);
    $action = $isEdit ? "/clientes/atualizar/{$client['id']}" : "/clientes/criar";
    $title = $isEdit ? "Editar Cliente" : "Novo Cliente";
    $submitLabel = $isEdit ? "Atualizar Cliente" : "Criar Cliente";
    
    ob_start();
    ?>
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-2"><?php echo $title; ?></h2>
                <p class="text-gray-600">Preencha os dados do cliente abaixo</p>
            </div>
            
            <?php if (!empty($errors)): ?>
                <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Erro ao salvar cliente</h3>
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
            
            <form action="<?php echo $action; ?>" method="POST" class="space-y-6">
                <!-- Informações Básicas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nome Completo <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="<?php echo htmlspecialchars($client['name'] ?? ''); ?>"
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
                               value="<?php echo htmlspecialchars($client['email'] ?? ''); ?>"
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
                               value="<?php echo htmlspecialchars($client['phone'] ?? ''); ?>"
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
                               value="<?php echo htmlspecialchars($client['company'] ?? ''); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
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
                                  placeholder="Rua, número, bairro..."><?php echo htmlspecialchars($client['address'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                Cidade
                            </label>
                            <input type="text" 
                                   id="city" 
                                   name="city" 
                                   value="<?php echo htmlspecialchars($client['city'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700 mb-2">
                                Estado
                            </label>
                            <input type="text" 
                                   id="state" 
                                   name="state" 
                                   value="<?php echo htmlspecialchars($client['state'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="zip_code" class="block text-sm font-medium text-gray-700 mb-2">
                                CEP
                            </label>
                            <input type="text" 
                                   id="zip_code" 
                                   name="zip_code" 
                                   value="<?php echo htmlspecialchars($client['zip_code'] ?? ''); ?>"
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
                               value="<?php echo htmlspecialchars($client['country'] ?? 'Brasil'); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none focus:bg-blue-600">
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
    <?php
    return ob_get_clean();
}
