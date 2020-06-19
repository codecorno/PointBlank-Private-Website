<?php
// FROM HASH: f7765c8f1e39adeaa2a354b96b129ffb
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formInfoRow('A backup code can be used when you don\'t have access to an alternative verification method. Once a backup code is used, it will no longer be usable. You will receive an email when you login using a backup code.', array(
	)) . '

' . $__templater->formTextBoxRow(array(
		'name' => 'code',
		'autofocus' => 'autofocus',
		'inputmode' => 'numeric',
		'pattern' => '[0-9]*',
	), array(
		'label' => 'Backup code',
	));
	return $__finalCompiled;
});