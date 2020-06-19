<?php
// FROM HASH: bfda7a8d77c300caf0f904ef58e8227e
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
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Confirm payment details');
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__vars['paymentProfile']['options']['payment_request_api_enable']) {
		$__compilerTemp1 .= '
				' . $__templater->formRow('
					<div id="payment-request-button"></div>
					<div class="formRow-explain">' . 'Click the button above to pay faster using your saved payment details.' . ' ' . 'Payments are processed securely by <a href="' . 'https://stripe.com/' . '" target="_blank">' . 'Stripe' . '</a>. We do not process or store your payment details.' . '</div>
				', array(
			'label' => 'Pay faster',
			'controlid' => 'payment-request-button',
			'rowtype' => 'input',
			'rowclass' => 'js-pr-remove',
			'style' => 'display: none;',
		)) . '

				<h3 class="block-formSectionHeader js-pr-remove" style="display: none;">' . 'Or enter payment details manually' . $__vars['xf']['language']['ellipsis'] . '</h3>
			';
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formInfoRow('
				<div class="block-rowMessage block-rowMessage--error block-rowMessage--iconic u-hidden" id="card-errors"></div>
			', array(
		'id' => 'card-errors-container',
		'rowclass' => 'u-hidden',
	)) . '

			' . $__compilerTemp1 . '

			' . $__templater->formRow('
				<div id="card-element" class="input"></div>
				<div class="formRow-explain">' . 'Payments are processed securely by <a href="' . 'https://stripe.com/' . '" target="_blank">' . 'Stripe' . '</a>. We do not process or store your payment details.' . '</div>
			', array(
		'controlid' => 'card-element',
		'rowtype' => 'input',
		'label' => 'Pay by card',
	)) . '

			' . '

			' . $__templater->formSubmitRow(array(
		'icon' => 'payment',
		'submit' => 'Pay ' . $__templater->filter($__vars['purchase']['cost'], array(array('currency', array($__vars['purchase']['currency'], )),), true) . '',
	), array(
	)) . '

			<script type="application/json" class="js-formStyles">
				{
					"base": {
						"color": "' . $__templater->filter($__templater->func('parse_less_color', array($__templater->func('property', array('textColor', '#141414', ), false), ), false), array(array('escape', array('json', )),), true) . '",
						"fontFamily": "' . $__templater->filter($__templater->func('property', array('fontFamilyUi', ), false), array(array('escape', array('json', )),), true) . '",
						"fontSize": "16px"
					},
					"invalid": {
						"color": "#c84448"
					}
				}
			</script>
		</div>
	</div>
', array(
		'action' => $__templater->func('link', array('purchase/process', null, array('request_key' => $__vars['purchaseRequest']['request_key'], ), ), false),
		'class' => 'block block--paymentInitiate',
		'data-no-auto-focus' => 'on',
		'data-xf-init' => 'stripe-payment-form',
		'data-publishable-key' => $__vars['publishableKey'],
		'data-recurring' => ($__vars['purchase']['recurring'] ? 'true' : 'false'),
		'data-pi-secret' => $__vars['paymentIntent']['client_secret'],
		'data-pr-enabled' => $__vars['paymentProfile']['options']['payment_request_api_enable'],
		'data-pr-country' => $__vars['paymentProfile']['options']['stripe_country'],
		'data-pr-cost' => $__vars['cost'],
		'data-pr-currency' => $__vars['purchase']['currency'],
		'data-pr-label' => $__vars['purchase']['title'],
	));
	return $__finalCompiled;
});