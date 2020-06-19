<?php
// FROM HASH: 16a7e5da76dc462e195a4b611bd52dd2
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Communication');
	$__finalCompiled .= '

' . $__templater->callMacro('section_nav_macros', 'section_nav', array(
		'section' => 'communication',
	), $__vars);
	return $__finalCompiled;
});