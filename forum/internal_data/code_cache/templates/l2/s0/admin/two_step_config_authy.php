<?php
// FROM HASH: b9cbeb95d125dfdc0490e207d3b7a8bc
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formTextBoxRow(array(
		'name' => 'options[authy_api_key]',
		'value' => $__vars['provider']['options']['authy_api_key'],
	), array(
		'label' => 'Authy API key',
		'explain' => 'To use Authy you must create an Authy API key in the <a href="https://www.twilio.com/console" target="_blank">Twilio console dashboard</a>.',
	));
	return $__finalCompiled;
});