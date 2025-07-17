# Plugin de Clientes - CoreCRM

## Visão Geral

O Plugin de Clientes é uma extensão completa para o sistema CoreCRM que fornece funcionalidades avançadas de gerenciamento de clientes. Este plugin oferece uma interface administrativa moderna e intuitiva para realizar operações CRUD (Create, Read, Update, Delete) em registros de clientes, incluindo recursos de busca, filtros, paginação e validação de dados.

## Características Principais

### Funcionalidades de Back-end
- **Modelo de Dados Robusto**: Estrutura de tabela otimizada com índices para melhor performance
- **Validação de Dados**: Sistema completo de validação com mensagens de erro personalizadas
- **API RESTful**: Endpoints JSON para integração com outras aplicações
- **Segurança**: Proteção contra SQL injection e validação de entrada
- **Logs de Sistema**: Registro detalhado de todas as operações

### Funcionalidades de Front-end
- **Interface Responsiva**: Design adaptável para desktop e dispositivos móveis
- **Busca em Tempo Real**: Sistema de busca instantânea com debounce
- **Filtros Avançados**: Filtros por status com atualização dinâmica
- **Paginação AJAX**: Navegação fluida sem recarregamento de página
- **Modais de Confirmação**: Confirmações elegantes para ações destrutivas
- **Notificações**: Sistema de feedback visual para o usuário

### Recursos Avançados
- **Auto-save**: Salvamento automático de rascunhos de formulários
- **Atalhos de Teclado**: Navegação rápida via teclado
- **Exportação de Dados**: Funcionalidade para exportar lista de clientes
- **Estatísticas em Tempo Real**: Dashboard com métricas atualizadas
- **Acessibilidade**: Conformidade com padrões de acessibilidade web

## Estrutura do Plugin

```
plugins/clientes/
├── main.php                 # Arquivo principal do plugin
├── plugin.json             # Configurações e metadados
├── models/
│   └── Cliente.php         # Modelo de dados
├── controllers/
│   └── ClienteController.php # Controlador principal
├── views/
│   ├── index.php           # Lista de clientes
│   └── form.php            # Formulário de cliente
├── assets/
│   ├── clientes.js         # JavaScript funcional
│   └── clientes.css        # Estilos customizados
└── README.md               # Esta documentação
```

## Instalação

### Pré-requisitos
- CoreCRM instalado e funcionando
- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Extensões PHP: PDO, PDO_MySQL

### Passos de Instalação

1. **Copiar arquivos do plugin**:
   ```bash
   cp -r clientes/ /caminho/para/CoreCRM/plugins/
   ```

2. **Ativar o plugin**:
   - Acesse o painel administrativo do CoreCRM
   - Vá para "Plugins" > "Gerenciar Plugins"
   - Localize "Plugin de Clientes" e clique em "Ativar"

3. **Verificar instalação**:
   - A tabela `clientes` será criada automaticamente
   - O menu "Clientes" aparecerá na barra lateral administrativa
   - Acesse `/admin/clientes` para verificar o funcionamento

## Configuração

### Configurações de Banco de Dados

O plugin utiliza as configurações de banco de dados do CoreCRM. A tabela `clientes` é criada automaticamente com a seguinte estrutura:

```sql
CREATE TABLE clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE,
    telefone VARCHAR(20),
    status ENUM('ativo', 'inativo', 'prospecto', 'bloqueado') DEFAULT 'prospecto',
    observacoes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nome (nome),
    INDEX idx_email (email),
    INDEX idx_status (status)
);
```

### Configurações de Status

Os status disponíveis para clientes são:
- **Prospecto**: Cliente em potencial (padrão)
- **Ativo**: Cliente ativo no sistema
- **Inativo**: Cliente temporariamente inativo
- **Bloqueado**: Cliente bloqueado por algum motivo

## Uso

### Gerenciamento de Clientes

#### Listar Clientes
- Acesse `/admin/clientes` para ver a lista completa
- Use a barra de busca para encontrar clientes específicos
- Filtre por status usando o dropdown
- Navegue pelas páginas usando a paginação

#### Adicionar Cliente
1. Clique em "Novo Cliente" na lista de clientes
2. Preencha os campos obrigatórios (nome e status)
3. Adicione informações opcionais (email, telefone, observações)
4. Clique em "Salvar Cliente"

#### Editar Cliente
1. Na lista de clientes, clique no ícone de edição
2. Modifique as informações necessárias
3. Clique em "Atualizar Cliente"

#### Excluir Cliente
1. Na lista de clientes, clique no ícone de lixeira
2. Confirme a exclusão no modal que aparece
3. O cliente será removido permanentemente

### API REST

O plugin fornece endpoints JSON para integração:

#### Listar Clientes
```
GET /api/clientes
Parâmetros:
- page: número da página (padrão: 1)
- per_page: itens por página (padrão: 20)
- search: termo de busca
- status: filtro por status
```

#### Busca AJAX
```
GET /admin/clientes/buscar
Parâmetros:
- q: termo de busca
- status: filtro por status
- page: número da página
```

### Atalhos de Teclado

- **Ctrl/Cmd + K**: Focar na barra de busca
- **Ctrl/Cmd + N**: Criar novo cliente
- **ESC**: Limpar busca ou fechar modais

## Personalização

### Modificar Status

Para adicionar novos status, edite o arquivo `models/Cliente.php`:

```php
// Adicione novas constantes
const STATUS_NOVO_STATUS = 'novo_status';

// Atualize o método getStatusOptions()
public static function getStatusOptions() {
    return [
        // ... status existentes
        self::STATUS_NOVO_STATUS => 'Novo Status'
    ];
}

// Atualize o método getStatusColor()
public function getStatusColor() {
    switch ($this->status) {
        // ... casos existentes
        case self::STATUS_NOVO_STATUS:
            return 'purple';
    }
}
```

### Personalizar Campos

Para adicionar novos campos à tabela de clientes:

1. **Modificar a tabela**:
   ```sql
   ALTER TABLE clientes ADD COLUMN novo_campo VARCHAR(255);
   ```

2. **Atualizar o modelo** (`models/Cliente.php`):
   ```php
   public $novo_campo;
   
   public function fill($data) {
       // ... código existente
       $this->novo_campo = $data['novo_campo'] ?? '';
   }
   ```

3. **Atualizar o controlador** (`controllers/ClienteController.php`):
   ```php
   $cliente->novo_campo = $_POST['novo_campo'] ?? '';
   ```

4. **Atualizar as views** (`views/form.php`):
   ```html
   <input type="text" name="novo_campo" value="<?= htmlspecialchars($cliente->novo_campo ?? '') ?>">
   ```

### Personalizar Estilos

Modifique o arquivo `assets/clientes.css` para personalizar a aparência:

```css
/* Exemplo: Alterar cor do tema */
.btn-primary {
    background-color: #your-color;
}

/* Exemplo: Personalizar cards de estatísticas */
.stats-card {
    border-left: 4px solid #your-accent-color;
}
```

## Desenvolvimento

### Estrutura do Código

#### Modelo (Cliente.php)
- Gerencia a interação com o banco de dados
- Implementa validações de dados
- Fornece métodos estáticos para consultas

#### Controlador (ClienteController.php)
- Processa requisições HTTP
- Coordena entre modelo e view
- Gerencia sessões e redirecionamentos

#### Views
- **index.php**: Lista paginada com busca e filtros
- **form.php**: Formulário unificado para criar/editar

#### Assets
- **clientes.js**: Funcionalidades JavaScript avançadas
- **clientes.css**: Estilos customizados e responsivos

### Padrões de Código

- **PSR-4**: Autoloading de classes
- **MVC**: Separação clara de responsabilidades
- **RESTful**: Endpoints seguem convenções REST
- **Responsive**: Design mobile-first
- **Accessible**: Conformidade com WCAG 2.1

### Testes

Para testar o plugin:

1. **Testes Manuais**:
   - Criar, editar e excluir clientes
   - Testar busca e filtros
   - Verificar paginação
   - Testar responsividade

2. **Testes de API**:
   ```bash
   # Listar clientes
   curl -X GET "http://seu-site.com/api/clientes"
   
   # Buscar clientes
   curl -X GET "http://seu-site.com/admin/clientes/buscar?q=joão"
   ```

## Solução de Problemas

### Problemas Comuns

#### Plugin não aparece no menu
- Verifique se o arquivo `plugin.json` está correto
- Confirme que o plugin está ativado no painel administrativo
- Verifique logs do sistema para erros

#### Erro de banco de dados
- Confirme que as credenciais de banco estão corretas
- Verifique se o usuário tem permissões para criar tabelas
- Execute manualmente o SQL de criação da tabela

#### JavaScript não funciona
- Verifique se os arquivos CSS e JS estão sendo carregados
- Abra o console do navegador para ver erros
- Confirme que jQuery está disponível (se necessário)

#### Problemas de performance
- Adicione índices nas colunas mais consultadas
- Implemente cache para consultas frequentes
- Otimize consultas SQL complexas

### Logs e Debugging

O plugin registra atividades no sistema de logs do CoreCRM:

```php
// Exemplo de log personalizado
System::log("Ação personalizada executada", "info");
```

Para habilitar logs de debug, modifique a configuração do CoreCRM:

```php
$config["debug"] = true;
```

## Segurança

### Medidas Implementadas

- **Prepared Statements**: Proteção contra SQL injection
- **Validação de Entrada**: Sanitização de dados do usuário
- **CSRF Protection**: Tokens para formulários (se implementado no CoreCRM)
- **Escape de Output**: Prevenção de XSS
- **Validação de Permissões**: Verificação de acesso administrativo

### Recomendações

1. **Backup Regular**: Faça backup da tabela `clientes` regularmente
2. **Atualizações**: Mantenha o plugin atualizado
3. **Monitoramento**: Monitore logs para atividades suspeitas
4. **Permissões**: Configure permissões adequadas no servidor

## Contribuição

### Como Contribuir

1. **Fork** o repositório
2. **Crie** uma branch para sua feature (`git checkout -b feature/nova-funcionalidade`)
3. **Commit** suas mudanças (`git commit -am 'Adiciona nova funcionalidade'`)
4. **Push** para a branch (`git push origin feature/nova-funcionalidade`)
5. **Abra** um Pull Request

### Padrões de Contribuição

- Siga os padrões de código existentes
- Adicione testes para novas funcionalidades
- Atualize a documentação conforme necessário
- Use mensagens de commit descritivas

## Changelog

### Versão 1.0.0 (Atual)
- Implementação inicial do plugin
- CRUD completo de clientes
- Interface administrativa responsiva
- Sistema de busca e filtros
- Paginação AJAX
- Validação de dados
- API REST básica

### Próximas Versões (Roadmap)
- Importação/Exportação CSV
- Integração com WhatsApp
- Histórico de interações
- Relatórios avançados
- Dashboard personalizado
- Integração com email marketing

## Suporte

Para suporte técnico:

1. **Documentação**: Consulte esta documentação primeiro
2. **Issues**: Abra uma issue no repositório do projeto
3. **Fórum**: Participe do fórum da comunidade CoreCRM
4. **Email**: Entre em contato com a equipe de desenvolvimento

## Licença

Este plugin é distribuído sob a mesma licença do CoreCRM. Consulte o arquivo LICENSE para mais detalhes.

## Créditos

- **Desenvolvimento**: Equipe CoreCRM
- **Design**: Baseado no tema padrão do CoreCRM
- **Ícones**: Font Awesome
- **Framework CSS**: Tailwind CSS

---

**Versão**: 1.0.0  
**Última Atualização**: <?= date('d/m/Y') ?>  
**Compatibilidade**: CoreCRM 1.0+

