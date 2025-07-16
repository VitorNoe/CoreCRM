<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'CRM V1'; ?></title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/public/assets/css/blocks.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { width: 80%; margin: 20px auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body>
<?php
require_once __DIR__ . '/blocks/BlockRenderer.php';
$user = AuthHandler::isLoggedIn() ? ['name' => $_SESSION['user_id'] ?? 'Usuário'] : null;
$actions = AuthHandler::isLoggedIn()
    ? [ ['label'=>'Logout','href'=>'/logout','class'=>'bg-red-500 hover:bg-red-600'] ]
    : [ ['label'=>'Login','href'=>'/login','class'=>'bg-indigo-500 hover:bg-indigo-600'] ];
echo BlockRenderer::render('Header', [
    'title' => $title ?? 'CRM V1',
    'logo' => '<a href="/" class="text-xl font-bold tracking-tight hover:underline">CoreCRM</a>',
    'user' => $user,
    'actions' => $actions,
    'class' => '',
]);
?>


