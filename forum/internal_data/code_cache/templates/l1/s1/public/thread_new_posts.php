<?php
// FROM HASH: 6fe4534f3f9d0cc2639df467786796b1
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__vars['firstUnshownPost']) {
		$__finalCompiled .= '
	<div class="message">
		<div class="message-inner">
			<div class="message-cell message-cell--alert">
				' . 'There are more posts to display.' . ' <a href="' . $__templater->func('link', array('posts', $__vars['firstUnshownPost'], ), true) . '">' . 'View them?' . '</a>
			</div>
		</div>
	</div>
';
	}
	$__finalCompiled .= '

';
	if ($__templater->isTraversable($__vars['posts'])) {
		foreach ($__vars['posts'] AS $__vars['post']) {
			$__finalCompiled .= '
	' . $__templater->callMacro('post_macros', 'post', array(
				'post' => $__vars['post'],
				'thread' => $__vars['thread'],
			), $__vars) . '
';
		}
	}
	return $__finalCompiled;
});