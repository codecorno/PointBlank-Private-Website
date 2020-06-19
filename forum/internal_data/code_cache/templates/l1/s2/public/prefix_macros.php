<?php
// FROM HASH: 369e34cf5af952d9438a41906bba23f3
return array('macros' => array('select' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'prefixes' => '!',
		'type' => '!',
		'selected' => '',
		'name' => 'prefix_id',
		'noneLabel' => $__vars['xf']['language']['parenthesis_open'] . 'No prefix' . $__vars['xf']['language']['parenthesis_close'],
		'multiple' => false,
		'includeAny' => false,
		'class' => '',
		'href' => '',
		'listenTo' => '',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	';
	$__compilerTemp1 = array();
	if ($__vars['includeAny']) {
		$__compilerTemp1[] = array(
			'value' => '-1',
			'label' => $__vars['xf']['language']['parenthesis_open'] . 'Any' . $__vars['xf']['language']['parenthesis_close'],
			'_type' => 'option',
		);
	}
	$__compilerTemp1[] = array(
		'value' => '0',
		'label' => $__templater->escape($__vars['noneLabel']),
		'_type' => 'option',
	);
	$__compilerTemp2 = $__templater->func('array_keys', array($__vars['prefixes'], ), false);
	if ($__templater->isTraversable($__compilerTemp2)) {
		foreach ($__compilerTemp2 AS $__vars['groupId']) {
			if ($__vars['groupId'] > 0) {
				$__compilerTemp1[] = array(
					'label' => $__templater->func('prefix_group', array($__vars['type'], $__vars['groupId'], ), false),
					'_type' => 'optgroup',
					'options' => array(),
				);
				end($__compilerTemp1); $__compilerTemp3 = key($__compilerTemp1);
				if ($__templater->isTraversable($__vars['prefixes'][$__vars['groupId']])) {
					foreach ($__vars['prefixes'][$__vars['groupId']] AS $__vars['prefixId'] => $__vars['prefix']) {
						$__compilerTemp1[$__compilerTemp3]['options'][] = array(
							'value' => $__vars['prefixId'],
							'label' => $__templater->func('prefix_title', array($__vars['type'], $__vars['prefixId'], ), true),
							'data-prefix-class' => $__vars['prefix']['css_class'],
							'_type' => 'option',
						);
					}
				}
			} else {
				if ($__templater->isTraversable($__vars['prefixes'][$__vars['groupId']])) {
					foreach ($__vars['prefixes'][$__vars['groupId']] AS $__vars['prefixId'] => $__vars['prefix']) {
						$__compilerTemp1[] = array(
							'value' => $__vars['prefixId'],
							'label' => $__templater->func('prefix_title', array($__vars['type'], $__vars['prefixId'], ), true),
							'data-prefix-class' => $__vars['prefix']['css_class'],
							'_type' => 'option',
						);
					}
				}
			}
		}
	}
	$__finalCompiled .= $__templater->formSelect(array(
		'name' => $__vars['name'],
		'value' => $__vars['selected'],
		'multiple' => $__vars['multiple'],
		'class' => $__vars['class'],
		'data-xf-init' => (($__vars['href'] AND $__vars['listenTo']) ? 'prefix-loader' : ''),
		'data-href' => $__vars['href'],
		'data-listen-to' => $__vars['listenTo'],
		'title' => 'Prefix',
	), $__compilerTemp1) . '
';
	return $__finalCompiled;
},
'row' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'prefixes' => '!',
		'type' => '!',
		'label' => 'Prefix',
		'explain' => '',
		'selected' => '',
		'name' => 'prefix_id',
		'noneLabel' => $__vars['xf']['language']['parenthesis_open'] . 'No prefix' . $__vars['xf']['language']['parenthesis_close'],
		'multiple' => false,
		'includeAny' => false,
		'class' => '',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	';
	$__compilerTemp1 = '';
	if ($__vars['explain']) {
		$__compilerTemp1 .= '
			<div class="formRow-explain">' . $__templater->filter($__vars['explain'], array(array('raw', array()),), true) . '</div>
		';
	}
	$__finalCompiled .= $__templater->formRow('
		' . $__templater->callMacro(null, 'select', array(
		'prefixes' => $__vars['prefixes'],
		'type' => $__vars['type'],
		'selected' => $__vars['selected'],
		'name' => $__vars['name'],
		'noneLabel' => $__vars['noneLabel'],
		'multiple' => $__vars['multiple'],
		'includeAny' => $__vars['includeAny'],
		'class' => $__vars['class'],
	), $__vars) . '
		' . $__compilerTemp1 . '
	', array(
		'rowtype' => 'input',
		'label' => $__templater->escape($__vars['label']),
	)) . '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '

';
	return $__finalCompiled;
});