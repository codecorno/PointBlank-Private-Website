<?php
// FROM HASH: 973030a140575c5977128d1469754b1b
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formTextBoxRow(array(
		'name' => 'options[merchant_id]',
		'value' => $__vars['profile']['options']['merchant_id'],
	), array(
		'label' => 'Merchant ID',
	)) . '

' . $__templater->formTextBoxRow(array(
		'name' => 'options[public_key]',
		'value' => $__vars['profile']['options']['public_key'],
	), array(
		'label' => 'Public key',
	)) . '

' . $__templater->formTextBoxRow(array(
		'name' => 'options[private_key]',
		'value' => $__vars['profile']['options']['private_key'],
	), array(
		'label' => 'Private key',
		'explain' => 'Enter the API keys and Merchant ID from the Account > My User > API Keys, Tokenization Keys, Encryption Keys > View Authorizations page in your <a href="https://www.braintreegateway.com/" target="_blank">Braintree Account</a>.<br />
<br />
If you wish to enable PayPal support, please <a href="https://articles.braintreepayments.com/guides/paypal/setup-guide#enter-your-paypal-credentials-in-the-braintree-control-panel" target="_blank">follow the instructions</a>.',
	)) . '

' . $__templater->formTextBoxRow(array(
		'name' => 'options[merchant_account]',
		'value' => $__vars['profile']['options']['merchant_account'],
	), array(
		'label' => 'Merchant account ID',
		'explain' => 'The merchant account ID is listed under the Settings > Processing page.<br />
<br />
<b>Note:</b> By default, Braintree does not support multiple currencies. The currency value you may select for your purchasable items <b><i>will be ignored</i></b>.<br />
<br />
To support multiple currencies, you need to set this up by <a href="mailto:support@braintreepayments.com">contacting Braintree</a> and ask them to create multiple Merchant Accounts. It is the Merchant Account which dictates which currency transactions will be processed in. You must specify which Merchant Account to use here.',
	)) . '

' . $__templater->formCheckBoxRow(array(
	), array(array(
		'checked' => $__vars['profile']['options']['plan_id'],
		'label' => 'Support recurring billing with following Plan ID' . $__vars['xf']['language']['label_separator'],
		'_dependent' => array($__templater->formTextBox(array(
		'name' => 'options[plan_id]',
		'value' => $__vars['profile']['options']['plan_id'],
	))),
		'_type' => 'option',
	)), array(
		'explain' => 'If you wish to support recurring billing for a purchasable item it must be specifically enabled here and you must provide the Plan ID you wish to use for any purchases made with this profile in your <a href="https://www.braintreegateway.com/" target="_blank">Braintree Account</a>.<br />
<br />
To support recurring billing and automatically reversing purchases when a payment dispute is opened, please ensure you enable Webhooks with the following URL otherwise you will have to monitor and manage this manually:<br />
<br />
' . $__templater->escape($__vars['xf']['options']['boardUrl']) . '/payment_callback.php?_xfProvider=braintree',
	)) . '

<hr class="formRowSep" />

' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'options[apple_pay_enable]',
		'selected' => $__vars['profile']['options']['apple_pay_enable'],
		'label' => '
		' . 'Enable Apple Pay support' . '
	',
		'_type' => 'option',
	)), array(
		'explain' => 'Requires domain verification using the Settings > Processing page in your Braintree Dashboard.',
	)) . '

' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'options[paypal_enable]',
		'selected' => $__vars['profile']['options']['paypal_enable'],
		'label' => '
		' . 'Enable PayPal support' . '
	',
		'_type' => 'option',
	)), array(
		'explain' => 'Requires a PayPal Business account and additional setup in your Braintree Dashboard.',
	));
	return $__finalCompiled;
});