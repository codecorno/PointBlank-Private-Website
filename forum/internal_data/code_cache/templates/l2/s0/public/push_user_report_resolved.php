<?php
// FROM HASH: 250c67aedbd9033aa4d4d96d3cb52aee
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__vars['extra']['comment']) {
		$__finalCompiled .= '
	' . 'Your recent report has been resolved: ' . $__templater->filter($__vars['extra']['title'], array(array('strip_tags', array()),), true) . ' - ' . $__templater->escape($__vars['extra']['comment']) . '' . '
';
	} else {
		$__finalCompiled .= '
	' . 'Your recent report has been resolved: ' . $__templater->filter($__vars['extra']['title'], array(array('strip_tags', array()),), true) . '' . '
';
	}
	return $__finalCompiled;
});