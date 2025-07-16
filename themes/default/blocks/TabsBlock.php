<?php
/**
 * TabsBlock
 *
 * Renderiza abas horizontais ou verticais, com suporte a múltiplas abas, ícones, inserir outros blocos como conteúdo, animação, responsividade, etc.
 *
 * Config:
 *   - tabs: array [['label'=>'Aba 1','icon'=>'fa-user','content'=>BlockRenderer::render('Card', [...])]]
 *   - active: int (aba ativa, padrão 0)
 *   - vertical: bool (abas verticais, padrão false)
 *   - class: string (classes extras)
 *   - id: string (id do bloco)
 *
 * Exemplo de uso:
 *   echo BlockRenderer::render('Tabs', [
 *     'tabs' => [
 *       ['label'=>'Usuários','icon'=>'fa-user','content'=>BlockRenderer::render('Card', [...])],
 *       ['label'=>'Vendas','icon'=>'fa-shopping-cart','content'=>BlockRenderer::render('Table', [...])],
 *     ],
 *     'vertical' => true,
 *   ]);
 */
/**
 * TabsBlock
 *
 * Renderiza abas horizontais ou verticais, com múltiplas abas, ícones, blocos como conteúdo, animação, customização, etc.
 *
 * Config:
 *   - tabs: array [ ['label'=>..., 'icon'=>..., 'content'=>..., 'badge'=>..., 'disabled'=>bool] ]
 *   - active: int (aba ativa, padrão 0)
 *   - orientation: 'horizontal'|'vertical' (padrão 'horizontal')
 *   - class: string (classes extras)
 *   - tabClass: string (classes extras para cada aba)
 *   - contentClass: string (classes extras para conteúdo)
 *
 * Exemplo de uso:
 *   echo BlockRenderer::render('Tabs', [
 *     'tabs' => [
 *       [ 'label' => 'Aba 1', 'icon' => 'fa-home', 'content' => BlockRenderer::render('Card', [...]) ],
 *       [ 'label' => 'Aba 2', 'icon' => 'fa-user', 'content' => BlockRenderer::render('Form', [...]) ],
 *     ],
 *     'active' => 0,
 *     'orientation' => 'vertical',
 *   ]);
 */
class TabsBlock {
    public static function render($config = []) {
        $tabs = $config['tabs'] ?? [];
        $active = $config['active'] ?? 0;
        $orientation = $config['orientation'] ?? (!empty($config['vertical']) ? 'vertical' : 'horizontal');
        $extraClass = $config['class'] ?? '';
        $tabClass = $config['tabClass'] ?? '';
        $contentClass = $config['contentClass'] ?? '';
        $id = $config['id'] ?? 'tabs_' . uniqid();
        $tabListClass = $orientation === 'vertical'
            ? 'flex flex-col w-40 min-w-max border-r border-gray-200 bg-gray-50'
            : 'flex flex-row border-b border-gray-200 bg-gray-50';
        $tabItemClass = $orientation === 'vertical'
            ? 'px-4 py-3 cursor-pointer flex items-center gap-2 border-l-4 transition-all'
            : 'px-4 py-2 cursor-pointer flex items-center gap-2 border-b-4 transition-all';
        $activeClass = $orientation === 'vertical'
            ? 'border-blue-500 bg-white font-bold'
            : 'border-blue-500 bg-white font-bold';
        $disabledClass = 'opacity-50 cursor-not-allowed';
        $containerClass = $orientation === 'vertical'
            ? 'flex flex-row'
            : '';
        ob_start();
        ?>
        <div id="<?= htmlspecialchars($id) ?>" class="block-tabs <?= $containerClass ?> <?= $extraClass ?>">
            <ul class="block-tabs-list <?= $tabListClass ?>">
                <?php foreach ($tabs as $i => $tab):
                    $isActive = $i == $active;
                    $isDisabled = !empty($tab['disabled']);
                    $icon = !empty($tab['icon']) ? '<i class=\"fa '.htmlspecialchars($tab['icon']).' mr-2\"></i>' : '';
                    $badge = isset($tab['badge']) ? '<span class=\"ml-2 px-2 py-1 text-xs rounded bg-blue-100 text-blue-700\">'.htmlspecialchars($tab['badge']).'</span>' : '';
                ?>
                    <li class="block-tabs-tab <?= $tabItemClass ?> <?= $tabClass ?><?= $isActive ? ' '.$activeClass : '' ?><?= $isDisabled ? ' '.$disabledClass : '' ?>"
                        data-tab="<?= $i ?>"
                        <?= $isDisabled ? 'aria-disabled="true"' : '' ?>
                        >
                        <?= $icon ?><?= htmlspecialchars($tab['label']) ?><?= $badge ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="block-tabs-content flex-1 <?= $contentClass ?> w-full">
                <?php
                // Renderiza apenas o conteúdo da aba ativa
                if (isset($tabs[$active]['content'])) {
                    echo $tabs[$active]['content'];
                }
                ?>
            </div>
        </div>
        <script>
        $(function(){
            $('#<?= htmlspecialchars($id) ?> .block-tabs-list .block-tabs-tab').on('click', function(){
                if($(this).hasClass('opacity-50')) return;
                var idx = $(this).data('tab');
                var $container = $(this).closest('.block-tabs');
                $container.find('.block-tabs-tab').removeClass('border-blue-500 bg-white font-bold active');
                $(this).addClass('border-blue-500 bg-white font-bold active');
                var tabs = <?php echo json_encode(array_map(function($tab){ return $tab['content'] ?? ''; }, $tabs)); ?>;
                $container.find('.block-tabs-content').html(tabs[idx]);
            });
        });
        </script>
        <?php
        return ob_get_clean();
    }
}
