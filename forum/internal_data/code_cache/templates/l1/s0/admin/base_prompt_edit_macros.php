<?php
// FROM HASH: 293fc833bace3a1f0656ffa3f7c0f1c3
return array('macros' => array('prompt_groups' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'prompt' => '!',
		'promptGroups' => '!',
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
		$__compilerTemp1 = $__templater->mergeChoiceOptions($__compilerTemp1, $__vars['promptGroups']);
		$__finalCompiled .= $__templater->formSelectRow(array(
			'name' => 'prompt_group_id',
			'value' => $__vars['prompt']['prompt_group_id'],
		), $__compilerTemp1, array(
			'label' => 'Prompt group',
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
		$__compilerTemp2 = $__templater->mergeChoiceOptions($__compilerTemp2, $__vars['promptGroups']);
		$__finalCompiled .= $__templater->formSelect(array(
			'name' => 'prompt_group_id',
			'value' => $__vars['prompt']['prompt_group_id'],
		), $__compilerTemp2) . '
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