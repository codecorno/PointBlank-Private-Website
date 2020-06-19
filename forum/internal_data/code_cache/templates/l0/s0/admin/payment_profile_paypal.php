<?php
// FROM HASH: 80205a50dec1a0ed4e011788e99726d6
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formTextBoxRow(array(
		'name' => 'options[primary_account]',
		'value' => $__vars['profile']['options']['primary_account'],
		'type' => 'email',
	), array(
		'label' => 'PayPal Primary Account Email',
		'hint' => 'Required',
		'explain' => '
		' . 'This is the primary email address on your PayPal account. If this is incorrect, payments may not be processed successfully. Note this must be a PayPal Premier or Business account and IPNs must be enabled.' . '
	',
	)) . '

' . $__templater->formTextAreaRow(array(
		'name' => 'options[alternate_accounts]',
		'value' => $__vars['profile']['options']['alternate_accounts'],
		'autosize' => 'true',
	), array(
		'label' => 'PayPal Alternate Accounts',
		'explain' => 'Enter the email address of any PayPal accounts other than the primary one that may receive payments for user upgrades. This can be useful if the primary account is changed and recurring payments are still coming from the old account, for example. If the old account is not listed as a valid alternative, payments will not be accepted for this account. Enter one account per line.',
	)) . '

' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'options[require_address]',
		'selected' => $__vars['profile']['options']['require_address'],
		'label' => 'Require address',
		'hint' => 'If enabled, the payment provider will collect the payee\'s address while taking the payment.',
		'_type' => 'option',
	)), array(
	)) . '

' . $__templater->formHiddenVal('options[legacy]', ($__vars['profile']['options']['legacy'] ? 1 : 0), array(
	));
	return $__finalCompiled;
});