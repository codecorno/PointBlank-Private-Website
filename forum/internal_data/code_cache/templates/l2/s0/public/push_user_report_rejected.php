<?php
// FROM HASH: 4326769c554f6978ed53edac19b7b9c2
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__vars['extra']['comment']) {
		$__finalCompiled .= '
	' . 'Unfortunately, your recent report has been rejected: ' . $__templater->filter($__vars['extra']['title'], array(array('strip_tags', array()),), true) . ' - ' . $__templater->escape($__vars['extra']['comment']) . '' . '
';
	} else {
		$__finalCompiled .= '
	' . 'Unfortunately, your recent report was rejected: ' . $__templater->filter($__vars['extra']['title'], array(array('strip_tags', array()),), true) . '' . '
';
	}
	return $__finalCompiled;
});