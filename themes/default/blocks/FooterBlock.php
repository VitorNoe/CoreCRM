<?php
/**
 * FooterBlock
 *
 * Renderiza o rodapé do sistema, com breadcrumbs, relógio, status, conteúdo customizado, responsividade, etc.
 *
 * Config:
 *   - breadcrumbs: bool (exibe breadcrumbs, padrão true)
 *   - clock: bool (exibe relógio, padrão true)
 *   - status: string (texto de status, padrão 'Online')
 *   - content: string|array (conteúdo extra)
 *   - class: string (classes extras)
 *
 * Exemplo de uso:
 *   echo BlockRenderer::render('Footer', [
 *     'breadcrumbs' => true,
 *     'clock' => true,
 *     'status' => 'Online',
 *     'content' => '&copy; 2025 CoreCRM',
 *   ]);
 */
class FooterBlock {
    public static function render($config = []) {
        $breadcrumbs = $config['breadcrumbs'] ?? true;
        $clock = $config['clock'] ?? true;
        $status = $config['status'] ?? 'Online';
        $content = $config['content'] ?? ('&copy; ' . date('Y') . ' CoreCRM');
        $extraClass = $config['class'] ?? '';
        ob_start();
        ?>
        <footer class="block-footer w-full flex flex-col sm:flex-row items-center justify-between px-4 py-2 bg-gray-100 border-t <?= $extraClass ?>">
            <div class="flex-1 flex items-center gap-4">
                <?php if ($breadcrumbs): ?>
                <span id="footer-breadcrumbs" class="truncate">
                    <span id="breadcrumb">Página: <span id="breadcrumb-current">Carregando...</span></span>
                </span>
                <?php endif; ?>
                <?php if ($clock): ?>
                <span id="footer-clock">--:--:--</span>
                <?php endif; ?>
            </div>
            <div class="flex-1 flex items-center justify-end gap-4">
                <span class="hidden sm:inline" id="footer-system-status">Status: <span class="text-green-400"><?= htmlspecialchars($status) ?></span></span>
                <span><?= is_array($content) ? implode(' ', $content) : $content ?></span>
            </div>
            <script>
            // Relógio dinâmico
            function updateClock() {
                const now = new Date();
                const time = now.toLocaleTimeString('pt-BR');
                const date = now.toLocaleDateString('pt-BR');
                document.getElementById('footer-clock').textContent = date + ' ' + time;
            }
            setInterval(updateClock, 1000);
            updateClock();
            // Breadcrumb dinâmico
            function getCurrentPage() {
                let path = window.location.pathname;
                if (path === '/') return 'Home';
                let parts = path.split('/').filter(Boolean);
                return parts.length ? parts[parts.length-1].replace(/-/g, ' ').replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'Página';
            }
            document.getElementById('breadcrumb-current').textContent = getCurrentPage();
            </script>
        </footer>
        <?php
        return ob_get_clean();
    }
}
