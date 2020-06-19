<?php
// FROM HASH: 403e795355b4c4930dcc1a89502d85a6
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->callMacro('prefix_macros', 'select', array(
		'name' => 'na',
		'prefixes' => $__vars['prefixes'],
		'type' => 'thread',
	), $__vars) . '
';
	return $__finalCompiled;
});