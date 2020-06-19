<?php
// FROM HASH: cbdad8ff707e85e35d8a1ae199aa7253
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['language'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add language');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit language' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['language']['title']));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['language'], 'isUpdate', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array('languages/delete', $__vars['language'], ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

';
	$__compilerTemp1 = array(array(
		'value' => '0',
		'label' => $__vars['xf']['language']['parenthesis_open'] . 'No parent' . $__vars['xf']['language']['parenthesis_close'],
		'_type' => 'option',
	));
	$__compilerTemp2 = $__templater->method($__vars['languageTree'], 'getFlattened', array(1, ));
	if ($__templater->isTraversable($__compilerTemp2)) {
		foreach ($__compilerTemp2 AS $__vars['treeEntry']) {
			$__compilerTemp1[] = array(
				'value' => $__vars['treeEntry']['record']['language_id'],
				'label' => $__templater->func('repeat', array('--', $__vars['treeEntry']['depth'], ), true) . ' ' . $__templater->escape($__vars['treeEntry']['record']['title']),
				'_type' => 'option',
			);
		}
	}
	$__compilerTemp3 = array(array(
		'value' => '',
		'label' => '&nbsp;',
		'_type' => 'option',
	));
	$__compilerTemp3 = $__templater->mergeChoiceOptions($__compilerTemp3, $__vars['locales']);
	$__compilerTemp4 = $__templater->mergeChoiceOptions(array(), $__vars['dateFormats']);
	$__compilerTemp4[] = array(
		'value' => '',
		'selected' => !$__vars['dateFormats'][$__vars['language']['date_format']],
		'label' => 'Other',
		'_dependent' => array('
						' . $__templater->formTextBox(array(
		'name' => 'date_format_other',
		'value' => ($__vars['dateFormats'][$__vars['language']['date_format']] ? '' : $__vars['language']['date_format']),
		'size' => '15',
		'maxlength' => $__templater->func('max_length', array($__vars['language'], 'date_format', ), false),
		'dir' => 'ltr',
	)) . '
						<p class="formRow-explain">' . 'Use PHP <a href="https://php.net/manual/en/function.date.php" target="_blank">date format</a>' . '</p>
					'),
		'_type' => 'option',
	);
	$__compilerTemp5 = $__templater->mergeChoiceOptions(array(), $__vars['timeFormats']);
	$__compilerTemp5[] = array(
		'value' => '',
		'selected' => !$__vars['timeFormats'][$__vars['language']['time_format']],
		'label' => 'Other',
		'_dependent' => array('
						' . $__templater->formTextBox(array(
		'name' => 'time_format_other',
		'value' => ($__vars['timeFormats'][$__vars['language']['time_format']] ? '' : $__vars['language']['time_format']),
		'size' => '15',
		'maxlength' => $__templater->func('max_length', array($__vars['language'], 'time_format', ), false),
		'dir' => 'ltr',
	)) . '
						<p class="formRow-explain">' . 'Use PHP <a href="https://php.net/manual/en/function.date.php" target="_blank">time format</a>' . '</p>
					'),
		'_type' => 'option',
	);
	$__compilerTemp6 = $__templater->mergeChoiceOptions(array(), $__vars['currencyFormats']);
	$__compilerTemp6[] = array(
		'value' => '',
		'selected' => !$__vars['currencyFormats'][$__vars['language']['currency_format']],
		'label' => 'Other',
		'_dependent' => array('
						' . $__templater->formTextBox(array(
		'name' => 'currency_format_other',
		'value' => ($__vars['currencyFormats'][$__vars['language']['currency_format']] ? '' : $__vars['language']['currency_format']),
		'size' => '15',
		'maxlength' => $__templater->func('max_length', array($__vars['language'], 'currency_format', ), false),
	)) . '
						<p class="formRow-explain">' . 'Use PHP <a href="https://php.net/manual/en/function.money-format.php" target="_blank">money format</a>' . '</p>
					'),
		'_type' => 'option',
	);
	$__compilerTemp7 = '';
	if (!$__templater->test($__vars['quickPhrases'], 'empty', array())) {
		$__compilerTemp7 .= '
			<h3 class="block-formSectionHeader">
				<span class="collapseTrigger collapseTrigger--block" data-xf-click="toggle" data-target="< :up:next">
					<span class="block-formSectionHeader-aligner">' . 'Commonly edited phrases' . '</span>
				</span>
			</h3>
			<div class="block-body block-body--collapsible">
				' . $__templater->formTextAreaRow(array(
			'name' => 'quick_phrases[privacy_policy_text]',
			'value' => $__vars['quickPhrases']['privacy_policy_text']['phrase_text'],
			'autosize' => 'true',
			'class' => 'input--fitHeight--short',
		), array(
			'label' => 'Privacy policy',
			'hint' => 'You may use HTML',
		)) . '

				' . $__templater->formTextAreaRow(array(
			'name' => 'quick_phrases[terms_rules_text]',
			'value' => $__vars['quickPhrases']['terms_rules_text']['phrase_text'],
			'autosize' => 'true',
			'class' => 'input--fitHeight--short',
		), array(
			'label' => 'Terms and rules',
			'hint' => 'You may use HTML',
		)) . '

				' . $__templater->formTextAreaRow(array(
			'name' => 'quick_phrases[extra_copyright]',
			'value' => $__vars['quickPhrases']['extra_copyright']['phrase_text'],
			'autosize' => 'true',
			'class' => 'input--fitHeight--short',
		), array(
			'label' => 'Extra footer copyright',
			'hint' => 'You may use HTML',
		)) . '

				' . $__templater->formTextAreaRow(array(
			'name' => 'quick_phrases[email_footer_html]',
			'value' => $__vars['quickPhrases']['email_footer_html']['phrase_text'],
			'autosize' => 'true',
			'class' => 'input--fitHeight--short',
		), array(
			'label' => 'Extra email footer (HTML)',
			'hint' => 'You may use HTML',
		)) . '

				' . $__templater->formTextAreaRow(array(
			'name' => 'quick_phrases[email_footer_text]',
			'value' => $__vars['quickPhrases']['email_footer_text']['phrase_text'],
			'autosize' => 'true',
			'class' => 'input--fitHeight--short',
		), array(
			'label' => 'Extra email footer (plain text)',
		)) . '
			</div>
		';
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formTextBoxRow(array(
		'name' => 'title',
		'value' => $__vars['language']['title'],
		'maxlength' => $__templater->func('max_length', array($__vars['language'], 'title', ), false),
	), array(
		'label' => 'Title',
	)) . '

			' . $__templater->formSelectRow(array(
		'name' => 'parent_id',
		'value' => $__vars['language']['parent_id'],
	), $__compilerTemp1, array(
		'label' => 'Parent language',
	)) . '

			' . $__templater->formSelectRow(array(
		'name' => 'language_code',
		'value' => $__vars['language']['language_code'],
	), $__compilerTemp3, array(
		'label' => 'Locale',
	)) . '

			' . $__templater->formRadioRow(array(
		'name' => 'text_direction',
		'value' => $__vars['language']['text_direction'],
	), array(array(
		'value' => 'LTR',
		'label' => 'Left-to-right',
		'_type' => 'option',
	),
	array(
		'value' => 'RTL',
		'label' => 'Right-to-left',
		'_type' => 'option',
	)), array(
		'label' => 'Text direction',
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formTextBoxRow(array(
		'name' => 'decimal_point',
		'value' => $__vars['language']['decimal_point'],
		'size' => '5',
		'class' => 'input--autoSize',
		'maxlength' => $__templater->func('max_length', array($__vars['language'], 'decimal_point', ), false),
	), array(
		'label' => 'Decimal point character',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'thousands_separator',
		'value' => $__vars['language']['thousands_separator'],
		'size' => '5',
		'class' => 'input--autoSize',
		'maxlength' => $__templater->func('max_length', array($__vars['language'], 'thousands_separator', ), false),
	), array(
		'label' => 'Thousands separator',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'label_separator',
		'value' => $__vars['language']['label_separator'],
		'size' => '5',
		'class' => 'input--autoSize',
		'maxlength' => $__templater->func('max_length', array($__vars['language'], 'label_separator', ), false),
	), array(
		'label' => 'Label separator',
		'explain' => 'This is used to separate labels from their associated values.',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'comma_separator',
		'value' => $__vars['language']['comma_separator'],
		'size' => '5',
		'class' => 'input--autoSize',
		'maxlength' => $__templater->func('max_length', array($__vars['language'], 'comma_separator', ), false),
	), array(
		'label' => 'Comma separator',
		'explain' => 'This is used as a separator in lists of values. Include a trailing space if needed.',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'ellipsis',
		'value' => $__vars['language']['ellipsis'],
		'size' => '5',
		'class' => 'input--autoSize',
		'maxlength' => $__templater->func('max_length', array($__vars['language'], 'ellipsis', ), false),
	), array(
		'label' => 'Ellipsis character',
	)) . '

			' . $__templater->formRow('

				<div class="inputGroup">
					' . $__templater->formTextBox(array(
		'name' => 'parenthesis_open',
		'value' => $__vars['language']['parenthesis_open'],
		'size' => '5',
		'class' => 'input--autoSize',
		'maxlength' => $__templater->func('max_length', array($__vars['language'], 'parenthesis_open', ), false),
	)) . '
					<span class="inputGroup-splitter"></span>
					' . $__templater->formTextBox(array(
		'name' => 'parenthesis_close',
		'value' => $__vars['language']['parenthesis_close'],
		'size' => '5',
		'class' => 'input--autoSize',
		'maxlength' => $__templater->func('max_length', array($__vars['language'], 'parenthesis_close', ), false),
	)) . '
				</div>
			', array(
		'label' => 'Parentheses characters',
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formSelectRow(array(
		'name' => 'week_start',
		'value' => $__vars['language']['week_start'],
	), array(array(
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
	)), array(
		'label' => 'Week start day',
	)) . '

			' . $__templater->formRadioRow(array(
		'name' => 'date_format',
		'value' => $__vars['language']['date_format'],
	), $__compilerTemp4, array(
		'label' => 'Date format',
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formRadioRow(array(
		'name' => 'time_format',
		'value' => $__vars['language']['time_format'],
	), $__compilerTemp5, array(
		'label' => 'Time format',
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formRadioRow(array(
		'name' => 'currency_format',
		'value' => $__vars['language']['currency_format'],
	), $__compilerTemp6, array(
		'label' => 'Currency format',
	)) . '

		</div>

		' . $__compilerTemp7 . '

		' . $__templater->formSubmitRow(array(
		'sticky' => 'true',
		'icon' => 'save',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('languages/save', $__vars['language'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});