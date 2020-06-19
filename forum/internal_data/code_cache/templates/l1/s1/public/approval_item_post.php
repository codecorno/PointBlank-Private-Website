<?php
// FROM HASH: 9913f773306175bc51f640c351bb27ab
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->callMacro('approval_queue_macros', 'item_message_type', array(
		'content' => $__vars['content'],
		'user' => $__vars['content']['User'],
		'messageHtml' => $__templater->func('bb_code', array($__vars['content']['message'], 'post', $__vars['content'], ), false),
		'typePhraseHtml' => 'Post',
		'spamDetails' => $__vars['spamDetails'],
		'unapprovedItem' => $__vars['unapprovedItem'],
		'handler' => $__vars['handler'],
		'headerPhraseHtml' => 'Posted in thread <a href="' . $__templater->func('link', array('posts', $__vars['content'], ), false) . '">' . $__vars['content']['Thread']['title'] . '</a> in forum <a href="' . $__templater->func('link', array('forums', $__vars['content']['Thread']['Forum'], ), false) . '">' . $__vars['content']['Thread']['Forum']['title'] . '</a>',
	), $__vars) . '
';
	return $__finalCompiled;
});