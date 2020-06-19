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
		'label' => 'Janeiro',
		'_type' => 'option',
	),
	array(
		'value' => '2',
		'label' => 'Fevereiro',
		'_type' => 'option',
	),
	array(
		'value' => '3',
		'label' => 'Março',
		'_type' => 'option',
	),
	array(
		'value' => '4',
		'label' => 'Abril',
		'_type' => 'option',
	),
	array(
		'value' => '5',
		'label' => 'Maio',
		'_type' => 'option',
	),
	array(
		'value' => '6',
		'label' => 'Junho',
		'_type' => 'option',
	),
	array(
		'value' => '7',
		'label' => 'Julho',
		'_type' => 'option',
	),
	array(
		'value' => '8',
		'label' => 'Agosto',
		'_type' => 'option',
	),
	array(
		'value' => '9',
		'label' => 'Setembro',
		'_type' => 'option',
	),
	array(
		'value' => '10',
		'label' => 'Outubro',
		'_type' => 'option',
	),
	array(
		'value' => '11',
		'label' => 'Novembro',
		'_type' => 'option',
	),
	array(
		'value' => '12',
		'label' => 'Dezembro',
		'_type' => 'option',
	))) . '
			<span class="inputGroup-splitter"></span>
			' . $__templater->formTextBox(array(
		'name' => 'dob_day',
		'value' => ($__vars['dobData']['dob_day'] ?: ''),
		'pattern' => '\\d*',
		'size' => '4',
		'maxlength' => '2',
		'placeholder' => 'Dia',
	)) . '
			<span class="inputGroup-splitter"></span>
			' . $__templater->formTextBox(array(
		'name' => 'dob_year',
		'value' => ($__vars['dobData']['dob_year'] ?: ''),
		'pattern' => '\\d*',
		'size' => '6',
		'maxlength' => '4',
		'placeholder' => 'Ano',
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
			'label' => 'Data de nascimento',
			'hint' => ($__vars['required'] ? 'Obrigatório' : ''),
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