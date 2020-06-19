<?php
// FROM HASH: f7c7e9a9a199b3f5444efc3fa2f3e9e0
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<div class="contentRow-title">
	';
	if ($__vars['user']['user_id'] == $__vars['content']['ProfileUser']['user_id']) {
		$__finalCompiled .= '
		' . '' . $__templater->func('username_link', array($__vars['user'], false, array('defaultname' => $__vars['newsFeed']['username'], ), ), true) . ' updated their <a href="' . $__templater->func('link', array('profile-posts', $__vars['content'], ), true) . '">status</a>.' . '
	';
	} else {
		$__finalCompiled .= '
		' . '' . $__templater->func('username_link', array($__vars['user'], false, array('defaultname' => $__vars['newsFeed']['username'], ), ), true) . ' left a message on ' . (((('<a href="' . $__templater->func('link', array('profile-posts', $__vars['content'], ), true)) . '">') . $__templater->escape($__vars['content']['ProfileUser']['username'])) . '</a>') . '\'s profile.' . '
	';
	}
	$__finalCompiled .= '
</div>

<div class="contentRow-snippet">' . $__templater->func('snippet', array($__vars['content']['message'], $__vars['xf']['options']['newsFeedMessageSnippetLength'], array('stripQuote' => true, ), ), true) . '</div>

<div class="contentRow-minor">' . $__templater->func('date_dynamic', array($__vars['newsFeed']['event_date'], array(
	))) . '</div>';
	return $__finalCompiled;
});