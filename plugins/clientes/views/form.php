<?php
/**
 * View: Formulário de Cliente
 * 
 * Formulário para criar ou editar um cliente
 */

$isEdit = isset($cliente->id) && $cliente->id;
$title = $isEdit ? 'Editar Cliente' : 'Novo Cliente';
$submitUrl = $isEdit ? "/admin/clientes/atualizar/{$cliente->id}" : '/admin/clientes/salvar';
?>

<div class="container mx-auto my-8 px-4">
    <!-- Cabeçalho da página -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900"><?= $title ?></h1>
            <p class="text-gray-600 mt-1">
                <?= $isEdit ? 'Atualize as informações do cliente' : 'Adicione um novo cliente ao sistema' ?>
            </p>
        </div>
        <div class="flex space-x-3">
            <a href="/admin/clientes" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Voltar
            </a>
        </div>
    </div>

    <!-- Exibir erros de validação -->
    <?php if (!empty($errors)): ?>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        Corrija os seguintes erros:
                    </h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc list-inside space-y-1">
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Formulário -->
    <div class="bg-white shadow rounded-lg">
        <form method="POST" action="<?= $submitUrl ?>" class="space-y-6">
            <div class="px-6 py-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nome -->
                    <div class="md:col-span-2">
                        <label for="nome" class="block text-sm font-medium text-gray-700 mb-2">
                            Nome <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="nome" 
                               name="nome" 
                               value="<?= htmlspecialchars($cliente->nome ?? '') ?>"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Digite o nome completo do cliente">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="<?= htmlspecialchars($cliente->email ?? '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="cliente@exemplo.com">
                    </div>

                    <!-- Telefone -->
                    <div>
                        <label for="telefone" class="block text-sm font-medium text-gray-700 mb-2">
                            Telefone
                        </label>
                        <input type="tel" 
                               id="telefone" 
                               name="telefone" 
                               value="<?= htmlspecialchars($cliente->telefone ?? '') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="(11) 99999-9999">
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select id="status" 
                                name="status" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <?php foreach ($statusOptions as $value => $label): ?>
                                <option value="<?= $value ?>" <?= ($cliente->status ?? Cliente::STATUS_PROSPECTO) === $value ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Observações -->
                    <div class="md:col-span-2">
                        <label for="observacoes" class="block text-sm font-medium text-gray-700 mb-2">
                            Observações
                        </label>
                        <textarea id="observacoes" 
                                  name="observacoes" 
                                  rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="Adicione observações sobre o cliente..."><?= htmlspecialchars($cliente->observacoes ?? '') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Rodapé do formulário -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                <div class="flex justify-end space-x-3">
                    <a href="/admin/clientes" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        <?= $isEdit ? 'Atualizar Cliente' : 'Salvar Cliente' ?>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Informações adicionais para edição -->
    <?php if ($isEdit): ?>
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">
                        Informações do Cliente
                    </h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p><strong>ID:</strong> <?= $cliente->id ?></p>
                        <p><strong>Criado em:</strong> <?= date('d/m/Y H:i', strtotime($cliente->created_at)) ?></p>
                        <?php if ($cliente->updated_at): ?>
                            <p><strong>Última atualização:</strong> <?= date('d/m/Y H:i', strtotime($cliente->updated_at)) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
// Máscara para telefone
document.getElementById('telefone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    
    if (value.length <= 11) {
        if (value.length <= 10) {
            value = value.replace(/(\d{2})(\d{4})(\d{4})/, '($1) $2-$3');
        } else {
            value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
        }
    }
    
    e.target.value = value;
});

// Validação em tempo real
document.getElementById('nome').addEventListener('blur', function(e) {
    const nome = e.target.value.trim();
    if (nome.length < 2) {
        e.target.classList.add('border-red-500');
        showFieldError(e.target, 'Nome deve ter pelo menos 2 caracteres');
    } else {
        e.target.classList.remove('border-red-500');
        hideFieldError(e.target);
    }
});

document.getElementById('email').addEventListener('blur', function(e) {
    const email = e.target.value.trim();
    if (email && !isValidEmail(email)) {
        e.target.classList.add('border-red-500');
        showFieldError(e.target, 'Email deve ter um formato válido');
    } else {
        e.target.classList.remove('border-red-500');
        hideFieldError(e.target);
    }
});

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function showFieldError(field, message) {
    hideFieldError(field);
    const errorDiv = document.createElement('div');
    errorDiv.className = 'text-red-500 text-sm mt-1 field-error';
    errorDiv.textContent = message;
    field.parentNode.appendChild(errorDiv);
}

function hideFieldError(field) {
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
}

// Validação antes do envio
document.querySelector('form').addEventListener('submit', function(e) {
    const nome = document.getElementById('nome').value.trim();
    const email = document.getElementById('email').value.trim();
    
    let hasErrors = false;
    
    if (nome.length < 2) {
        document.getElementById('nome').classList.add('border-red-500');
        showFieldError(document.getElementById('nome'), 'Nome é obrigatório');
        hasErrors = true;
    }
    
    if (email && !isValidEmail(email)) {
        document.getElementById('email').classList.add('border-red-500');
        showFieldError(document.getElementById('email'), 'Email deve ter um formato válido');
        hasErrors = true;
    }
    
    if (hasErrors) {
        e.preventDefault();
        // Scroll para o primeiro erro
        const firstError = document.querySelector('.border-red-500');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstError.focus();
        }
    }
});

// Auto-save (opcional - salva rascunho no localStorage)
const formFields = ['nome', 'email', 'telefone', 'status', 'observacoes'];
const formKey = 'cliente_form_draft_<?= $cliente->id ?? 'new' ?>';

// Carrega rascunho se existir (apenas para novos clientes)
<?php if (!$isEdit): ?>
    const savedDraft = localStorage.getItem(formKey);
    if (savedDraft) {
        const draft = JSON.parse(savedDraft);
        formFields.forEach(field => {
            const element = document.getElementById(field);
            if (element && draft[field]) {
                element.value = draft[field];
            }
        });
    }
<?php endif; ?>

// Salva rascunho automaticamente
formFields.forEach(field => {
    const element = document.getElementById(field);
    if (element) {
        element.addEventListener('input', function() {
            const draft = {};
            formFields.forEach(f => {
                const el = document.getElementById(f);
                if (el) {
                    draft[f] = el.value;
                }
            });
            localStorage.setItem(formKey, JSON.stringify(draft));
        });
    }
});

// Remove rascunho ao enviar o formulário
document.querySelector('form').addEventListener('submit', function() {
    localStorage.removeItem(formKey);
});
</script>

