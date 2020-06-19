<?php
// FROM HASH: 1c988e700ffe1b93f8c2c58ad5db7c40
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<!-- 2Checkout insert begins here -->
<form action="' . ($__vars['xf']['livePayments'] ? 'https://2checkout.com/checkout/purchase' : 'https://sandbox.2checkout.com/checkout/purchase') . '" method="post" style="display: none" id="js-2CheckoutForm">
	' . $__templater->formHiddenVal('sid', $__vars['paymentProfile']['options']['account_number'], array(
	)) . '
	' . $__templater->formHiddenVal('mode', '2CO', array(
	)) . '
	' . $__templater->formHiddenVal('li_0_type', 'product', array(
	)) . '
	' . $__templater->formHiddenVal('li_0_name', $__vars['purchase']['title'], array(
	)) . '
	' . $__templater->formHiddenVal('li_0_price', $__vars['purchase']['cost'], array(
	)) . '
	' . $__templater->formHiddenVal('li_0_tangible', 'N', array(
	)) . '

	';
	if ($__vars['recurrence']) {
		$__finalCompiled .= '
		' . $__templater->formHiddenVal('li_0_recurrence', $__vars['recurrence'], array(
		)) . '
	';
	}
	$__finalCompiled .= '

	' . $__templater->formHiddenVal('currency_code', $__vars['purchase']['currency'], array(
	)) . '
	' . $__templater->formHiddenVal('email', $__vars['xf']['visitor']['email'], array(
	)) . '
	' . $__templater->formHiddenVal('merchant_order_id', $__vars['purchaseRequest']['request_key'], array(
	)) . '
	' . $__templater->formHiddenVal('x_receipt_link_url', $__templater->func('link', array('canonical:purchase/process', null, array('request_key' => $__vars['purchaseRequest']['request_key'], ), ), false), array(
	)) . '

		<!-- TODO: If we provide a way to input address details, the inline checkout can be used. -->
		<input type="hidden" name="card_holder_name" value="" />
		<input type="hidden" name="street_address" value="" />
		<input type="hidden" name="street_address2" value="" />
		<input type="hidden" name="city" value="" />
		<input type="hidden" name="state" value="" />
		<input type="hidden" name="zip" value="" />
		<input type="hidden" name="country" value="" />

	';
	$__templater->inlineJs('
		$.getScript(\'https://www.2checkout.com/static/checkout/javascript/direct.min.js\', function()
		{
			$(\'#js-2CheckoutForm\').submit();
		});
	');
	$__finalCompiled .= '
</form>';
	return $__finalCompiled;
});