# Guia de Instalação - Plugin de Clientes

## Pré-requisitos

Antes de instalar o Plugin de Clientes, certifique-se de que seu ambiente atende aos seguintes requisitos:

### Requisitos do Sistema
- **PHP**: Versão 7.4 ou superior
- **MySQL**: Versão 5.7 ou superior (ou MariaDB 10.2+)
- **CoreCRM**: Versão 1.0 ou superior instalada e funcionando
- **Extensões PHP**: PDO, PDO_MySQL, mbstring, json

### Verificação do Ambiente

Execute os seguintes comandos para verificar seu ambiente:

```bash
# Verificar versão do PHP
php -v

# Verificar extensões PHP necessárias
php -m | grep -E "(pdo|pdo_mysql|mbstring|json)"

# Verificar versão do MySQL
mysql --version
```

## Instalação Automática

### Método 1: Via Interface Administrativa (Recomendado)

1. **Acesse o painel administrativo** do CoreCRM
2. **Navegue** para "Plugins" > "Instalar Plugin"
3. **Faça upload** do arquivo ZIP do plugin
4. **Clique** em "Instalar Plugin"
5. **Ative** o plugin na lista de plugins instalados

### Método 2: Via Linha de Comando

```bash
# Navegue até o diretório do CoreCRM
cd /caminho/para/CoreCRM

# Baixe o plugin (substitua pela URL real)
wget https://github.com/seu-repo/plugin-clientes.zip

# Extraia o plugin
unzip plugin-clientes.zip -d plugins/

# Defina permissões corretas
chmod -R 755 plugins/clientes/
chown -R www-data:www-data plugins/clientes/
```

## Instalação Manual

### Passo 1: Copiar Arquivos

```bash
# Crie o diretório do plugin
mkdir -p /caminho/para/CoreCRM/plugins/clientes

# Copie todos os arquivos do plugin
cp -r clientes/* /caminho/para/CoreCRM/plugins/clientes/
```

### Passo 2: Configurar Permissões

```bash
# Defina o proprietário correto (substitua www-data pelo usuário do seu servidor web)
chown -R www-data:www-data /caminho/para/CoreCRM/plugins/clientes/

# Defina permissões adequadas
chmod -R 755 /caminho/para/CoreCRM/plugins/clientes/
chmod 644 /caminho/para/CoreCRM/plugins/clientes/plugin.json
```

### Passo 3: Ativar Plugin

1. **Acesse** o painel administrativo do CoreCRM
2. **Vá** para "Plugins" > "Gerenciar Plugins"
3. **Localize** "Plugin de Clientes" na lista
4. **Clique** em "Ativar"

## Configuração do Banco de Dados

### Criação Automática da Tabela

O plugin criará automaticamente a tabela `clientes` quando for ativado. Se por algum motivo isso não acontecer, execute manualmente:

```sql
CREATE TABLE IF NOT EXISTS clientes (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Verificação da Instalação

Execute a seguinte consulta para verificar se a tabela foi criada:

```sql
DESCRIBE clientes;
```

Resultado esperado:
```
+-------------+--------------------------------------------------------------+------+-----+-------------------+-----------------------------+
| Field       | Type                                                         | Null | Key | Default           | Extra                       |
+-------------+--------------------------------------------------------------+------+-----+-------------------+-----------------------------+
| id          | int(11)                                                      | NO   | PRI | NULL              | auto_increment              |
| nome        | varchar(255)                                                 | NO   |     | NULL              |                             |
| email       | varchar(255)                                                 | YES  | UNI | NULL              |                             |
| telefone    | varchar(20)                                                  | YES  |     | NULL              |                             |
| status      | enum('ativo','inativo','prospecto','bloqueado')             | YES  | MUL | prospecto         |                             |
| observacoes | text                                                         | YES  |     | NULL              |                             |
| created_at  | timestamp                                                    | NO   |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED           |
| updated_at  | timestamp                                                    | NO   |     | CURRENT_TIMESTAMP | DEFAULT_GENERATED on update |
+-------------+--------------------------------------------------------------+------+-----+-------------------+-----------------------------+
```

## Configuração do Servidor Web

### Apache

Adicione ao arquivo `.htaccess` do CoreCRM (se necessário):

```apache
# Regras para o plugin de clientes
RewriteRule ^admin/clientes/?$ plugins/clientes/main.php [L]
RewriteRule ^admin/clientes/(.*)$ plugins/clientes/main.php [L]
RewriteRule ^api/clientes/?$ plugins/clientes/main.php [L]
```

### Nginx

Adicione à configuração do Nginx:

```nginx
# Configuração para o plugin de clientes
location ~* ^/admin/clientes {
    try_files $uri $uri/ /plugins/clientes/main.php?$query_string;
}

location ~* ^/api/clientes {
    try_files $uri $uri/ /plugins/clientes/main.php?$query_string;
}
```

## Configurações Avançadas

### Configuração de Cache

Para melhor performance, configure cache para assets estáticos:

```apache
# Apache - adicione ao .htaccess
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>
```

```nginx
# Nginx - adicione à configuração
location ~* \.(css|js)$ {
    expires 1M;
    add_header Cache-Control "public, immutable";
}
```

### Configuração de Logs

Para habilitar logs detalhados do plugin, adicione ao arquivo de configuração do CoreCRM:

```php
// config/app.config.php
$config['plugins']['clientes']['debug'] = true;
$config['plugins']['clientes']['log_level'] = 'info';
```

### Configuração de Email

Se o plugin enviar emails (funcionalidade futura), configure:

```php
// config/mail.config.php
$config['mail']['from_name'] = 'Sistema de Clientes';
$config['mail']['from_email'] = 'clientes@seudominio.com';
```

## Verificação da Instalação

### Teste Básico

1. **Acesse** `/admin/clientes` no seu navegador
2. **Verifique** se a página carrega sem erros
3. **Teste** a criação de um cliente de exemplo
4. **Confirme** que o cliente aparece na lista

### Teste de API

```bash
# Teste o endpoint da API
curl -X GET "http://seudominio.com/api/clientes" \
     -H "Accept: application/json"
```

Resposta esperada:
```json
{
    "data": [],
    "meta": {
        "current_page": 1,
        "per_page": 20,
        "total": 0,
        "total_pages": 0
    },
    "filters": {
        "search": "",
        "status": ""
    }
}
```

### Verificação de Logs

Verifique os logs do CoreCRM para confirmar que o plugin foi carregado:

```bash
tail -f logs/system.log | grep "Plugin de Clientes"
```

Você deve ver uma mensagem similar a:
```
[2024-01-15 10:30:00] INFO: Plugin de Clientes carregado com sucesso.
```

## Solução de Problemas

### Problema: Plugin não aparece na lista

**Possíveis causas:**
- Arquivo `plugin.json` malformado
- Permissões incorretas
- Diretório no local errado

**Soluções:**
```bash
# Verificar JSON
php -l plugins/clientes/plugin.json

# Corrigir permissões
chmod 644 plugins/clientes/plugin.json
chown www-data:www-data plugins/clientes/plugin.json

# Verificar localização
ls -la plugins/clientes/
```

### Problema: Erro de banco de dados

**Possíveis causas:**
- Credenciais incorretas
- Usuário sem permissões
- Tabela não criada

**Soluções:**
```sql
-- Verificar permissões do usuário
SHOW GRANTS FOR 'usuario_db'@'localhost';

-- Criar tabela manualmente
SOURCE plugins/clientes/database/schema.sql;

-- Verificar conexão
SELECT 1;
```

### Problema: Erro 404 nas rotas

**Possíveis causas:**
- Configuração do servidor web
- Mod_rewrite desabilitado
- Rotas não registradas

**Soluções:**
```bash
# Apache - verificar mod_rewrite
apache2ctl -M | grep rewrite

# Nginx - verificar configuração
nginx -t

# Verificar logs de erro
tail -f /var/log/apache2/error.log
tail -f /var/log/nginx/error.log
```

### Problema: JavaScript não funciona

**Possíveis causas:**
- Arquivos JS não carregados
- Conflitos com outros scripts
- Erros de sintaxe

**Soluções:**
```bash
# Verificar se arquivos existem
ls -la plugins/clientes/assets/

# Verificar sintaxe JavaScript
node -c plugins/clientes/assets/clientes.js

# Verificar no navegador
# Abra F12 > Console e procure por erros
```

## Desinstalação

### Método 1: Via Interface Administrativa

1. **Acesse** "Plugins" > "Gerenciar Plugins"
2. **Localize** "Plugin de Clientes"
3. **Clique** em "Desativar"
4. **Clique** em "Desinstalar"

### Método 2: Manual

```bash
# Desativar plugin (edite o arquivo de configuração)
# Remover arquivos
rm -rf plugins/clientes/

# Remover tabela do banco (CUIDADO: isso apagará todos os dados!)
mysql -u usuario -p database_name -e "DROP TABLE IF EXISTS clientes;"
```

## Backup e Restauração

### Backup

```bash
# Backup dos arquivos
tar -czf clientes-plugin-backup.tar.gz plugins/clientes/

# Backup da tabela
mysqldump -u usuario -p database_name clientes > clientes-backup.sql
```

### Restauração

```bash
# Restaurar arquivos
tar -xzf clientes-plugin-backup.tar.gz

# Restaurar tabela
mysql -u usuario -p database_name < clientes-backup.sql
```

## Atualizações

### Processo de Atualização

1. **Faça backup** dos dados e arquivos
2. **Desative** o plugin temporariamente
3. **Substitua** os arquivos antigos pelos novos
4. **Execute** scripts de migração (se houver)
5. **Reative** o plugin
6. **Teste** as funcionalidades

### Scripts de Migração

Para futuras versões, scripts de migração estarão disponíveis em:
```
plugins/clientes/migrations/
```

Execute-os na ordem correta:
```bash
php plugins/clientes/migrations/001_add_new_field.php
php plugins/clientes/migrations/002_update_status_enum.php
```

## Suporte

Para suporte durante a instalação:

- **Documentação**: Consulte o README.md
- **Issues**: https://github.com/seu-repo/plugin-clientes/issues
- **Email**: suporte@seudominio.com
- **Fórum**: https://forum.corecrm.com

---

**Nota**: Este guia assume uma instalação padrão do CoreCRM. Adaptações podem ser necessárias para configurações personalizadas.

