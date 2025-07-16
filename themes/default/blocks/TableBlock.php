<?php
/**
 * TableBlock
 *
 * Renderiza uma tabela avançada, com suporte a busca, filtros, ordenação, paginação, seleção, ações, responsividade, layout flexível e documentação.
 *
 * Config:
 *   - columns: array [['key'=>'nome','label'=>'Nome','sortable'=>true,'filter'=>['options'=>['A','B']],'width'=>'w-32','align'=>'center']]
 *   - data: array (dados da tabela)
 *   - search: bool|string (true ou placeholder)
 *   - filters: array (filtros customizados)
 *   - sortable: bool (colunas ordenáveis)
 *   - pagination: array ['perPage'=>10,'page'=>1]
 *   - actions: array (['label'=>'Editar','icon'=>'fa-edit','callback'=>'jsFunction'])
 *   - selectable: bool (checkbox para seleção)
 *   - class: string (classes extras)
 *
 * Exemplo de uso:
 *   echo BlockRenderer::render('Table', [
 *     'columns' => [
 *       ['key'=>'nome','label'=>'Nome','sortable'=>true],
 *       ['key'=>'email','label'=>'Email'],
 *       ['key'=>'status','label'=>'Status','filter'=>['options'=>['Ativo','Inativo']]],
 *     ],
 *     'data' => [ ... ],
 *     'search' => 'Buscar usuário...',
 *     'sortable' => true,
 *     'pagination' => ['perPage'=>10,'page'=>1],
 *     'selectable' => true,
 *     'actions' => [ ['label'=>'Editar','icon'=>'fa-edit','callback'=>'editUser'] ],
 *   ]);
 */
class TableBlock {
    public static function render($config = []) {
        $columns = $config['columns'] ?? [];
        $data = $config['data'] ?? [];
        $search = $config['search'] ?? false;
        $filters = $config['filters'] ?? [];
        $sortable = $config['sortable'] ?? false;
        $pagination = $config['pagination'] ?? null;
        $actions = $config['actions'] ?? [];
        $selectable = !empty($config['selectable']);
        $extraClass = $config['class'] ?? '';
        $id = $config['id'] ?? 'table_' . uniqid();
        $perPage = $pagination['perPage'] ?? 10;
        $page = $pagination['page'] ?? 1;
        $total = count($data);
        $totalPages = $perPage > 0 ? ceil($total / $perPage) : 1;
        $start = $perPage > 0 ? ($page - 1) * $perPage : 0;
        $pagedData = $perPage > 0 ? array_slice($data, $start, $perPage) : $data;
        ob_start();
        ?>
        <div class="block-table-wrapper <?= $extraClass ?>">
            <?php if ($search): ?>
                <input type="text" class="block-table-search mb-2 px-3 py-2 rounded border w-full" placeholder="<?= is_string($search) ? htmlspecialchars($search) : 'Buscar...' ?>" data-table-search="<?= $id ?>" />
            <?php endif; ?>
            <?php if (!empty($filters)): ?>
                <div class="block-table-filters flex gap-4 mb-2">
                    <?php foreach ($filters as $filter): ?>
                        <label class="text-sm font-semibold">
                            <?= htmlspecialchars($filter['label']) ?>
                            <select class="ml-2 px-2 py-1 rounded border" data-table-filter="<?= htmlspecialchars($filter['key']) ?>">
                                <option value="">Todos</option>
                                <?php foreach ($filter['options'] as $opt): ?>
                                    <option value="<?= htmlspecialchars($opt) ?>"> <?= htmlspecialchars($opt) ?> </option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <div class="overflow-x-auto rounded-lg shadow">
                <table class="block-table min-w-full">
                    <thead>
                        <tr>
                            <?php if ($selectable): ?><th class="px-4 py-2"><input type="checkbox" data-table-select-all="<?= $id ?>" /></th><?php endif; ?>
                            <?php foreach ($columns as $col): ?>
                                <th class="px-4 py-2 text-left font-semibold <?= $col['width'] ?? '' ?> <?= $col['align'] ?? '' ?>" <?= $sortable && !empty($col['sortable']) ? 'data-sortable="true"' : '' ?>>
                                    <?= htmlspecialchars($col['label'] ?? $col['key']) ?>
                                </th>
                            <?php endforeach; ?>
                            <?php if (!empty($actions)): ?><th class="px-4 py-2">Ações</th><?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pagedData as $row): ?>
                            <tr>
                                <?php if ($selectable): ?><td class="px-4 py-2"><input type="checkbox" data-table-select-row="<?= $id ?>" /></td><?php endif; ?>
                                <?php foreach ($columns as $col): ?>
                                    <td class="px-4 py-2 <?= $col['align'] ?? '' ?>">
                                        <?php
                                        // Garante que $row é array
                                        if (is_array($row)) {
                                            echo htmlspecialchars($row[$col['key']] ?? '');
                                        } else {
                                            echo htmlspecialchars((string)$row);
                                        }
                                        ?>
                                    </td>
                                <?php endforeach; ?>
                                <?php if (!empty($actions)): ?>
                                    <td class="px-4 py-2">
                                        <div class="flex gap-2">
                                            <?php foreach ($actions as $action): ?>
                                                <button type="button" class="inline-flex items-center gap-1 px-3 py-1 rounded block-table-action text-xs font-semibold transition" onclick="<?= htmlspecialchars($action['callback'] ?? '') ?>(this)">
                                                    <?php if (!empty($action['icon'])): ?><i class="fa <?= htmlspecialchars($action['icon']) ?>"></i><?php endif; ?>
                                                    <?= htmlspecialchars($action['label']) ?>
                                                </button>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($totalPages > 1): ?>
                <div class="block-table-pagination flex justify-end items-center gap-2 mt-2">
                    <button type="button" class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300 text-xs font-semibold" data-table-page="<?= $id ?>" data-page="<?= max(1, $page-1) ?>" <?= $page <= 1 ? 'disabled' : '' ?>>Anterior</button>
                    <span class="text-xs">Página <?= $page ?> de <?= $totalPages ?></span>
                    <button type="button" class="px-3 py-1 rounded bg-gray-200 hover:bg-gray-300 text-xs font-semibold" data-table-page="<?= $id ?>" data-page="<?= min($totalPages, $page+1) ?>" <?= $page >= $totalPages ? 'disabled' : '' ?>>Próxima</button>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
