<?php
// FROM HASH: fa1a8f3a59e3124e5134f560ef52208c
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['page'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Adicionar página');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Editar página' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['node']['title']));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['page'], 'isUpdate', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array('pages/delete', $__vars['node'], ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->callMacro('node_edit_macros', 'node_name', array(
		'node' => $__vars['node'],
		'optional' => false,
	), $__vars) . '

			' . $__templater->callMacro('node_edit_macros', 'title', array(
		'node' => $__vars['node'],
	), $__vars) . '
			' . $__templater->callMacro('node_edit_macros', 'description', array(
		'node' => $__vars['node'],
	), $__vars) . '
			' . $__templater->callMacro('node_edit_macros', 'position', array(
		'node' => $__vars['node'],
		'nodeTree' => $__vars['nodeTree'],
	), $__vars) . '
			' . $__templater->callMacro('node_edit_macros', 'navigation', array(
		'node' => $__vars['node'],
		'navChoices' => $__vars['navChoices'],
	), $__vars) . '

			' . $__templater->formCodeEditorRow(array(
		'name' => 'template',
		'value' => ($__templater->method($__vars['page'], 'isUpdate', array()) ? $__vars['page']['MasterTemplate']['template'] : ''),
		'mode' => 'html',
		'class' => 'codeEditor--short',
	), array(
		'hint' => ($__templater->method($__vars['page'], 'isUpdate', array()) ? $__templater->escape($__templater->method($__vars['page'], 'getTemplateName', array())) : ''),
		'label' => 'Modelo HTML',
		'explain' => 'Você pode usar a sintaxe do modelo XenForo aqui.',
	)) . '

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'log_visits',
		'selected' => $__vars['page']['log_visits'],
		'label' => 'Log and count visits to this page',
		'_type' => 'option',
	),
	array(
		'name' => 'list_siblings',
		'selected' => $__vars['page']['list_siblings'],
		'label' => 'List sibling nodes',
		'_type' => 'option',
	),
	array(
		'name' => 'list_children',
		'selected' => $__vars['page']['list_children'],
		'label' => 'List child nodes',
		'_type' => 'option',
	)), array(
		'rowclass' => 'surplusLabel',
		'label' => 'Componentes opcionais',
	)) . '

			' . $__templater->formRow('

				' . $__templater->callMacro('helper_callback_fields', 'callback_fields', array(
		'data' => $__vars['page'],
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
		'name' => 'advanced_mode',
		'value' => '1',
		'selected' => $__vars['page']['advanced_mode'],
		'label' => 'Modo avançado',
		'hint' => 'Se ativado, o HTML para sua página não será contido dentro de um bloco.',
		'_type' => 'option',
	)), array(
	)) . '

			' . $__templater->callMacro('node_edit_macros', 'style', array(
		'node' => $__vars['node'],
		'styleTree' => $__vars['styleTree'],
	), $__vars) . '
		</div>

		' . $__templater->formSubmitRow(array(
		'sticky' => 'true',
		'icon' => 'save',
	), array(
	)) . '
	</div>

', array(
		'action' => $__templater->func('link', array('pages/save', $__vars['node'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});