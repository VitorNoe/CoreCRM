<?php
require_once __DIR__ . '/blocks/BlockRenderer.php';
echo BlockRenderer::render('Footer', [
    'breadcrumbs' => true,
    'clock' => true,
    'status' => 'Online',
    'content' => '&copy; ' . date('Y') . ' CoreCRM',
    'class' => '',
]);
?>


