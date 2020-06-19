<?php
// FROM HASH: aa4b18ba12981b209e37c8586f223f34
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->func('reaction', array(array(
		'id' => $__vars['reaction'],
		'content' => $__vars['content'],
		'link' => $__vars['link'],
		'params' => $__vars['params'],
		'list' => $__vars['list'],
		'class' => $__vars['class'],
		'init' => 'true',
		'hasreaction' => $__vars['hasReaction'],
		'small' => 'true',
		'showtitle' => 'true',
	)));
	return $__finalCompiled;
});