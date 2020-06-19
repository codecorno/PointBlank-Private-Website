<?php
// FROM HASH: 56626b9e46ff1258e8048624eefdd076
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Development');
	$__finalCompiled .= '

' . $__templater->callMacro('section_nav_macros', 'section_nav', array(
		'section' => 'development',
	), $__vars);
	return $__finalCompiled;
});