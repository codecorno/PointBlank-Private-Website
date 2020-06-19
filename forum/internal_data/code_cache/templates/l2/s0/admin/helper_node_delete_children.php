<?php
// FROM HASH: c64c9ad7000bbb84a741989e9b57012f
return array('macros' => array('delete_row' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'node' => '!',
		'nodeTree' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	if ($__templater->method($__vars['node'], 'hasChildren', array())) {
		$__finalCompiled .= '
		' . $__templater->formRadioRow(array(
			'name' => 'child_nodes_action',
		), array(array(
			'value' => 'move',
			'selected' => true,
			'label' => 'Attach this node\'s children to its parent',
			'_type' => 'option',
		),
		array(
			'value' => 'delete',
			'label' => 'Delete this node\'s children',
			'_type' => 'option',
		)), array(
		)) . '
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';

	return $__finalCompiled;
});