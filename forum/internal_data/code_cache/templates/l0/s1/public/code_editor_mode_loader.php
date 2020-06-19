<?php
// FROM HASH: 1812a1992090c0ec41f8ff7e37c43d41
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->callMacro('public:code_editor', 'editor_setup', array(
		'modeConfig' => $__vars['modeConfig'],
	), $__vars);
	return $__finalCompiled;
});