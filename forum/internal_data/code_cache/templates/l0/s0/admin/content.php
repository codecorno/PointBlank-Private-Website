<?php
// FROM HASH: 727210c1fedc3330cba26ea2c762f005
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Content');
	$__finalCompiled .= '

' . $__templater->callMacro('section_nav_macros', 'section_nav', array(
		'section' => 'content',
	), $__vars);
	return $__finalCompiled;
});