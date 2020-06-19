<?php
// FROM HASH: c05e04b4328c792d76d369d6b623407a
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Tools');
	$__finalCompiled .= '

' . $__templater->callMacro('section_nav_macros', 'section_nav', array(
		'section' => 'tools',
	), $__vars);
	return $__finalCompiled;
});