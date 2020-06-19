<?php
// FROM HASH: 554acccd8932c7ce065f602f7f0eebd5
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__vars['firstUnshownMessage']) {
		$__finalCompiled .= '
	<div class="message">
		<div class="message-inner">
			<div class="message-cell message-cell--alert">
				' . 'There are more messages to display.' . ' <a href="' . $__templater->func('link', array('conversations/messages', $__vars['firstUnshownMessage'], ), true) . '">' . 'View them?' . '</a>
			</div>
		</div>
	</div>
';
	}
	$__finalCompiled .= '

';
	if ($__templater->isTraversable($__vars['messages'])) {
		foreach ($__vars['messages'] AS $__vars['message']) {
			$__finalCompiled .= '
	' . $__templater->callMacro('conversation_message_macros', 'message', array(
				'message' => $__vars['message'],
				'conversation' => $__vars['conversation'],
			), $__vars) . '
';
		}
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
});