<?php
// FROM HASH: 96b7808a8eac0e3d902ba3f5802ab2ee
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__vars['extra']['profileUserId'] == $__vars['xf']['visitor']['user_id']) {
		$__finalCompiled .= '
	' . 'Your status update was deleted.' . '
';
	} else {
		$__finalCompiled .= '
	' . 'Your profile post for ' . $__templater->escape($__vars['extra']['profileUser']) . ' was deleted.' . '
	<push:url>' . $__templater->func('base_url', array($__vars['extra']['profileLink'], 'canonical', ), true) . '</push:url>
';
	}
	$__finalCompiled .= '
';
	if ($__vars['extra']['reason']) {
		$__finalCompiled .= 'Reason' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['extra']['reason']);
	}
	return $__finalCompiled;
});