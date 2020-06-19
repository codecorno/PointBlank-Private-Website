<?php
// FROM HASH: 47459eb4087dc629e52f2818ba74aa68
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->callMacro('thread_list_macros', 'item', array(
		'thread' => $__vars['thread'],
		'allowInlineMod' => ($__vars['noInlineMod'] ? false : true),
		'forum' => ($__vars['forumName'] ? null : $__vars['forum']),
	), $__vars) . '
';
	return $__finalCompiled;
});