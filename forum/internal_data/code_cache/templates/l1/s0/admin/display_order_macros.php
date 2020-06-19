<?php
// FROM HASH: a2e981cd90be92305896a5dc06f02fe5
return array('macros' => array('row' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'name' => 'display_order',
		'value' => '',
		'step' => '1',
		'label' => 'Display order',
		'hint' => '',
		'readOnly' => '',
		'explain' => '',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	' . $__templater->formNumberBoxRow(array(
		'name' => $__vars['name'],
		'value' => $__vars['value'],
		'min' => '0',
		'step' => $__vars['step'],
		'readonly' => ($__vars['readOnly'] ? 'readonly' : ''),
	), array(
		'label' => $__templater->escape($__vars['label']),
		'hint' => $__templater->escape($__vars['hint']),
		'explain' => $__templater->escape($__vars['explain']),
	)) . '
';
	return $__finalCompiled;
},
'input' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'name' => 'display_order',
		'value' => '',
		'step' => '1',
		'readOnly' => '',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	' . $__templater->formNumberBox(array(
		'name' => $__vars['name'],
		'value' => $__vars['value'],
		'min' => '0',
		'step' => $__vars['step'],
		'readonly' => ($__vars['readOnly'] ? 'readonly' : ''),
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