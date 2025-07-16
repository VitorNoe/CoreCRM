<?php
/**
 * ModalBlock
 *
 * Renderiza um modal flexível, com suporte a título, conteúdo customizado, ícone, botões, tamanho, overlay, animação, múltiplos modais, etc.
 *
 * Config:
 *   - id: string (id do modal)
 *   - title: string (título do modal)
 *   - icon: string (classe FontAwesome)
 *   - content: string|HTML|callable
 *   - buttons: array [['label'=>'Fechar','type'=>'button','icon'=>'fa-times','color'=>'bg-gray-400','close'=>true,'callback'=>'jsFunction']]
 *   - size: string (ex: 'min-w-[400px] max-w-lg', padrão)
 *   - overlay: string (cor do overlay, ex: 'bg-black/40', padrão)
 *   - open: bool (se inicia aberto)
 *   - class: string (classes extras)
 *
 * Exemplo de uso:
 *   echo BlockRenderer::render('Modal', [
 *     'id' => 'modal1',
 *     'title' => 'Confirmação',
 *     'icon' => 'fa-exclamation',
 *     'content' => '<p>Deseja realmente excluir?</p>',
 *     'buttons' => [
 *       ['label'=>'Cancelar','type'=>'button','icon'=>'fa-times','color'=>'bg-gray-400','close'=>true],
 *       ['label'=>'Excluir','type'=>'submit','icon'=>'fa-trash','color'=>'bg-red-600','callback'=>'deleteItem'],
 *     ],
 *     'size' => 'max-w-md',
 *     'open' => false,
 *   ]);
 */
class ModalBlock {
    public static function render($config = []) {
        $id = $config['id'] ?? 'modal_' . uniqid();
        $title = $config['title'] ?? '';
        $icon = $config['icon'] ?? '';
        $content = $config['content'] ?? '';
        $buttons = $config['buttons'] ?? [ ['label'=>'Fechar','type'=>'button','color'=>'bg-gray-400','close'=>true] ];
        $size = $config['size'] ?? 'min-w-[320px] max-w-lg';
        $overlay = $config['overlay'] ?? 'bg-black/40';
        $open = !empty($config['open']);
        $extraClass = $config['class'] ?? '';
        $modalClass = "block-modal fixed inset-0 flex items-center justify-center z-50 $overlay $extraClass";
        $contentClass = "block-modal-content rounded-lg shadow-lg p-8 relative $size";
        ob_start();
        ?>
        <div id="<?= htmlspecialchars($id) ?>" class="<?= $modalClass ?>" style="display:<?= $open ? 'flex' : 'none' ?>;">
            <div class="absolute inset-0 <?= $overlay ?>" onclick="document.getElementById('<?= htmlspecialchars($id) ?>').style.display='none'"></div>
            <div class="relative z-10 <?= $contentClass ?>">
                <?php if ($icon): ?><span class="text-3xl block-modal-icon mb-2 block"><i class="fa <?= htmlspecialchars($icon) ?>"></i></span><?php endif; ?>
                <?php if ($title): ?><h2 class="text-xl font-bold mb-2"> <?= htmlspecialchars($title) ?> </h2><?php endif; ?>
                <div class="block-modal-body mb-4"> <?= is_callable($content) ? $content() : $content ?> </div>
                <div class="block-modal-actions flex gap-2 justify-end mt-4">
                    <?php foreach ($buttons as $btn): ?>
                        <button type="<?= htmlspecialchars($btn['type'] ?? 'button') ?>" class="px-4 py-2 rounded block-modal-action font-semibold flex items-center gap-2" onclick="<?php if (!empty($btn['close'])): ?>document.getElementById('<?= htmlspecialchars($id) ?>').style.display='none';<?php endif; ?><?= !empty($btn['callback']) ? htmlspecialchars($btn['callback']) . '(this);' : '' ?>">
                            <?php if (!empty($btn['icon'])): ?><i class="fa <?= htmlspecialchars($btn['icon']) ?>"></i><?php endif; ?>
                            <?= htmlspecialchars($btn['label'] ?? 'Fechar') ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
