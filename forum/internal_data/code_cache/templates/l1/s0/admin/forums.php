<?php
// FROM HASH: 73bf8d8a1f675b4d8a890fd49c0e0418
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Forums');
	$__finalCompiled .= '

' . $__templater->callMacro('section_nav_macros', 'section_nav', array(
		'section' => 'forums',
	), $__vars);
	return $__finalCompiled;
});