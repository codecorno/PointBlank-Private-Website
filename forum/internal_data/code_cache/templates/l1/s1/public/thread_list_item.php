<?php
// FROM HASH: cf0aca55813359e7a7776693f4f631d8
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->callMacro('thread_list_macros', 'item', array(
		'thread' => $__vars['thread'],
		'forum' => $__vars['forum'],
		'forceRead' => $__vars['inlineMode'],
		'allowInlineMod' => false,
	), $__vars);
	return $__finalCompiled;
});