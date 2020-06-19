<?php
// FROM HASH: b6fc7727f153d640e390054283dd3d8f
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formTextBoxRow(array(
		'name' => 'options[account_number]',
		'value' => $__vars['profile']['options']['account_number'],
	), array(
		'label' => 'Account number',
	)) . '

' . $__templater->formTextBoxRow(array(
		'name' => 'options[secret_word]',
		'value' => $__vars['profile']['options']['secret_word'],
	), array(
		'label' => 'Secret word',
		'explain' => 'Your account number is available in your <a href="https://www.2checkout.com/login" target="_blank">2Checkout Account</a>. When logged in to your account you can set your Secret Word by going to Account > Site Management.',
	)) . '

' . $__templater->formTextBoxRow(array(
		'name' => 'options[api_username]',
		'value' => $__vars['profile']['options']['api_username'],
	), array(
		'label' => 'API username',
		'hint' => 'Optional',
	)) . '

' . $__templater->formTextBoxRow(array(
		'name' => 'options[api_password]',
		'value' => $__vars['profile']['options']['api_password'],
	), array(
		'label' => 'API password',
		'hint' => 'Optional',
		'explain' => 'An API username and password are required if you wish to allow users to be able to cancel recurring subscriptions.<br />
<br />
Without this, recurring subscriptions can only be cancelled from your <a href="https://www.2checkout.com/login" target="_blank">2Checkout Account</a>.',
	));
	return $__finalCompiled;
});