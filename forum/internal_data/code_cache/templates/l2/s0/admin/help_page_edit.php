<?php
// FROM HASH: 546d238a133701b0c16e307ac181981b
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['page'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Adicionar página de ajuda');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Editar página de ajuda' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['page']['title']));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['page'], 'isUpdate', array()) AND $__templater->method($__vars['page'], 'canEdit', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array('help-pages/delete', $__vars['page'], ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

';
	if (!$__templater->method($__vars['page'], 'canEdit', array())) {
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
		'name' => 'page_name',
		'value' => $__vars['page']['page_name'],
		'readonly' => (!$__templater->method($__vars['page'], 'canEdit', array())),
		'maxlength' => $__templater->func('max_length', array($__vars['page'], 'page_name', ), false),
	), array(
		'label' => 'Parte de URL',
		'explain' => '
					' . 'Isso representa a parte do URL após a parte <i>ajuda</i> que identifica esta página exclusivamente. Você pode usar apenas caracteres a-z, 0-9, _ e -.' . '
				',
	)) . '

			' . $__templater->formHiddenVal('page_id', $__vars['page']['page_id'], array(
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'title',
		'value' => ($__templater->method($__vars['page'], 'exists', array()) ? $__vars['page']['MasterTitle']['phrase_text'] : ''),
		'readonly' => (!$__templater->method($__vars['page'], 'canEdit', array())),
	), array(
		'label' => 'Título',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'description',
		'value' => ($__templater->method($__vars['page'], 'exists', array()) ? $__vars['page']['MasterDescription']['phrase_text'] : ''),
		'readonly' => (!$__templater->method($__vars['page'], 'canEdit', array())),
	), array(
		'label' => 'Descrição',
	)) . '

			' . $__templater->callMacro('display_order_macros', 'row', array(
		'value' => $__vars['page']['display_order'],
		'explain' => (($__vars['xf']['development'] AND $__vars['page']['addon_id']) ? 'The value of this field will not be changed when this add-on is upgraded.' : ''),
	), $__vars) . '

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'advanced_mode',
		'value' => '1',
		'selected' => $__vars['page']['advanced_mode'],
		'readonly' => (!$__templater->method($__vars['page'], 'canEdit', array())),
		'label' => 'Modo avançado',
		'hint' => 'Se ativado, o HTML para sua página não será contido dentro de um bloco.',
		'_type' => 'option',
	)), array(
	)) . '

			' . $__templater->formCodeEditorRow(array(
		'name' => 'content',
		'value' => ($__templater->method($__vars['page'], 'isUpdate', array()) ? $__vars['page']['MasterTemplate']['template'] : ''),
		'mode' => 'html',
		'class' => 'codeEditor--short',
		'readonly' => (!$__templater->method($__vars['page'], 'canEdit', array())),
	), array(
		'label' => 'Conteúdo da página',
		'explain' => 'Você pode usar a sintaxe do modelo XenForo aqui.',
	)) . '

			' . $__templater->formRow('
				' . $__templater->callMacro('helper_callback_fields', 'callback_fields', array(
		'data' => $__vars['page'],
		'readOnly' => (!$__templater->method($__vars['page'], 'canEdit', array())),
	), $__vars) . '
			', array(
		'rowtype' => 'input',
		'label' => 'PHP callback',
		'explain' => 'Você pode opcionalmente especificar um retorno de chamada PHP aqui para buscar mais dados ou alterar a resposta do controlador para sua página.<br />
<br />
Argumentos de retorno de chamada:
<ol>
	<li><code>\\XF\\Pub\\Controller\\AbstractController $controller</code><br />A instância do controlador. A partir daí você pode inspecionar o pedido, resposta, etc.</li>
	<li><code>\\XF\\Mvc\\Reply\\AbstractReply &$reply</code><br />A resposta padrão do controlador de página.</li>
</ol>',
	)) . '

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'active',
		'value' => '1',
		'selected' => $__vars['page']['active'],
		'hint' => (($__vars['xf']['development'] AND $__vars['page']['addon_id']) ? 'The value of this field will not be changed when this add-on is upgraded.' : ''),
		'label' => '
					' . 'Ativado' . '
				',
		'_type' => 'option',
	)), array(
	)) . '

			' . $__templater->callMacro('addon_macros', 'addon_edit', array(
		'addOnId' => $__vars['page']['addon_id'],
	), $__vars) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'sticky' => 'true',
		'icon' => 'save',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('help-pages/save', $__vars['page'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});