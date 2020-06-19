<?php
// FROM HASH: 410932bcf89ff8585a17f98d6fa3e2e4
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->callMacro('conversation_message_macros', 'message', array(
		'message' => $__vars['message'],
		'conversation' => $__vars['conversation'],
	), $__vars);
	return $__finalCompiled;
});