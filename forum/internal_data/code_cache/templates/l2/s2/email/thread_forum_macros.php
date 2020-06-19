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
			<a href="' . $__templater->func('link', array('canonical:threads/unread', $__vars['thread'], array('new' => 1, ), ), true) . '" class="button">' . 'Ver este tópico' . '</a>
		</td>
		<td align="' . ($__vars['xf']['isRtl'] ? 'left' : 'right') . '">
			';
	if ($__vars['watchType'] == 'threads') {
		$__finalCompiled .= '
				<a href="' . $__templater->func('link', array('canonical:watched/threads', ), true) . '" class="buttonFake">' . 'Tópicos seguidos' . '</a>
			';
	} else if ($__vars['watchType'] == 'forums') {
		$__finalCompiled .= '
				<a href="' . $__templater->func('link', array('canonical:watched/forums', ), true) . '" class="buttonFake">' . 'Fóruns seguidos' . '</a>
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
	' . '<p class="minorText">Por favor, não responda a esta mensagem. Você deve visitar o fórum para responder.</p>

<p class="minorText">Esta mensagem foi enviada para você porque você optou por assistir ao fórum "' . $__templater->escape($__vars['forum']['title']) . '" no ' . $__templater->escape($__vars['xf']['options']['boardTitle']) . ' com notificação por e-mail de novos tópicos ou mensagens. Você não receberá mais e-mails sobre esse tópico até que tenha lido as novas mensagens.</p>

<p class="minorText">Se você não deseja mais receber esses e-mails, você pode <a href="' . $__templater->func('link', array('canonical:email-stop/content', $__vars['xf']['toUser'], array('t' => 'forum', 'id' => $__vars['forum']['node_id'], ), ), true) . '">desativar e-mails deste fórum</a> ou <a href="' . $__templater->func('link', array('canonical:email-stop/all', $__vars['xf']['toUser'], ), true) . '">desativar todos os e-mails</a>.</p>' . '
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
	' . '<p class="minorText">Por favor, não responda a esta mensagem. Você deve visitar o fórum para responder.</p>

<p class="minorText">Esta mensagem foi enviada para você porque você optou por assinar o tópico ' . (((('<a href="' . $__templater->func('link', array('canonical:threads', $__vars['thread'], ), true)) . '">') . $__templater->escape($__vars['thread']['title'])) . '</a>') . ' no ' . $__templater->escape($__vars['xf']['options']['boardTitle']) . ' com notificação por e-mail de novas respostas. Você não receberá mais e-mails sobre esse tópico até que tenha lido as novas mensagens.</p>

<p class="minorText">Se você não deseja mais receber esses e-mails, você pode <a href="' . $__templater->func('link', array('canonical:email-stop/content', $__vars['xf']['toUser'], array('t' => 'thread', 'id' => $__vars['thread']['thread_id'], ), ), true) . '">desativar e-mails deste tópico</a> ou <a href="' . $__templater->func('link', array('canonical:email-stop/all', $__vars['xf']['toUser'], ), true) . '">desativar todos os e-mails</a>.' . '
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