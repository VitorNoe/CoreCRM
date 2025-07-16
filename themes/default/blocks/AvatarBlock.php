<?php
/**
 * AvatarBlock
 *
 * Renderiza um avatar circular, com suporte a tamanho customizável, borda, status e ícone FontAwesome opcional.
 *
 * Config:
 *   - src: string (URL da imagem)
 *   - alt: string (texto alternativo)
 *   - size: int (tamanho em px, padrão 40)
 *   - border: bool (exibe borda, padrão true)
 *   - status: string ("online", "offline", "busy", etc, exibe bolinha de status)
 *   - icon: string (classe FontAwesome, ex: 'fa-user')
 *   - badge: string (texto de badge, ex: número de notificações)
 *
 * Exemplo de uso:
 *   echo BlockRenderer::render('Avatar', [
 *     'src' => 'https://randomuser.me/api/portraits/men/1.jpg',
 *     'alt' => 'João',
 *     'size' => 48,
 *     'status' => 'online',
 *     'icon' => 'fa-user',
 *     'badge' => '3',
 *   ]);
 */
class AvatarBlock {
    public static function render($config = []) {
        $src = $config['src'] ?? '';
        $alt = $config['alt'] ?? 'Avatar';
        $size = $config['size'] ?? 40;
        $border = $config['border'] ?? true;
        $status = $config['status'] ?? null;
        $icon = $config['icon'] ?? null;
        $badge = $config['badge'] ?? null;
        $borderClass = $border ? 'ring-2 ring-indigo-400' : '';
        $statusColor = [
            'online' => 'bg-green-400',
            'offline' => 'bg-gray-400',
            'busy' => 'bg-red-500',
            'away' => 'bg-yellow-400',
        ];
        ob_start();
        ?>
        <div class="relative inline-block align-middle" style="width:<?= (int)$size ?>px;height:<?= (int)$size ?>px;">
            <?php if ($src): ?>
                <img class="block-avatar object-cover rounded-full <?= $borderClass ?>" src="<?= htmlspecialchars($src) ?>" alt="<?= htmlspecialchars($alt) ?>" style="width:<?= (int)$size ?>px;height:<?= (int)$size ?>px;" />
            <?php elseif ($icon): ?>
                <div class="flex items-center justify-center block-avatar-icon rounded-full <?= $borderClass ?>" style="width:<?= (int)$size ?>px;height:<?= (int)$size ?>px;font-size:<?= (int)($size*0.55) ?>px;">
                    <i class="fa <?= htmlspecialchars($icon) ?>"></i>
                </div>
            <?php else: ?>
                <div class="flex items-center justify-center block-avatar-icon rounded-full <?= $borderClass ?>" style="width:<?= (int)$size ?>px;height:<?= (int)$size ?>px;font-size:<?= (int)($size*0.5) ?>px;">
                    <i class="fa fa-user"></i>
                </div>
            <?php endif; ?>
            <?php if ($status && isset($statusColor[$status])): ?>
                <span class="absolute bottom-0 right-0 block w-3 h-3 rounded-full border-2 block-avatar-status <?= $statusColor[$status] ?>"></span>
            <?php endif; ?>
            <?php if ($badge): ?>
                <span class="absolute -top-1 -right-1 block-avatar-badge text-xs rounded-full px-1.5 py-0.5 shadow font-bold" style="font-size:10px;min-width:18px;line-height:1;"> <?= htmlspecialchars($badge) ?> </span>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
