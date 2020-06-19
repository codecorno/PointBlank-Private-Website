<?php
// FROM HASH: 2d97e52458f4c4035a94b6db68d801ed
return array('macros' => array('upgrade_list' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'upgrades' => '!',
		'viewMoreUrl' => '',
		'active' => '1',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	
	';
	$__compilerTemp1 = '';
	if ($__templater->isTraversable($__vars['upgrades'])) {
		foreach ($__vars['upgrades'] AS $__vars['upgrade']) {
			$__compilerTemp1 .= '
			';
			$__vars['paymentProfile'] = $__vars['upgrade']['PurchaseRequest']['PaymentProfile'];
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
			$__compilerTemp3 = array(array(
				'_type' => 'cell',
				'html' => '
					' . $__templater->func('username_link', array($__vars['upgrade']['User'], false, array(
				'defaultname' => 'Unknown user',
				'href' => $__templater->func('link', array('users/edit', $__vars['upgrade']['User'], ), false),
			))) . '
				',
			)
,array(
				'_type' => 'cell',
				'html' => '
					<a href="' . $__templater->func('link', array('user-upgrades/edit', $__vars['upgrade']['Upgrade'], ), true) . '">' . $__templater->escape($__vars['upgrade']['Upgrade']['title']) . '</a>
				',
			)
,array(
				'_type' => 'cell',
				'html' => '
					' . '' . '
					' . $__compilerTemp2 . '
				',
			)
,array(
				'_type' => 'cell',
				'html' => $__templater->func('date_dynamic', array($__vars['upgrade']['start_date'], array(
			))),
			)
,array(
				'_type' => 'cell',
				'html' => ($__vars['upgrade']['end_date'] ? $__templater->func('date', array($__vars['upgrade']['end_date'], ), true) : 'Permanent'),
			));
			if ($__vars['active']) {
				$__compilerTemp3[] = array(
					'overlay' => 'true',
					'href' => $__templater->func('link', array('user-upgrades/edit-active', null, array('user_upgrade_record_id' => $__vars['upgrade']['user_upgrade_record_id'], ), ), false),
					'_type' => 'action',
					'html' => 'Edit',
				);
				$__compilerTemp3[] = array(
					'overlay' => 'true',
					'href' => $__templater->func('link', array('user-upgrades/downgrade', null, array('user_upgrade_record_id' => $__vars['upgrade']['user_upgrade_record_id'], ), ), false),
					'_type' => 'action',
					'html' => 'Downgrade',
				);
			}
			$__compilerTemp1 .= $__templater->dataRow(array(
				'rowclass' => 'dataList-row--noHover',
			), $__compilerTemp3) . '
		';
		}
	}
	$__finalCompiled .= $__templater->dataList('
		' . $__templater->dataRow(array(
		'rowtype' => 'header',
	), array(array(
		'_type' => 'cell',
		'html' => 'User',
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
		'html' => 'Start date',
	),
	array(
		'colspan' => ($__vars['active'] ? 3 : 1),
		'_type' => 'cell',
		'html' => 'End date',
	))) . '
		' . $__compilerTemp1 . '
	', array(
		'data-xf-init' => 'responsive-data-list',
	)) . '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('User upgrades');
	$__finalCompiled .= '

';
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('Add user upgrade', array(
		'href' => $__templater->func('link', array('user-upgrades/add', ), false),
		'icon' => 'add',
	), '', array(
	)) . '
');
	$__finalCompiled .= '

';
	if (!$__vars['xf']['livePayments']) {
		$__finalCompiled .= '
	<div class="blockMessage blockMessage--important blockMessage--iconic">
		' . 'Live payments have been disabled so, where available, purchase requests will be made to sandbox/test endpoints. To rectify this, please edit config.php.' . '
	</div>
';
	}
	$__finalCompiled .= '

';
	if (!$__templater->test($__vars['upgrades'], 'empty', array())) {
		$__finalCompiled .= '
	';
		$__compilerTemp1 = '';
		if ($__templater->isTraversable($__vars['upgrades'])) {
			foreach ($__vars['upgrades'] AS $__vars['upgrade']) {
				$__compilerTemp1 .= '
						' . $__templater->dataRow(array(
					'label' => $__templater->escape($__vars['upgrade']['title']),
					'hint' => $__templater->escape($__vars['upgrade']['cost_phrase']),
					'href' => $__templater->func('link', array('user-upgrades/edit', $__vars['upgrade'], ), false),
					'delete' => $__templater->func('link', array('user-upgrades/delete', $__vars['upgrade'], ), false),
				), array(array(
					'name' => 'can_purchase[' . $__vars['upgrade']['user_upgrade_id'] . ']',
					'selected' => $__vars['upgrade']['can_purchase'],
					'class' => 'dataList-cell--separated',
					'submit' => 'true',
					'tooltip' => 'Enable / disable \'' . $__vars['upgrade']['title'] . '\'',
					'_type' => 'toggle',
					'html' => '',
				),
				array(
					'class' => 'dataList-cell--action',
					'_type' => 'popup',
					'html' => '
								<div class="menu" data-menu="menu" aria-hidden="true">
									<div class="menu-content">
										<h3 class="menu-header">' . 'Actions' . '</h3>
										<a href="' . $__templater->func('link', array('user-upgrades/active', $__vars['upgrade'], ), true) . '" class="menu-linkRow">' . 'View users' . '</a>
										<a href="' . $__templater->func('link', array('user-upgrades/expired', $__vars['upgrade'], ), true) . '" class="menu-linkRow">' . 'View expired upgrades' . '</a>
										<a href="' . $__templater->func('link', array('user-upgrades/manual', $__vars['upgrade'], ), true) . '" class="menu-linkRow">' . 'Manually upgrade user' . '</a>
									</div>
								</div>
							',
				))) . '
					';
			}
		}
		$__finalCompiled .= $__templater->form('
		<div class="block-outer">
			' . $__templater->callMacro('filter_macros', 'quick_filter', array(
			'key' => 'user-upgrades',
			'class' => 'block-outer-opposite',
		), $__vars) . '
		</div>
		<div class="block-container">
			<div class="block-body">
				' . $__templater->dataList('
					' . $__compilerTemp1 . '
				', array(
		)) . '
			</div>
			<div class="block-footer">
				<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['upgrades'], ), true) . '</span>
			</div>
		</div>
	', array(
			'action' => $__templater->func('link', array('user-upgrades/toggle', ), false),
			'class' => 'block',
			'ajax' => 'true',
		)) . '

	<div class="block">
		<div class="block-container">
			<h2 class="block-header"><a href="' . $__templater->func('link', array('user-upgrades/active', ), true) . '">' . 'Active user upgrades' . '</a></h2>
			';
		if (!$__templater->test($__vars['activeUpgrades'], 'empty', array())) {
			$__finalCompiled .= '
				<div class="block-body">
					' . $__templater->callMacro(null, 'upgrade_list', array(
				'upgrades' => $__vars['activeUpgrades'],
				'active' => '1',
			), $__vars) . '
				</div>
				<div class="block-footer block-footer--split">
					<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['activeUpgrades'], $__vars['totalActiveUpgrades'], ), true) . '</span>
					<span class="block-footer-controls">' . $__templater->button('View more' . $__vars['xf']['language']['ellipsis'], array(
				'href' => $__templater->func('link', array('user-upgrades/active', ), false),
			), '', array(
			)) . '</span>
				</div>
			';
		} else {
			$__finalCompiled .= '
				<div class="block-body block-row">' . 'No results found.' . '</div>
			';
		}
		$__finalCompiled .= '
		</div>
	</div>

	<div class="block">
		<div class="block-container">
			<h2 class="block-header"><a href="' . $__templater->func('link', array('user-upgrades/expired', ), true) . '">' . 'Expired user upgrades' . '</a></h2>
			';
		if (!$__templater->test($__vars['expiredUpgrades'], 'empty', array())) {
			$__finalCompiled .= '
				<div class="block-body">
					' . $__templater->callMacro(null, 'upgrade_list', array(
				'upgrades' => $__vars['expiredUpgrades'],
				'active' => '0',
			), $__vars) . '
				</div>
				<div class="block-footer block-footer--split">
					<span class="block-footer-counter">' . $__templater->func('display_totals', array($__vars['expiredUpgrades'], $__vars['totalExpiredUpgrades'], ), true) . '</span>
					<span class="block-footer-controls">' . $__templater->button('View more' . $__vars['xf']['language']['ellipsis'], array(
				'href' => $__templater->func('link', array('user-upgrades/expired', ), false),
			), '', array(
			)) . '</span>
				</div>
			';
		} else {
			$__finalCompiled .= '
				<div class="block-body block-row">' . 'No results found.' . '</div>
			';
		}
		$__finalCompiled .= '
		</div>
	</div>
';
	} else {
		$__finalCompiled .= '
	<div class="blockMessage">' . 'No items have been created yet.' . '</div>
';
	}
	$__finalCompiled .= '

';
	return $__finalCompiled;
});