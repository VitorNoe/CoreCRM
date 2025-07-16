<?php
/**
 * ToastBlock
 *
 * Renderiza um toast/alerta flutuante, com suporte a tipo, ícone, tempo de exibição, animação, botão de fechar, múltiplos toasts, etc.
 *
 * Config:
 *   - message: string (mensagem do toast)
 *   - type: string ('info', 'success', 'error', 'warning')
 *   - icon: string (classe FontAwesome)
 *   - duration: int (ms, tempo de exibição)
 *   - close: bool (exibe botão de fechar)
 *   - id: string (id do toast)
 *   - class: string (classes extras)
 *
 * Exemplo de uso:
 *   echo BlockRenderer::render('Toast', [
 *     'message' => 'Salvo com sucesso!',
 *     'type' => 'success',
 *     'icon' => 'fa-check',
 *     'duration' => 4000,
 *     'close' => true,
 *   ]);
 */
class ToastBlock {
    public static function render($config = []) {
        $message = $config['message'] ?? '';
        $type = $config['type'] ?? 'info';
        $icon = $config['icon'] ?? '';
        $duration = $config['duration'] ?? 3000;
        $close = !empty($config['close']);
        $id = $config['id'] ?? 'toast_' . uniqid();
        $extraClass = $config['class'] ?? '';
        $typeClass = [
            'info' => 'block-toast-info bg-blue-600',
            'success' => 'block-toast-success bg-green-600',
            'error' => 'block-toast-error bg-red-600',
            'warning' => 'block-toast-warning bg-yellow-500 text-gray-900',
        ][$type] ?? 'block-toast-info bg-blue-600';
        ob_start();
        ?>
        <div id="<?= htmlspecialchars($id) ?>" class="block-toast <?= $typeClass ?> <?= $extraClass ?> flex items-center gap-3 animate-fade-in-up" style="animation-duration:0.3s;">
            <?php if ($icon): ?><i class="fa <?= htmlspecialchars($icon) ?> text-xl"></i><?php endif; ?>
            <span class="flex-1"> <?= htmlspecialchars($message) ?> </span>
            <?php if ($close): ?>
                <button type="button" class="ml-2 px-2 py-1 rounded bg-white/20 hover:bg-white/30 text-xs font-bold" onclick="document.getElementById('<?= htmlspecialchars($id) ?>').style.display='none'">
                    <i class="fa fa-times"></i>
                </button>
            <?php endif; ?>
            <script>
                setTimeout(function(){
                    var el = document.getElementById('<?= htmlspecialchars($id) ?>');
                    if(el) el.style.display = 'none';
                }, <?= (int)$duration ?>);
            </script>
        </div>
        <?php
        return ob_get_clean();
    }
}
