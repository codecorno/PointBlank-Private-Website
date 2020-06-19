<?php
// FROM HASH: ce5a2008bd4eb04b20822ddf60b01955
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__vars['providerData']['profile_link']) {
		$__finalCompiled .= '
	<a href="' . $__templater->escape($__vars['providerData']['profile_link']) . '" target="_blank">
		<img src="' . $__templater->escape($__vars['providerData']['pictureUrl']) . '" width="48" alt="" />
	</a>
	<div><a href="' . $__templater->escape($__vars['providerData']['profile_link']) . '" target="_blank">' . $__templater->escape($__vars['providerData']['formattedName']) . '</a></div>
';
	} else {
		$__finalCompiled .= '
	' . 'Account associated' . '
';
	}
	return $__finalCompiled;
});