<?php
// FROM HASH: 1b88dda15fb7060760ab2e3e2495c1c0
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__compilerTemp1 = $__vars;
	$__compilerTemp1['extraOptions'] = $__templater->preEscaped('
		' . $__templater->formCheckBoxRow(array(
		'name' => 'apply_node_ids',
	), array(array(
		'label' => 'Apply forum options' . $__vars['xf']['language']['label_separator'],
		'_dependent' => array('
					' . $__templater->callMacro('forum_selection_macros', 'select_forums', array(
		'nodeIds' => $__vars['nodeIds'],
		'nodeTree' => $__vars['nodeTree'],
		'withRow' => '0',
	), $__vars) . '
				'),
		'_type' => 'option',
	)), array(
		'label' => 'Applicable forums',
	)) . '
	');
	$__finalCompiled .= $__templater->includeTemplate('base_prefix_quickset_editor', $__compilerTemp1);
	return $__finalCompiled;
});