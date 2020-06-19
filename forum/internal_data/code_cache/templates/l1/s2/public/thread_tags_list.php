<?php
// FROM HASH: 3b53c7a26eec97cece7ede6264a54f81
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->callMacro('tag_macros', 'list', array(
		'tags' => $__vars['thread']['tags'],
		'tagList' => 'tagList--thread-' . $__vars['thread']['thread_id'],
		'editLink' => ($__templater->method($__vars['thread'], 'canEditTags', array()) ? $__templater->func('link', array('threads/tags', $__vars['thread'], ), false) : ''),
	), $__vars);
	return $__finalCompiled;
});