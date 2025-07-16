<?php
/**
 * BannerBlock
 *
 * Renderiza um banner/hero responsivo com imagem de fundo, overlay, título, subtítulo, botão (CTA), ícone FontAwesome e alinhamento customizável.
 *
 * Config:
 *   - title: string (título principal)
 *   - subtitle: string (subtítulo)
 *   - image: string (URL da imagem de fundo)
 *   - overlay: string (cor do overlay, ex: 'bg-black/50')
 *   - align: string ('left', 'center', 'right')
 *   - icon: string (classe FontAwesome, ex: 'fa-bullhorn')
 *   - button: array (['label' => 'Texto', 'href' => '/rota', 'icon' => 'fa-arrow-right'])
 *   - height: string (altura, ex: 'h-64', 'min-h-[300px]')
 *
 * Exemplo de uso:
 *   echo BlockRenderer::render('Banner', [
 *     'title' => 'Bem-vindo ao CoreCRM',
 *     'subtitle' => 'Sua gestão, seu jeito.',
 *     'image' => '/assets/img/banner.jpg',
 *     'overlay' => 'bg-indigo-900/60',
 *     'align' => 'center',
 *     'icon' => 'fa-bullhorn',
 *     'button' => [
 *       'label' => 'Comece agora',
 *       'href' => '/cadastro',
 *       'icon' => 'fa-arrow-right',
 *     ],
 *     'height' => 'h-72',
 *   ]);
 */
class BannerBlock {
    public static function render($config = []) {
        $title = $config['title'] ?? '';
        $subtitle = $config['subtitle'] ?? '';
        $image = $config['image'] ?? '';
        $overlay = $config['overlay'] ?? 'bg-black/40';
        $align = $config['align'] ?? 'center';
        $icon = $config['icon'] ?? null;
        $button = $config['button'] ?? null;
        $height = $config['height'] ?? 'h-64';
        $alignClass = [
            'left' => 'items-start text-left',
            'center' => 'items-center text-center',
            'right' => 'items-end text-right',
        ][$align] ?? 'items-center text-center';
        ob_start();
        ?>
        <section class="relative w-full flex justify-center overflow-hidden <?= $height ?>">
            <?php if ($image): ?>
                <img src="<?= htmlspecialchars($image) ?>" alt="Banner" class="absolute inset-0 w-full h-full object-cover z-0" />
            <?php endif; ?>
            <div class="absolute inset-0 <?= $overlay ?> z-10"></div>
            <div class="relative z-20 flex flex-col gap-4 justify-center <?= $alignClass ?> w-full max-w-3xl px-6 mx-auto">
                <?php if ($icon): ?>
                    <span class="inline-block text-4xl text-white/80 mb-2"><i class="fa <?= htmlspecialchars($icon) ?>"></i></span>
                <?php endif; ?>
                <?php if ($title): ?>
                    <h1 class="text-3xl md:text-4xl font-bold text-white drop-shadow-lg"><?= htmlspecialchars($title) ?></h1>
                <?php endif; ?>
                <?php if ($subtitle): ?>
                    <p class="text-lg md:text-xl text-white/90 mb-2"><?= htmlspecialchars($subtitle) ?></p>
                <?php endif; ?>
                <?php if ($button && !empty($button['label'])): ?>
                    <a href="<?= htmlspecialchars($button['href'] ?? '#') ?>" class="inline-flex items-center gap-2 px-6 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white font-semibold shadow transition">
                        <?php if (!empty($button['icon'])): ?><i class="fa <?= htmlspecialchars($button['icon']) ?>"></i><?php endif; ?>
                        <?= htmlspecialchars($button['label']) ?>
                    </a>
                <?php endif; ?>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }
}
