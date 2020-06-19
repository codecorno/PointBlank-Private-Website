<?php
// FROM HASH: 9ceb0fe6a4c69c240d5287462fa6db2c
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Payment provider log');
	$__finalCompiled .= '

<div class="block">
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formRow('
				' . (($__vars['entry']['log_type'] == 'payment') ? 'Payment' : (($__vars['entry']['log_type'] == 'info') ? 'Information' : (($__vars['entry']['log_type'] == 'error') ? 'Error' : (($__vars['entry']['log_type'] == 'cancel') ? 'Cancellation' : $__templater->escape($__vars['entry']['log_type']))))) . $__templater->escape($__vars['xf']['language']['label_separator']) . '
				<span dir="auto">' . $__templater->escape($__vars['entry']['log_message']) . '</span>
			', array(
		'label' => 'Action',
	)) . '

			';
	$__compilerTemp1 = '';
	if ($__vars['purchaseRequest']['User']) {
		$__compilerTemp1 .= '
					' . $__templater->func('username_link', array($__vars['purchaseRequest']['User'], false, array(
			'href' => $__templater->func('link', array('logs/payment-provider', null, array('user_id' => $__vars['purchaseRequest']['User']['user_id'], ), ), false),
		))) . '
				';
	} else {
		$__compilerTemp1 .= '
					' . 'Unknown user' . '
				';
	}
	$__finalCompiled .= $__templater->formRow('
				' . $__compilerTemp1 . '
			', array(
		'label' => 'User',
	)) . '

			' . $__templater->formRow('
				' . $__templater->func('date_dynamic', array($__vars['entry']['log_date'], array(
	))) . '
			', array(
		'label' => 'Date',
	)) . '

			';
	$__compilerTemp2 = '';
	if ($__vars['entry']['purchase_request_key']) {
		$__compilerTemp2 .= '
					<a href="' . $__templater->func('link', array('logs/payment-provider', null, array('purchase_request_key' => $__vars['entry']['purchase_request_key'], ), ), true) . '">
						' . $__templater->escape($__vars['entry']['purchase_request_key']) . '
					</a>
				';
	} else {
		$__compilerTemp2 .= '
					' . 'N/A' . '
				';
	}
	$__finalCompiled .= $__templater->formRow('
				' . $__compilerTemp2 . '
			', array(
		'label' => 'Purchase request key',
	)) . '

			';
	$__compilerTemp3 = '';
	if ($__vars['entry']['transaction_id']) {
		$__compilerTemp3 .= '
					<a href="' . $__templater->func('link', array('logs/payment-provider', null, array('transaction_id' => $__vars['entry']['transaction_id'], ), ), true) . '">
						' . $__templater->escape($__vars['entry']['transaction_id']) . '
					</a>
				';
	} else {
		$__compilerTemp3 .= '
					' . 'N/A' . '
				';
	}
	$__finalCompiled .= $__templater->formRow('
				' . $__compilerTemp3 . '
			', array(
		'label' => 'Transaction ID',
	)) . '

			';
	$__compilerTemp4 = '';
	if ($__vars['entry']['subscriber_id']) {
		$__compilerTemp4 .= '
					<a href="' . $__templater->func('link', array('logs/payment-provider', null, array('subscriber_id' => $__vars['entry']['subscriber_id'], ), ), true) . '">
						' . $__templater->escape($__vars['entry']['subscriber_id']) . '
					</a>
				';
	} else {
		$__compilerTemp4 .= '
					' . 'N/A' . '
				';
	}
	$__finalCompiled .= $__templater->formRow('
				' . $__compilerTemp4 . '
			', array(
		'label' => 'Subscriber ID',
	)) . '

			';
	$__compilerTemp5 = '';
	if ($__vars['purchaseRequest']['PaymentProfile']) {
		$__compilerTemp5 .= '
					<a href="' . $__templater->func('link', array('logs/payment-provider', null, array('payment_profile_id' => $__vars['purchaseRequest']['PaymentProfile']['payment_profile_id'], ), ), true) . '">
						' . $__templater->escape($__vars['purchaseRequest']['PaymentProfile']['title']) . '
					</a>
				';
	} else {
		$__compilerTemp5 .= '
					' . 'Unknown profile' . '
				';
	}
	$__finalCompiled .= $__templater->formRow('
				' . $__compilerTemp5 . '
			', array(
		'label' => 'Payment profile',
	)) . '
		</div>
	</div>
</div>

';
	if ($__vars['purchasable'] AND $__vars['purchasableItem']) {
		$__finalCompiled .= '
	<div class="block">
		<div class="block-container">
			<div class="block-body">
				' . $__templater->formRow('
					<a href="' . $__templater->func('link', array('logs/payment-provider', null, array('purchasable_type_id' => $__vars['purchasable']['purchasable_type_id'], ), ), true) . '">
						' . $__templater->escape($__vars['purchasable']['title']) . '
					</a>
				', array(
			'label' => 'Purchasable details',
		)) . '

				' . $__templater->formRow('
					<a href="' . $__templater->escape($__vars['purchasableItem']['link']) . '">' . $__templater->escape($__vars['purchasableItem']['title']) . '</a>
				', array(
			'label' => 'Purchasable item',
		)) . '
			</div>
		</div>
	</div>
';
	}
	$__finalCompiled .= '

<div class="block">
	<div class="block-container">
		<h2 class="block-header">' . 'Log details' . '</h2>
		<div class="block-body block-body--contained block-row" dir="ltr">
			' . $__templater->func('dump_simple', array($__vars['entry']['log_details'], ), true) . '
		</div>
	</div>
</div>';
	return $__finalCompiled;
});