<?php
// FROM HASH: af569c1fbc8bb0847da9e90564fae984
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->callMacro('multi_quote_macros', 'block', array(
		'quotes' => $__vars['quotes'],
		'messages' => $__vars['messages'],
		'containerRelation' => 'Conversation',
		'dateKey' => 'message_date',
		'bbCodeContext' => 'conversation_message',
	), $__vars) . '
';
	return $__finalCompiled;
});