<?php
// FROM HASH: a2ff5e3a327a826d0a1bd600b233781f
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->callMacro('approval_queue_macros', 'item_message_type', array(
		'content' => $__vars['content'],
		'user' => $__vars['content']['User'],
		'messageHtml' => $__templater->func('bb_code', array($__vars['content']['message'], 'profile_post', $__vars['content'], ), false),
		'typePhraseHtml' => 'Profile post',
		'spamDetails' => $__vars['spamDetails'],
		'unapprovedItem' => $__vars['unapprovedItem'],
		'handler' => $__vars['handler'],
		'headerPhraseHtml' => 'Posted on profile <a href="' . $__templater->func('link', array('profile-posts', $__vars['content'], ), false) . '">' . $__vars['content']['ProfileUser']['username'] . '</a>',
	), $__vars);
	return $__finalCompiled;
});