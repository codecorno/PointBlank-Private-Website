<?php
// FROM HASH: 359a2d602f21610ed129a0913aa1466d
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Setup');
	$__finalCompiled .= '

' . $__templater->callMacro('section_nav_macros', 'section_nav', array(
		'section' => 'setup',
	), $__vars);
	return $__finalCompiled;
});