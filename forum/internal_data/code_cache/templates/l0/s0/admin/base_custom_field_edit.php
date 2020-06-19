<?php
// FROM HASH: 896539064e9d7457f71690f1f2541897
return array('macros' => array('number_dependent' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'field' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	<div class="inputGroup inputGroup--numbers">
		' . $__templater->formNumberBox(array(
		'name' => 'match_params[number_min]',
		'value' => $__vars['field']['match_params']['number_min'],
		'size' => '5',
		'class' => 'input--numberNarrow',
		'placeholder' => 'Minimum',
	)) . '
		&nbsp;
		' . $__templater->formNumberBox(array(
		'name' => 'match_params[number_max]',
		'value' => $__vars['field']['match_params']['number_max'],
		'size' => '5',
		'class' => 'input--numberNarrow',
		'placeholder' => 'Maximum',
	)) . '
	</div>
	' . $__templater->formCheckBox(array(
	), array(array(
		'name' => 'match_params[number_integer]',
		'selected' => $__vars['field']['match_params']['number_integer'],
		'label' => 'Require whole number (no decimal point)',
		'_type' => 'option',
	))) . '
';
	return $__finalCompiled;
},
'regex_dependent' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'field' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	' . $__templater->formTextBox(array(
		'name' => 'match_params[regex]',
		'value' => $__vars['field']['match_params']['regex'],
		'placeholder' => 'Regular expression',
		'code' => 'true',
	)) . '
';
	return $__finalCompiled;
},
'validator_dependent' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'field' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	' . $__templater->formTextBox(array(
		'name' => 'match_params[validator]',
		'value' => $__vars['field']['match_params']['validator'],
		'placeholder' => 'Validator',
		'dir' => 'ltr',
	)) . '
';
	return $__finalCompiled;
},
'date_dependent' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'field' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	' . $__templater->formRadio(array(
		'name' => 'match_params[date_constraint]',
		'value' => ($__vars['field']['match_params']['date_constraint'] ?: ''),
	), array(array(
		'value' => 'past',
		'label' => 'Date in the past',
		'_type' => 'option',
	),
	array(
		'value' => 'future',
		'label' => 'Date in the future',
		'_type' => 'option',
	),
	array(
		'value' => '',
		'label' => 'Any',
		'_type' => 'option',
	))) . '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['field'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add field');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit field' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->filter($__vars['field']['title'], array(array('htmlspecialchars', array()),), true));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['field'], 'isUpdate', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array($__vars['prefix'] . '/delete', $__vars['field'], ), false),
			'overlay' => 'true',
			'icon' => 'delete',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

';
	$__templater->includeJs(array(
		'src' => 'xf/sort.js, vendor/dragula/dragula.js',
	));
	$__finalCompiled .= '
';
	$__templater->includeCss('public:dragula.less');
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__templater->method($__vars['field'], 'exists', array())) {
		$__compilerTemp1 .= '
				' . $__templater->formRow('
					<span class="u-ltr">' . $__templater->escape($__vars['field']['field_id']) . '</span>
				', array(
			'label' => 'Field ID',
			'explain' => 'This is the unique identifier for this field. It cannot be changed once set.',
		)) . '
			';
	} else {
		$__compilerTemp1 .= '
				' . $__templater->formTextBoxRow(array(
			'name' => 'field_id',
			'id' => 'ctrl_field_id',
			'maxlength' => $__templater->func('max_length', array($__vars['field'], 'field_id', ), false),
			'dir' => 'ltr',
		), array(
			'label' => 'Field ID',
			'explain' => 'This is the unique identifier for this field. It cannot be changed once set.',
		)) . '
			';
	}
	$__compilerTemp2 = $__templater->mergeChoiceOptions(array(), $__vars['displayGroups']);
	$__compilerTemp3 = array();
	if ($__templater->isTraversable($__vars['fieldTypes'])) {
		foreach ($__vars['fieldTypes'] AS $__vars['fieldType'] => $__vars['fieldDef']) {
			if ((!$__vars['existingType']) OR ($__vars['fieldDef']['compatible'] == $__vars['existingType']['compatible'])) {
				$__compilerTemp3[] = array(
					'class' => $__vars['fieldDef']['type'],
					'value' => $__vars['fieldType'],
					'label' => $__templater->escape($__vars['fieldDef']['label']),
					'_type' => 'option',
				);
			}
		}
	}
	$__compilerTemp4 = '';
	if ((!$__vars['existingType']) OR ($__vars['existingType']['options'] == 'text')) {
		$__compilerTemp4 .= '
			<h3 class="block-formSectionHeader">
				<span class="collapseTrigger collapseTrigger--block" data-xf-click="toggle" data-target="< :up:next">
					<span class="block-formSectionHeader-aligner">' . 'Options for text fields' . '</span>
				</span>
			</h3>
			<div class="block-body block-body--collapsible">
				' . $__templater->formRadioRow(array(
			'name' => 'match_type',
			'value' => $__vars['field']['match_type'],
			'listclass' => '_listColumns',
		), array(array(
			'value' => 'none',
			'label' => $__templater->escape($__vars['matchTypes']['none']),
			'_type' => 'option',
		),
		array(
			'value' => 'number',
			'data-hide' => 'true',
			'label' => $__templater->escape($__vars['matchTypes']['number']),
			'_dependent' => array($__templater->callMacro(null, 'number_dependent', array(
			'field' => $__vars['field'],
		), $__vars)),
			'_type' => 'option',
		),
		array(
			'value' => 'alphanumeric',
			'label' => $__templater->escape($__vars['matchTypes']['alphanumeric']),
			'_type' => 'option',
		),
		array(
			'value' => 'email',
			'label' => $__templater->escape($__vars['matchTypes']['email']),
			'_type' => 'option',
		),
		array(
			'value' => 'url',
			'label' => $__templater->escape($__vars['matchTypes']['url']),
			'_type' => 'option',
		),
		array(
			'value' => 'color',
			'label' => $__templater->escape($__vars['matchTypes']['color']),
			'_type' => 'option',
		),
		array(
			'value' => 'date',
			'data-hide' => 'true',
			'label' => $__templater->escape($__vars['matchTypes']['date']),
			'_dependent' => array($__templater->callMacro(null, 'date_dependent', array(
			'field' => $__vars['field'],
		), $__vars)),
			'_type' => 'option',
		),
		array(
			'value' => 'regex',
			'data-hide' => 'true',
			'label' => $__templater->escape($__vars['matchTypes']['regex']),
			'_dependent' => array($__templater->callMacro(null, 'regex_dependent', array(
			'field' => $__vars['field'],
		), $__vars)),
			'_type' => 'option',
		),
		array(
			'value' => 'callback',
			'data-hide' => 'true',
			'label' => $__templater->escape($__vars['matchTypes']['callback']),
			'_dependent' => array('
							' . $__templater->callMacro('helper_callback_fields', 'callback_fields', array(
			'className' => 'match_params[callback_class]',
			'methodName' => 'match_params[callback_method]',
			'classValue' => $__vars['field']['match_params']['callback_class'],
			'methodValue' => $__vars['field']['match_params']['callback_method'],
			'size' => '23',
		), $__vars) . '
							<p class="formRow-explain">
								<code>
									<em>\\XF\\CustomField\\Definition</em> $definition,
									&amp;$value,
									&amp;$error
								</code>
							</p>
						'),
			'_type' => 'option',
		),
		array(
			'value' => 'validator',
			'data-hide' => 'true',
			'label' => $__templater->escape($__vars['matchTypes']['validator']),
			'_dependent' => array($__templater->callMacro(null, 'validator_dependent', array(
			'field' => $__vars['field'],
		), $__vars)),
			'_type' => 'option',
		)), array(
			'label' => 'Value match requirements',
			'hint' => 'Empty values are always allowed.',
		)) . '

				<hr class="formRowSep" />

				' . $__templater->formNumberBoxRow(array(
			'name' => 'max_length',
			'value' => $__vars['field']['max_length'],
			'min' => '0',
		), array(
			'label' => 'Maximum length',
		)) . '
			</div>
		';
	}
	$__compilerTemp5 = '';
	if ((!$__vars['existingType']) OR ($__vars['existingType']['options'] == 'choice')) {
		$__compilerTemp5 .= '
			<h3 class="block-formSectionHeader">
				<span class="collapseTrigger collapseTrigger--block" data-xf-click="toggle" data-target="< :up:next">
					<span class="block-formSectionHeader-aligner">' . 'Options for choice fields' . '</span>
				</span>
			</h3>
			<div class="block-body block-body--collapsible">
				';
		$__compilerTemp6 = '';
		if ($__templater->isTraversable($__vars['field']['field_choices'])) {
			foreach ($__vars['field']['field_choices'] AS $__vars['choice'] => $__vars['text']) {
				$__compilerTemp6 .= '
							<div class="inputGroup">
								<span class="inputGroup-text dragHandle"
									aria-label="' . $__templater->filter('Drag handle', array(array('for_attr', array()),), true) . '"></span>
								' . $__templater->formTextBox(array(
					'name' => 'field_choice[]',
					'value' => $__vars['choice'],
					'placeholder' => 'Value (A-Z, 0-9, and _ only)',
					'size' => '24',
					'maxlength' => '25',
					'dir' => 'ltr',
				)) . '
								<span class="inputGroup-splitter"></span>
								' . $__templater->formTextBox(array(
					'name' => 'field_choice_text[]',
					'value' => $__vars['text'],
					'placeholder' => 'Text',
					'size' => '24',
				)) . '
							</div>
						';
			}
		}
		$__compilerTemp5 .= $__templater->formRow('

					<div class="inputGroup-container" data-xf-init="list-sorter" data-drag-handle=".dragHandle">
						' . $__compilerTemp6 . '
						<div class="inputGroup is-undraggable js-blockDragafter" data-xf-init="field-adder"
							data-remove-class="is-undraggable js-blockDragafter">
							<span class="inputGroup-text dragHandle"
								aria-label="' . $__templater->filter('Drag handle', array(array('for_attr', array()),), true) . '"></span>
							' . $__templater->formTextBox(array(
			'name' => 'field_choice[]',
			'placeholder' => 'Value (A-Z, 0-9, and _ only)',
			'size' => '24',
			'maxlength' => '25',
			'data-i' => '0',
			'dir' => 'ltr',
		)) . '
							<span class="inputGroup-splitter"></span>
							' . $__templater->formTextBox(array(
			'name' => 'field_choice_text[]',
			'placeholder' => 'Text',
			'size' => '24',
			'data-i' => '0',
		)) . '
						</div>
					</div>
				', array(
			'rowtype' => 'input',
			'label' => 'Possible choices',
			'explain' => 'The value represents the internal value for the choice. The text field is shown when the field is displayed. You should not change the value field if any users have selected that choice; if you do, users will lose their selection.',
		)) . '
			</div>
		';
	}
	$__compilerTemp7 = '';
	if (!$__templater->test($__vars['extraOptions'], 'empty', array())) {
		$__compilerTemp7 .= '
				' . $__templater->filter($__vars['extraOptions'], array(array('raw', array()),), true) . '

				<hr class="formRowSep" />
			';
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__compilerTemp1 . '

			<hr class="formRowSep" />

			' . $__templater->formTextBoxRow(array(
		'name' => 'title',
		'value' => ($__templater->method($__vars['field'], 'exists', array()) ? $__vars['field']['MasterTitle']['phrase_text'] : ''),
	), array(
		'label' => 'Title',
	)) . '

			' . $__templater->formTextAreaRow(array(
		'name' => 'description',
		'value' => ($__templater->method($__vars['field'], 'exists', array()) ? $__vars['field']['MasterDescription']['phrase_text'] : ''),
		'autosize' => 'true',
	), array(
		'label' => 'Description',
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formRadioRow(array(
		'name' => 'display_group',
		'value' => $__vars['field']['display_group'],
	), $__compilerTemp2, array(
		'label' => 'Display location',
	)) . '

			' . $__templater->callMacro('display_order_macros', 'row', array(
		'value' => $__vars['field']['display_order'],
	), $__vars) . '

			' . $__templater->filter($__vars['displayOptions'], array(array('raw', array()),), true) . '

			<hr class="formRowSep" />

			' . $__templater->formRadioRow(array(
		'name' => 'field_type',
		'value' => $__vars['field']['field_type'],
		'listclass' => 'listColumns',
	), $__compilerTemp3, array(
		'label' => 'Field type',
	)) . '
		</div>

		' . $__compilerTemp4 . '

		' . $__compilerTemp5 . '

		<h3 class="block-formSectionHeader">
			<span class="collapseTrigger collapseTrigger--block" data-xf-click="toggle" data-target="< :up:next">
				<span class="block-formSectionHeader-aligner">' . 'General options' . '</span>
			</span>
		</h3>
		<div class="block-body block-body--collapsible">
			' . $__compilerTemp7 . '

			' . $__templater->formCodeEditorRow(array(
		'name' => 'display_template',
		'value' => $__vars['field']['display_template'],
		'mode' => 'html',
		'data-line-wrapping' => 'true',
		'class' => 'codeEditor--autoSize',
	), array(
		'label' => 'Value display HTML',
		'explain' => 'If not empty, this allows you to format the value of this field using HTML, allowing you to do things like link or mark up the output. You may use these placeholders: <b>{$value}</b> - the field\'s display value; <b>{$valueUrl}</b> - the field\'s display value for use in a URL, <b>{$choice}</b> - the underlying value of the chosen option, and <b>{$fieldId}</b> - the ID of this field (<span id="FieldId">' . $__templater->escape($__vars['field']['field_id']) . '</span>).',
	)) . '
		</div>

		' . $__templater->formSubmitRow(array(
		'sticky' => 'true',
		'icon' => 'save',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array($__vars['prefix'] . '/save', $__vars['field'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	)) . '

' . '

' . '

' . '

';
	return $__finalCompiled;
});