<?php
/**
 * MenuBlock
 *
 * Renderiza menus horizontais ou verticais, com suporte a ícones FontAwesome, badges, submenus, estados ativo/desabilitado, separadores e responsividade.
 *
 * Config:
 *   - items: array [['label'=>..., 'href'=>..., 'icon'=>'fa-home', 'badge'=>'3', 'active'=>true, 'disabled'=>false, 'submenu'=>[...], 'separator'=>true]]
 *   - vertical: bool (menu vertical, padrão false)
 *   - class: string (classes extras)
 *
 * Exemplo de uso:
 *   echo BlockRenderer::render('Menu', [
 *     'items' => [
 *       ['label'=>'Dashboard','href'=>'/','icon'=>'fa-home','active'=>true],
 *       ['label'=>'Clientes','href'=>'/clientes','icon'=>'fa-users','badge'=>'12'],
 *       ['separator'=>true],
 *       ['label'=>'Configurações','href'=>'/config','icon'=>'fa-cog','disabled'=>true],
 *       ['label'=>'Mais','submenu'=>[
 *         ['label'=>'Perfil','href'=>'/perfil','icon'=>'fa-user'],
 *         ['label'=>'Sair','href'=>'/logout','icon'=>'fa-sign-out'],
 *       ]],
 *     ],
 *     'vertical' => true,
 *   ]);
 */
class MenuBlock {
    public static function render($config = []) {
        $items = $config['items'] ?? [];
        $vertical = $config['vertical'] ?? false;
        $extraClass = $config['class'] ?? '';
        $menuClass = $vertical ? 'flex flex-col' : 'flex flex-row';
        $gapClass = $vertical ? 'gap-2' : 'gap-4';
        ob_start();
        ?>
        <nav class="block-menu <?= $menuClass ?> <?= $gapClass ?> <?= $extraClass ?>">
            <?php foreach ($items as $item):
                if (!empty($item['separator'])): ?>
                    <span class="block-menu-separator w-px h-6 bg-gray-300 mx-2"></span>
                <?php continue; endif;
                $active = !empty($item['active']);
                $disabled = !empty($item['disabled']);
                $hasSubmenu = !empty($item['submenu']);
            ?>
                <div class="relative group">
                    <a href="<?= htmlspecialchars($item['href'] ?? '#') ?>"
                       class="block-menu-item flex items-center px-3 py-2 rounded transition font-semibold <?= $active ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-indigo-50' ?> <?= $disabled ? 'opacity-50 cursor-not-allowed pointer-events-none' : '' ?>"
                       <?= $disabled ? 'tabindex="-1" aria-disabled="true"' : '' ?>
                    >
                        <?php if (!empty($item['icon'])): ?><i class="fa <?= htmlspecialchars($item['icon']) ?> mr-2"></i><?php endif; ?>
                        <span><?= htmlspecialchars($item['label'] ?? '') ?></span>
                        <?php if (!empty($item['badge'])): ?><span class="ml-2 bg-indigo-600 text-white text-xs rounded-full px-2 py-0.5"> <?= htmlspecialchars($item['badge']) ?> </span><?php endif; ?>
                        <?php if ($hasSubmenu): ?><i class="fa fa-chevron-down ml-2"></i><?php endif; ?>
                    </a>
                    <?php if ($hasSubmenu): ?>
                        <div class="absolute left-0 top-full min-w-[160px] bg-white shadow rounded mt-2 z-20 hidden group-hover:block">
                            <?php foreach ($item['submenu'] as $sub): ?>
                                <a href="<?= htmlspecialchars($sub['href'] ?? '#') ?>" class="block px-4 py-2 text-gray-700 hover:bg-indigo-50 flex items-center gap-2">
                                    <?php if (!empty($sub['icon'])): ?><i class="fa <?= htmlspecialchars($sub['icon']) ?>"></i><?php endif; ?>
                                    <?= htmlspecialchars($sub['label'] ?? '') ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </nav>
        <?php
        return ob_get_clean();
    }
}
