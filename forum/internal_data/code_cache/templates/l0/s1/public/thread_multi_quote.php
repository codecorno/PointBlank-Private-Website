<?php
// FROM HASH: 7490a27e68539d506e0a9519a29b826c
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->callMacro('multi_quote_macros', 'block', array(
		'quotes' => $__vars['quotes'],
		'messages' => $__vars['posts'],
		'containerRelation' => 'Thread',
		'dateKey' => 'post_date',
		'bbCodeContext' => 'post',
	), $__vars) . '
';
	return $__finalCompiled;
});