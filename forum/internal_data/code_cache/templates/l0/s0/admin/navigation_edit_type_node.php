<?php
// FROM HASH: b3f50535790990b0709082a5c38fef8a
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__compilerTemp1 = array(array(
		'value' => '0',
		'label' => '&nbsp;',
		'_type' => 'option',
	));
	$__compilerTemp2 = $__templater->method($__vars['nodeTree'], 'getFlattened', array(0, ));
	if ($__templater->isTraversable($__compilerTemp2)) {
		foreach ($__compilerTemp2 AS $__vars['treeEntry']) {
			$__compilerTemp1[] = array(
				'value' => $__vars['treeEntry']['record']['node_id'],
				'label' => $__templater->func('repeat', array('--', $__vars['treeEntry']['depth'], ), true) . ' ' . $__templater->escape($__vars['treeEntry']['record']['title']),
				'_type' => 'option',
			);
		}
	}
	$__finalCompiled .= $__templater->formSelectRow(array(
		'name' => $__vars['formPrefix'] . '[node_id]',
		'value' => $__vars['config']['node_id'],
	), $__compilerTemp1, array(
		'label' => 'Node',
	)) . '

' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => $__vars['formPrefix'] . '[node_title]',
		'selected' => $__vars['config']['node_title'],
		'label' => '
		' . 'Display the node title instead of the navigation entry title' . '
	',
		'_type' => 'option',
	),
	array(
		'name' => $__vars['formPrefix'] . '[with_children]',
		'selected' => $__vars['config']['with_children'],
		'label' => '
		' . 'Display children in navigation' . '
	',
		'_type' => 'option',
	)), array(
	));
	return $__finalCompiled;
});