<?php
/**
 * TopbarBlock
 *
 * Renderiza uma barra superior responsiva, com suporte a logo, menu, avatar, notificações, busca, widgets, cor, fixação, etc.
 *
 * Config:
 *   - logo: string|HTML (logo ou texto)
 *   - menu: array (config do MenuBlock)
 *   - avatar: array (config do AvatarBlock)
 *   - notifications: array (['icon'=>'fa-bell','count'=>3,'href'=>'/notificacoes'])
 *   - search: array (['placeholder'=>'Buscar...','action'=>'/buscar'])
 *   - widgets: array (HTML ou blocos customizados)
 *   - color: string (ex: 'bg-indigo-700', padrão)
 *   - fixed: bool (topbar fixa no topo, padrão false)
 *   - class: string (classes extras)
 *
 * Exemplo de uso:
 *   echo BlockRenderer::render('Topbar', [
 *     'logo' => '<span class="font-bold text-xl">CoreCRM</span>',
 *     'menu' => [ ... ],
 *     'avatar' => [ ... ],
 *     'notifications' => [ 'icon'=>'fa-bell','count'=>3,'href'=>'/notificacoes' ],
 *     'search' => [ 'placeholder'=>'Buscar...','action'=>'/buscar' ],
 *     'color' => 'bg-indigo-700',
 *     'fixed' => true,
 *   ]);
 */
class TopbarBlock {
    public static function render($config = []) {
        $logo = $config['logo'] ?? '';
        $menu = $config['menu'] ?? null;
        $avatar = $config['avatar'] ?? null;
        $notifications = $config['notifications'] ?? null;
        $search = $config['search'] ?? null;
        $widgets = $config['widgets'] ?? [];
        $color = $config['color'] ?? 'bg-indigo-700';
        $fixed = !empty($config['fixed']);
        $extraClass = $config['class'] ?? '';
        $topbarClass = "w-full flex items-center justify-between px-6 py-3 shadow text-white $color $extraClass";
        if ($fixed) $topbarClass .= ' fixed top-0 left-0 z-40';
        ob_start();
        ?>
        <div class="block-topbar <?= $topbarClass ?>">
            <div class="flex items-center gap-4">
                <?php if ($logo): ?>
                    <div class="block-topbar-logo"> <?= $logo ?> </div>
                <?php endif; ?>
                <?php if ($menu): ?>
                    <div class="block-topbar-menu hidden md:block"> <?= BlockRenderer::render('Menu', $menu) ?> </div>
                <?php endif; ?>
            </div>
            <div class="flex items-center gap-4">
                <?php if ($search): ?>
                    <form action="<?= htmlspecialchars($search['action'] ?? '') ?>" method="get" class="block-topbar-search hidden sm:block">
                        <input type="text" name="q" placeholder="<?= htmlspecialchars($search['placeholder'] ?? 'Buscar...') ?>" class="px-3 py-1 rounded bg-white/20 text-white placeholder-white/80 focus:bg-white/30 focus:outline-none" />
                    </form>
                <?php endif; ?>
                <?php if ($notifications): ?>
                    <a href="<?= htmlspecialchars($notifications['href'] ?? '#') ?>" class="relative block-topbar-notification">
                        <i class="fa <?= htmlspecialchars($notifications['icon'] ?? 'fa-bell') ?> text-xl"></i>
                        <?php if (!empty($notifications['count'])): ?>
                            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 shadow font-bold" style="font-size:10px;min-width:18px;line-height:1;"> <?= (int)$notifications['count'] ?> </span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
                <?php if ($widgets): ?>
                    <?php foreach ($widgets as $widget): ?>
                        <div class="block-topbar-widget"> <?= $widget ?> </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <?php if ($avatar): ?>
                    <div class="block-topbar-avatar"> <?= BlockRenderer::render('Avatar', $avatar) ?> </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
