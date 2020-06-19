<?php
// FROM HASH: 82049dfe0b2d5ab6daec6dfe0b198476
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Appearance');
	$__finalCompiled .= '

' . $__templater->callMacro('section_nav_macros', 'section_nav', array(
		'section' => 'appearance',
	), $__vars);
	return $__finalCompiled;
});