<?php
// FROM HASH: ceaa1d6aa20a3b363621702a4a40bd70
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->callMacro('approval_queue_macros', 'item_message_type', array(
		'content' => $__vars['content'],
		'contentDate' => $__vars['content']['comment_date'],
		'user' => $__vars['content']['User'],
		'messageHtml' => $__templater->func('bb_code', array($__vars['content']['message'], 'profile_post_comment', $__vars['content'], ), false),
		'typePhraseHtml' => 'Profile post comment',
		'spamDetails' => $__vars['spamDetails'],
		'unapprovedItem' => $__vars['unapprovedItem'],
		'handler' => $__vars['handler'],
		'headerPhraseHtml' => 'Posted on profile post by <a href="' . $__templater->func('link', array('profile-posts/comments', $__vars['content'], ), false) . '">' . $__vars['content']['ProfilePost']['username'] . '</a>',
	), $__vars);
	return $__finalCompiled;
});