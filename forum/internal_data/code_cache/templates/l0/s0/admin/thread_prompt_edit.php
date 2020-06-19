<?php
// FROM HASH: dfd1f5aee5ba9f377a6743aa698d2771
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__compilerTemp1 = $__vars;
	$__compilerTemp1['extraOptions'] = $__templater->preEscaped('
		' . $__templater->callMacro('forum_selection_macros', 'select_forums', array(
		'nodeIds' => $__vars['nodeIds'],
		'nodeTree' => $__vars['nodeTree'],
	), $__vars) . '
	');
	$__finalCompiled .= $__templater->includeTemplate('base_prompt_edit', $__compilerTemp1);
	return $__finalCompiled;
});