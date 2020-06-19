<?php
// FROM HASH: ae2dc489d90bee2149efe6d3732bd0ec
return array('macros' => array('display_style' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'prefix' => '!',
		'displayStyles' => '!',
		'withRow' => '1',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	$__compilerTemp1 = array();
	if ($__templater->isTraversable($__vars['displayStyles'])) {
		foreach ($__vars['displayStyles'] AS $__vars['class']) {
			$__compilerTemp1[] = array(
				'value' => $__vars['class'],
				'label' => '<span title="' . $__templater->escape($__vars['class']) . '" class="' . $__templater->escape($__vars['class']) . '" data-xf-init="tooltip" style="display: inline">' . 'Prefix' . '</span>',
				'_type' => 'option',
			);
		}
	}
	$__compilerTemp1[] = array(
		'value' => '',
		'selected' => $__vars['prefix']['css_class'] AND (!$__templater->func('in_array', array($__vars['prefix']['css_class'], $__vars['displayStyles'], ), false)),
		'label' => 'Other, using custom CSS class name',
		'_dependent' => array($__templater->formTextBox(array(
		'name' => 'css_class',
		'value' => (($__vars['prefix']['css_class'] AND (!$__templater->func('in_array', array($__vars['prefix']['css_class'], $__vars['displayStyles'], ), false))) ? $__vars['prefix']['css_class'] : ''),
		'maxlength' => $__templater->func('max_length', array($__vars['prefix'], 'css_class', ), false),
	))),
		'_type' => 'option',
	);
	$__vars['inner'] = $__templater->preEscaped('
		' . $__templater->formRadio(array(
		'name' => 'css_class',
		'value' => ($__vars['prefix']['css_class'] ? $__vars['prefix']['css_class'] : 'label label--hidden'),
		'listclass' => 'listColumns',
	), $__compilerTemp1) . '
	');
	$__finalCompiled .= '

	';
	if ($__vars['withRow']) {
		$__finalCompiled .= '
		' . $__templater->formRow('
			' . $__templater->filter($__vars['inner'], array(array('raw', array()),), true) . '
		', array(
			'label' => 'Display styling',
		)) . '
	';
	} else {
		$__finalCompiled .= '
		' . $__templater->filter($__vars['inner'], array(array('raw', array()),), true) . '
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'prefix_groups' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'prefix' => '!',
		'prefixGroups' => '!',
		'withRow' => '1',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	';
	if ($__vars['withRow']) {
		$__finalCompiled .= '
		';
		$__compilerTemp1 = array(array(
			'value' => '',
			'label' => $__vars['xf']['language']['parenthesis_open'] . 'None' . $__vars['xf']['language']['parenthesis_close'],
			'_type' => 'option',
		));
		$__compilerTemp1 = $__templater->mergeChoiceOptions($__compilerTemp1, $__vars['prefixGroups']);
		$__finalCompiled .= $__templater->formSelectRow(array(
			'name' => 'prefix_group_id',
			'value' => $__vars['prefix']['prefix_group_id'],
		), $__compilerTemp1, array(
			'label' => 'Prefix group',
		)) . '
	';
	} else {
		$__finalCompiled .= '
		';
		$__compilerTemp2 = array(array(
			'value' => '',
			'label' => $__vars['xf']['language']['parenthesis_open'] . 'None' . $__vars['xf']['language']['parenthesis_close'],
			'_type' => 'option',
		));
		$__compilerTemp2 = $__templater->mergeChoiceOptions($__compilerTemp2, $__vars['prefixGroups']);
		$__finalCompiled .= $__templater->formSelect(array(
			'name' => 'prefix_group_id',
			'value' => $__vars['prefix']['prefix_group_id'],
		), $__compilerTemp2) . '
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '

';
	return $__finalCompiled;
});