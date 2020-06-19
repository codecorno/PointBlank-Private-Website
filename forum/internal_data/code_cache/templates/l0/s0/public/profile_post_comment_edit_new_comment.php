<?php
// FROM HASH: 0cee8256a5318414827ada031e81cdf2
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->callMacro('profile_post_macros', 'comment', array(
		'profilePost' => $__vars['profilePost'],
		'comment' => $__vars['comment'],
	), $__vars);
	return $__finalCompiled;
});