<?php
// FROM HASH: 9e173f4dd5c156b176a1f60236a89888
return array('macros' => array('go_thread_bar' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'thread' => '!',
		'watchType' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	<table cellpadding="10" cellspacing="0" border="0" width="100%" class="linkBar">
	<tr>
		<td>
			<a href="' . $__templater->func('link', array('canonical:threads/unread', $__vars['thread'], array('new' => 1, ), ), true) . '" class="button">' . 'View this thread' . '</a>
		</td>
		<td align="' . ($__vars['xf']['isRtl'] ? 'left' : 'right') . '">
			';
	if ($__vars['watchType'] == 'threads') {
		$__finalCompiled .= '
				<a href="' . $__templater->func('link', array('canonical:watched/threads', ), true) . '" class="buttonFake">' . 'Watched threads' . '</a>
			';
	} else if ($__vars['watchType'] == 'forums') {
		$__finalCompiled .= '
				<a href="' . $__templater->func('link', array('canonical:watched/forums', ), true) . '" class="buttonFake">' . 'Watched forums' . '</a>
			';
	}
	$__finalCompiled .= '
		</td>
	</tr>
	</table>
';
	return $__finalCompiled;
},
'watched_forum_footer' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'thread' => '!',
		'forum' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	' . '<p class="minorText">Please do not reply to this message. You must visit the forum to reply.</p>

<p class="minorText">This message was sent to you because you opted to watch the forum "' . $__templater->escape($__vars['forum']['title']) . '" at ' . $__templater->escape($__vars['xf']['options']['boardTitle']) . ' with email notification of new threads or messages. You will not receive any further emails about this thread until you have read the new messages.</p>

<p class="minorText">If you no longer wish to receive these emails, you may <a href="' . $__templater->func('link', array('canonical:email-stop/content', $__vars['xf']['toUser'], array('t' => 'forum', 'id' => $__vars['forum']['node_id'], ), ), true) . '">disable emails from this forum</a> or <a href="' . $__templater->func('link', array('canonical:email-stop/all', $__vars['xf']['toUser'], ), true) . '">disable all emails</a>.</p>' . '
';
	return $__finalCompiled;
},
'watched_thread_footer' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'thread' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	' . '<p class="minorText">Please do not reply to this message. You must visit the forum to reply.</p>

<p class="minorText">This message was sent to you because you opted to watch the thread ' . (((('<a href="' . $__templater->func('link', array('canonical:threads', $__vars['thread'], ), true)) . '">') . $__templater->escape($__vars['thread']['title'])) . '</a>') . ' at ' . $__templater->escape($__vars['xf']['options']['boardTitle']) . ' with email notification of new replies. You will not receive any further emails about this thread until you have read the new messages.</p>

<p class="minorText">If you no longer wish to receive these emails, you may <a href="' . $__templater->func('link', array('canonical:email-stop/content', $__vars['xf']['toUser'], array('t' => 'thread', 'id' => $__vars['thread']['thread_id'], ), ), true) . '">disable emails from this thread</a> or <a href="' . $__templater->func('link', array('canonical:email-stop/all', $__vars['xf']['toUser'], ), true) . '">disable all emails</a>.</p>' . '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '

' . '

';
	return $__finalCompiled;
});