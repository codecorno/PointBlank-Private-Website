<?php
// FROM HASH: 7e1910369e668bb494015eba16a6d6e7
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Cast vote');
	$__finalCompiled .= '

';
	$__templater->breadcrumbs($__vars['breadcrumbs']);
	$__finalCompiled .= '

' . $__templater->callMacro('poll_macros', 'poll_block', array(
		'poll' => $__vars['poll'],
		'showVoteBlock' => true,
		'simpleDisplay' => $__vars['simpleDisplay'],
	), $__vars) . '
';
	return $__finalCompiled;
});