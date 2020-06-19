<?php
// FROM HASH: 03e380507a73164a5dc1ca150bfc65ea
return array('macros' => array('custom_fields_view' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'type' => '!',
		'group' => '!',
		'set' => '!',
		'onlyInclude' => null,
		'additionalFilters' => array(),
		'wrapperClass' => '',
		'valueClass' => 'pairs pairs--columns pairs--fixedSmall',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	';
	$__compilerTemp1 = '';
	$__compilerTemp1 .= '
			' . $__templater->callMacro(null, 'custom_fields_values', array(
		'type' => $__vars['type'],
		'group' => $__vars['group'],
		'set' => $__vars['set'],
		'onlyInclude' => $__vars['onlyInclude'],
		'additionalFilters' => $__vars['additionalFilters'],
		'valueClass' => $__vars['valueClass'],
	), $__vars) . '
		';
	if (strlen(trim($__compilerTemp1)) > 0) {
		$__finalCompiled .= '
		<div class="' . $__templater->escape($__vars['wrapperClass']) . '">
		' . $__compilerTemp1 . '
		</div>
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'custom_fields_values' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'type' => '!',
		'group' => '!',
		'set' => '!',
		'onlyInclude' => null,
		'additionalFilters' => array(),
		'valueClass' => 'pairs pairs--columns pairs--fixedSmall',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	';
	$__compilerTemp1 = $__templater->method($__vars['xf']['app'], 'getCustomFields', array($__vars['type'], $__vars['group'], $__vars['onlyInclude'], $__vars['additionalFilters'], ));
	if ($__templater->isTraversable($__compilerTemp1)) {
		foreach ($__compilerTemp1 AS $__vars['fieldId'] => $__vars['fieldDefinition']) {
			$__finalCompiled .= '
		';
			if ($__templater->method($__vars['fieldDefinition'], 'hasValue', array($__vars['set'][$__vars['fieldDefinition']['field_id']], ))) {
				$__finalCompiled .= '
			<dl class="' . $__templater->escape($__vars['valueClass']) . '">
				<dt>' . $__templater->escape($__vars['fieldDefinition']['title']) . '</dt>
				<dd>
					' . $__templater->callMacro(null, 'custom_field_value', array(
					'definition' => $__vars['fieldDefinition'],
					'value' => $__vars['set'][$__vars['fieldDefinition']['field_id']],
				), $__vars) . '
				</dd>
			</dl>
		';
			}
			$__finalCompiled .= '
	';
		}
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'custom_field_value' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'definition' => '!',
		'value' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	if ($__vars['definition']['field_type'] == 'stars') {
		$__finalCompiled .= '
		' . $__templater->callMacro('rating_macros', 'stars', array(
			'rating' => $__vars['value'],
		), $__vars) . '
	';
	} else {
		$__finalCompiled .= '
		';
		if ($__vars['definition']['match_type'] == 'date') {
			$__finalCompiled .= '
			' . $__templater->callMacro(null, 'custom_field_value_date', array(
				'date' => $__vars['value'],
			), $__vars) . '
		';
		} else if ($__vars['definition']['match_type'] == 'color') {
			$__finalCompiled .= '
			' . $__templater->callMacro(null, 'custom_field_value_color', array(
				'color' => $__vars['value'],
			), $__vars) . '
		';
		} else {
			$__finalCompiled .= '
			' . $__templater->filter($__templater->method($__vars['definition'], 'getFormattedValue', array($__vars['value'], )), array(array('raw', array()),), true) . '
		';
		}
		$__finalCompiled .= '
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'custom_field_value_date' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'date' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	' . $__templater->func('date', array($__templater->func('date_from_format', array('Y-m-d', $__vars['date'], ), false), ), true) . '
';
	return $__finalCompiled;
},
'custom_field_value_color' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'color' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	<span class="colorChip" data-xf-init="tooltip" title="' . $__templater->escape($__vars['color']) . '">
		<span class="colorChip-inner" style="background-color: ' . $__templater->escape($__vars['color']) . '">
			<span class="colorChip-value">' . $__templater->escape($__vars['color']) . '</span>
		</span>
	</span>
';
	return $__finalCompiled;
},
'custom_fields_edit' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'type' => '!',
		'group' => null,
		'set' => '!',
		'editMode' => 'user',
		'onlyInclude' => null,
		'additionalFilters' => array(),
		'rowType' => '',
		'rowClass' => '',
		'namePrefix' => 'custom_fields',
		'requiredOnly' => false,
	), $__arguments, $__vars);
	$__finalCompiled .= '

	';
	$__compilerTemp1 = $__templater->method($__vars['xf']['app'], 'getCustomFieldsForEdit', array($__vars['type'], $__vars['set'], $__vars['editMode'], $__vars['group'], $__vars['onlyInclude'], $__vars['additionalFilters'], ));
	if ($__templater->isTraversable($__compilerTemp1)) {
		foreach ($__compilerTemp1 AS $__vars['fieldId'] => $__vars['fieldDefinition']) {
			$__finalCompiled .= '
		';
			if ((!$__vars['requiredOnly']) OR ($__vars['requiredOnly'] AND $__vars['fieldDefinition']['required'])) {
				$__finalCompiled .= '
			' . $__templater->formRow('

				' . $__templater->callMacro(null, 'custom_fields_edit_' . $__vars['fieldDefinition']['field_type'], array(
					'set' => $__vars['set'],
					'definition' => $__vars['fieldDefinition'],
					'editMode' => $__vars['editMode'],
					'namePrefix' => $__vars['namePrefix'],
				), $__vars) . '
			', array(
					'label' => $__templater->escape($__vars['fieldDefinition']['title']),
					'explain' => $__templater->escape($__vars['fieldDefinition']['description']),
					'hint' => ($__templater->method($__vars['fieldDefinition'], 'isRequired', array($__vars['editMode'], )) ? 'Required' : ''),
					'rowtype' => $__vars['rowType'] . ' customField ' . ($__templater->func('in_array', array($__vars['fieldDefinition']['field_type'], array('textbox', 'textarea', 'bbcode', 'select', ), ), false) ? 'input' : ''),
					'rowclass' => $__vars['rowClass'],
					'data-field' => $__vars['fieldDefinition']['field_id'],
				)) . '
		';
			}
			$__finalCompiled .= '
	';
		}
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'custom_fields_edit_groups' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'type' => '!',
		'groups' => '!',
		'set' => '!',
		'editMode' => 'user',
		'onlyInclude' => null,
		'additionalFilters' => array(),
		'rowType' => '',
		'namePrefix' => 'custom_fields',
		'requiredOnly' => '',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	';
	if ($__templater->isTraversable($__vars['groups'])) {
		foreach ($__vars['groups'] AS $__vars['group']) {
			$__finalCompiled .= '
		' . $__templater->callMacro(null, 'custom_fields_edit', array(
				'type' => $__vars['type'],
				'group' => $__vars['group'],
				'set' => $__vars['set'],
				'editMode' => $__vars['editMode'],
				'onlyInclude' => $__vars['onlyInclude'],
				'additionalFilters' => $__vars['additionalFilters'],
				'rowType' => $__vars['rowType'],
				'namePrefix' => $__vars['namePrefix'],
				'requiredOnly' => $__vars['requiredOnly'],
			), $__vars) . '
	';
		}
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'custom_fields_edit_textbox' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'set' => '!',
		'definition' => '!',
		'editMode' => '!',
		'namePrefix' => 'custom_fields',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	';
	if ($__vars['definition']['match_type'] == 'date') {
		$__finalCompiled .= '

		' . $__templater->formDateInput(array(
			'name' => $__vars['namePrefix'] . '[' . $__vars['definition']['field_id'] . ']',
			'value' => $__vars['set'][$__vars['definition']['field_id']],
			'data-field' => $__vars['definition']['field_id'],
			'maxlength' => ($__vars['definition']['max_length'] ? $__vars['definition']['max_length'] : ''),
			'required' => ($__templater->method($__vars['definition'], 'isRequired', array($__vars['editMode'], )) ? 'required' : ''),
			'class' => 'field_' . $__vars['definition']['field_id'],
		)) . '

	';
	} else if ($__vars['definition']['match_type'] == 'color') {
		$__finalCompiled .= '

		' . $__templater->callMacro('color_picker_macros', 'color_picker', array(
			'name' => $__vars['namePrefix'] . '[' . $__vars['definition']['field_id'] . ']',
			'value' => $__vars['set'][$__vars['definition']['field_id']],
			'mapName' => '',
			'row' => false,
			'required' => $__templater->method($__vars['definition'], 'isRequired', array($__vars['editMode'], )),
		), $__vars) . '

	';
	} else if ($__vars['definition']['match_type'] == 'number') {
		$__finalCompiled .= '

		';
		$__vars['step'] = '1';
		$__finalCompiled .= '

		';
		if ($__vars['definition']['match_params']['number_integer']) {
			$__finalCompiled .= '
			';
			if ($__vars['definition']['match_params']['number_min'] >= 0) {
				$__finalCompiled .= '
				';
				$__vars['pattern'] = '\\d*';
				$__finalCompiled .= '
			';
			}
			$__finalCompiled .= '
		';
		} else {
			$__finalCompiled .= '
			';
			$__vars['step'] = 'any';
			$__finalCompiled .= '
		';
		}
		$__finalCompiled .= '
		';
		if ($__vars['definition']['match_params']['number_min'] !== '') {
			$__finalCompiled .= '
			';
			$__vars['min'] = $__vars['definition']['match_params']['number_min'];
			$__finalCompiled .= '
		';
		}
		$__finalCompiled .= '
		';
		if ($__vars['definition']['match_params']['number_max'] !== '') {
			$__finalCompiled .= '
			';
			$__vars['max'] = $__vars['definition']['match_params']['number_max'];
			$__finalCompiled .= '
		';
		}
		$__finalCompiled .= '

		' . $__templater->formNumberBox(array(
			'name' => $__vars['namePrefix'] . '[' . $__vars['definition']['field_id'] . ']',
			'value' => $__vars['set'][$__vars['definition']['field_id']],
			'maxlength' => ($__vars['definition']['max_length'] ? $__vars['definition']['max_length'] : ''),
			'pattern' => $__vars['pattern'],
			'default' => '',
			'min' => $__vars['min'],
			'max' => $__vars['max'],
			'step' => $__vars['step'],
			'required' => ($__templater->method($__vars['definition'], 'isRequired', array($__vars['editMode'], )) ? 'required' : ''),
			'class' => 'field_' . $__vars['definition']['field_id'],
		)) . '

	';
	} else {
		$__finalCompiled .= '

		';
		if ($__templater->func('in_array', array($__vars['definition']['match_type'], array('regex', 'alphanumeric', ), ), false)) {
			$__finalCompiled .= '

			';
			$__vars['type'] = 'text';
			$__finalCompiled .= '
			';
			$__vars['pattern'] = (($__vars['definition']['match_type'] == 'regex') ? $__vars['definition']['match_params']['regex'] : '\\w+');
			$__finalCompiled .= '
			';
			$__vars['title'] = $__templater->preEscaped('Please enter a value that matches the required format.');
			$__finalCompiled .= '

		';
		} else if ($__templater->func('in_array', array($__vars['definition']['match_type'], array('date', 'email', 'url', 'color', ), ), false)) {
			$__finalCompiled .= '

			';
			$__vars['type'] = $__vars['definition']['match_type'];
			$__finalCompiled .= '

		';
		} else {
			$__finalCompiled .= '

			';
			$__vars['type'] = 'text';
			$__finalCompiled .= '

		';
		}
		$__finalCompiled .= '

		' . $__templater->formTextBox(array(
			'name' => $__vars['namePrefix'] . '[' . $__vars['definition']['field_id'] . ']',
			'value' => $__vars['set'][$__vars['definition']['field_id']],
			'type' => $__vars['type'],
			'maxlength' => ($__vars['definition']['max_length'] ? $__vars['definition']['max_length'] : ''),
			'pattern' => $__vars['pattern'],
			'title' => $__vars['title'],
			'min' => $__vars['min'],
			'max' => $__vars['max'],
			'step' => $__vars['step'],
			'required' => ($__templater->method($__vars['definition'], 'isRequired', array($__vars['editMode'], )) ? 'required' : ''),
			'class' => 'field_' . $__vars['definition']['field_id'],
		)) . '
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'custom_fields_edit_textarea' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'set' => '!',
		'definition' => '!',
		'editMode' => '!',
		'namePrefix' => 'custom_fields',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	' . $__templater->formTextArea(array(
		'name' => $__vars['namePrefix'] . '[' . $__vars['definition']['field_id'] . ']',
		'value' => $__vars['set'][$__vars['definition']['field_id']],
		'maxlength' => ($__vars['definition']['max_length'] ? $__vars['definition']['max_length'] : ''),
		'autosize' => 'true',
		'required' => ($__templater->method($__vars['definition'], 'isRequired', array($__vars['editMode'], )) ? 'required' : ''),
		'class' => 'field_' . $__vars['definition']['field_id'],
	)) . '
';
	return $__finalCompiled;
},
'custom_fields_edit_bbcode' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'set' => '!',
		'definition' => '!',
		'editMode' => '!',
		'namePrefix' => 'custom_fields',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	' . $__templater->formEditor(array(
		'name' => $__vars['namePrefix'] . '[' . $__vars['definition']['field_id'] . ']',
		'value' => $__vars['set'][$__vars['definition']['field_id']],
		'previewable' => '0',
		'data-min-height' => '80',
		'class' => 'field_' . $__vars['definition']['field_id'],
	)) . '
';
	return $__finalCompiled;
},
'custom_fields_edit_select' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'set' => '!',
		'definition' => '!',
		'editMode' => '!',
		'multi' => '',
		'namePrefix' => 'custom_fields',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	';
	$__compilerTemp1 = array(array(
		'value' => '',
		'_type' => 'option',
	));
	$__compilerTemp1 = $__templater->mergeChoiceOptions($__compilerTemp1, $__vars['definition']['field_choices']);
	$__finalCompiled .= $__templater->formSelect(array(
		'name' => $__vars['namePrefix'] . '[' . $__vars['definition']['field_id'] . ']',
		'value' => (($__vars['set'][$__vars['definition']['field_id']] === null) ? '' : $__vars['set'][$__vars['definition']['field_id']]),
		'multiple' => $__vars['multi'],
		'class' => 'field_' . $__vars['definition']['field_id'],
	), $__compilerTemp1) . '
';
	return $__finalCompiled;
},
'custom_fields_edit_radio' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'set' => '!',
		'definition' => '!',
		'editMode' => '!',
		'namePrefix' => 'custom_fields',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	';
	$__compilerTemp1 = array();
	if (!$__templater->method($__vars['definition'], 'isRequired', array($__vars['editMode'], ))) {
		$__compilerTemp1[] = array(
			'value' => '',
			'label' => 'No selection',
			'_type' => 'option',
		);
	}
	$__compilerTemp1 = $__templater->mergeChoiceOptions($__compilerTemp1, $__vars['definition']['field_choices']);
	$__finalCompiled .= $__templater->formRadio(array(
		'name' => $__vars['namePrefix'] . '[' . $__vars['definition']['field_id'] . ']',
		'value' => (($__vars['set'][$__vars['definition']['field_id']] === null) ? '' : $__vars['set'][$__vars['definition']['field_id']]),
		'required' => ($__templater->method($__vars['definition'], 'isRequired', array($__vars['editMode'], )) ? 'required' : ''),
		'class' => 'field_' . $__vars['definition']['field_id'],
		'listclass' => 'listColumns',
	), $__compilerTemp1) . '
';
	return $__finalCompiled;
},
'custom_fields_edit_checkbox' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'set' => '!',
		'definition' => '!',
		'editMode' => '!',
		'namePrefix' => 'custom_fields',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	';
	$__compilerTemp1 = $__templater->mergeChoiceOptions(array(), $__vars['definition']['field_choices']);
	$__finalCompiled .= $__templater->formCheckBox(array(
		'name' => $__vars['namePrefix'] . '[' . $__vars['definition']['field_id'] . ']',
		'value' => $__vars['set'][$__vars['definition']['field_id']],
		'required' => ($__templater->method($__vars['definition'], 'isRequired', array($__vars['editMode'], )) ? 'required' : ''),
		'listclass' => 'field_' . $__vars['definition']['field_id'] . ' listColumns',
	), $__compilerTemp1) . '
';
	return $__finalCompiled;
},
'custom_fields_edit_multiselect' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'set' => '!',
		'definition' => '!',
		'editMode' => '!',
		'namePrefix' => 'custom_fields',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	' . $__templater->callMacro(null, 'custom_fields_edit_select', array(
		'set' => $__vars['set'],
		'definition' => $__vars['definition'],
		'editMode' => '!',
		'multi' => '1',
		'namePrefix' => $__vars['namePrefix'],
	), $__vars) . '
';
	return $__finalCompiled;
},
'custom_fields_edit_stars' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'set' => '!',
		'definition' => '!',
		'editMode' => '!',
		'namePrefix' => 'custom_fields',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	' . $__templater->callMacro('rating_macros', 'rating', array(
		'name' => $__vars['namePrefix'] . '[' . $__vars['definition']['field_id'] . ']',
		'currentRating' => $__vars['set'][$__vars['definition']['field_id']],
		'deselectable' => ($__templater->method($__vars['definition'], 'isRequired', array($__vars['editMode'], )) ? 'false' : 'true'),
		'row' => false,
	), $__vars) . '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '

' . '

' . '

' . '

' . '

' . '

' . '

' . '

' . '

' . '

' . '

' . '

' . '

' . '

';
	return $__finalCompiled;
});