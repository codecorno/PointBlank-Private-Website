<?php
// FROM HASH: d0d7510fb0e3c297b46b3192f7e6bc95
return array('macros' => array('currency_list' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'value' => '!',
		'includePopular' => true,
		'row' => false,
		'rowLabel' => '',
		'name' => 'cost_currency',
		'rowClass' => '',
		'class' => '',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	$__vars['currencyData'] = $__templater->method($__vars['xf']['app'], 'data', array('XF:Currency', ));
	$__finalCompiled .= '
	';
	$__vars['currencies'] = $__templater->method($__vars['currencyData'], 'getCurrencyOptions', array());
	$__finalCompiled .= '
	';
	if ($__vars['includePopular']) {
		$__finalCompiled .= '
		';
		$__vars['popularCurrencies'] = $__templater->method($__vars['currencyData'], 'getCurrencyOptions', array(true, ));
		$__finalCompiled .= '
	';
	}
	$__finalCompiled .= '

	';
	$__compilerTemp1 = array(array(
		'value' => '',
		'label' => '&nbsp;',
		'_type' => 'option',
	));
	if ($__vars['popularCurrencies']) {
		$__compilerTemp1[] = array(
			'label' => 'Popular currencies',
			'_type' => 'optgroup',
			'options' => array(),
		);
		end($__compilerTemp1); $__compilerTemp2 = key($__compilerTemp1);
		$__compilerTemp1[$__compilerTemp2]['options'] = $__templater->mergeChoiceOptions($__compilerTemp1[$__compilerTemp2]['options'], $__vars['popularCurrencies']);
		$__compilerTemp1[] = array(
			'label' => 'Other currencies',
			'_type' => 'optgroup',
			'options' => array(),
		);
		end($__compilerTemp1); $__compilerTemp3 = key($__compilerTemp1);
		$__compilerTemp1[$__compilerTemp3]['options'] = $__templater->mergeChoiceOptions($__compilerTemp1[$__compilerTemp3]['options'], $__vars['currencies']);
	} else {
		$__compilerTemp1 = $__templater->mergeChoiceOptions($__compilerTemp1, $__vars['currencies']);
	}
	$__vars['select'] = $__templater->preEscaped('
		' . $__templater->formSelect(array(
		'name' => $__vars['name'],
		'value' => $__vars['value'],
		'class' => $__vars['class'],
	), $__compilerTemp1) . '
	');
	$__finalCompiled .= '

	';
	if ($__vars['row']) {
		$__finalCompiled .= '
		' . $__templater->formRow('

			' . $__templater->filter($__vars['select'], array(array('raw', array()),), true) . '
		', array(
			'rowtype' => 'input',
			'class' => $__vars['rowClass'],
			'label' => $__templater->escape($__vars['rowLabel']),
		)) . '
	';
	} else {
		$__finalCompiled .= '
		' . $__templater->filter($__vars['select'], array(array('raw', array()),), true) . '
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