<?php
// FROM HASH: fc3377588a39db99fd09899fa46dafc4
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['memberStat'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add member stat');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit member stat' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['memberStat']['title']));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['memberStat'], 'isUpdate', array()) AND $__templater->method($__vars['memberStat'], 'canEdit', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array('member-stats/delete', $__vars['memberStat'], ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

';
	if ((!$__templater->method($__vars['memberStat'], 'canEdit', array()))) {
		$__finalCompiled .= '
	<div class="blockMessage blockMessage--important blockMessage--iconic">
		' . 'Only a limited number of fields in this item may be edited.' . '
	</div>
';
	}
	$__finalCompiled .= '

';
	$__compilerTemp1 = $__vars;
	$__compilerTemp1['noSpecificUser'] = true;
	$__compilerTemp1['readOnly'] = (!$__templater->method($__vars['memberStat'], 'canEdit', array()));
	$__compilerTemp2 = '';
	if ($__templater->method($__vars['memberStat'], 'canEdit', array())) {
		$__compilerTemp2 .= '
				';
		$__compilerTemp3 = $__templater->mergeChoiceOptions(array(), $__vars['sortOrders']);
		$__compilerTemp2 .= $__templater->formRow('
					<div class="inputPair">
						' . $__templater->formSelect(array(
			'name' => 'sort_order',
			'value' => $__vars['memberStat']['sort_order'],
		), $__compilerTemp3) . '

					' . $__templater->formSelect(array(
			'name' => 'sort_direction',
			'value' => $__vars['memberStat']['sort_direction'],
		), array(array(
			'value' => 'desc',
			'label' => 'Descending',
			'_type' => 'option',
		),
		array(
			'value' => 'asc',
			'label' => 'Ascending',
			'_type' => 'option',
		))) . '
					</div>
				', array(
			'label' => 'Sort',
			'rowtype' => 'input',
		)) . '
			';
	} else {
		$__compilerTemp2 .= '
				';
		$__compilerTemp4 = '';
		if ($__vars['memberStat']['sort_direction'] == 'desc') {
			$__compilerTemp4 .= '
						' . 'Descending' . '
					';
		} else {
			$__compilerTemp4 .= '
						' . 'Ascending' . '
					';
		}
		$__compilerTemp2 .= $__templater->formRow('
					' . $__templater->escape($__vars['sortOrders'][$__vars['memberStat']['sort_order']]) . '
					&middot;
					' . $__compilerTemp4 . '

					' . $__templater->formHiddenVal('sort_order', $__vars['memberStat']['sort_order'], array(
		)) . '
					' . $__templater->formHiddenVal('sort_direction', $__vars['memberStat']['sort_direction'], array(
		)) . '
				', array(
			'label' => 'Sort',
		)) . '
			';
	}
	$__compilerTemp5 = array(array(
		'value' => '',
		'label' => $__vars['xf']['language']['parenthesis_open'] . 'None' . $__vars['xf']['language']['parenthesis_close'],
		'_type' => 'option',
	));
	if ($__templater->isTraversable($__vars['permissionsData']['interfaceGroups'])) {
		foreach ($__vars['permissionsData']['interfaceGroups'] AS $__vars['interfaceGroupId'] => $__vars['interfaceGroup']) {
			$__compilerTemp5[] = array(
				'label' => $__vars['interfaceGroup']['title'],
				'_type' => 'optgroup',
				'options' => array(),
			);
			end($__compilerTemp5); $__compilerTemp6 = key($__compilerTemp5);
			if ($__templater->isTraversable($__vars['permissionsData']['permissionsGrouped'][$__vars['interfaceGroupId']])) {
				foreach ($__vars['permissionsData']['permissionsGrouped'][$__vars['interfaceGroupId']] AS $__vars['permission']) {
					$__compilerTemp5[$__compilerTemp6]['options'][] = array(
						'value' => $__vars['permission']['permission_group_id'] . '-' . $__vars['permission']['permission_id'],
						'label' => '
								' . $__templater->escape($__vars['permission']['title']) . '
							',
						'_type' => 'option',
					);
				}
			}
		}
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formTextBoxRow(array(
		'name' => 'title',
		'value' => ($__vars['memberStat']['member_stat_id'] ? $__vars['memberStat']['MasterTitle']['phrase_text'] : ''),
		'readonly' => (!$__templater->method($__vars['memberStat'], 'canEdit', array())),
	), array(
		'label' => 'Title',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'member_stat_key',
		'value' => $__vars['memberStat']['member_stat_key'],
		'maxlength' => $__templater->func('max_length', array($__vars['memberStat'], 'member_stat_key', ), false),
		'readonly' => (!$__templater->method($__vars['memberStat'], 'canEdit', array())),
	), array(
		'label' => 'Member stat key',
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formRadioRow(array(
		'name' => 'user_criteria',
		'readonly' => (!$__templater->method($__vars['memberStat'], 'canEdit', array())),
	), array(array(
		'value' => 'all',
		'selected' => !$__vars['memberStat']['criteria'],
		'label' => 'All valid users',
		'_type' => 'option',
	),
	array(
		'value' => 'searcher',
		'selected' => $__vars['memberStat']['criteria'],
		'label' => 'Searcher criteria',
		'data-xf-init' => 'disabler',
		'data-container' => '.js-hiderContainer',
		'data-hide' => 'yes',
		'_type' => 'option',
	)), array(
		'label' => 'User criteria',
	)) . '

			<div class="js-hiderContainer">
				<hr class="formRowSep" />

				' . $__templater->includeTemplate('helper_user_search_criteria', $__compilerTemp1) . '

				<hr class="formRowSep" />
			</div>

			' . $__compilerTemp2 . '

			' . $__templater->formRow('

				' . $__templater->callMacro('helper_callback_fields', 'callback_fields', array(
		'data' => $__vars['memberStat'],
		'readOnly' => (!$__templater->method($__vars['memberStat'], 'canEdit', array())),
	), $__vars) . '
			', array(
		'rowtype' => 'input',
		'label' => 'PHP callback',
		'explain' => 'You may optionally set a PHP callback here to manipulate the finder with additional criteria, or override existing criteria.<br />
<br />
Callback arguments:
<ol>
	<li>
		<code>\\XF\\Entity\\MemberStat $memberStat</code>
		<br />
		This member stat entity.
	</li>
	<li>
		<code>\\XF\\Finder\\User $finder</code>
		<br />
		The User finder set up with the criteria/order settings already specified above.
	</li>
</ol>
The callback should either return an array or nothing at all. If nothing is returned, the results will be formatted automatically. Example array:<br />
<br />
<code>return [$user->user_id => \\XF::language()->numberFormat($value)];</code>',
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formSelectRow(array(
		'name' => 'permission_limit',
		'value' => $__vars['memberStat']['permission_limit_'],
		'disabled' => (!$__templater->method($__vars['memberStat'], 'canEdit', array())),
		'readonly' => (!$__templater->method($__vars['memberStat'], 'canEdit', array())),
	), $__compilerTemp5, array(
		'label' => 'Permission limit',
		'explain' => 'If a permission is specified here, this stat will only be visible to users with that permission.',
	)) . '

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'show_value',
		'selected' => $__vars['memberStat']['show_value'],
		'readonly' => (!$__templater->method($__vars['memberStat'], 'canEdit', array())),
		'label' => 'Show value',
		'hint' => 'If enabled, and provided, a value will be displayed for each user.',
		'_type' => 'option',
	),
	array(
		'name' => 'overview_display',
		'selected' => $__vars['memberStat']['overview_display'],
		'label' => 'Show on overview tab',
		'hint' => (('If enabled, the top 5 members of this stat will be displayed on the overview tab.' . ' ') . (($__vars['xf']['development'] AND $__vars['memberStat']['addon_id']) ? 'The value of this field will not be changed when this add-on is upgraded.' : '')),
		'_type' => 'option',
	)), array(
		'label' => 'Options',
	)) . '

			' . $__templater->formNumberBoxRow(array(
		'name' => 'user_limit',
		'value' => $__vars['memberStat']['user_limit'],
		'min' => '0',
		'step' => '1',
	), array(
		'label' => 'User limit',
		'explain' => (($__vars['xf']['development'] AND $__vars['memberStat']['addon_id']) ? 'The value of this field will not be changed when this add-on is upgraded.' : ''),
	)) . '

			' . $__templater->callMacro('display_order_macros', 'row', array(
		'value' => $__vars['memberStat']['display_order'],
		'explain' => (($__vars['xf']['development'] AND $__vars['memberStat']['addon_id']) ? 'The value of this field will not be changed when this add-on is upgraded.' : ''),
	), $__vars) . '

			' . $__templater->formCheckBoxRow(array(
		'readonly' => (!$__templater->method($__vars['memberStat'], 'canEdit', array())),
	), array(array(
		'selected' => $__vars['memberStat']['cache_lifetime'],
		'label' => 'Enable cache and cache for X minutes' . $__vars['xf']['language']['label_separator'],
		'_dependent' => array($__templater->formNumberBox(array(
		'name' => 'cache_lifetime',
		'value' => ($__vars['memberStat']['cache_lifetime'] ?: 60),
		'min' => '0',
		'readonly' => (!$__templater->method($__vars['memberStat'], 'canEdit', array())),
	))),
		'_type' => 'option',
	)), array(
	)) . '

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'active',
		'selected' => $__vars['memberStat']['active'],
		'label' => 'Member stat is active',
		'hint' => (('Use this to disable the member stat from showing on the Members page.' . ' ') . (($__vars['xf']['development'] AND $__vars['memberStat']['addon_id']) ? 'The value of this field will not be changed when this add-on is upgraded.' : '')),
		'_type' => 'option',
	)), array(
	)) . '

			' . $__templater->callMacro('addon_macros', 'addon_edit', array(
		'addOnId' => $__vars['memberStat']['addon_id'],
	), $__vars) . '
		</div>

		' . $__templater->formSubmitRow(array(
		'icon' => 'save',
		'sticky' => 'true',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('member-stats/save', $__vars['memberStat'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});