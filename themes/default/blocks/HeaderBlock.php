<?php
/**
 * HeaderBlock
 *
 * Renderiza o cabeçalho do sistema, com logo, título, menu, avatar, ações, responsividade, etc.
 *
 * Config:
 *   - title: string (título principal)
 *   - logo: string|false (HTML/logo, padrão 'CoreCRM')
 *   - menu: array (itens do menu)
 *   - user: array (dados do usuário)
 *   - actions: array (ações rápidas)
 *   - class: string (classes extras)
 *
 * Exemplo de uso:
 *   echo BlockRenderer::render('Header', [
 *     'title' => 'Dashboard',
 *     'logo' => '<a href="/">CoreCRM</a>',
 *     'user' => ['name'=>'João'],
 *     'actions' => [ ['label'=>'Logout','href'=>'/logout','class'=>'bg-red-500'] ],
 *   ]);
 */
class HeaderBlock {
    public static function render($config = []) {
        $title = $config['title'] ?? '';
        $logo = $config['logo'] ?? '<a href="/" class="text-xl font-bold tracking-tight hover:underline">CoreCRM</a>';
        $menu = $config['menu'] ?? [];
        $user = $config['user'] ?? null;
        $actions = $config['actions'] ?? [];
        $extraClass = $config['class'] ?? '';
        ob_start();
        ?>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title><?= htmlspecialchars($title ?: 'CRM V1') ?></title>
            <!-- Tailwind CSS -->
            <script src="https://cdn.tailwindcss.com"></script>
            <link rel="stylesheet" href="/public/assets/css/blocks.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <style>
                body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; }
                .container { width: 80%; margin: 20px auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
            </style>
        </head>
        <body>
        <header class="block-header w-full flex items-center justify-between px-6 py-3 bg-indigo-700 shadow text-white <?= $extraClass ?>">
            <div class="flex items-center gap-4">
                <?= $logo ?>
                <?php if ($menu && is_array($menu)): ?>
                    <nav class="hidden sm:flex gap-2">
                        <?php foreach ($menu as $item): ?>
                            <a href="<?= htmlspecialchars($item['href'] ?? '#') ?>" class="px-2 py-1 rounded hover:bg-indigo-600 transition-colors <?= $item['class'] ?? '' ?>">
                                <?php if (!empty($item['icon'])): ?><i class="fa <?= htmlspecialchars($item['icon']) ?> mr-1"></i><?php endif; ?>
                                <?= htmlspecialchars($item['label'] ?? '') ?>
                            </a>
                        <?php endforeach; ?>
                    </nav>
                <?php endif; ?>
            </div>
            <div class="flex items-center gap-4">
                <?php if ($user): ?>
                    <span class="hidden sm:inline">Olá, <?= htmlspecialchars($user['name'] ?? 'Usuário') ?></span>
                <?php endif; ?>
                <?php if ($actions && is_array($actions)):
                    foreach ($actions as $action): ?>
                        <a href="<?= htmlspecialchars($action['href'] ?? '#') ?>" class="px-3 py-1 rounded text-white font-semibold transition-colors <?= $action['class'] ?? 'bg-indigo-500 hover:bg-indigo-600' ?>">
                            <?= htmlspecialchars($action['label'] ?? '') ?>
                        </a>
                <?php endforeach; endif; ?>
            </div>
        </header>
        <?php
        return ob_get_clean();
    }
}
