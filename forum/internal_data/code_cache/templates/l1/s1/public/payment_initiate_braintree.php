<?php
// FROM HASH: f0ebeac76ac0a7debdcb7c953e445daa
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->includeCss('payment_initiate.less');
	$__finalCompiled .= '
';
	$__templater->includeJs(array(
		'src' => 'xf/payment.js',
		'min' => '1',
	));
	$__finalCompiled .= '

';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Enter payment details');
	$__finalCompiled .= '

<div class="blocks">
	' . $__templater->form('

		<div class="block-container">
			<div class="block-body">
				' . $__templater->formRow('
					<div class="inputGroup">
						<div class="inputGroup-text"><span style="width: 30px;">' . $__templater->fontAwesome('fa-lg fa-credit-card', array(
		'id' => 'brand-icon',
	)) . '</span></div>
						<div id="card-number" class="input is-disabled"></div>
						<div class="inputGroup-splitter"></div>
						<div id="card-expiry" class="input is-disabled" style="width: 130px"></div>
						<div class="inputGroup-splitter"></div>
						<div id="card-cvv" class="input is-disabled" style="width: 75px"></div>
					</div>
					<div class="formRow-explain">' . 'Payments are processed securely by <a href="' . 'https://braintreepayments.com/' . '" target="_blank">' . 'Braintree' . '</a>. We do not process or store your payment details.' . '</div>
				', array(
		'label' => 'Pay by card',
		'controlid' => 'card-number',
		'rowtype' => 'input',
	)) . '

				<hr class="formRowSep" />

				' . $__templater->formRow('
					' . $__templater->button('
						' . 'Pay ' . $__templater->filter($__vars['purchase']['cost'], array(array('currency', array($__vars['purchase']['currency'], )),), true) . '' . '
					', array(
		'type' => 'submit',
		'icon' => 'payment',
	), '', array(
	)) . '
				', array(
		'label' => '',
		'rowtype' => 'button',
	)) . '

				<script type="application/json" class="js-formStyles">
					{
						"input": {
							"color": "' . $__templater->func('property', array('textColor', '#141414', ), true) . '",
							"font-family": "' . $__templater->func('property', array('fontFamilyUi', ), true) . '",
							"font-size": "16px",
							"line-height": "' . $__templater->func('property', array('lineHeightDefault', '1.4', ), true) . '"
						},
						"input.invalid": {
							"color": "#c84448"
						}
					}
				</script>
			</div>
		</div>
	', array(
		'action' => $__templater->func('link', array('purchase/process', null, array('request_key' => $__vars['purchaseRequest']['request_key'], ), ), false),
		'class' => 'block',
		'data-xf-init' => 'braintree-payment-form',
		'data-client-token' => $__vars['clientToken'],
	)) . '

	';
	if ($__vars['paymentProfile']['options']['apple_pay_enable'] OR $__vars['paymentProfile']['options']['paypal_enable']) {
		$__finalCompiled .= '
		<div class="blocks-textJoiner"><span></span><em>' . 'or' . '</em><span></span></div>

		';
		if ($__vars['paymentProfile']['options']['apple_pay_enable']) {
			$__finalCompiled .= '
			' . $__templater->form('

				<div class="block-container">
					<div class="block-body">
						' . $__templater->formRow('
							' . $__templater->button('&nbsp;', array(
				'class' => 'button--apple js-applePayButton',
			), '', array(
			)) . '
						', array(
				'rowtype' => 'button',
				'label' => 'Pay with Apple Pay',
			)) . '
					</div>
				</div>
			', array(
				'action' => $__templater->func('link', array('purchase/process', null, array('request_key' => $__vars['purchaseRequest']['request_key'], ), ), false),
				'class' => 'block u-hidden',
				'data-xf-init' => 'braintree-apple-pay-form',
				'data-client-token' => $__vars['clientToken'],
				'data-currency-code' => $__vars['purchase']['currency'],
				'data-board-title' => $__vars['xf']['options']['boardTitle'],
				'data-title' => $__vars['purchase']['purchasableTitle'],
				'data-amount' => $__vars['purchase']['cost'],
			)) . '
		';
		}
		$__finalCompiled .= '
		';
		if ($__vars['paymentProfile']['options']['paypal_enable']) {
			$__finalCompiled .= '
			' . $__templater->form('

				<div class="block-container">
					<div class="block-body">
						' . $__templater->formRow('
							<div id="paypal-button"></div>
						', array(
				'rowtype' => 'button',
				'label' => 'Pay with PayPal',
			)) . '
					</div>
				</div>
			', array(
				'action' => $__templater->func('link', array('purchase/process', null, array('request_key' => $__vars['purchaseRequest']['request_key'], ), ), false),
				'class' => 'block',
				'data-xf-init' => 'braintree-paypal-form',
				'data-client-token' => $__vars['clientToken'],
				'data-test-payments' => ($__vars['xf']['livePayments'] ? 'off' : 'on'),
			)) . '
		';
		}
		$__finalCompiled .= '
	';
	}
	$__finalCompiled .= '
</div>';
	return $__finalCompiled;
});