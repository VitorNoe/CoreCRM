<?php
/**
 * BreadcrumbBlock
 *
 * Renderiza breadcrumbs responsivos com suporte a ícones FontAwesome, separador customizável, último item destacado, e opção de menu dropdown.
 *
 * Config:
 *   - items: array [['label'=>..., 'href'=>..., 'icon'=>'fa-home', 'dropdown'=>[...]]]
 *   - separator: string (ex: '>', padrão: '/')
 *   - class: string (classes extras)
 *
 * Exemplo de uso:
 *   echo BlockRenderer::render('Breadcrumb', [
 *     'items' => [
 *       ['label'=>'Início','href'=>'/','icon'=>'fa-home'],
 *       ['label'=>'Clientes','href'=>'/clientes'],
 *       ['label'=>'João','href'=>'','icon'=>'fa-user'],
 *     ],
 *     'separator' => '>',
 *   ]);
 */
class BreadcrumbBlock {
    public static function render($config = []) {
        $items = $config['items'] ?? [];
        $separator = $config['separator'] ?? '/';
        $extraClass = $config['class'] ?? '';
        ob_start();
        ?>
        <nav class="block-breadcrumb flex items-center text-sm text-gray-500 <?= $extraClass ?>" aria-label="Breadcrumb">
            <?php foreach ($items as $i => $item):
                $isLast = $i === count($items) - 1;
            ?>
                <div class="flex items-center">
                    <?php if (!empty($item['icon'])): ?><i class="fa <?= htmlspecialchars($item['icon']) ?> mr-1"></i><?php endif; ?>
                    <?php if (!empty($item['href']) && !$isLast): ?>
                        <a href="<?= htmlspecialchars($item['href']) ?>" class="block-breadcrumb-item hover:underline text-indigo-600 flex items-center">
                            <?= htmlspecialchars($item['label']) ?>
                        </a>
                    <?php else: ?>
                        <span class="block-breadcrumb-current font-semibold text-gray-900 flex items-center">
                            <?= htmlspecialchars($item['label']) ?>
                        </span>
                    <?php endif; ?>
                    <?php if ($i < count($items) - 1): ?>
                        <span class="block-breadcrumb-sep mx-2 select-none text-gray-400"> <?= htmlspecialchars($separator) ?> </span>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </nav>
        <?php
        return ob_get_clean();
    }
}
