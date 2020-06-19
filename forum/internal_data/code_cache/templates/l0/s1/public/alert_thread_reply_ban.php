<?php
// FROM HASH: 6510dc1e694a02d51c94ec793b4499a0
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__vars['extra']['expiry']) {
		$__finalCompiled .= '
	' . 'You will be unable to reply to the thread ' . ((((('<a href="' . $__templater->func('link', array('threads', $__vars['content'], ), true)) . '" class="fauxBlockLink-blockLink">') . $__templater->func('prefix', array('thread', $__vars['thread'], ), true)) . $__templater->escape($__vars['content']['title'])) . '</a>') . ' until ' . $__templater->func('date', array($__vars['extra']['expiry'], ), true) . '.' . '
';
	} else {
		$__finalCompiled .= '
	' . 'You are no longer able to reply to the thread ' . ((((('<a href="' . $__templater->func('link', array('threads', $__vars['content'], ), true)) . '" class="fauxBlockLink-blockLink">') . $__templater->func('prefix', array('thread', $__vars['thread'], ), true)) . $__templater->escape($__vars['content']['title'])) . '</a>') . '.' . '
';
	}
	$__finalCompiled .= '
';
	if ($__vars['extra']['reason']) {
		$__finalCompiled .= 'Reason' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['extra']['reason']);
	}
	return $__finalCompiled;
});