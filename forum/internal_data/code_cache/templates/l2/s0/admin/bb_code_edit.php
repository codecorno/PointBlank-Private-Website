<?php
// FROM HASH: 602eeed15360a4d54c4e4db89041eece
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['bbCode'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Adicionar BBcode');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Editar BBcode' . $__vars['xf']['language']['label_separator'] . ' [' . $__templater->escape($__vars['bbCode']['bb_code_id']) . ']');
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['bbCode'], 'isUpdate', array()) AND $__templater->method($__vars['bbCode'], 'canEdit', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array('bb-codes/delete', $__vars['bbCode'], ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

';
	if ((!$__templater->method($__vars['bbCode'], 'canEdit', array()))) {
		$__finalCompiled .= '
	<div class="blockMessage blockMessage--important blockMessage--iconic">
		' . 'Only a limited number of fields in this item may be edited.' . '
	</div>
';
	}
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">

		<div class="block-body">
			' . $__templater->formTextBoxRow(array(
		'name' => 'bb_code_id',
		'value' => $__vars['bbCode']['bb_code_id'],
		'maxlength' => $__templater->func('max_length', array($__vars['bbCode'], 'bb_code_id', ), false),
		'readonly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
		'dir' => 'ltr',
	), array(
		'label' => 'Tag BBcode',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'title',
		'value' => ($__templater->method($__vars['bbCode'], 'exists', array()) ? $__vars['bbCode']['MasterTitle']['phrase_text'] : ''),
		'readonly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
	), array(
		'label' => 'Título',
	)) . '
			' . $__templater->formTextAreaRow(array(
		'name' => 'desc',
		'value' => ($__templater->method($__vars['bbCode'], 'exists', array()) ? $__vars['bbCode']['MasterDesc']['phrase_text'] : ''),
		'autosize' => 'true',
		'readonly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
	), array(
		'label' => 'Descrição',
	)) . '

			' . $__templater->formRadioRow(array(
		'name' => 'bb_code_mode',
		'value' => $__vars['bbCode']['bb_code_mode'],
		'readonly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
	), array(array(
		'value' => 'replace',
		'label' => 'Substituição simples',
		'_type' => 'option',
	),
	array(
		'value' => 'callback',
		'label' => 'PHP callback',
		'_type' => 'option',
	)), array(
		'label' => 'Modo de substituição',
	)) . '

			' . $__templater->formRadioRow(array(
		'name' => 'has_option',
		'value' => $__vars['bbCode']['has_option'],
		'readonly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
	), array(array(
		'value' => 'yes',
		'label' => 'Sim',
		'_type' => 'option',
	),
	array(
		'value' => 'no',
		'label' => 'Não',
		'_type' => 'option',
	),
	array(
		'value' => 'optional',
		'explain' => 'Essa tag funcionará com e sem a opção fornecida. Isso é mais comumente usado com callbacks PHP.',
		'label' => '
					' . 'Opcional' . '
				',
		'_type' => 'option',
	)), array(
		'label' => 'Supports option parameter',
	)) . '

			' . $__templater->formCodeEditorRow(array(
		'name' => 'replace_html',
		'value' => $__vars['bbCode']['replace_html'],
		'mode' => 'html',
		'data-line-wrapping' => 'true',
		'readonly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
		'class' => 'codeEditor--autoSize',
	), array(
		'label' => 'Substituição HTML',
		'explain' => 'Use {option} para fazer referência ao conteúdo dentro da opção da tag (se fornecida) e {text} para fazer referência ao conteúdo da tag.',
	)) . '

			' . $__templater->callMacro('helper_callback_fields', 'callback_row', array(
		'label' => 'PHP callback',
		'explain' => 'Esse retorno de chamada receberá esses parâmetros: $tagChildren, $tagOption, $tag,  array $options, \\XF\\BbCode\\Renderer\\AbstractRenderer $renderer.',
		'data' => $__vars['bbCode'],
		'readOnly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
	), $__vars) . '

			<hr class="formRowSep" />

			' . $__templater->formRadioRow(array(
		'name' => 'editor_icon_type',
		'value' => $__vars['bbCode']['editor_icon_type'],
		'readonly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
	), array(array(
		'value' => '',
		'label' => 'Nenhuma',
		'_type' => 'option',
	),
	array(
		'value' => 'fa',
		'label' => 'Ícone Font Awesome',
		'_dependent' => array($__templater->formTextBox(array(
		'name' => 'editor_icon_fa',
		'readonly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
		'value' => (($__vars['bbCode']['editor_icon_type'] == 'fa') ? $__vars['bbCode']['editor_icon_value'] : ''),
		'maxlength' => $__templater->func('max_length', array($__vars['bbCode'], 'editor_icon_value', ), false),
		'dir' => 'ltr',
	))),
		'_type' => 'option',
	),
	array(
		'value' => 'image',
		'label' => 'Imagem',
		'_dependent' => array($__templater->formTextBox(array(
		'name' => 'editor_icon_image',
		'readonly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
		'value' => (($__vars['bbCode']['editor_icon_type'] == 'image') ? $__vars['bbCode']['editor_icon_value'] : ''),
		'maxlength' => $__templater->func('max_length', array($__vars['bbCode'], 'editor_icon_value', ), false),
		'dir' => 'ltr',
	))),
		'_type' => 'option',
	)), array(
		'label' => 'Editar ícone',
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formTextAreaRow(array(
		'name' => 'example',
		'value' => ($__templater->method($__vars['bbCode'], 'exists', array()) ? $__vars['bbCode']['MasterExample']['phrase_text'] : ''),
		'autosize' => 'true',
		'readonly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
	), array(
		'label' => 'Example usage',
		'explain' => 'Se você fornecer um exemplo, este BBcode aparecerá na página de ajuda BBcode.',
	)) . '

			' . $__templater->formTextAreaRow(array(
		'name' => 'output',
		'value' => ($__templater->method($__vars['bbCode'], 'exists', array()) ? $__vars['bbCode']['MasterOutput']['phrase_text'] : ''),
		'autosize' => 'true',
		'readonly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
	), array(
		'label' => 'Exemplo de saída',
		'explain' => 'Controle como o exemplo aparecerá na página de ajuda BBcode. Se uma saída não for inserida, o exemplo será renderizado.',
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'allow_signature',
		'value' => '1',
		'selected' => $__vars['bbCode']['allow_signature'],
		'label' => '
					' . 'Permitir este BBcode em assinaturas' . '
				',
		'_type' => 'option',
	)), array(
	)) . '

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'active',
		'value' => '1',
		'selected' => $__vars['bbCode']['active'],
		'hint' => (($__vars['xf']['development'] AND $__vars['bbCode']['addon_id']) ? 'The value of this field will not be changed when this add-on is upgraded.' : ''),
		'label' => '
					' . 'Ativado' . '
				',
		'_type' => 'option',
	)), array(
	)) . '

			' . $__templater->callMacro('addon_macros', 'addon_edit', array(
		'addOnId' => $__vars['bbCode']['addon_id'],
	), $__vars) . '
		</div>

		<h3 class="block-formSectionHeader">
			<span class="collapseTrigger collapseTrigger--block" data-xf-click="toggle" data-target="< :up:next">
				<span class="block-formSectionHeader-aligner">' . 'Opções avançadas' . '</span>
			</span>
		</h3>
		<div class="block-body block-body--collapsible">
			' . $__templater->formTextAreaRow(array(
		'name' => 'option_regex',
		'value' => $__vars['bbCode']['option_regex'],
		'code' => 'true',
		'autosize' => 'true',
		'readonly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
	), array(
		'label' => 'Option match regular expression',
		'explain' => 'Se fornecida, a tag só será válida se a opção corresponder a esta expressão regular. Isso será ignorado se nenhuma opção for fornecida. Inclua os delimitadores e modificadores de padrão.',
	)) . '

			' . $__templater->formCheckBoxRow(array(
		'readonly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
	), array(array(
		'name' => 'disable_smilies',
		'value' => '1',
		'selected' => $__vars['bbCode']['disable_smilies'],
		'label' => '
					' . 'Desativar smilies' . '
				',
		'_type' => 'option',
	),
	array(
		'name' => 'disable_nl2br',
		'value' => '1',
		'selected' => $__vars['bbCode']['disable_nl2br'],
		'label' => '
					' . 'Disable line break conversion' . '
				',
		'_type' => 'option',
	),
	array(
		'name' => 'disable_autolink',
		'value' => '1',
		'selected' => $__vars['bbCode']['disable_autolink'],
		'label' => '
					' . 'Disable auto-linking' . '
				',
		'_type' => 'option',
	),
	array(
		'name' => 'plain_children',
		'value' => '1',
		'selected' => $__vars['bbCode']['plain_children'],
		'label' => '
					' . 'Parar de analisar BBcode' . '
				',
		'_type' => 'option',
	)), array(
		'label' => 'Dentro deste BBcode',
	)) . '

			' . $__templater->formCheckBoxRow(array(
		'readonly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
	), array(array(
		'name' => 'allow_empty',
		'value' => '1',
		'selected' => $__vars['bbCode']['allow_empty'],
		'label' => 'Exibir substituição HTML quando vazio',
		'explain' => 'Se selecionado, o HTML de substituição será exibido mesmo se não houver nenhum texto dentro deste BBcode. Normalmente, as tags BBcode vazias são silenciosamente ignoradas.',
		'_type' => 'option',
	)), array(
	)) . '

			' . $__templater->formNumberBoxRow(array(
		'name' => 'trim_lines_after',
		'value' => $__vars['bbCode']['trim_lines_after'],
		'min' => '0',
		'max' => '10',
		'readonly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
	), array(
		'label' => 'Trim line breaks after',
		'explain' => 'Se essa tag for uma tag de nível de bloco, você pode querer ignorar 1 ou 2 quebras de linha que vêm após essa tag. Isso impede a aparência de quebras de linha extras sendo inserido se os usuários colocar essa marca em sua própria linha.',
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formCodeEditorRow(array(
		'name' => 'replace_html_email',
		'value' => $__vars['bbCode']['replace_html_email'],
		'mode' => 'html',
		'data-line-wrapping' => 'true',
		'readonly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
		'class' => 'codeEditor--autoSize',
	), array(
		'label' => 'Substituição de e-mail HTML',
		'explain' => 'Se fornecido, isso substituirá a substituição HTML ao ser processado para um email HTML. Se este for deixado vazio, a substituição HTML padrão será usada.',
	)) . '

			' . $__templater->formCodeEditorRow(array(
		'name' => 'replace_text',
		'value' => $__vars['bbCode']['replace_text'],
		'mode' => 'text',
		'data-line-wrapping' => 'true',
		'readonly' => (!$__templater->method($__vars['bbCode'], 'canEdit', array())),
		'class' => 'codeEditor--autoSize',
	), array(
		'label' => 'Substituição de texto',
		'explain' => 'Se fornecido, esta substituição será usada ao renderizar essa tag para o texto. Se isso for deixado vazio, a tag será efetivamente ignorada, deixando apenas o texto dentro dele.',
	)) . '
		</div>

		' . $__templater->formSubmitRow(array(
		'sticky' => 'true',
		'icon' => 'save',
	), array(
	)) . '
	</div>

', array(
		'action' => $__templater->func('link', array('bb-codes/save', $__vars['bbCode'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});