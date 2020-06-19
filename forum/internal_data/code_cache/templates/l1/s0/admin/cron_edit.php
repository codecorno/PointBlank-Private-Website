<?php
// FROM HASH: 3d66aa5fbff086405c97268d26a2e0ac
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['entry'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add cron entry');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit cron entry' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['entry']['title']));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['entry'], 'isUpdate', array()) AND $__templater->method($__vars['entry'], 'canEdit', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array('cron/delete', $__vars['entry'], ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

';
	if ((!$__templater->method($__vars['entry'], 'canEdit', array()))) {
		$__finalCompiled .= '
	<div class="blockMessage blockMessage--important blockMessage--iconic">
		' . 'Only a limited number of fields in this item may be edited.' . '
	</div>
';
	}
	$__finalCompiled .= '

' . $__templater->form('

	<div class="block-container">
		<div class="block-body">

			' . $__templater->formTextBoxRow(array(
		'name' => 'entry_id',
		'value' => $__vars['entry']['entry_id'],
		'maxlength' => $__templater->func('max_length', array($__vars['entry'], 'entry_id', ), false),
		'readonly' => (!$__templater->method($__vars['entry'], 'canEdit', array())),
		'dir' => 'ltr',
	), array(
		'label' => 'Cron entry ID',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'title',
		'value' => ($__templater->method($__vars['entry'], 'exists', array()) ? $__vars['entry']['MasterTitle']['phrase_text'] : ''),
		'readonly' => (!$__templater->method($__vars['entry'], 'canEdit', array())),
	), array(
		'label' => 'Title',
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formRow('

				' . $__templater->callMacro('helper_callback_fields', 'callback_fields', array(
		'data' => $__vars['entry'],
		'className' => 'cron_class',
		'methodName' => 'cron_method',
		'readOnly' => (!$__templater->method($__vars['entry'], 'canEdit', array())),
	), $__vars) . '
			', array(
		'rowtype' => 'input',
		'label' => 'Cron callback',
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formRadioRow(array(
		'name' => 'run_rules[day_type]',
		'value' => $__vars['entry']['run_rules']['day_type'],
		'readonly' => (!$__templater->method($__vars['entry'], 'canEdit', array())),
	), array(array(
		'value' => 'dom',
		'label' => 'Day of the month' . $__vars['xf']['language']['label_separator'],
		'_dependent' => array($__templater->formSelect(array(
		'name' => 'run_rules[dom]',
		'readonly' => (!$__templater->method($__vars['entry'], 'canEdit', array())),
		'value' => $__templater->filter($__vars['entry']['run_rules']['dom'], array(array('raw', array()),), false),
		'multiple' => 'true',
		'size' => '8',
		'style' => 'width: 200px',
	), array(array(
		'value' => '-1',
		'label' => 'Any',
		'_type' => 'option',
	),
	array(
		'value' => '1',
		'label' => '1',
		'_type' => 'option',
	),
	array(
		'value' => '2',
		'label' => '2',
		'_type' => 'option',
	),
	array(
		'value' => '3',
		'label' => '3',
		'_type' => 'option',
	),
	array(
		'value' => '4',
		'label' => '4',
		'_type' => 'option',
	),
	array(
		'value' => '5',
		'label' => '5',
		'_type' => 'option',
	),
	array(
		'value' => '6',
		'label' => '6',
		'_type' => 'option',
	),
	array(
		'value' => '7',
		'label' => '7',
		'_type' => 'option',
	),
	array(
		'value' => '8',
		'label' => '8',
		'_type' => 'option',
	),
	array(
		'value' => '9',
		'label' => '9',
		'_type' => 'option',
	),
	array(
		'value' => '10',
		'label' => '10',
		'_type' => 'option',
	),
	array(
		'value' => '11',
		'label' => '11',
		'_type' => 'option',
	),
	array(
		'value' => '12',
		'label' => '12',
		'_type' => 'option',
	),
	array(
		'value' => '13',
		'label' => '13',
		'_type' => 'option',
	),
	array(
		'value' => '14',
		'label' => '14',
		'_type' => 'option',
	),
	array(
		'value' => '15',
		'label' => '15',
		'_type' => 'option',
	),
	array(
		'value' => '16',
		'label' => '16',
		'_type' => 'option',
	),
	array(
		'value' => '17',
		'label' => '17',
		'_type' => 'option',
	),
	array(
		'value' => '18',
		'label' => '18',
		'_type' => 'option',
	),
	array(
		'value' => '19',
		'label' => '19',
		'_type' => 'option',
	),
	array(
		'value' => '20',
		'label' => '20',
		'_type' => 'option',
	),
	array(
		'value' => '21',
		'label' => '21',
		'_type' => 'option',
	),
	array(
		'value' => '22',
		'label' => '22',
		'_type' => 'option',
	),
	array(
		'value' => '23',
		'label' => '23',
		'_type' => 'option',
	),
	array(
		'value' => '24',
		'label' => '24',
		'_type' => 'option',
	),
	array(
		'value' => '25',
		'label' => '25',
		'_type' => 'option',
	),
	array(
		'value' => '26',
		'label' => '26',
		'_type' => 'option',
	),
	array(
		'value' => '27',
		'label' => '27',
		'_type' => 'option',
	),
	array(
		'value' => '28',
		'label' => '28',
		'_type' => 'option',
	),
	array(
		'value' => '29',
		'label' => '29',
		'_type' => 'option',
	),
	array(
		'value' => '30',
		'label' => '30',
		'_type' => 'option',
	),
	array(
		'value' => '31',
		'label' => '31',
		'_type' => 'option',
	)))),
		'_type' => 'option',
	),
	array(
		'value' => 'dow',
		'label' => 'Day of the week' . $__vars['xf']['language']['label_separator'],
		'_dependent' => array($__templater->formSelect(array(
		'name' => 'run_rules[dow]',
		'readonly' => (!$__templater->method($__vars['entry'], 'canEdit', array())),
		'value' => $__templater->filter($__vars['entry']['run_rules']['dow'], array(array('raw', array()),), false),
		'multiple' => 'true',
		'size' => '8',
		'style' => 'width: 200px',
	), array(array(
		'value' => '-1',
		'label' => 'Any',
		'_type' => 'option',
	),
	array(
		'value' => '0',
		'label' => 'Sunday',
		'_type' => 'option',
	),
	array(
		'value' => '1',
		'label' => 'Monday',
		'_type' => 'option',
	),
	array(
		'value' => '2',
		'label' => 'Tuesday',
		'_type' => 'option',
	),
	array(
		'value' => '3',
		'label' => 'Wednesday',
		'_type' => 'option',
	),
	array(
		'value' => '4',
		'label' => 'Thursday',
		'_type' => 'option',
	),
	array(
		'value' => '5',
		'label' => 'Friday',
		'_type' => 'option',
	),
	array(
		'value' => '6',
		'label' => 'Saturday',
		'_type' => 'option',
	)))),
		'_type' => 'option',
	)), array(
		'label' => 'Run on type of day',
	)) . '

			' . $__templater->formSelectRow(array(
		'name' => 'run_rules[hours]',
		'readonly' => (!$__templater->method($__vars['entry'], 'canEdit', array())),
		'value' => $__templater->filter($__vars['entry']['run_rules']['hours'], array(array('raw', array()),), false),
		'multiple' => 'true',
		'size' => '8',
		'style' => 'width: 200px',
	), array(array(
		'value' => '-1',
		'label' => 'Any',
		'_type' => 'option',
	),
	array(
		'value' => '0',
		'label' => '0 ' . $__vars['xf']['language']['parenthesis_open'] . 'Midnight' . $__vars['xf']['language']['parenthesis_close'],
		'_type' => 'option',
	),
	array(
		'value' => '1',
		'label' => '1',
		'_type' => 'option',
	),
	array(
		'value' => '2',
		'label' => '2',
		'_type' => 'option',
	),
	array(
		'value' => '3',
		'label' => '3',
		'_type' => 'option',
	),
	array(
		'value' => '4',
		'label' => '4',
		'_type' => 'option',
	),
	array(
		'value' => '5',
		'label' => '5',
		'_type' => 'option',
	),
	array(
		'value' => '6',
		'label' => '6',
		'_type' => 'option',
	),
	array(
		'value' => '7',
		'label' => '7',
		'_type' => 'option',
	),
	array(
		'value' => '8',
		'label' => '8',
		'_type' => 'option',
	),
	array(
		'value' => '9',
		'label' => '9',
		'_type' => 'option',
	),
	array(
		'value' => '10',
		'label' => '10',
		'_type' => 'option',
	),
	array(
		'value' => '11',
		'label' => '11',
		'_type' => 'option',
	),
	array(
		'value' => '12',
		'label' => '12 ' . $__vars['xf']['language']['parenthesis_open'] . 'Noon' . $__vars['xf']['language']['parenthesis_close'],
		'_type' => 'option',
	),
	array(
		'value' => '13',
		'label' => '13',
		'_type' => 'option',
	),
	array(
		'value' => '14',
		'label' => '14',
		'_type' => 'option',
	),
	array(
		'value' => '15',
		'label' => '15',
		'_type' => 'option',
	),
	array(
		'value' => '16',
		'label' => '16',
		'_type' => 'option',
	),
	array(
		'value' => '17',
		'label' => '17',
		'_type' => 'option',
	),
	array(
		'value' => '18',
		'label' => '18',
		'_type' => 'option',
	),
	array(
		'value' => '19',
		'label' => '19',
		'_type' => 'option',
	),
	array(
		'value' => '20',
		'label' => '20',
		'_type' => 'option',
	),
	array(
		'value' => '21',
		'label' => '21',
		'_type' => 'option',
	),
	array(
		'value' => '22',
		'label' => '22',
		'_type' => 'option',
	),
	array(
		'value' => '23',
		'label' => '23',
		'_type' => 'option',
	)), array(
		'label' => 'Run at hours',
		'explain' => 'Run times are based on the UTC time zone.',
	)) . '

			' . $__templater->formSelectRow(array(
		'name' => 'run_rules[minutes]',
		'readonly' => (!$__templater->method($__vars['entry'], 'canEdit', array())),
		'value' => $__templater->filter($__vars['entry']['run_rules']['minutes'], array(array('raw', array()),), false),
		'multiple' => 'true',
		'size' => '8',
		'style' => 'width: 200px',
	), array(array(
		'value' => '-1',
		'label' => 'Any',
		'_type' => 'option',
	),
	array(
		'value' => '0',
		'label' => '0',
		'_type' => 'option',
	),
	array(
		'value' => '1',
		'label' => '1',
		'_type' => 'option',
	),
	array(
		'value' => '2',
		'label' => '2',
		'_type' => 'option',
	),
	array(
		'value' => '3',
		'label' => '3',
		'_type' => 'option',
	),
	array(
		'value' => '4',
		'label' => '4',
		'_type' => 'option',
	),
	array(
		'value' => '5',
		'label' => '5',
		'_type' => 'option',
	),
	array(
		'value' => '6',
		'label' => '6',
		'_type' => 'option',
	),
	array(
		'value' => '7',
		'label' => '7',
		'_type' => 'option',
	),
	array(
		'value' => '8',
		'label' => '8',
		'_type' => 'option',
	),
	array(
		'value' => '9',
		'label' => '9',
		'_type' => 'option',
	),
	array(
		'value' => '10',
		'label' => '10',
		'_type' => 'option',
	),
	array(
		'value' => '11',
		'label' => '11',
		'_type' => 'option',
	),
	array(
		'value' => '12',
		'label' => '12',
		'_type' => 'option',
	),
	array(
		'value' => '13',
		'label' => '13',
		'_type' => 'option',
	),
	array(
		'value' => '14',
		'label' => '14',
		'_type' => 'option',
	),
	array(
		'value' => '15',
		'label' => '15',
		'_type' => 'option',
	),
	array(
		'value' => '16',
		'label' => '16',
		'_type' => 'option',
	),
	array(
		'value' => '17',
		'label' => '17',
		'_type' => 'option',
	),
	array(
		'value' => '18',
		'label' => '18',
		'_type' => 'option',
	),
	array(
		'value' => '19',
		'label' => '19',
		'_type' => 'option',
	),
	array(
		'value' => '20',
		'label' => '20',
		'_type' => 'option',
	),
	array(
		'value' => '21',
		'label' => '21',
		'_type' => 'option',
	),
	array(
		'value' => '22',
		'label' => '22',
		'_type' => 'option',
	),
	array(
		'value' => '23',
		'label' => '23',
		'_type' => 'option',
	),
	array(
		'value' => '24',
		'label' => '24',
		'_type' => 'option',
	),
	array(
		'value' => '25',
		'label' => '25',
		'_type' => 'option',
	),
	array(
		'value' => '26',
		'label' => '26',
		'_type' => 'option',
	),
	array(
		'value' => '27',
		'label' => '27',
		'_type' => 'option',
	),
	array(
		'value' => '28',
		'label' => '28',
		'_type' => 'option',
	),
	array(
		'value' => '29',
		'label' => '29',
		'_type' => 'option',
	),
	array(
		'value' => '30',
		'label' => '30',
		'_type' => 'option',
	),
	array(
		'value' => '31',
		'label' => '31',
		'_type' => 'option',
	),
	array(
		'value' => '32',
		'label' => '32',
		'_type' => 'option',
	),
	array(
		'value' => '33',
		'label' => '33',
		'_type' => 'option',
	),
	array(
		'value' => '34',
		'label' => '34',
		'_type' => 'option',
	),
	array(
		'value' => '35',
		'label' => '35',
		'_type' => 'option',
	),
	array(
		'value' => '36',
		'label' => '36',
		'_type' => 'option',
	),
	array(
		'value' => '37',
		'label' => '37',
		'_type' => 'option',
	),
	array(
		'value' => '38',
		'label' => '38',
		'_type' => 'option',
	),
	array(
		'value' => '39',
		'label' => '39',
		'_type' => 'option',
	),
	array(
		'value' => '40',
		'label' => '40',
		'_type' => 'option',
	),
	array(
		'value' => '41',
		'label' => '41',
		'_type' => 'option',
	),
	array(
		'value' => '42',
		'label' => '42',
		'_type' => 'option',
	),
	array(
		'value' => '43',
		'label' => '43',
		'_type' => 'option',
	),
	array(
		'value' => '44',
		'label' => '44',
		'_type' => 'option',
	),
	array(
		'value' => '45',
		'label' => '45',
		'_type' => 'option',
	),
	array(
		'value' => '46',
		'label' => '46',
		'_type' => 'option',
	),
	array(
		'value' => '47',
		'label' => '47',
		'_type' => 'option',
	),
	array(
		'value' => '48',
		'label' => '48',
		'_type' => 'option',
	),
	array(
		'value' => '49',
		'label' => '49',
		'_type' => 'option',
	),
	array(
		'value' => '50',
		'label' => '50',
		'_type' => 'option',
	),
	array(
		'value' => '51',
		'label' => '51',
		'_type' => 'option',
	),
	array(
		'value' => '52',
		'label' => '52',
		'_type' => 'option',
	),
	array(
		'value' => '53',
		'label' => '53',
		'_type' => 'option',
	),
	array(
		'value' => '54',
		'label' => '54',
		'_type' => 'option',
	),
	array(
		'value' => '55',
		'label' => '55',
		'_type' => 'option',
	),
	array(
		'value' => '56',
		'label' => '56',
		'_type' => 'option',
	),
	array(
		'value' => '57',
		'label' => '57',
		'_type' => 'option',
	),
	array(
		'value' => '58',
		'label' => '58',
		'_type' => 'option',
	),
	array(
		'value' => '59',
		'label' => '59',
		'_type' => 'option',
	)), array(
		'label' => 'Run at minutes',
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'active',
		'selected' => $__vars['entry']['active'],
		'hint' => (($__vars['xf']['development'] AND $__vars['entry']['addon_id']) ? 'The value of this field will not be changed when this add-on is upgraded.' : ''),
		'label' => '
					' . 'Allow cron entry to run automatically when scheduled' . '
				',
		'_type' => 'option',
	)), array(
	)) . '

			' . $__templater->callMacro('addon_macros', 'addon_edit', array(
		'addOnId' => $__vars['entry']['addon_id'],
	), $__vars) . '
		</div>

		' . $__templater->formSubmitRow(array(
		'sticky' => 'true',
		'icon' => 'save',
	), array(
	)) . '
	</div>

	' . $__templater->formHiddenVal('original_entry_id', $__vars['entry']['entry_id'], array(
	)) . '
', array(
		'action' => $__templater->func('link', array('cron/save', $__vars['entry'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});