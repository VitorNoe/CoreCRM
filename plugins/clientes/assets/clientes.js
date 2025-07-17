/**
 * JavaScript para o Plugin de Clientes
 * 
 * Funcionalidades avançadas de busca, filtros e interações
 */

class ClientesManager {
    constructor() {
        this.searchTimeout = null;
        this.currentPage = 1;
        this.isLoading = false;
        this.init();
    }

    init() {
        this.bindEvents();
        this.initializeFilters();
        this.setupAutoSave();
    }

    bindEvents() {
        // Busca em tempo real
        const searchInput = document.getElementById('search');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.handleSearch(e.target.value);
            });
        }

        // Filtro de status
        const statusFilter = document.getElementById('status');
        if (statusFilter) {
            statusFilter.addEventListener('change', (e) => {
                this.handleStatusFilter(e.target.value);
            });
        }

        // Paginação AJAX
        this.bindPaginationEvents();

        // Atalhos de teclado
        this.bindKeyboardShortcuts();

        // Seleção múltipla
        this.bindBulkActions();
    }

    handleSearch(query) {
        clearTimeout(this.searchTimeout);
        
        this.searchTimeout = setTimeout(() => {
            this.performSearch(query);
        }, 300); // Debounce de 300ms
    }

    async performSearch(query) {
        if (this.isLoading) return;

        this.isLoading = true;
        this.showLoadingState();

        try {
            const params = new URLSearchParams({
                q: query,
                status: document.getElementById('status')?.value || '',
                page: 1
            });

            const response = await fetch(`/admin/clientes/buscar?${params}`);
            const data = await response.json();

            this.updateClientesList(data);
            this.updatePagination(data);
            this.updateURL(query, document.getElementById('status')?.value || '');
            
        } catch (error) {
            console.error('Erro na busca:', error);
            this.showError('Erro ao realizar busca. Tente novamente.');
        } finally {
            this.isLoading = false;
            this.hideLoadingState();
        }
    }

    handleStatusFilter(status) {
        const searchQuery = document.getElementById('search')?.value || '';
        this.performFilter(searchQuery, status);
    }

    async performFilter(search, status) {
        if (this.isLoading) return;

        this.isLoading = true;
        this.showLoadingState();

        try {
            const params = new URLSearchParams({
                q: search,
                status: status,
                page: 1
            });

            const response = await fetch(`/admin/clientes/buscar?${params}`);
            const data = await response.json();

            this.updateClientesList(data);
            this.updatePagination(data);
            this.updateURL(search, status);
            
        } catch (error) {
            console.error('Erro no filtro:', error);
            this.showError('Erro ao aplicar filtro. Tente novamente.');
        } finally {
            this.isLoading = false;
            this.hideLoadingState();
        }
    }

    updateClientesList(data) {
        const tbody = document.querySelector('table tbody');
        if (!tbody) return;

        if (data.clientes.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
                            <p class="text-lg font-medium">Nenhum cliente encontrado</p>
                            <p class="text-sm">Tente ajustar os filtros de busca</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = data.clientes.map(cliente => this.renderClienteRow(cliente)).join('');
        
        // Reaplica eventos nos novos elementos
        this.bindRowEvents();
    }

    renderClienteRow(cliente) {
        const statusColors = {
            'ativo': 'bg-green-100 text-green-800',
            'inativo': 'bg-gray-100 text-gray-800',
            'prospecto': 'bg-blue-100 text-blue-800',
            'bloqueado': 'bg-red-100 text-red-800'
        };

        const statusColor = statusColors[cliente.status] || 'bg-gray-100 text-gray-800';
        const createdAt = new Date(cliente.created_at).toLocaleDateString('pt-BR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

        return `
            <tr class="hover:bg-gray-50 transition-colors" data-cliente-id="${cliente.id}">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-user text-blue-600"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">
                                ${this.escapeHtml(cliente.nome)}
                            </div>
                            ${cliente.observacoes ? `
                                <div class="text-sm text-gray-500 truncate max-w-xs" title="${this.escapeHtml(cliente.observacoes)}">
                                    ${this.escapeHtml(cliente.observacoes.substring(0, 50))}${cliente.observacoes.length > 50 ? '...' : ''}
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">
                        ${cliente.email ? `
                            <div class="flex items-center mb-1">
                                <i class="fas fa-envelope text-gray-400 mr-2"></i>
                                <a href="mailto:${this.escapeHtml(cliente.email)}" class="text-blue-600 hover:text-blue-800">
                                    ${this.escapeHtml(cliente.email)}
                                </a>
                            </div>
                        ` : ''}
                        ${cliente.telefone ? `
                            <div class="flex items-center">
                                <i class="fas fa-phone text-gray-400 mr-2"></i>
                                <a href="tel:${this.escapeHtml(cliente.telefone)}" class="text-blue-600 hover:text-blue-800">
                                    ${this.escapeHtml(cliente.telefone)}
                                </a>
                            </div>
                        ` : ''}
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${statusColor}">
                        ${cliente.status_label}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${createdAt}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div class="flex items-center justify-end space-x-2">
                        <a href="/admin/clientes/editar/${cliente.id}" 
                           class="text-blue-600 hover:text-blue-900 p-2 rounded-lg hover:bg-blue-50 transition-colors"
                           title="Editar cliente">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="confirmarExclusao(${cliente.id}, '${this.escapeHtml(cliente.nome)}')"
                                class="text-red-600 hover:text-red-900 p-2 rounded-lg hover:bg-red-50 transition-colors"
                                title="Excluir cliente">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }

    updatePagination(data) {
        const paginationContainer = document.querySelector('.pagination-container');
        if (!paginationContainer || data.totalPages <= 1) {
            if (paginationContainer) paginationContainer.style.display = 'none';
            return;
        }

        paginationContainer.style.display = 'block';
        // Implementar atualização da paginação AJAX aqui
    }

    bindPaginationEvents() {
        document.addEventListener('click', (e) => {
            if (e.target.matches('.pagination-link')) {
                e.preventDefault();
                const page = e.target.dataset.page;
                this.loadPage(page);
            }
        });
    }

    async loadPage(page) {
        if (this.isLoading) return;

        this.isLoading = true;
        this.showLoadingState();

        try {
            const params = new URLSearchParams({
                q: document.getElementById('search')?.value || '',
                status: document.getElementById('status')?.value || '',
                page: page
            });

            const response = await fetch(`/admin/clientes/buscar?${params}`);
            const data = await response.json();

            this.updateClientesList(data);
            this.updatePagination(data);
            this.currentPage = parseInt(page);
            
        } catch (error) {
            console.error('Erro ao carregar página:', error);
            this.showError('Erro ao carregar página. Tente novamente.');
        } finally {
            this.isLoading = false;
            this.hideLoadingState();
        }
    }

    bindKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + K para focar na busca
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                const searchInput = document.getElementById('search');
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
            }

            // Ctrl/Cmd + N para novo cliente
            if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
                e.preventDefault();
                window.location.href = '/admin/clientes/novo';
            }

            // ESC para limpar busca
            if (e.key === 'Escape') {
                const searchInput = document.getElementById('search');
                if (searchInput && searchInput.value) {
                    searchInput.value = '';
                    this.handleSearch('');
                }
            }
        });
    }

    bindBulkActions() {
        // Implementar seleção múltipla e ações em lote
        const selectAllCheckbox = document.getElementById('select-all');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', (e) => {
                const checkboxes = document.querySelectorAll('.cliente-checkbox');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = e.target.checked;
                });
                this.updateBulkActionsVisibility();
            });
        }

        document.addEventListener('change', (e) => {
            if (e.target.matches('.cliente-checkbox')) {
                this.updateBulkActionsVisibility();
            }
        });
    }

    updateBulkActionsVisibility() {
        const selectedCheckboxes = document.querySelectorAll('.cliente-checkbox:checked');
        const bulkActions = document.getElementById('bulk-actions');
        
        if (bulkActions) {
            bulkActions.style.display = selectedCheckboxes.length > 0 ? 'block' : 'none';
        }
    }

    bindRowEvents() {
        // Adiciona eventos de hover e clique nas linhas
        document.querySelectorAll('table tbody tr').forEach(row => {
            row.addEventListener('dblclick', () => {
                const clienteId = row.dataset.clienteId;
                if (clienteId) {
                    window.location.href = `/admin/clientes/editar/${clienteId}`;
                }
            });
        });
    }

    setupAutoSave() {
        // Auto-save para formulários
        const form = document.querySelector('form');
        if (form) {
            const inputs = form.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                input.addEventListener('input', () => {
                    this.saveFormDraft(form);
                });
            });
        }
    }

    saveFormDraft(form) {
        const formData = new FormData(form);
        const data = {};
        
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }
        
        const formId = form.dataset.formId || 'cliente-form';
        localStorage.setItem(`draft_${formId}`, JSON.stringify(data));
    }

    loadFormDraft(formId) {
        const draft = localStorage.getItem(`draft_${formId}`);
        if (draft) {
            const data = JSON.parse(draft);
            Object.keys(data).forEach(key => {
                const input = document.querySelector(`[name="${key}"]`);
                if (input) {
                    input.value = data[key];
                }
            });
        }
    }

    clearFormDraft(formId) {
        localStorage.removeItem(`draft_${formId}`);
    }

    showLoadingState() {
        const loadingOverlay = document.getElementById('loading-overlay');
        if (loadingOverlay) {
            loadingOverlay.classList.remove('hidden');
        }

        // Adiciona classe de loading na tabela
        const table = document.querySelector('table');
        if (table) {
            table.classList.add('opacity-50', 'pointer-events-none');
        }
    }

    hideLoadingState() {
        const loadingOverlay = document.getElementById('loading-overlay');
        if (loadingOverlay) {
            loadingOverlay.classList.add('hidden');
        }

        // Remove classe de loading da tabela
        const table = document.querySelector('table');
        if (table) {
            table.classList.remove('opacity-50', 'pointer-events-none');
        }
    }

    showError(message) {
        // Cria ou atualiza notificação de erro
        let errorNotification = document.getElementById('error-notification');
        
        if (!errorNotification) {
            errorNotification = document.createElement('div');
            errorNotification.id = 'error-notification';
            errorNotification.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform';
            document.body.appendChild(errorNotification);
        }

        errorNotification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.classList.add('translate-x-full')" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        // Mostra a notificação
        setTimeout(() => {
            errorNotification.classList.remove('translate-x-full');
        }, 100);

        // Auto-hide após 5 segundos
        setTimeout(() => {
            errorNotification.classList.add('translate-x-full');
        }, 5000);
    }

    showSuccess(message) {
        // Similar ao showError, mas com estilo de sucesso
        let successNotification = document.getElementById('success-notification');
        
        if (!successNotification) {
            successNotification = document.createElement('div');
            successNotification.id = 'success-notification';
            successNotification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform';
            document.body.appendChild(successNotification);
        }

        successNotification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.classList.add('translate-x-full')" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        setTimeout(() => {
            successNotification.classList.remove('translate-x-full');
        }, 100);

        setTimeout(() => {
            successNotification.classList.add('translate-x-full');
        }, 3000);
    }

    updateURL(search, status) {
        const url = new URL(window.location);
        
        if (search) {
            url.searchParams.set('search', search);
        } else {
            url.searchParams.delete('search');
        }
        
        if (status) {
            url.searchParams.set('status', status);
        } else {
            url.searchParams.delete('status');
        }
        
        url.searchParams.delete('page'); // Reset page to 1
        
        window.history.replaceState({}, '', url);
    }

    initializeFilters() {
        // Inicializa filtros baseados na URL
        const urlParams = new URLSearchParams(window.location.search);
        const search = urlParams.get('search');
        const status = urlParams.get('status');

        if (search) {
            const searchInput = document.getElementById('search');
            if (searchInput) searchInput.value = search;
        }

        if (status) {
            const statusSelect = document.getElementById('status');
            if (statusSelect) statusSelect.value = status;
        }
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Métodos utilitários para exportação
    async exportClientes(format = 'csv') {
        try {
            const params = new URLSearchParams({
                format: format,
                search: document.getElementById('search')?.value || '',
                status: document.getElementById('status')?.value || ''
            });

            const response = await fetch(`/admin/clientes/exportar?${params}`);
            
            if (response.ok) {
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `clientes_${new Date().toISOString().split('T')[0]}.${format}`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
                
                this.showSuccess('Exportação realizada com sucesso!');
            } else {
                throw new Error('Erro na exportação');
            }
        } catch (error) {
            console.error('Erro na exportação:', error);
            this.showError('Erro ao exportar clientes. Tente novamente.');
        }
    }
}

// Inicializa o gerenciador quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    window.clientesManager = new ClientesManager();
});

// Funções globais para compatibilidade
function confirmarExclusao(id, nome) {
    document.getElementById('nomeClienteExclusao').textContent = nome;
    document.getElementById('formExclusao').action = '/admin/clientes/excluir/' + id;
    document.getElementById('modalExclusao').classList.remove('hidden');
}

function fecharModalExclusao() {
    document.getElementById('modalExclusao').classList.add('hidden');
}

