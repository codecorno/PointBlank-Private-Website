<?php
// FROM HASH: dcd96205fcae7d658192c8b08c5c6553
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->callMacro('profile_post_macros', 'profile_post', array(
		'profilePost' => $__vars['profilePost'],
		'allowInlineMod' => ($__vars['noInlineMod'] ? false : true),
	), $__vars) . '

';
	return $__finalCompiled;
});