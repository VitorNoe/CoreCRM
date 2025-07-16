<?php
/**
 * BlockRenderer
 *
 * Centralizador de blocos reutilizáveis do tema Default.
 * Faz o require e executa o render do bloco solicitado.
 *
 * Uso:
 *   echo BlockRenderer::render('Card', [...]);
 *
 * Todos os blocos devem estar em /themes/default/blocks/ e seguir o padrão NomeBlock.php.
 */
class BlockRenderer {
    /**
     * Renderiza um bloco reutilizável do tema.
     * @param string $block Nome do bloco (ex: 'Card', 'Banner', 'Table')
     * @param array $config Configuração do bloco
     * @return string HTML renderizado
     */
    public static function render($block, $config = []) {
        $file = __DIR__ . '/' . ucfirst($block) . 'Block.php';
        if (file_exists($file)) {
            require_once $file;
            $class = ucfirst($block) . 'Block';
            if (class_exists($class)) {
                return $class::render($config);
            }
        }
        return "<!-- Bloco $block não encontrado -->";
    }
}
