<?php
// FROM HASH: f450071d34f4e9749622d127f92bf419
return array('macros' => array('dob_edit' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'dobData' => array(),
		'row' => true,
		'required' => false,
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	$__vars['inputGroup'] = $__templater->preEscaped('
		<div class="inputGroup inputGroup--auto">
			' . $__templater->formSelect(array(
		'name' => 'dob_month',
		'value' => ($__vars['dobData']['dob_month'] ?: 0),
	), array(array(
		'value' => '0',
		'_type' => 'option',
	),
	array(
		'value' => '1',
		'label' => 'January',
		'_type' => 'option',
	),
	array(
		'value' => '2',
		'label' => 'February',
		'_type' => 'option',
	),
	array(
		'value' => '3',
		'label' => 'March',
		'_type' => 'option',
	),
	array(
		'value' => '4',
		'label' => 'April',
		'_type' => 'option',
	),
	array(
		'value' => '5',
		'label' => 'May',
		'_type' => 'option',
	),
	array(
		'value' => '6',
		'label' => 'June',
		'_type' => 'option',
	),
	array(
		'value' => '7',
		'label' => 'July',
		'_type' => 'option',
	),
	array(
		'value' => '8',
		'label' => 'August',
		'_type' => 'option',
	),
	array(
		'value' => '9',
		'label' => 'September',
		'_type' => 'option',
	),
	array(
		'value' => '10',
		'label' => 'October',
		'_type' => 'option',
	),
	array(
		'value' => '11',
		'label' => 'November',
		'_type' => 'option',
	),
	array(
		'value' => '12',
		'label' => 'December',
		'_type' => 'option',
	))) . '
			<span class="inputGroup-splitter"></span>
			' . $__templater->formTextBox(array(
		'name' => 'dob_day',
		'value' => ($__vars['dobData']['dob_day'] ?: ''),
		'pattern' => '\\d*',
		'size' => '4',
		'maxlength' => '2',
		'placeholder' => 'Day',
	)) . '
			<span class="inputGroup-splitter"></span>
			' . $__templater->formTextBox(array(
		'name' => 'dob_year',
		'value' => ($__vars['dobData']['dob_year'] ?: ''),
		'pattern' => '\\d*',
		'size' => '6',
		'maxlength' => '4',
		'placeholder' => 'Year',
	)) . '
		</div>
	');
	$__finalCompiled .= '
	';
	if ($__vars['row']) {
		$__finalCompiled .= '
		' . $__templater->formRow('

			' . $__templater->filter($__vars['inputGroup'], array(array('raw', array()),), true) . '
		', array(
			'rowtype' => 'input',
			'label' => 'Date of birth',
			'hint' => ($__vars['required'] ? 'Required' : ''),
		)) . '
		';
	} else {
		$__finalCompiled .= '
		' . $__templater->filter($__vars['inputGroup'], array(array('raw', array()),), true) . '
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';

	return $__finalCompiled;
});