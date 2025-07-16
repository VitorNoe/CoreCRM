<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'CRM V1'; ?></title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/public/assets/css/blocks.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { width: 80%; margin: 20px auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
    </style>
    <script src="/public/assets/js/theme.js"></script>
</head>
<body>
    <div style="position:fixed;top:10px;right:10px;z-index:9999;">
        <button id="theme-toggle-btn" onclick="toggleTheme()" style="padding:6px 12px;border-radius:6px;border:1px solid var(--color-border);background:var(--color-bg);color:var(--color-text);cursor:pointer;display:flex;align-items:center;gap:8px;">
            <span id="theme-toggle-icon">
                <i class="fa fa-moon"></i>
            </span>
            <span id="theme-toggle-label">Alternar tema</span>
        </button>
        <script>
        function updateThemeIcon() {
            var theme = document.body.getAttribute('data-theme');
            var icon = document.getElementById('theme-toggle-icon');
            if (theme === 'dark') {
                icon.innerHTML = '<i class="fa fa-sun"></i>';
            } else {
                icon.innerHTML = '<i class="fa fa-moon"></i>';
            }
        }
        document.addEventListener('DOMContentLoaded', updateThemeIcon);
        document.body.addEventListener('themechange', updateThemeIcon);
        // Atualiza ícone ao trocar tema
        window.toggleTheme = function() {
            var current = document.body.getAttribute('data-theme');
            var next = current === 'dark' ? 'light' : 'dark';
            document.body.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
            updateThemeIcon();
            var event = new Event('themechange');
            document.body.dispatchEvent(event);
        };
        </script>
    </div>
<?php
require_once __DIR__ . '/blocks/BlockRenderer.php';
$user = AuthHandler::isLoggedIn() ? ['name' => $_SESSION['user_id'] ?? 'Usuário'] : null;
$actions = AuthHandler::isLoggedIn()
    ? [ ['label'=>'Logout','href'=>'/logout','class'=>'bg-red-500 hover:bg-red-600'] ]
    : [ ['label'=>'Login','href'=>'/login','class'=>'bg-indigo-500 hover:bg-indigo-600'] ];
echo BlockRenderer::render('Header', [
    'title' => $title ?? 'CRM V1',
    'logo' => '<a href="https://crm.alunostds.dev.br" class="text-xl font-bold tracking-tight hover:underline">CoreCRM</a>',
    'user' => $user,
    'actions' => $actions,
    'class' => '',
]);
?>


