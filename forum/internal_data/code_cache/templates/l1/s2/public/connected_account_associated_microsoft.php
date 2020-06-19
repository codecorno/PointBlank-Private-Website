<?php
// FROM HASH: 46d6e06af229f5bb84cf0d1e540d3de3
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__vars['providerData']['avatar_url']) {
		$__finalCompiled .= '
	<a href="https://profile.live.com/' . $__templater->escape($__vars['connectedAccounts']['microsoft']) . '" target="_blank">
		<img src="' . $__templater->escape($__vars['providerData']['avatar_url']) . '" width="48" alt="" />
	</a>
';
	}
	$__finalCompiled .= '
<div><a href="https://profile.live.com/' . $__templater->escape($__vars['connectedAccounts']['microsoft']) . '" target="_blank">' . ($__templater->escape($__vars['providerData']['username']) ?: 'View account') . '</a></div>';
	return $__finalCompiled;
});