<?php
/**
 * TimelineBlock
 *
 * Renderiza uma timeline vertical ou horizontal, com suporte a múltiplos itens, ícones, animação, inserir outros blocos como item, cor, responsividade, etc.
 *
 * Config:
 *   - items: array [['date'=>'2025-07-16','content'=>'Texto ou bloco','icon'=>'fa-user','color'=>'bg-indigo-600','block'=>BlockRenderer::render('Card', [...])]]
 *   - horizontal: bool (timeline horizontal, padrão false)
 *   - class: string (classes extras)
 *
 * Exemplo de uso:
 *   echo BlockRenderer::render('Timeline', [
 *     'items' => [
 *       ['date'=>'2025-07-16','content'=>'Novo usuário','icon'=>'fa-user','color'=>'bg-indigo-600'],
 *       ['date'=>'2025-07-17','block'=>BlockRenderer::render('Card', [...])],
 *     ],
 *     'horizontal' => true,
 *   ]);
 */
class TimelineBlock {
    public static function render($config = []) {
        $items = $config['items'] ?? [];
        $horizontal = !empty($config['horizontal']);
        $extraClass = $config['class'] ?? '';
        $timelineClass = $horizontal
            ? 'block-timeline-horizontal flex gap-8 overflow-x-auto py-4 ' . $extraClass
            : 'block-timeline-vertical flex flex-col gap-8 py-4 ' . $extraClass;
        ob_start();
        ?>
        <ul class="block-timeline <?= $timelineClass ?>">
            <?php foreach ($items as $item):
                $icon = $item['icon'] ?? '';
                $color = $item['color'] ?? 'bg-indigo-600';
                $date = $item['date'] ?? '';
                $content = $item['content'] ?? '';
                $block = $item['block'] ?? '';
            ?>
                <li class="block-timeline-item flex <?= $horizontal ? 'flex-col items-center min-w-[220px]' : 'items-start' ?> animate-fade-in-up">
                    <div class="flex items-center gap-2 mb-2">
                        <?php if ($icon): ?><span class="block-timeline-icon w-8 h-8 rounded-full flex items-center justify-center <?= $color ?> text-white"><i class="fa <?= htmlspecialchars($icon) ?>"></i></span><?php endif; ?>
                        <?php if ($date): ?><span class="block-timeline-date text-xs text-gray-400"> <?= htmlspecialchars($date) ?> </span><?php endif; ?>
                    </div>
                    <div class="block-timeline-content w-full">
                        <?php if ($block): ?>
                            <?= $block ?>
                        <?php else: ?>
                            <span><?= $content ?></span>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php
        return ob_get_clean();
    }
}
