<?php
// FROM HASH: 04208a6ccc0811946ff5060573abae6a
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Users');
	$__finalCompiled .= '

' . $__templater->callMacro('section_nav_macros', 'section_nav', array(
		'section' => 'users',
	), $__vars);
	return $__finalCompiled;
});