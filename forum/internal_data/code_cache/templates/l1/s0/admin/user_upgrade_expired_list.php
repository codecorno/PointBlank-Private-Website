<?php
// FROM HASH: 1cb6dd783591f47e1790ea4682f4f3a8
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Expired user upgrades' . ($__vars['userUpgrade'] ? (': ' . $__templater->escape($__vars['userUpgrade']['title'])) : ''));
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formRow('
				' . $__templater->formTextBox(array(
		'name' => 'username',
		'class' => 'input--inline',
		'ac' => 'single',
	)) . '
				' . $__templater->button('Filter', array(
		'type' => 'submit',
	), '', array(
	)) . '
			', array(
		'label' => 'Filter by user',
		'rowtype' => 'input',
	)) . '
		</div>
	</div>
', array(
		'action' => $__templater->func('link', array('user-upgrades/expired', $__vars['userUpgrade'], ), false),
		'class' => 'block',
	)) . '

';
	if (!$__templater->test($__vars['expiredUpgrades'], 'empty', array())) {
		$__finalCompiled .= '
	<div class="block">
		<div class="block-container">
			<div class="block-body">
				';
		$__compilerTemp1 = '';
		if ($__templater->isTraversable($__vars['expiredUpgrades'])) {
			foreach ($__vars['expiredUpgrades'] AS $__vars['expiredUpgrade']) {
				$__compilerTemp1 .= '
						';
				$__vars['paymentProfile'] = $__vars['expiredUpgrade']['PurchaseRequest']['PaymentProfile'];
				$__compilerTemp2 = '';
				if ($__vars['paymentProfile']) {
					$__compilerTemp2 .= '
									<a href="' . $__templater->func('link', array('payment-profiles/edit', $__vars['paymentProfile'], ), true) . '">' . $__templater->escape($__vars['paymentProfile']['title']) . '</a>
								';
				} else {
					$__compilerTemp2 .= '
									' . 'N/A' . '
								';
				}
				$__compilerTemp1 .= $__templater->dataRow(array(
					'rowclass' => 'dataList-row--noHover',
				), array(array(
					'_type' => 'cell',
					'html' => '
								' . $__templater->func('username_link', array($__vars['expiredUpgrade']['User'], false, array(
					'defaultname' => 'Unknown user',
					'href' => $__templater->func('link', array('users/edit', $__vars['expiredUpgrade']['User'], ), false),
				))) . '
							',
				),
				array(
					'_type' => 'cell',
					'html' => '
								<a href="' . $__templater->func('link', array('user-upgrades/edit', $__vars['expiredUpgrade']['Upgrade'], ), true) . '">' . $__templater->escape($__vars['expiredUpgrade']['Upgrade']['title']) . '</a>
							',
				),
				array(
					'_type' => 'cell',
					'html' => '
								' . '' . '
								' . $__compilerTemp2 . '
							',
				),
				array(
					'_type' => 'cell',
					'html' => $__templater->func('date_dynamic', array($__vars['expiredUpgrade']['start_date'], array(
				))),
				),
				array(
					'_type' => 'cell',
					'html' => ($__vars['expiredUpgrade']['end_date'] ? $__templater->func('date', array($__vars['expiredUpgrade']['end_date'], ), true) : 'Permanent'),
				))) . '
					';
			}
		}
		$__finalCompiled .= $__templater->dataList('
					' . $__templater->dataRow(array(
			'rowtype' => 'header',
		), array(array(
			'_type' => 'cell',
			'html' => '<a href="' . $__templater->func('link', array('user-upgrades/expired', $__vars['userUpgrade'], array('order' => 'username', 'direction' => '', ) + $__vars['linkParams'], ), true) . '">' . 'User' . '</a>',
		),
		array(
			'_type' => 'cell',
			'html' => 'Upgrade title',
		),
		array(
			'_type' => 'cell',
			'html' => 'Payment profile',
		),
		array(
			'_type' => 'cell',
			'html' => '<a href="' . $__templater->func('link', array('user-upgrades/expired', $__vars['userUpgrade'], array('order' => 'start_date', 'direction' => 'desc', ) + $__vars['linkParams'], ), true) . '">' . 'Start date' . '</a>',
		),
		array(
			'_type' => 'cell',
			'html' => '<a href="' . $__templater->func('link', array('user-upgrades/expired', $__vars['userUpgrade'], array('order' => 'end_date', 'direction' => 'desc', ) + $__vars['linkParams'], ), true) . '">' . 'End date' . '</a>',
		))) . '
					' . $__compilerTemp1 . '
				', array(
			'data-xf-init' => 'responsive-data-list',
		)) . '
			</div>
			<div class="block-footer">
				<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['expiredUpgrades'], $__vars['totalExpired'], ), true) . '</span>
			</div>
		</div>

		' . $__templater->func('page_nav', array(array(
			'page' => $__vars['page'],
			'total' => $__vars['totalExpired'],
			'link' => 'user-upgrades/expired',
			'data' => $__vars['userUpgrade'],
			'params' => $__vars['linkParams'],
			'wrapperclass' => 'block-outer block-outer--after',
			'perPage' => $__vars['perPage'],
		))) . '
	</div>
';
	} else {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'No results found.' . '</div>
';
	}
	return $__finalCompiled;
});