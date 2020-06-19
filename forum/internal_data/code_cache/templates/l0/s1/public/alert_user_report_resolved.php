<?php
// FROM HASH: dca965b99ee9fcc14c01a60a29f3edc3
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__vars['extra']['comment']) {
		$__finalCompiled .= '
	' . 'Your recent report has been resolved: ' . $__templater->escape($__vars['extra']['title']) . ' - ' . $__templater->escape($__vars['extra']['comment']) . '' . '
';
	} else {
		$__finalCompiled .= '
	' . 'Your recent report has been resolved: ' . $__templater->escape($__vars['extra']['title']) . '' . '
';
	}
	return $__finalCompiled;
});