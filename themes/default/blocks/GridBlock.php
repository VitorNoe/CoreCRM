<?php
/**
 * GridBlock
 *
 * Renderiza um grid flexível, com suporte a número de colunas, gap, responsividade, alinhamento, conteúdo customizado, etc.
 *
 * Config:
 *   - columns: int (número de colunas, padrão 2)
 *   - gap: string (ex: 'gap-4', padrão 'gap-4')
 *   - align: string ('start', 'center', 'end', padrão 'start')
 *   - items: array (conteúdo dos itens do grid)
 *   - class: string (classes extras)
 *
 * Exemplo de uso:
 *   echo BlockRenderer::render('Grid', [
 *     'columns' => 3,
 *     'gap' => 'gap-6',
 *     'align' => 'center',
 *     'items' => [
 *       BlockRenderer::render('Card', [...]),
 *       BlockRenderer::render('Card', [...]),
 *     ],
 *   ]);
 */
class GridBlock {
    public static function render($config = []) {
        $columns = $config['columns'] ?? 2;
        $gap = $config['gap'] ?? 'gap-4';
        $align = $config['align'] ?? 'start';
        $items = $config['items'] ?? [];
        $extraClass = $config['class'] ?? '';
        $alignClass = [
            'start' => 'items-start',
            'center' => 'items-center',
            'end' => 'items-end',
        ][$align] ?? 'items-start';
        ob_start();
        ?>
        <div class="block-grid grid <?= $gap ?> <?= $alignClass ?> grid-cols-1 sm:grid-cols-<?= (int)$columns ?> <?= $extraClass ?>">
            <?php foreach ($items as $item): ?>
                <div class="block-grid-item"> <?= $item ?> </div>
            <?php endforeach; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
