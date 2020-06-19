<?php
// FROM HASH: bcf2a877018318c08947bbb76fac6ec2
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__vars['extra']['comment']) {
		$__finalCompiled .= '
	' . 'Unfortunately, your recent report has been rejected: ' . $__templater->escape($__vars['extra']['title']) . ' - ' . $__templater->escape($__vars['extra']['comment']) . '' . '
';
	} else {
		$__finalCompiled .= '
	' . 'Unfortunately, your recent report was rejected: ' . $__templater->escape($__vars['extra']['title']) . '' . '
';
	}
	return $__finalCompiled;
});