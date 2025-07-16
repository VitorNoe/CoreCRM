<?php
/**
 * SidebarBlock
 *
 * Renderiza uma sidebar vertical, com suporte a menu, avatar, título, rodapé, widgets customizados, cor, largura, fixação, etc.
 *
 * Config:
 *   - title: string (título da sidebar)
 *   - avatar: array (config do AvatarBlock)
 *   - menu: array (config do MenuBlock)
 *   - widgets: array (HTML ou blocos customizados)
 *   - footer: string|HTML
 *   - width: string (ex: 'w-64', padrão 'w-64')
 *   - color: string (ex: 'bg-white/90', padrão)
 *   - fixed: bool (sidebar fixa à esquerda, padrão false)
 *   - class: string (classes extras)
 *
 * Exemplo de uso:
 *   echo BlockRenderer::render('Sidebar', [
 *     'title' => 'Painel',
 *     'avatar' => [ 'src'=>'...', 'alt'=>'João' ],
 *     'menu' => [ ... ],
 *     'widgets' => [ '<div>Widget</div>' ],
 *     'footer' => '<span>&copy; 2025</span>',
 *     'width' => 'w-72',
 *     'color' => 'bg-white/90',
 *     'fixed' => true,
 *   ]);
 */
class SidebarBlock {
    public static function render($config = []) {
        $title = $config['title'] ?? '';
        $avatar = $config['avatar'] ?? null;
        $menu = $config['menu'] ?? null;
        $widgets = $config['widgets'] ?? [];
        $footer = $config['footer'] ?? '';
        $width = $config['width'] ?? 'w-64';
        $color = $config['color'] ?? 'bg-white/90';
        $fixed = !empty($config['fixed']);
        $extraClass = $config['class'] ?? '';
        $sidebarClass = "$width $color shadow-lg p-6 flex flex-col min-h-screen $extraClass";
        if ($fixed) $sidebarClass .= ' fixed left-0 top-0 z-30';
        ob_start();
        ?>
        <aside class="block-sidebar <?= $sidebarClass ?>">
            <?php if ($avatar): ?>
                <div class="mb-4 flex justify-center">
                    <?= BlockRenderer::render('Avatar', $avatar) ?>
                </div>
            <?php endif; ?>
            <?php if ($title): ?>
                <h2 class="text-xl font-bold text-indigo-700 mb-2 text-center"> <?= htmlspecialchars($title) ?> </h2>
            <?php endif; ?>
            <?php if ($menu): ?>
                <div class="mb-6">
                    <?= BlockRenderer::render('Menu', array_merge($menu, ['vertical'=>true])) ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($widgets)): ?>
                <div class="flex-1 flex flex-col gap-4 mb-6">
                    <?php foreach ($widgets as $widget): ?>
                        <div class="block-sidebar-widget"> <?= $widget ?> </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if ($footer): ?>
                <div class="mt-auto pt-8 text-center text-xs text-gray-400"> <?= $footer ?> </div>
            <?php endif; ?>
        </aside>
        <?php
        return ob_get_clean();
    }
}
