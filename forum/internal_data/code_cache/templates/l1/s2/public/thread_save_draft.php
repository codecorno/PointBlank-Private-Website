<?php
// FROM HASH: 4541ab7dba7fe2746f4e446c2c194441
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__vars['hasNewPost']) {
		$__finalCompiled .= '
	<div class="message js-newMessagesIndicator">
		<div class="message-inner">
			<div class="message-cell message-cell--alert">
				' . 'Messages have been posted since you loaded this page.' . '
				<a href="' . $__templater->func('link', array('threads/new-posts', $__vars['thread'], array('after' => $__vars['lastDate'], ), ), true) . '" data-xf-click="message-loader">' . 'View them?' . '</a>
			</div>
		</div>
	</div>
';
	}
	return $__finalCompiled;
});