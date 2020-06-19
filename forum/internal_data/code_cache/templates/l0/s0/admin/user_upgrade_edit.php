<?php
// FROM HASH: dd16f693df1a880f646e887d4002d07d
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['upgrade'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add user upgrade');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit user upgrade' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['upgrade']['title']));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['upgrade'], 'isUpdate', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array('user-upgrades/delete', $__vars['upgrade'], ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

';
	$__compilerTemp1 = array();
	if ($__templater->isTraversable($__vars['profiles'])) {
		foreach ($__vars['profiles'] AS $__vars['profileId'] => $__vars['profile']) {
			$__compilerTemp1[] = array(
				'value' => $__vars['profileId'],
				'label' => (($__vars['profile']['Provider']['title'] !== $__vars['profile']['title']) ? (($__templater->escape($__vars['profile']['Provider']['title']) . ' - ') . $__templater->escape($__vars['profile']['title'])) : $__templater->escape($__vars['profile']['Provider']['title'])),
				'_type' => 'option',
			);
		}
	}
	$__compilerTemp2 = $__templater->mergeChoiceOptions(array(), $__vars['userGroups']);
	$__compilerTemp3 = '';
	if (!$__templater->test($__vars['upgrades'], 'empty', array())) {
		$__compilerTemp3 .= '
				';
		$__compilerTemp4 = $__templater->mergeChoiceOptions(array(), $__vars['upgrades']);
		$__compilerTemp3 .= $__templater->formCheckBoxRow(array(
			'name' => 'disabled_upgrade_ids',
			'value' => $__vars['upgrade']['disabled_upgrade_ids'],
			'listclass' => 'listColumns',
		), $__compilerTemp4, array(
			'label' => 'Disabled user upgrades',
			'explain' => 'Disables the selected user upgrades while this upgrade is active. This is helpful if you have tiers of the same upgrade and don\'t want people to buy multiple levels.',
		)) . '
			';
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formTextBoxRow(array(
		'name' => 'title',
		'value' => $__vars['upgrade']['title'],
		'maxlength' => $__templater->func('max_length', array($__vars['upgrade'], 'title', ), false),
	), array(
		'label' => 'Title',
	)) . '

			' . $__templater->formTextAreaRow(array(
		'name' => 'description',
		'value' => $__vars['upgrade']['description'],
		'autosize' => 'true',
	), array(
		'label' => 'Description',
		'hint' => 'You may use HTML',
	)) . '

			' . $__templater->callMacro('display_order_macros', 'row', array(
		'value' => ($__vars['upgrade']['display_order'] ?: 1),
	), $__vars) . '

			' . $__templater->formRow('

				<div class="inputGroup">
					' . $__templater->formTextBox(array(
		'name' => 'cost_amount',
		'value' => ($__vars['upgrade']['cost_amount'] ?: 5),
		'style' => 'width: 120px',
	)) . '
					<span class="inputGroup-splitter"></span>
					' . $__templater->callMacro('public:currency_macros', 'currency_list', array(
		'value' => ($__vars['upgrade']['cost_currency'] ?: 'USD'),
		'class' => 'input--autoSize',
	), $__vars) . '
				</div>

				<div class="formRow-explain">' . '<strong>Note:</strong> Ensure your merchant account with the selected payment profiles supports the above currencies. Currency support may vary by region.' . '</div>
			', array(
		'rowtype' => 'input',
		'label' => 'Cost',
	)) . '

			' . $__templater->formRadioRow(array(
		'name' => 'length_type',
	), array(array(
		'value' => 'permanent',
		'selected' => $__vars['upgrade']['length_unit'] == '',
		'label' => 'Permanent',
		'_type' => 'option',
	),
	array(
		'value' => 'timed',
		'selected' => $__vars['upgrade']['length_unit'] != '',
		'label' => 'For length' . $__vars['xf']['language']['label_separator'],
		'_dependent' => array('
						<div class="inputGroup">
							' . $__templater->formNumberBox(array(
		'name' => 'length_amount',
		'value' => ($__vars['upgrade']['length_amount'] ?: 1),
		'min' => '1',
	)) . '
							<span class="inputGroup-splitter"></span>
							' . $__templater->formSelect(array(
		'name' => 'length_unit',
		'value' => ((($__vars['upgrade']['length_unit'] == 'permanent') OR (!$__vars['upgrade']['length_amount'])) ? 'months' : $__vars['upgrade']['length_unit']),
		'class' => 'input--inline',
	), array(array(
		'value' => 'day',
		'label' => 'Days',
		'_type' => 'option',
	),
	array(
		'value' => 'month',
		'label' => 'Months',
		'_type' => 'option',
	),
	array(
		'value' => 'year',
		'label' => 'Years',
		'_type' => 'option',
	))) . '
						</div>

					', '
						' . $__templater->formCheckBox(array(
	), array(array(
		'name' => 'recurring',
		'value' => '1',
		'selected' => $__vars['upgrade']['recurring'],
		'label' => 'Recurring payments',
		'hint' => 'A payment will automatically be taken every time period to keep the upgrade active.<br />
<br />
<strong>Note:</strong> If enabled, all payment profiles assigned to this upgrade must support recurring payments.',
		'_type' => 'option',
	))) . '
					'),
		'_type' => 'option',
	)), array(
		'label' => 'Length',
	)) . '

			' . $__templater->formCheckBoxRow(array(
		'name' => 'payment_profile_ids',
		'value' => $__vars['upgrade']['payment_profile_ids'],
	), $__compilerTemp1, array(
		'label' => 'Payment profile',
	)) . '

			' . $__templater->formCheckBoxRow(array(
		'name' => 'extra_group_ids',
		'value' => $__vars['upgrade']['extra_group_ids'],
		'listclass' => 'listColumns',
	), $__compilerTemp2, array(
		'label' => 'Additional user groups',
		'explain' => 'Puts the user in the selected groups while the upgrade is active.',
	)) . '

			' . $__compilerTemp3 . '

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'can_purchase',
		'selected' => $__vars['upgrade']['can_purchase'],
		'label' => 'Can be purchased',
		'_type' => 'option',
	)), array(
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'icon' => 'save',
		'sticky' => 'true',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('user-upgrades/save', $__vars['upgrade'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});