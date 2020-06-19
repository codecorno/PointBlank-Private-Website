<?php
// FROM HASH: 1833c5ab6cca01a7592a4593531d7cfa
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Logs');
	$__finalCompiled .= '

' . $__templater->callMacro('section_nav_macros', 'section_nav', array(
		'section' => 'logs',
	), $__vars);
	return $__finalCompiled;
});