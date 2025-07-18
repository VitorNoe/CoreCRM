<?php
$isEdit = isset($user);
require_once __DIR__ . '/../../../../themes/default/blocks/BlockRenderer.php';

echo BlockRenderer::render('Header', [
    'title' => $isEdit ? 'Editar Usuário' : 'Novo Usuário',
    'logo' => '<a href="/usuarios" class="text-xl font-bold tracking-tight hover:underline">CoreCRM Usuários</a>',
    'user' => ['name' => $_SESSION['user_id'] ?? 'Usuário'],
    'actions' => [
        ['label' => 'Voltar', 'href' => '/usuarios', 'class' => 'bg-gray-300 hover:bg-gray-400 text-gray-700']
    ]
]);

$inputClass = 'mt-1 block w-full rounded border border-gray-300 bg-white text-gray-900 dark:bg-gray-800 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150';
$selectClass = 'mt-1 block w-full rounded border border-gray-300 bg-white text-gray-900 dark:bg-gray-800 dark:text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-150';
$checkboxClass = 'accent-indigo-600 dark:accent-indigo-400';

$formHtml = '<form method="POST" class="space-y-4 w-full max-w-lg mx-auto">'
    . '<label class="block">Name:<input type="text" name="name" value="' . htmlspecialchars($user['name'] ?? '') . '" required class="' . $inputClass . '"></label>'
    . '<label class="block">Nome:<input type="text" name="nome" value="' . htmlspecialchars($user['nome'] ?? '') . '" required class="' . $inputClass . '"></label>'
    . '<label class="block">Email:<input type="email" name="email" value="' . htmlspecialchars($user['email'] ?? '') . '" required class="' . $inputClass . '"></label>'
    . '<label class="block">Username:<input type="text" name="username" value="' . htmlspecialchars($user['username'] ?? '') . '" class="' . $inputClass . '"></label>'
    . '<label class="block">Role:<select name="role" class="' . $selectClass . '">' 
        . '<option value="admin"' . (($user['role'] ?? '')=='admin' ? ' selected' : '') . '>Admin</option>'
        . '<option value="user"' . (($user['role'] ?? '')=='user' ? ' selected' : '') . '>User</option>'
        . '<option value="moderator"' . (($user['role'] ?? '')=='moderator' ? ' selected' : '') . '>Moderator</option>'
    . '</select></label>'
    . '<label class="block">Tipo:<select name="tipo" class="' . $selectClass . '">' 
        . '<option value="admin"' . (($user['tipo'] ?? '')=='admin' ? ' selected' : '') . '>Admin</option>'
        . '<option value="usuario"' . (($user['tipo'] ?? '')=='usuario' ? ' selected' : '') . '>Usuário</option>'
        . '<option value="moderador"' . (($user['tipo'] ?? '')=='moderador' ? ' selected' : '') . '>Moderador</option>'
    . '</select></label>'
    . '<label class="flex items-center gap-2"><input type="checkbox" name="active" value="1"' . (($user['active'] ?? 1) ? ' checked' : '') . ' class="' . $checkboxClass . '"> Active</label>'
    . '<label class="flex items-center gap-2"><input type="checkbox" name="ativo" value="1"' . (($user['ativo'] ?? 1) ? ' checked' : '') . ' class="' . $checkboxClass . '"> Ativo</label>'
    . '<label class="block">Password:<input type="password" name="password" class="' . $inputClass . '"' . (!$isEdit ? ' required' : '') . '>'
        . ($isEdit ? '<span class="text-xs text-gray-400 dark:text-gray-300">Preencha para alterar a senha</span>' : '')
    . '</label>'
    . '<label class="block">Senha:<input type="password" name="senha" class="' . $inputClass . '"' . (!$isEdit ? ' required' : '') . '>'
        . ($isEdit ? '<span class="text-xs text-gray-400 dark:text-gray-300">Preencha para alterar a senha</span>' : '')
    . '</label>'
    . '<div class="flex flex-col sm:flex-row gap-4 mt-4">'
        . '<button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 w-full sm:w-auto">' . ($isEdit ? 'Salvar' : 'Criar') . '</button>'
        . '<a href="/usuarios" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 w-full sm:w-auto text-center">Cancelar</a>'
    . '</div>'
    . '</form>';

echo '<div class="flex items-center justify-center min-h-screen w-full p-2 sm:p-4">';
echo BlockRenderer::render('Card', [
    'title' => $isEdit ? 'Editar Usuário' : 'Novo Usuário',
    'icon' => 'fa-user',
    'content' => $formHtml
]);
echo '</div>';

echo BlockRenderer::render('Footer', [
    'breadcrumbs' => true,
    'clock' => true,
    'status' => 'Admin Online',
    'content' => '&copy; ' . date('Y') . ' CoreCRM Admin'
]);
