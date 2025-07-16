<?php
/**
 * FormBlock
 *
 * Renderiza um formulário flexível, com suporte a vários tipos de campos, validação básica, layout responsivo, botões, agrupamentos, ícones, etc.
 *
 * Config:
 *   - fields: array [['type'=>'text','name'=>'nome','label'=>'Nome','value'=>'','placeholder'=>'','icon'=>'fa-user','required'=>true,'options'=>[]]]
 *   - action: string (URL de envio)
 *   - method: string ('post' ou 'get')
 *   - buttons: array [['label'=>'Salvar','type'=>'submit','icon'=>'fa-save','color'=>'bg-indigo-600']]
 *   - groups: array (agrupamentos de campos)
 *   - class: string (classes extras)
 *
 * Exemplo de uso:
 *   echo BlockRenderer::render('Form', [
 *     'action' => '/salvar',
 *     'method' => 'post',
 *     'fields' => [
 *       ['type'=>'text','name'=>'nome','label'=>'Nome','icon'=>'fa-user','required'=>true],
 *       ['type'=>'email','name'=>'email','label'=>'Email','required'=>true],
 *       ['type'=>'select','name'=>'status','label'=>'Status','options'=>['Ativo','Inativo']],
 *     ],
 *     'buttons' => [ ['label'=>'Salvar','type'=>'submit','icon'=>'fa-save','color'=>'bg-indigo-600'] ],
 *   ]);
 */
class FormBlock {
    public static function render($config = []) {
        $fields = $config['fields'] ?? [];
        $action = $config['action'] ?? '#';
        $method = $config['method'] ?? 'post';
        $buttons = $config['buttons'] ?? [ ['label'=>'Salvar','type'=>'submit','color'=>'bg-indigo-600'] ];
        $groups = $config['groups'] ?? [];
        $extraClass = $config['class'] ?? '';
        ob_start();
        ?>
        <form class="block-form flex flex-col gap-4 <?= $extraClass ?>" action="<?= htmlspecialchars($action) ?>" method="<?= htmlspecialchars($method) ?>">
            <?php if (!empty($groups)): ?>
                <?php foreach ($groups as $group): ?>
                    <fieldset class="block-form-group border rounded p-4 mb-2">
                        <?php if (!empty($group['label'])): ?><legend class="font-bold text-sm mb-2"> <?= htmlspecialchars($group['label']) ?> </legend><?php endif; ?>
                        <?php foreach ($group['fields'] as $field): ?>
                            <?= self::renderField($field) ?>
                        <?php endforeach; ?>
                    </fieldset>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php foreach ($fields as $field): ?>
                <?= self::renderField($field) ?>
            <?php endforeach; ?>
            <div class="block-form-actions flex gap-2 mt-2">
                <?php foreach ($buttons as $btn): ?>
                    <button type="<?= htmlspecialchars($btn['type'] ?? 'submit') ?>" class="px-4 py-2 rounded <?= $btn['color'] ?? 'bg-indigo-600' ?> text-white font-semibold flex items-center gap-2">
                        <?php if (!empty($btn['icon'])): ?><i class="fa <?= htmlspecialchars($btn['icon']) ?>"></i><?php endif; ?>
                        <?= htmlspecialchars($btn['label'] ?? 'Salvar') ?>
                    </button>
                <?php endforeach; ?>
            </div>
        </form>
        <?php
        return ob_get_clean();
    }

    public static function renderField($field) {
        $type = $field['type'] ?? 'text';
        $name = $field['name'] ?? '';
        $label = $field['label'] ?? '';
        $value = $field['value'] ?? '';
        $placeholder = $field['placeholder'] ?? '';
        $icon = $field['icon'] ?? '';
        $required = !empty($field['required']);
        $options = $field['options'] ?? [];
        $fieldClass = 'block-form-field flex flex-col gap-1';
        ob_start();
        ?>
        <div class="<?= $fieldClass ?>">
            <?php if ($label): ?><label class="font-semibold text-sm mb-1" for="<?= htmlspecialchars($name) ?>"> <?= htmlspecialchars($label) ?> <?= $required ? '<span class=\"text-red-500\">*</span>' : '' ?> </label><?php endif; ?>
            <div class="flex items-center gap-2">
                <?php if ($icon): ?><i class="fa <?= htmlspecialchars($icon) ?> text-gray-400"></i><?php endif; ?>
                <?php if ($type === 'select'): ?>
                    <select name="<?= htmlspecialchars($name) ?>" class="px-3 py-2 rounded border w-full" <?= $required ? 'required' : '' ?>>
                        <?php foreach ($options as $opt): ?>
                            <option value="<?= htmlspecialchars($opt) ?>" <?= $value == $opt ? 'selected' : '' ?>> <?= htmlspecialchars($opt) ?> </option>
                        <?php endforeach; ?>
                    </select>
                <?php elseif ($type === 'textarea'): ?>
                    <textarea name="<?= htmlspecialchars($name) ?>" class="px-3 py-2 rounded border w-full" placeholder="<?= htmlspecialchars($placeholder) ?>" <?= $required ? 'required' : '' ?>><?= htmlspecialchars($value) ?></textarea>
                <?php else: ?>
                    <input type="<?= htmlspecialchars($type) ?>" name="<?= htmlspecialchars($name) ?>" value="<?= htmlspecialchars($value) ?>" placeholder="<?= htmlspecialchars($placeholder) ?>" class="px-3 py-2 rounded border w-full" <?= $required ? 'required' : '' ?> />
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
