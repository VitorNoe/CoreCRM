<?php
/**
 * CardBlock
 *
 * Renderiza um ou vários cards informativos, com suporte a ícone FontAwesome, badge, valor, subtítulo, ações, cor, layout flex/grid e responsivo.
 *
 * Config:
 *   - cards: array de arrays de config de card (ou um único card como array)
 *   - layout: 'flex' | 'grid' (padrão: 'flex')
 *   - columns: int (número de colunas se grid, padrão 3)
 *   - gap: string (ex: 'gap-4', padrão 'gap-4')
 *   - Card config:
 *       - title: string
 *       - subtitle: string
 *       - value: string|number
 *       - icon: string (classe FontAwesome)
 *       - badge: string
 *       - color: string (ex: 'bg-indigo-600')
 *       - actions: array [['label'=>..., 'href'=>..., 'icon'=>...]]
 *       - content: string (HTML ou texto)
 *
 * Exemplo de uso:
 *   echo BlockRenderer::render('Card', [
 *     'cards' => [
 *       [
 *         'title' => 'Usuários',
 *         'value' => 120,
 *         'icon' => 'fa-users',
 *         'badge' => '+5%',
 *         'color' => 'bg-indigo-600',
 *         'actions' => [
 *           ['label'=>'Ver todos','href'=>'/usuarios','icon'=>'fa-arrow-right']
 *         ]
 *       ],
 *       [
 *         'title' => 'Vendas',
 *         'value' => 'R$ 12.300',
 *         'icon' => 'fa-shopping-cart',
 *         'color' => 'bg-green-600',
 *       ]
 *     ],
 *     'layout' => 'grid',
 *     'columns' => 2,
 *   ]);
 */
class CardBlock {
    public static function render($config = []) {
        $cards = $config['cards'] ?? [];
        if (isset($config['title']) || isset($config['value'])) {
            $cards[] = $config;
        }
        $layout = $config['layout'] ?? 'flex';
        $columns = $config['columns'] ?? 3;
        $gap = $config['gap'] ?? 'gap-4';
        $containerClass = $layout === 'grid'
            ? "grid $gap grid-cols-1 sm:grid-cols-" . (int)$columns
            : "flex flex-wrap $gap";
        ob_start();
        ?>
        <div class="block-card-group <?= $containerClass ?>">
            <?php foreach ($cards as $card):
                $color = $card['color'] ?? 'bg-white';
                $icon = $card['icon'] ?? null;
                $badge = $card['badge'] ?? null;
                $title = $card['title'] ?? '';
                $subtitle = $card['subtitle'] ?? '';
                $value = $card['value'] ?? '';
                $actions = $card['actions'] ?? [];
                $content = $card['content'] ?? '';
            ?>
            <div class="block-card flex flex-col justify-between rounded-lg shadow p-6 min-w-[220px] relative mb-2">
                <div class="flex items-center gap-3 mb-2">
                    <?php if ($icon): ?><span class="text-3xl"><i class="fa <?= htmlspecialchars($icon) ?>"></i></span><?php endif; ?>
                    <div class="flex-1">
                        <div class="block-card-title text-lg font-bold leading-tight">
                            <?= htmlspecialchars($title) ?>
                            <?php if ($badge): ?><span class="ml-2 inline-block block-card-badge text-xs px-2 py-0.5 rounded-full align-middle"> <?= htmlspecialchars($badge) ?> </span><?php endif; ?>
                        </div>
                        <?php if ($subtitle): ?><div class="block-card-subtitle text-xs"> <?= htmlspecialchars($subtitle) ?> </div><?php endif; ?>
                    </div>
                </div>
                <?php if ($value): ?><div class="block-card-value text-2xl font-extrabold mb-2"> <?= htmlspecialchars($value) ?> </div><?php endif; ?>
                <?php if ($content): ?><div class="block-card-content text-sm mb-2"> <?= $content ?> </div><?php endif; ?>
                <?php if (!empty($actions)): ?>
                    <div class="block-card-actions flex gap-2 mt-2">
                        <?php foreach ($actions as $action): ?>
                            <a href="<?= htmlspecialchars($action['href'] ?? '#') ?>" class="inline-flex items-center gap-1 px-3 py-1 rounded block-card-action text-xs font-semibold transition">
                                <?php if (!empty($action['icon'])): ?><i class="fa <?= htmlspecialchars($action['icon']) ?>"></i><?php endif; ?>
                                <?= htmlspecialchars($action['label'] ?? '') ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
