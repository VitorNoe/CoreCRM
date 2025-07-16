<?php

// Este arquivo é o ponto de entrada principal para o seu plugin.
// Ele é carregado pelo PluginHandler quando o plugin está ativo.

// Exemplo de registro de uma rota para o plugin
RoutesHandler::addRoute("GET", "/exemplo-plugin/teste", function() {
    echo "Esta é uma página de teste do Exemplo de Plugin!";
});

// Nova página de exemplos de blocos do tema
RoutesHandler::addRoute("GET", "/exemplo-plugin/exemplos", function() {
    require_once __DIR__ . '/../../themes/default/blocks/BlockRenderer.php';
    echo BlockRenderer::render('Header', [
        'title' => 'Exemplos de Blocos',
        'logo' => '<a href="https://crm.alunostds.dev.br" class=\"text-xl font-bold tracking-tight hover:underline\">CoreCRM</a>',
        'user' => ['name'=>'Demo'],
        'actions' => [ ['label'=>'Voltar','href'=>'/','class'=>'bg-indigo-500 hover:bg-indigo-600'] ],
    ]);
    echo '<div class="container mx-auto my-8">';
    echo '<h2 class="text-2xl font-bold mb-6">Todos os Blocos do Tema</h2>';
    // AvatarBlock
    echo BlockRenderer::render('Card', [
        'title' => 'AvatarBlock',
        'content' => BlockRenderer::render('Avatar', [
            'name' => 'João',
            'status' => 'online',
            'icon' => 'fa-user',
            'badge' => 'Admin',
        ]),
    ]);
    // BannerBlock
    echo BlockRenderer::render('Banner', [
        'title' => 'Bem-vindo!',
        'subtitle' => 'Este é um banner de exemplo',
        'image' => 'https://picsum.photos/800/200',
        'button' => ['label'=>'Saiba mais','href'=>'#'],
        'icon' => 'fa-star',
    ]);
    // CardBlock (grid)
    echo BlockRenderer::render('Grid', [
        'columns' => 3,
        'items' => [
            BlockRenderer::render('Card', ['title'=>'Card 1','content'=>'Conteúdo 1','icon'=>'fa-cube']),
            BlockRenderer::render('Card', ['title'=>'Card 2','content'=>'Conteúdo 2','icon'=>'fa-cube']),
            BlockRenderer::render('Card', ['title'=>'Card 3','content'=>'Conteúdo 3','icon'=>'fa-cube']),
        ],
    ]);
    // MenuBlock
    echo BlockRenderer::render('Menu', [
        'items' => [
            ['label'=>'Home','icon'=>'fa-home','href'=>'/'],
            ['label'=>'Exemplos','icon'=>'fa-puzzle-piece','href'=>'/exemplo-plugin/exemplos','badge'=>'Novo'],
        ],
        'orientation' => 'horizontal',
    ]);
    // SidebarBlock
    echo BlockRenderer::render('Sidebar', [
        'avatar' => BlockRenderer::render('Avatar', ['name'=>'Demo','icon'=>'fa-user']),
        'menu' => [
            ['label'=>'Dashboard','icon'=>'fa-tachometer','href'=>'/'],
            ['label'=>'Exemplos','icon'=>'fa-puzzle-piece','href'=>'/exemplo-plugin/exemplos'],
        ],
        'widgets' => ['<div class="p-2">Widget customizado</div>'],
    ]);
    // TopbarBlock
    echo BlockRenderer::render('Topbar', [
        'logo' => '<span class="font-bold">CoreCRM</span>',
        'menu' => [
            ['label'=>'Home','icon'=>'fa-home','href'=>'/'],
            ['label'=>'Exemplos','icon'=>'fa-puzzle-piece','href'=>'/exemplo-plugin/exemplos'],
        ],
        'avatar' => BlockRenderer::render('Avatar', ['name'=>'Demo','icon'=>'fa-user']),
    ]);
    // TableBlock
    echo BlockRenderer::render('Table', [
        'columns' => [
            ['key'=>'name','label'=>'Name'],
            ['key'=>'role','label'=>'Role'],
            ['key'=>'status','label'=>'Status'],
        ],
        'data' => [
            ['name'=>'João','role'=>'Admin','status'=>'Online'],
            ['name'=>'Maria','role'=>'User','status'=>'Offline'],
        ],
        'search' => true,
        'actions' => [ ['label'=>'Edit','icon'=>'fa-edit'] ],
    ]);
    // FormBlock
    echo BlockRenderer::render('Form', [
        'fields' => [
            ['type'=>'text','label'=>'Nome','name'=>'nome'],
            ['type'=>'email','label'=>'Email','name'=>'email'],
        ],
        'buttons' => [ ['label'=>'Enviar','type'=>'submit','icon'=>'fa-paper-plane'] ],
    ]);
    // ModalBlock
    echo BlockRenderer::render('Modal', [
        'title' => 'Exemplo de Modal',
        'content' => 'Conteúdo do modal',
        'icon' => 'fa-info-circle',
        'buttons' => [ ['label'=>'Fechar','type'=>'close'] ],
    ]);
    // ToastBlock
    echo BlockRenderer::render('Toast', [
        'type' => 'success',
        'message' => 'Operação realizada com sucesso!',
        'icon' => 'fa-check',
    ]);
    // GridBlock
    echo BlockRenderer::render('Grid', [
        'columns' => 2,
        'items' => [
            BlockRenderer::render('Card', ['title'=>'Grid 1','content'=>'Conteúdo 1']),
            BlockRenderer::render('Card', ['title'=>'Grid 2','content'=>'Conteúdo 2']),
        ],
    ]);
    // TimelineBlock
    echo BlockRenderer::render('Timeline', [
        'items' => [
            ['label'=>'Início','icon'=>'fa-play','color'=>'blue'],
            ['label'=>'Processo','icon'=>'fa-cog','color'=>'gray'],
            ['label'=>'Fim','icon'=>'fa-flag','color'=>'green'],
        ],
    ]);
    // TabsBlock
    echo BlockRenderer::render('Tabs', [
        'tabs' => [
            ['label'=>'Aba 1','icon'=>'fa-cube','content'=>BlockRenderer::render('Card',['title'=>'Conteúdo Aba 1','content'=>'Exemplo'])],
            ['label'=>'Aba 2','icon'=>'fa-cube','content'=>BlockRenderer::render('Card',['title'=>'Conteúdo Aba 2','content'=>'Outro exemplo'])],
        ],
        'orientation' => 'horizontal',
    ]);
    // BreadcrumbBlock
    echo BlockRenderer::render('Breadcrumb', [
        'items' => [
            ['label'=>'Home','icon'=>'fa-home','href'=>'/'],
            ['label'=>'Exemplos','icon'=>'fa-puzzle-piece','href'=>'/exemplo-plugin/exemplos'],
        ],
    ]);
    echo '</div>';
    echo BlockRenderer::render('Footer', [
        'breadcrumbs' => true,
        'clock' => true,
        'status' => 'Online',
        'content' => '&copy; ' . date('Y') . ' CoreCRM',
    ]);
});

// Exemplo de registro de um hook
HookHandler::register_hook("after_gerar_relatorio", function() {
    System::log("Hook do plugin executado após gerar relatório.", "info");
});

// Adiciona conteúdo ao painel admin
System::addAdminContent(function() {
    echo '<div class="bg-indigo-50 border-l-4 border-indigo-400 p-4 rounded shadow mb-4">';
    echo '<h2 class="text-lg font-bold text-indigo-700 mb-1">Exemplo de Plugin</h2>';
    echo '<p class="text-gray-700">Este bloco foi adicionado pelo Exemplo de Plugin! Você pode usar este espaço para mostrar informações, gráficos ou ações do seu plugin.</p>';
    echo '</div>';
});

System::addAdminSidebarMenuItem([
    'name' => 'Exemplo Plugin',
    'icon' => 'extension',
    'url'  => '/exemplo-plugin/teste'
]);
System::addAdminSidebarMenuItem([
    'name' => 'Exemplos de Blocos',
    'icon' => 'puzzle-piece',
    'url'  => '/exemplo-plugin/exemplos'
]);

System::log("Plugin Exemplo de Plugin carregado com sucesso.");



