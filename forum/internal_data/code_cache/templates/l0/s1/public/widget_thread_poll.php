<?php
// FROM HASH: 737cb382f2cb04f1eb41ff4185d82403
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->callMacro('poll_macros', 'poll_block', array(
		'poll' => $__vars['poll'],
		'simpleDisplay' => true,
		'forceTitle' => $__vars['title'],
	), $__vars);
	return $__finalCompiled;
});