<?php
// FROM HASH: a5894b2788e608c32efa59f3f7b3ef4d
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Confirmar ação');
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formInfoRow('
				' . 'Confirme que deseja excluir o seguinte' . $__vars['xf']['language']['label_separator'] . '
				<strong><a href="' . $__templater->func('link', array('pages/edit', $__vars['node'], ), true) . '">' . $__templater->escape($__vars['node']['title']) . '</a></strong>
			', array(
		'rowtype' => 'confirm',
	)) . '

			' . $__templater->callMacro('helper_node_delete_children', 'delete_row', array(
		'node' => $__vars['node'],
		'nodeTree' => $__vars['nodeTree'],
	), $__vars) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'icon' => 'delete',
	), array(
		'rowtype' => ((!$__templater->method($__vars['node'], 'hasChildren', array())) ? 'simple' : ''),
	)) . '
	</div>
	' . $__templater->func('redirect_input', array(null, null, true)) . '
', array(
		'action' => $__templater->func('link', array('pages/delete', $__vars['node'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});