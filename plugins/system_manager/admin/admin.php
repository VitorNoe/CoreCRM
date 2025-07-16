<?php

class SystemManagerAdmin {
    private static $adminContentCallbacks = [];

    public static function registerRoutes() {
        // Rota de login (GET)
        RoutesHandler::addRoute("GET", "/login", [self::class, 'loginGet']);

        // Rota de login (POST)
        RoutesHandler::addRoute("POST", "/login", [self::class, 'loginPost']);

        // Rota principal do painel admin (protegida)
        RoutesHandler::addRoute("GET", "/admin", [self::class, 'adminPanel'], [
            "auth" => true,
            "permission" => "admin"
        ]);
        // Rota para CRUD de usuários dentro do admin
        RoutesHandler::addRoute("GET", "/admin/usuarios", function() {
            require_once __DIR__ . '/users_crud.php';
            SystemManagerUsersCrud::listUsers();
        }, ["auth" => true, "permission" => "admin"]);

        // Rota de logout
        RoutesHandler::addRoute("GET", "/logout", [self::class, 'logout']);

        // Rota para página de plugins (GET e POST)
        RoutesHandler::addRoute("GET", "/admin/plugins", [self::class, 'pluginsPage'], ["auth" => true, "permission" => "admin"]);
        RoutesHandler::addRoute("POST", "/admin/plugins", [self::class, 'pluginsUpload'], ["auth" => true, "permission" => "admin"]);

        // Rota para ativar/desativar plugins
        RoutesHandler::addRoute("POST", "/admin/plugins/toggle", [self::class, 'togglePlugin'], ["auth" => true, "permission" => "admin"]);

        // Rota para página de logs
        RoutesHandler::addRoute("GET", "/admin/logs", [self::class, 'logsPage'], ["auth" => true, "permission" => "admin"]);
    }

    public static function loginGet() {
        $error = '';
        $redirect = $_GET['redirect'] ?? '/admin';
        include __DIR__ . '/templates/login.php';
    }

    public static function loginPost() {
        $error = '';
        $user = $_POST['user'] ?? '';
        $pass = isset($_POST['password']) ? $_POST['password'] : '';
        $redirect = $_POST['redirect'] ?? '/admin';
        // Busca usuário no banco de dados
        $pdo = DatabaseHandler::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
        $stmt->execute([$user, $user]);
        $dbUser = $stmt->fetch(PDO::FETCH_ASSOC);
        // Ajusta campos para inglês
        // Se vier do banco como 'senha', converte para 'password'
        if (isset($dbUser['senha']) && !isset($dbUser['password'])) {
            $dbUser['password'] = $dbUser['senha'];
        }
        if (isset($dbUser['nome']) && !isset($dbUser['name'])) {
            $dbUser['name'] = $dbUser['nome'];
        }
        if (isset($dbUser['tipo']) && !isset($dbUser['role'])) {
            $dbUser['role'] = $dbUser['tipo'];
        }
        if (isset($dbUser['ativo']) && !isset($dbUser['active'])) {
            $dbUser['active'] = $dbUser['ativo'];
        }
        // var_dump($dbUser); // Debug: Verifica se o usuário foi encontrado
        if ($dbUser && AuthHandler::verifyPassword($pass, $dbUser['password'])) {
            AuthHandler::login($dbUser['username'], $dbUser['role']);
            // Redireciona para a rota original, se for segura
            if (strpos($redirect, '/') === 0) {
                AuthHandler::redirect($redirect);
            } else {
                AuthHandler::redirect('/admin');
            }
        } else {
            $error = 'Usuário ou senha inválidos!';
            echo "<script>alert('$error');</script>";
            include __DIR__ . '/templates/login.php';
        }
    }

    public static function adminPanel() {
        AuthHandler::requireAuth();
        if (!AuthHandler::checkPermission('admin')) {
            echo "Acesso negado.";
            return;
        }
        // Busca plugins ativos
        $plugins = PluginHandler::getActivePlugins();
        // Torna plugins disponíveis para o template
        $sidebarMenu = self::renderSidebarMenu($plugins);
        // Torna callbacks disponíveis para o template
        $adminContentCallbacks = \System::$adminContentCallbacks;
        include __DIR__ . '/templates/admin_panel.php';
    }

    /**
     * Permite que plugins registrem conteúdo para o painel admin
     * @param callable $callback
     */
    public static function addAdminContent(callable $callback) {
        self::$adminContentCallbacks[] = $callback;
    }

    /**
     * Renderiza o menu lateral com base nos plugins ativos
     * @param array $plugins
     * @return string HTML do menu
     */
    public static function renderSidebarMenu($plugins) {
        // Usa os itens registrados manualmente pelos plugins
        $items = \System::$adminSidebarMenuItems;
        $html = '<nav class="h-full"><ul class="space-y-2">';
        foreach ($items as $item) {
            $name = htmlspecialchars($item['name'] ?? 'Item');
            $icon = $item['icon'] ?? 'circle';
            $url = htmlspecialchars($item['url'] ?? '#');
            $html .= "<li><a href=\"{$url}\" class=\"flex items-center gap-2 px-4 py-2 rounded hover:bg-indigo-100 text-indigo-700 font-semibold\"><span class=\"material-icons text-base\">{$icon}</span>{$name}</a></li>";
        }
        $html .= '</ul></nav>';
        return $html;
    }

    public static function logout() {
        AuthHandler::logout();
        // O método logout já faz redirect para /login
    }

    public static function pluginsPage() {
        AuthHandler::requireAuth();
        if (!AuthHandler::checkPermission('admin')) {
            echo "Acesso negado.";
            return;
        }
        $plugins = self::getAllPlugins();
        include __DIR__ .'/templates/plugins.php';
    }

    public static function pluginsUpload() {
        AuthHandler::requireAuth();
        if (!AuthHandler::checkPermission('admin')) {
            echo "Acesso negado.";
            return;
        }
        if (!isset($_FILES['plugin_zip']) || $_FILES['plugin_zip']['error'] !== UPLOAD_ERR_OK) {
            echo "Erro no upload do arquivo.";
            return;
        }
        $zipPath = $_FILES['plugin_zip']['tmp_name'];
        $zip = new ZipArchive();
        if ($zip->open($zipPath) === TRUE) {
            // Extrai para uma pasta temporária
            $tmpDir = sys_get_temp_dir() . '/plugin_' . uniqid();
            mkdir($tmpDir);
            $zip->extractTo($tmpDir);
            $zip->close();
            // Move a(s) pasta(s) extraída(s) para plugins/
            $pluginBase = dirname(__DIR__, 2) . '/plugins/';
            $moved = false;
            foreach (scandir($tmpDir) as $item) {
                if ($item === '.' || $item === '..') continue;
                $src = $tmpDir . '/' . $item;
                $dest = $pluginBase . $item;
                if (is_dir($src)) {
                    if (!file_exists($dest)) {
                        rename($src, $dest);
                        $moved = true;
                    }
                }
            }
            // Limpa temp
            array_map('unlink', glob("$tmpDir/*"));
            rmdir($tmpDir);
            if ($moved) {
                System::log("Plugin enviado e instalado com sucesso.", "info");
            } else {
                System::log("Nenhuma pasta de plugin foi movida. Talvez já exista.", "warning");
            }
        } else {
            echo "Não foi possível abrir o arquivo ZIP.";
            return;
        }
        header('Location: /admin/plugins');
        exit;
    }

    public static function togglePlugin() {
        AuthHandler::requireAuth();
        if (!AuthHandler::checkPermission('admin')) {
            echo "Acesso negado.";
            return;
        }
        $slug = $_POST['slug'] ?? '';
        $action = $_POST['action'] ?? '';
        if (!$slug || !in_array($action, ['activate', 'deactivate'])) {
            echo "Requisição inválida.";
            return;
        }
        $pdo = DatabaseHandler::getConnection();
        $active = $action === 'activate' ? 1 : 0;
        $stmt = $pdo->prepare("UPDATE plugins SET active = ? WHERE slug = ?");
        $stmt->execute([$active, $slug]);
        // Reload para refletir mudança
        header('Location: /admin/plugins');
        exit;
    }

    public static function logsPage() {
        AuthHandler::requireAuth();
        if (!AuthHandler::checkPermission('admin')) {
            echo "Acesso negado.";
            return;
        }
        $logsDir = dirname(__DIR__, 3) . '/logs/';
        $logFiles = [];
        if (is_dir($logsDir)) {
            foreach (scandir($logsDir) as $file) {
                if (substr($file, -4) === '.log') {
                    $logFiles[] = $file;
                }
            }
        }
        $selectedLog = $_GET['logfile'] ?? ($logFiles[0] ?? '');
        $logContent = '';
        if ($selectedLog && in_array($selectedLog, $logFiles)) {
            $logPath = $logsDir . $selectedLog;
            if (file_exists($logPath)) {
                $logContent = file_get_contents($logPath);
            }
        }
        include __DIR__ . '/templates/logs.php';
    }

    private static function getAllPlugins() {
        $pdo = DatabaseHandler::getConnection();
        $plugins = [];
        $dbPlugins = [];
        // Busca todos do banco
        try {
            $stmt = $pdo->query("SELECT * FROM plugins");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $plugins[$row['slug']] = [
                    'name' => $row['name'],
                    'folder' => $row['slug'],
                    'active' => (bool)$row['active']
                ];
                $dbPlugins[$row['slug']] = true;
            }
        } catch (\Exception $e) {
            // fallback: ignora banco
        }
        // Complementa com plugins da pasta que não estão no banco
        $pluginDir = dirname(__DIR__, 2) . '/';
        $folders = [];
        if (is_dir($pluginDir)) {
            $folders = array_filter(scandir($pluginDir), function($f) use ($pluginDir) {
                return $f !== '.' && $f !== '..' && is_dir($pluginDir . $f);
            });
        }
        foreach ($folders as $folder) {
            if (!isset($dbPlugins[$folder])) {
                $pluginJson = $pluginDir . $folder . '/plugin.json';
                $name = $folder;
                if (file_exists($pluginJson)) {
                    $meta = json_decode(file_get_contents($pluginJson), true);
                    if (isset($meta['name'])) $name = $meta['name'];
                }
                $plugins[$folder] = [
                    'name' => $name,
                    'folder' => $folder,
                    'active' => true // fallback: ativo
                ];
            }
        }
        return array_values($plugins);
    }
}

// Registra item de menu lateral para Plugins
\System::addAdminSidebarMenuItem([
    'name' => 'Plugins',
    'icon' => 'extension',
    'url'  => '/admin/plugins'
]);

// Registra item de menu lateral para Logs
\System::addAdminSidebarMenuItem([
    'name' => 'Logs',
    'icon' => 'description',
    'url'  => '/admin/logs'
]);

SystemManagerAdmin::registerRoutes();