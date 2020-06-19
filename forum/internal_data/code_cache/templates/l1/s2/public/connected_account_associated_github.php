<?php
// FROM HASH: 9364af72617762098f8958df0ebd6be4
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__vars['providerData']['profile_link']) {
		$__finalCompiled .= '
	<a href="' . $__templater->escape($__vars['providerData']['profile_link']) . '" target="_blank">
		<img src="' . ($__templater->escape($__vars['providerData']['avatar_url']) ?: 'https://avatars.githubusercontent.com/u/{$connectedAccounts.github}?v=3') . '" width="48" alt="" />
	</a>
	<div><a href="' . $__templater->escape($__vars['providerData']['profile_link']) . '" target="_blank">' . ($__templater->escape($__vars['providerData']['username']) ?: 'View account') . '</a></div>
';
	} else {
		$__finalCompiled .= '
	<img src="h' . ($__templater->escape($__vars['providerData']['avatar_url']) ?: 'https://avatars.githubusercontent.com/u/{$connectedAccounts.github}?v=3') . '" width="48" alt="" />
	<div>' . ($__templater->escape($__vars['providerData']['username']) ?: 'Account associated') . '</div>
';
	}
	return $__finalCompiled;
});