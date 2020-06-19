<?php
// FROM HASH: a7bf856494ff92c9b4899fd742284339
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formTextBoxRow(array(
		'name' => 'options[client_id]',
		'value' => $__vars['options']['client_id'],
	), array(
		'label' => 'Client ID',
		'hint' => 'Required',
		'explain' => 'The client ID that is associated with your <a href="https://www.linkedin.com/developer/apps/" target="_blank">LinkedIn application</a> for this domain.',
	)) . '

' . $__templater->formTextBoxRow(array(
		'name' => 'options[client_secret]',
		'value' => $__vars['options']['client_secret'],
	), array(
		'label' => 'Client secret',
		'hint' => 'Required',
		'explain' => 'The client secret for the LinkedIn application you created for this domain.',
	));
	return $__finalCompiled;
});