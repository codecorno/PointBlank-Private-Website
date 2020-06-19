<?php
// FROM HASH: 0a08ba7a105955464bf3e2344c1fd1aa
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Payment provider log');
	$__finalCompiled .= '

';
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('Search logs', array(
		'href' => $__templater->func('link', array('logs/payment-provider/search', ), false),
		'icon' => 'search',
	), '', array(
	)) . '
');
	$__finalCompiled .= '

';
	if (!$__templater->test($__vars['entries'], 'empty', array())) {
		$__finalCompiled .= '
	<div class="block">
		<div class="block-container">
			<div class="block-body">
				';
		$__compilerTemp1 = '';
		if ($__templater->isTraversable($__vars['entries'])) {
			foreach ($__vars['entries'] AS $__vars['entry']) {
				$__compilerTemp1 .= '
						';
				$__vars['purchaseRequest'] = $__vars['entry']['PurchaseRequest'];
				$__compilerTemp1 .= '
						' . $__templater->dataRow(array(
				), array(array(
					'href' => $__templater->func('link', array('logs/payment-provider', $__vars['entry'], ), false),
					'overlay' => 'true',
					'label' => '
									' . (($__vars['entry']['log_type'] == 'payment') ? 'Payment' : (($__vars['entry']['log_type'] == 'info') ? 'Information' : (($__vars['entry']['log_type'] == 'error') ? 'Error' : (($__vars['entry']['log_type'] == 'cancel') ? 'Cancellation' : $__templater->escape($__vars['entry']['log_type']))))) . $__templater->escape($__vars['xf']['language']['label_separator']) . '
									<span dir="auto">' . $__templater->escape($__vars['entry']['log_message']) . '</span>
								',
					'explain' => '
									<ul class="listInline listInline--bullet">
										<li>' . ($__templater->escape($__vars['purchaseRequest']['PaymentProfile']['title']) ?: 'Unknown payment profile') . '</li>
										<li>' . ($__templater->escape($__vars['purchaseRequest']['Purchasable']['title']) ?: 'Unknown purchasable') . '</li>
										<li>' . $__templater->func('date_dynamic', array($__vars['entry']['log_date'], array(
				))) . '</li>
										<li>' . ($__vars['purchaseRequest']['User'] ? $__templater->escape($__vars['purchaseRequest']['User']['username']) : 'Unknown user') . '</li>
									</ul>
								',
					'_type' => 'main',
					'html' => '',
				))) . '
					';
			}
		}
		$__finalCompiled .= $__templater->dataList('
					' . $__compilerTemp1 . '
				', array(
		)) . '
			</div>
			<div class="block-footer">
				<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['entries'], $__vars['total'], ), true) . '</span>
			</div>
		</div>
		' . $__templater->func('page_nav', array(array(
			'page' => $__vars['page'],
			'total' => $__vars['total'],
			'link' => 'logs/payment-provider',
			'params' => $__vars['linkParams'],
			'wrapperclass' => 'block-outer block-outer--after',
			'perPage' => $__vars['perPage'],
		))) . '
	</div>
';
	} else {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'No entries have been logged.' . '</div>
';
	}
	return $__finalCompiled;
});