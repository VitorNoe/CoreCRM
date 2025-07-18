<?php
// Lista de usuários
echo BlockRenderer::render('Header', [
    'title' => 'Usuários',
    'logo' => '<a href="/admin" class="text-xl font-bold tracking-tight hover:underline">CoreCRM Usuários</a>',
    'user' => ['name' => $_SESSION['user_id'] ?? 'Usuário'],
    'actions' => [
        ['label' => 'Voltar', 'href' => '/usuarios', 'class' => 'bg-gray-300 hover:bg-gray-400 text-gray-700']
    ]
]);
?>
<div class="container mx-auto mt-10">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-indigo-800">Usuários</h1>
        <a href="/usuarios/novo" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Novo Usuário</a>
    </div>
    <table class="min-w-full bg-white rounded shadow">
        <thead>
            <tr>
                <th class="py-2 px-4">ID</th>
                <th class="py-2 px-4">Username</th>
                <th class="py-2 px-4">Role</th>
                <th class="py-2 px-4">Name</th>
                <th class="py-2 px-4">Email</th>
                <th class="py-2 px-4">Criado em</th>
                <th class="py-2 px-4">Atualizado em</th>
                <th class="py-2 px-4">Ativo</th>
                <th class="py-2 px-4">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr class="justify-center itens-center text-center border-b hover:bg-indigo-50">
                <td class="justify-center py-2 px-4"><?php echo $user['id']; ?></td>
                <td class="justify-center py-2 px-4"><?php echo htmlspecialchars($user['username']); ?></td>
                <td class="justify-center py-2 px-4"><?php echo htmlspecialchars($user['role']); ?></td>
                <td class="justify-center py-2 px-4"><?php echo htmlspecialchars($user['name']); ?></td>
                <td class="justify-center py-2 px-4"><?php echo htmlspecialchars($user['email']); ?></td>
                <td class="justify-center py-2 px-4"><?php echo htmlspecialchars($user['created_at']); ?></td>
                <td class="justify-center py-2 px-4"><?php echo htmlspecialchars($user['updated_at']); ?></td>
                <td class="justify-center py-2 px-4"><?php echo $user['active'] ? 'Yes' : 'No'; ?></td>
                <td class="justify-center itens-center content-center py-2 px-4 flex gap-2">
                    <a href="/usuarios/editar/<?php echo $user['id']; ?>" class="align-middle text-center h-8 w-15 bg-yellow-400 text-white rounded my-auto content-center hover:bg-yellow-500 px-2 py-1 ">Editar</a>
                    <form method="post" action="/usuarios/apagar/<?php echo $user['id']; ?>" onsubmit="return confirm('Tem certeza que deseja apagar este usuário?');" class="inline my-auto">
                        <button type="submit" class="align-middle text-center h-8 w-15 bg-red-400 text-white rounded my-auto content-center hover:bg-red-500 px-2 py-1 ">Apagar</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php ThemeHandler::render_footer(); ?>
