<?php
// FROM HASH: 0bfaa833c35c597f28edd89cbadfee62
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Active user upgrades' . ($__vars['userUpgrade'] ? (': ' . $__templater->escape($__vars['userUpgrade']['title'])) : ''));
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
		'action' => $__templater->func('link', array('user-upgrades/active', $__vars['userUpgrade'], ), false),
		'class' => 'block',
	)) . '

';
	if (!$__templater->test($__vars['activeUpgrades'], 'empty', array())) {
		$__finalCompiled .= '
	<div class="block">
		<div class="block-container">
			<div class="block-body">
				';
		$__compilerTemp1 = '';
		if ($__templater->isTraversable($__vars['activeUpgrades'])) {
			foreach ($__vars['activeUpgrades'] AS $__vars['activeUpgrade']) {
				$__compilerTemp1 .= '
						';
				$__vars['paymentProfile'] = $__vars['activeUpgrade']['PurchaseRequest']['PaymentProfile'];
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
								' . $__templater->func('username_link', array($__vars['activeUpgrade']['User'], false, array(
					'defaultname' => 'Unknown user',
					'href' => $__templater->func('link', array('users/edit', $__vars['activeUpgrade']['User'], ), false),
				))) . '
							',
				),
				array(
					'_type' => 'cell',
					'html' => '
								<a href="' . $__templater->func('link', array('user-upgrades/edit', $__vars['activeUpgrade']['Upgrade'], ), true) . '">' . $__templater->escape($__vars['activeUpgrade']['Upgrade']['title']) . '</a>
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
					'html' => $__templater->func('date_dynamic', array($__vars['activeUpgrade']['start_date'], array(
				))),
				),
				array(
					'_type' => 'cell',
					'html' => ($__vars['activeUpgrade']['end_date'] ? $__templater->func('date', array($__vars['activeUpgrade']['end_date'], ), true) : 'Permanent'),
				),
				array(
					'overlay' => 'true',
					'href' => $__templater->func('link', array('user-upgrades/edit-active', null, array('user_upgrade_record_id' => $__vars['activeUpgrade']['user_upgrade_record_id'], ), ), false),
					'_type' => 'action',
					'html' => 'Edit',
				),
				array(
					'overlay' => 'true',
					'href' => $__templater->func('link', array('user-upgrades/downgrade', null, array('user_upgrade_record_id' => $__vars['activeUpgrade']['user_upgrade_record_id'], ), ), false),
					'_type' => 'action',
					'html' => 'Downgrade',
				))) . '
					';
			}
		}
		$__finalCompiled .= $__templater->dataList('
					' . $__templater->dataRow(array(
			'rowtype' => 'header',
		), array(array(
			'_type' => 'cell',
			'html' => '<a href="' . $__templater->func('link', array('user-upgrades/active', $__vars['userUpgrade'], array('order' => 'username', 'direction' => '', ) + $__vars['linkParams'], ), true) . '">' . 'User' . '</a>',
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
			'html' => '<a href="' . $__templater->func('link', array('user-upgrades/active', $__vars['userUpgrade'], array('order' => 'start_date', 'direction' => 'desc', ) + $__vars['linkParams'], ), true) . '">' . 'Start date' . '</a>',
		),
		array(
			'_type' => 'cell',
			'html' => '<a href="' . $__templater->func('link', array('user-upgrades/active', $__vars['userUpgrade'], array('order' => 'end_date', 'direction' => 'desc', ) + $__vars['linkParams'], ), true) . '">' . 'End date' . '</a>',
		),
		array(
			'colspan' => '2',
			'_type' => 'cell',
			'html' => '',
		))) . '
					' . $__compilerTemp1 . '
				', array(
			'data-xf-init' => 'responsive-data-list',
		)) . '
			</div>
			<div class="block-footer">
				<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['activeUpgrades'], $__vars['totalActive'], ), true) . '</span>
			</div>
		</div>

		' . $__templater->func('page_nav', array(array(
			'page' => $__vars['page'],
			'total' => $__vars['totalActive'],
			'link' => 'user-upgrades/active',
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