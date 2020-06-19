<?php
// FROM HASH: 69e60126f25d32023391e8f50e7e14f0
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__vars['providerData']) {
		$__finalCompiled .= '
	<a href="https://twitter.com/' . $__templater->escape($__vars['providerData']['screen_name']) . '" target="_blank">
		<img src="' . $__templater->escape($__vars['providerData']['avatar_url']) . '" width="48" alt="" />
	</a>
	<div><a href="https://twitter.com/' . $__templater->escape($__vars['providerData']['screen_name']) . '" target="_blank">@' . $__templater->escape($__vars['providerData']['screen_name']) . ' ' . $__templater->filter($__vars['providerData']['username'], array(array('parens', array()),), true) . '</a></div>
';
	} else {
		$__finalCompiled .= '
	' . 'Account associated' . '
';
	}
	return $__finalCompiled;
});