<?php
// FROM HASH: c1e17cceabfcad0aad1717da54f3aa0c
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<a href="' . ($__templater->escape($__vars['providerData']['profile_link']) ?: ('http://www.facebook.com/profile.php?id=' . $__templater->escape($__vars['connectedAccounts']['facebook']))) . '" target="_blank">
	<img src="https://graph.facebook.com/' . $__templater->escape($__vars['connectedAccounts']['facebook']) . '/picture" width="48" alt="" />
</a>
<div><a href="' . ($__templater->escape($__vars['providerData']['profile_link']) ?: ('https://www.facebook.com/profile.php?id=' . $__templater->escape($__vars['connectedAccounts']['facebook']))) . '" target="_blank">' . ($__templater->escape($__vars['providerData']['username']) ?: 'View account') . '</a></div>';
	return $__finalCompiled;
});