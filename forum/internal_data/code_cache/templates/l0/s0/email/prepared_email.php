<?php
// FROM HASH: 83921aed5cfa4dd252216d50da544319
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<mail:subject>' . $__templater->escape($__vars['title']) . '</mail:subject>

' . $__templater->filter($__vars['htmlBody'], array(array('raw', array()),), true) . '

<mail:text>' . $__templater->escape($__vars['textBody']) . '</mail:text>

';
	if ($__vars['raw']) {
		$__finalCompiled .= '
	';
		$__templater->setPageParam('template', '');
		$__finalCompiled .= '
';
	}
	return $__finalCompiled;
});