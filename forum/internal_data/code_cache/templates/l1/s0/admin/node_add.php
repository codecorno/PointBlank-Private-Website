<?php
// FROM HASH: c633421b225abb67c9dca5f9b56c3507
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add node');
	$__finalCompiled .= '

';
	$__compilerTemp1 = array();
	if ($__templater->isTraversable($__vars['nodeTypes'])) {
		foreach ($__vars['nodeTypes'] AS $__vars['nodeType']) {
			$__compilerTemp1[] = array(
				'value' => $__vars['nodeType']['node_type_id'],
				'label' => $__templater->escape($__vars['nodeType']['title']),
				'_type' => 'option',
			);
		}
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formSelectRow(array(
		'name' => 'node_type_id',
	), $__compilerTemp1, array(
		'label' => 'Node type',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'submit' => 'Proceed' . $__vars['xf']['language']['ellipsis'],
	), array(
	)) . '
	</div>
	' . $__templater->formHiddenVal('parent_node_id', $__vars['parentNodeId'], array(
	)) . '
', array(
		'action' => $__templater->func('link', array('nodes/add', ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});