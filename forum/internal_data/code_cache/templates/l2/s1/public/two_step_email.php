<?php
// FROM HASH: 75c9ba0a58622d79fed04d1cc4ddaceb
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formInfoRow('An email has been sent to <b>' . $__templater->escape($__vars['email']) . '</b> with a single-use code. Please enter that code to continue.', array(
	)) . '

' . $__templater->formTextBoxRow(array(
		'name' => 'code',
		'autofocus' => 'autofocus',
		'inputmode' => 'numeric',
		'pattern' => '[0-9]*',
	), array(
		'label' => 'Email confirmation code',
	));
	return $__finalCompiled;
});