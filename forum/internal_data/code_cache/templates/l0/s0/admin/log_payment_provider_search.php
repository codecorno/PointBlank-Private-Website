<?php
// FROM HASH: fc834779b322dedf81c8173d22c1745e
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Payment provider log search');
	$__finalCompiled .= '

';
	$__compilerTemp1 = array(array(
		'label' => $__vars['xf']['language']['parenthesis_open'] . 'Any' . $__vars['xf']['language']['parenthesis_close'],
		'_type' => 'option',
	));
	$__compilerTemp1 = $__templater->mergeChoiceOptions($__compilerTemp1, $__vars['profiles']);
	$__compilerTemp2 = array(array(
		'label' => $__vars['xf']['language']['parenthesis_open'] . 'Any' . $__vars['xf']['language']['parenthesis_close'],
		'_type' => 'option',
	));
	$__compilerTemp2 = $__templater->mergeChoiceOptions($__compilerTemp2, $__vars['purchasables']);
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formTextBoxRow(array(
		'name' => 'purchase_request_key',
	), array(
		'label' => 'Purchase request key',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'transaction_id',
	), array(
		'label' => 'Transaction ID',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'subscriber_id',
	), array(
		'label' => 'Subscriber ID',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'username',
		'ac' => 'single',
	), array(
		'label' => 'User',
	)) . '

			' . $__templater->formSelectRow(array(
		'name' => 'payment_profile_id',
	), $__compilerTemp1, array(
		'label' => 'Payment profile',
	)) . '

			' . $__templater->formSelectRow(array(
		'name' => 'purchasable_type_id',
	), $__compilerTemp2, array(
		'label' => 'Purchasable type',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'sticky' => 'true',
		'icon' => 'search',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('logs/payment-provider', ), false),
		'class' => 'block',
	));
	return $__finalCompiled;
});