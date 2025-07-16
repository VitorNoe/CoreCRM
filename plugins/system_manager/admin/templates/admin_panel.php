<?php
require_once __DIR__ . '/../../../../themes/default/blocks/BlockRenderer.php';

// Renderiza o HeaderBlock com tema dinâmico
echo BlockRenderer::render('Header', [
    'title' => 'Painel Admin - CoreCRM',
    'logo' => '<a href="/" class="text-xl font-bold tracking-tight hover:underline">CoreCRM Admin</a>',
    'user' => ['name' => $_SESSION['user_id'] ?? 'Usuário'],
    'actions' => [
        ['label' => 'Sair', 'href' => '/logout', 'class' => 'bg-red-500 hover:bg-red-600']
    ]
]);
?>
    <div class="container mx-auto my-8">
        <?php
        // Usar SidebarBlock
        $sidebarContent = BlockRenderer::render('Sidebar', [
            'avatar' => BlockRenderer::render('Avatar', [
                'name' => $_SESSION['user_id'] ?? 'Usuário',
                'icon' => 'fa-user-shield',
                'status' => 'online'
            ]),
            'menu' => [
                ['label' => 'Dashboard', 'icon' => 'fa-tachometer-alt', 'href' => '/admin'],
                ['label' => 'Usuários', 'icon' => 'fa-users', 'href' => '/admin/users'],
                ['label' => 'Plugins', 'icon' => 'fa-puzzle-piece', 'href' => '/admin/plugins'],
            ],
            'widgets' => [
                '<div class="p-4 block-card"><p class="text-sm">Papel: ' . htmlspecialchars($_SESSION['user_role'] ?? 'N/A') . '</p></div>'
            ]
        ]);
        ?>
        
        <div class="flex gap-6">
            <div class="w-64">
                <?= $sidebarContent ?>
            </div>
            <div class="flex-1">
                <?php
                // Usar CardBlock para o conteúdo principal
                echo BlockRenderer::render('Card', [
                    'title' => 'Dashboard',
                    'icon' => 'fa-chart-line',
                    'content' => '<div class="space-y-6">' . 
                        (function() {
                            global $adminContentCallbacks;
                            ob_start();
                            if (!empty($adminContentCallbacks)) {
                                foreach ($adminContentCallbacks as $callback) {
                                    if (is_callable($callback)) {
                                        call_user_func($callback);
                                    }
                                }
                            } else {
                                echo '<div class="text-center block-secondary">Nenhum plugin adicionou conteúdo ao painel ainda.</div>';
                            }
                            return ob_get_clean();
                        })() . '</div>'
                ]);
                ?>
            </div>
        </div>
    </div>
<?php
// Usar FooterBlock
echo BlockRenderer::render('Footer', [
    'breadcrumbs' => true,
    'clock' => true,
    'status' => 'Admin Online',
    'content' => '&copy; ' . date('Y') . ' CoreCRM Admin'
]);
?>
