<?php
// FROM HASH: 8b8e8e53f4c8d224c65020e1f5436542
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if (!$__vars['noSpecificUser']) {
		$__finalCompiled .= '
	' . $__templater->formTextBoxRow(array(
			'name' => 'criteria[username]',
			'value' => $__vars['criteria']['username'],
			'readonly' => $__vars['readOnly'],
		), array(
			'label' => 'User name',
		)) . '

	' . $__templater->formTextBoxRow(array(
			'name' => 'criteria[email]',
			'value' => $__vars['criteria']['email'],
		), array(
			'label' => 'Email',
		)) . '
';
	}
	$__finalCompiled .= '

<hr class="formRowSep" />

';
	if (!$__vars['readOnly']) {
		$__finalCompiled .= '
	';
		$__compilerTemp1 = array(array(
			'label' => $__vars['xf']['language']['parenthesis_open'] . 'Any' . $__vars['xf']['language']['parenthesis_close'],
			'_type' => 'option',
		));
		if ($__templater->isTraversable($__vars['userGroups'])) {
			foreach ($__vars['userGroups'] AS $__vars['userGroup']) {
				$__compilerTemp1[] = array(
					'value' => $__vars['userGroup']['user_group_id'],
					'label' => $__templater->escape($__vars['userGroup']['title']),
					'_type' => 'option',
				);
			}
		}
		$__finalCompiled .= $__templater->formSelectRow(array(
			'name' => 'criteria[user_group_id]',
			'value' => $__vars['criteria']['user_group_id'],
		), $__compilerTemp1, array(
			'label' => 'Primary user group',
		)) . '
';
	} else {
		$__finalCompiled .= '
	';
		$__compilerTemp2 = '';
		if ($__vars['userGroups'][$__vars['criteria_user_group_id']]['title']) {
			$__compilerTemp2 .= '
			' . $__templater->escape($__vars['userGroups'][$__vars['criteria_user_group_id']]['title']) . '
		';
		} else {
			$__compilerTemp2 .= '
			' . $__vars['xf']['language']['parenthesis_open'] . 'Any' . $__vars['xf']['language']['parenthesis_close'] . '
		';
		}
		$__finalCompiled .= $__templater->formRow('

		' . $__compilerTemp2 . '
	', array(
			'label' => 'Primary user group',
		)) . '
	' . $__templater->formHiddenVal('criteria[user_group_id]', $__vars['criteria']['user_group_id'], array(
		)) . '
';
	}
	$__finalCompiled .= '

';
	$__compilerTemp3 = array();
	if ($__templater->isTraversable($__vars['userGroups'])) {
		foreach ($__vars['userGroups'] AS $__vars['userGroup']) {
			$__compilerTemp3[] = array(
				'value' => $__vars['userGroup']['user_group_id'],
				'label' => $__templater->escape($__vars['userGroup']['title']),
				'_type' => 'option',
			);
		}
	}
	$__finalCompiled .= $__templater->formRadioRow(array(
		'name' => 'criteria[no_secondary_group_ids]',
		'value' => $__vars['criteria']['no_secondary_group_ids'],
		'readonly' => $__vars['readOnly'],
	), array(array(
		'value' => '1',
		'label' => 'Member of no secondary groups',
		'_type' => 'option',
	),
	array(
		'value' => '0',
		'label' => 'Member of any of these secondary groups',
		'_dependent' => array($__templater->formCheckBox(array(
		'name' => 'criteria[secondary_group_ids]',
		'value' => $__vars['criteria']['secondary_group_ids'],
		'listclass' => 'listColumns',
		'readonly' => $__vars['readOnly'],
	), $__compilerTemp3)),
		'_type' => 'option',
	)), array(
		'label' => 'Secondary user groups',
	)) . '

';
	$__compilerTemp4 = array();
	if ($__templater->isTraversable($__vars['userGroups'])) {
		foreach ($__vars['userGroups'] AS $__vars['userGroup']) {
			$__compilerTemp4[] = array(
				'value' => $__vars['userGroup']['user_group_id'],
				'label' => $__templater->escape($__vars['userGroup']['title']),
				'_type' => 'option',
			);
		}
	}
	$__finalCompiled .= $__templater->formCheckBoxRow(array(
		'name' => 'criteria[not_secondary_group_ids]',
		'value' => $__vars['criteria']['not_secondary_group_ids'],
		'listclass' => 'listColumns',
		'readonly' => $__vars['readOnly'],
	), $__compilerTemp4, array(
		'label' => 'Not in secondary groups',
	)) . '

<hr class="formRowSep" />

' . $__templater->formRow('

	<div class="inputGroup">
		' . $__templater->formDateInput(array(
		'name' => 'criteria[register_date][start]',
		'value' => $__vars['criteria']['register_date']['start'],
		'size' => '15',
		'readonly' => $__vars['readOnly'],
	)) . '
		<span class="inputGroup-text">-</span>
		' . $__templater->formDateInput(array(
		'name' => 'criteria[register_date][end]',
		'value' => $__vars['criteria']['register_date']['end'],
		'size' => '15',
		'readonly' => $__vars['readOnly'],
	)) . '
	</div>
', array(
		'rowtype' => 'input',
		'label' => 'Registered between',
	)) . '

' . $__templater->formRow('

	<div class="inputGroup">
		' . $__templater->formDateInput(array(
		'name' => 'criteria[last_activity][start]',
		'value' => $__vars['criteria']['last_activity']['start'],
		'size' => '15',
		'readonly' => $__vars['readOnly'],
	)) . '
		<span class="inputGroup-text">-</span>
		' . $__templater->formDateInput(array(
		'name' => 'criteria[last_activity][end]',
		'value' => $__vars['criteria']['last_activity']['end'],
		'size' => '15',
		'readonly' => $__vars['readOnly'],
	)) . '
	</div>
', array(
		'rowtype' => 'input',
		'label' => 'Last visited between',
		'explain' => 'Visits in the last hour may not be considered.',
	)) . '

' . $__templater->formRow('

	<div class="inputGroup">
		' . $__templater->formNumberBox(array(
		'name' => 'criteria[message_count][start]',
		'value' => $__vars['criteria']['message_count']['start'],
		'min' => '0',
		'readonly' => $__vars['readOnly'],
	)) . '
		<span class="inputGroup-text">-</span>
		' . $__templater->formNumberBox(array(
		'name' => 'criteria[message_count][end]',
		'value' => $__vars['criteria']['message_count']['end'],
		'min' => '-1',
		'readonly' => $__vars['readOnly'],
	)) . '
	</div>
', array(
		'rowtype' => 'input',
		'label' => 'Message count between',
		'explain' => 'Use -1 to specify no maximum.',
	)) . '

' . $__templater->formRow('

	<div class="inputGroup">
		' . $__templater->formNumberBox(array(
		'name' => 'criteria[trophy_points][start]',
		'value' => $__vars['criteria']['trophy_points']['start'],
		'min' => '0',
		'readonly' => $__vars['readOnly'],
	)) . '
		<span class="inputGroup-text">-</span>
		' . $__templater->formNumberBox(array(
		'name' => 'criteria[trophy_points][end]',
		'value' => $__vars['criteria']['trophy_points']['end'],
		'min' => '-1',
		'readonly' => $__vars['readOnly'],
	)) . '
	</div>
', array(
		'rowtype' => 'input',
		'label' => 'Trophy points between',
		'explain' => 'Use -1 to specify no maximum.',
	)) . '

' . $__templater->formRow('
	<div class="inputGroup">
		' . $__templater->formNumberBox(array(
		'name' => 'criteria[reaction_score][start]',
		'value' => $__vars['criteria']['reaction_score']['start'],
		'readonly' => $__vars['readOnly'],
	)) . '
		<span class="inputGroup-text">-</span>
		' . $__templater->formNumberBox(array(
		'name' => 'criteria[reaction_score][end]',
		'value' => $__vars['criteria']['reaction_score']['end'],
		'readonly' => $__vars['readOnly'],
	)) . '
	</div>
', array(
		'rowtype' => 'input',
		'label' => 'Reaction score between',
	)) . '

<hr class="formRowSep" />

' . $__templater->formCheckBoxRow(array(
		'name' => 'criteria[user_state]',
		'readonly' => $__vars['readOnly'],
	), array(array(
		'value' => 'valid',
		'selected' => $__templater->func('in_array', array('valid', $__vars['criteria']['user_state'], ), false),
		'label' => 'Valid',
		'_type' => 'option',
	),
	array(
		'value' => 'email_confirm',
		'selected' => $__templater->func('in_array', array('email_confirm', $__vars['criteria']['user_state'], ), false),
		'label' => 'Awaiting email confirmation',
		'_type' => 'option',
	),
	array(
		'value' => 'email_confirm_edit',
		'selected' => $__templater->func('in_array', array('email_confirm_edit', $__vars['criteria']['user_state'], ), false),
		'label' => 'Awaiting email confirmation (from edit)',
		'_type' => 'option',
	),
	array(
		'value' => 'email_bounce',
		'selected' => $__templater->func('in_array', array('email_bounce', $__vars['criteria']['user_state'], ), false),
		'label' => 'Email invalid (bounced)',
		'_type' => 'option',
	),
	array(
		'value' => 'moderated',
		'selected' => $__templater->func('in_array', array('moderated', $__vars['criteria']['user_state'], ), false),
		'label' => 'Awaiting moderation',
		'_type' => 'option',
	),
	array(
		'value' => 'rejected',
		'selected' => $__templater->func('in_array', array('rejected', $__vars['criteria']['user_state'], ), false),
		'label' => 'Rejected',
		'_type' => 'option',
	),
	array(
		'value' => 'disabled',
		'selected' => $__templater->func('in_array', array('disabled', $__vars['criteria']['user_state'], ), false),
		'label' => 'Disabled',
		'_type' => 'option',
	)), array(
		'label' => 'User state',
	)) . '

' . $__templater->formCheckBoxRow(array(
		'name' => 'criteria[is_banned]',
		'readonly' => $__vars['readOnly'],
	), array(array(
		'value' => '0',
		'selected' => $__templater->func('in_array', array(0, $__vars['criteria']['is_banned'], ), false),
		'label' => 'Not banned',
		'_type' => 'option',
	),
	array(
		'value' => '1',
		'selected' => $__templater->func('in_array', array(1, $__vars['criteria']['is_banned'], ), false),
		'label' => 'Banned',
		'_type' => 'option',
	)), array(
		'label' => 'Banned state',
	)) . '

' . $__templater->formCheckBoxRow(array(
		'name' => 'criteria[Option][is_discouraged]',
		'readonly' => $__vars['readOnly'],
	), array(array(
		'value' => '0',
		'selected' => $__templater->func('in_array', array(0, $__vars['criteria']['Option']['is_discouraged'], ), false),
		'label' => 'Not discouraged',
		'_type' => 'option',
	),
	array(
		'value' => '1',
		'selected' => $__templater->func('in_array', array(1, $__vars['criteria']['Option']['is_discouraged'], ), false),
		'label' => 'Discouraged',
		'_type' => 'option',
	)), array(
		'label' => 'Discouragement state',
	)) . '

' . $__templater->formCheckBoxRow(array(
		'name' => 'criteria[is_staff]',
		'readonly' => $__vars['readOnly'],
	), array(array(
		'value' => '0',
		'selected' => $__templater->func('in_array', array(0, $__vars['criteria']['is_staff'], ), false),
		'label' => 'Not staff member',
		'_type' => 'option',
	),
	array(
		'value' => '1',
		'selected' => $__templater->func('in_array', array(1, $__vars['criteria']['is_staff'], ), false),
		'label' => 'Staff member',
		'_type' => 'option',
	)), array(
		'label' => 'Staff state',
	)) . '

';
	$__compilerTemp5 = '';
	$__compilerTemp5 .= '
		';
	$__compilerTemp6 = $__templater->method($__templater->method($__vars['xf']['app']['em'], 'getRepository', array('XF:UserField', )), 'getDisplayGroups', array());
	if ($__templater->isTraversable($__compilerTemp6)) {
		foreach ($__compilerTemp6 AS $__vars['fieldGroup'] => $__vars['phrase']) {
			$__compilerTemp5 .= '
			';
			$__vars['customFields'] = $__templater->method($__vars['xf']['app'], 'getCustomFields', array('users', $__vars['fieldGroup'], ));
			$__compilerTemp5 .= '
			';
			$__compilerTemp7 = '';
			$__compilerTemp7 .= '
					';
			if ($__templater->isTraversable($__vars['customFields'])) {
				foreach ($__vars['customFields'] AS $__vars['fieldId'] => $__vars['fieldDefinition']) {
					$__compilerTemp7 .= '
						';
					$__vars['choices'] = $__vars['fieldDefinition']['field_choices'];
					$__compilerTemp7 .= '
						';
					$__vars['fieldName'] = 'criteria[user_field]' . (($__vars['choices'] AND ($__vars['fieldDefinition']['type_group'] != 'multiple')) ? '[exact]' : '') . '[' . $__vars['fieldId'] . ']';
					$__compilerTemp7 .= '
						';
					$__compilerTemp8 = '';
					if (!$__vars['choices']) {
						$__compilerTemp8 .= '
								' . $__templater->formTextBox(array(
							'name' => $__vars['fieldName'],
							'value' => $__vars['criteria']['user_field'][$__vars['fieldId']],
							'readonly' => $__vars['readOnly'],
						)) . '
							';
					} else {
						$__compilerTemp8 .= '

								';
						$__compilerTemp9 = array();
						if ($__templater->isTraversable($__vars['choices'])) {
							foreach ($__vars['choices'] AS $__vars['val'] => $__vars['choice']) {
								$__compilerTemp9[] = array(
									'value' => (($__vars['fieldDefinition']['type_group'] == 'multiple') ? (((('s:' . $__templater->func('strlen', array($__vars['val'], ), false)) . ':"') . $__vars['val']) . '"') : $__vars['val']),
									'label' => $__templater->escape($__vars['choice']),
									'_type' => 'option',
								);
							}
						}
						$__compilerTemp8 .= $__templater->formCheckBox(array(
							'name' => $__vars['fieldName'],
							'value' => (($__vars['fieldDefinition']['type_group'] != 'multiple') ? $__vars['criteria']['user_field']['exact'][$__vars['fieldId']] : $__vars['criteria']['user_field'][$__vars['fieldId']]),
							'listclass' => 'listColumns',
							'readonly' => $__vars['readOnly'],
						), $__compilerTemp9) . '

							';
					}
					$__compilerTemp7 .= $__templater->formRow('

							' . $__compilerTemp8 . '

						', array(
						'rowtype' => ($__vars['choices'] ? '' : 'input'),
						'label' => $__templater->escape($__vars['fieldDefinition']['title']),
					)) . '
					';
				}
			}
			$__compilerTemp7 .= '
				';
			if (strlen(trim($__compilerTemp7)) > 0) {
				$__compilerTemp5 .= '
				' . $__compilerTemp7 . '
			';
			}
			$__compilerTemp5 .= '
		';
		}
	}
	$__compilerTemp5 .= '
	';
	if (strlen(trim($__compilerTemp5)) > 0) {
		$__finalCompiled .= '
	<hr class="formRowSep" />
	' . $__compilerTemp5 . '
';
	}
	return $__finalCompiled;
});