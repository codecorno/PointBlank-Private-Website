<?php
// FROM HASH: bc2db5fa7c867de534966fb69a743872
return array('macros' => array('db_host' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'value' => 'localhost',
		'placeholder' => null,
		'explain' => null,
	), $__arguments, $__vars);
	$__finalCompiled .= '

	' . $__templater->callMacro(null, 'db_textbox', array(
		'name' => 'host',
		'label' => 'MySQL server',
		'value' => $__vars['value'],
		'placeholder' => $__vars['placeholder'],
		'explain' => $__vars['explain'],
	), $__vars) . '
';
	return $__finalCompiled;
},
'db_dbname' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'value' => null,
		'placeholder' => null,
		'explain' => null,
	), $__arguments, $__vars);
	$__finalCompiled .= '

	' . $__templater->callMacro(null, 'db_textbox', array(
		'name' => 'dbname',
		'label' => 'MySQL database name',
		'value' => $__vars['value'],
		'placeholder' => $__vars['placeholder'],
		'explain' => $__vars['explain'],
	), $__vars) . '
';
	return $__finalCompiled;
},
'db_username' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'value' => null,
		'placeholder' => null,
		'explain' => null,
	), $__arguments, $__vars);
	$__finalCompiled .= '

	' . $__templater->callMacro(null, 'db_textbox', array(
		'name' => 'username',
		'label' => 'MySQL user name',
		'value' => $__vars['value'],
		'placeholder' => $__vars['placeholder'],
		'explain' => $__vars['explain'],
	), $__vars) . '
';
	return $__finalCompiled;
},
'db_password' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'value' => null,
		'placeholder' => null,
		'explain' => null,
	), $__arguments, $__vars);
	$__finalCompiled .= '

	' . $__templater->callMacro(null, 'db_textbox', array(
		'name' => 'password',
		'label' => 'MySQL password',
		'value' => $__vars['value'],
		'placeholder' => $__vars['placeholder'],
		'autocomplete' => 'off',
		'explain' => $__vars['explain'],
	), $__vars) . '
';
	return $__finalCompiled;
},
'db_port' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'value' => '3306',
		'placeholder' => null,
		'explain' => null,
	), $__arguments, $__vars);
	$__finalCompiled .= '

	' . $__templater->callMacro(null, 'db_textbox', array(
		'name' => 'port',
		'label' => 'MySQL port',
		'value' => $__vars['value'],
		'placeholder' => $__vars['placeholder'],
		'explain' => $__vars['explain'],
	), $__vars) . '
';
	return $__finalCompiled;
},
'db_tablePrefix' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'value' => null,
		'placeholder' => null,
		'explain' => null,
	), $__arguments, $__vars);
	$__finalCompiled .= '

	' . $__templater->callMacro(null, 'db_textbox', array(
		'name' => 'tablePrefix',
		'label' => 'MySQL table prefix',
		'value' => $__vars['value'],
		'placeholder' => $__vars['placeholder'],
		'explain' => $__vars['explain'],
	), $__vars) . '
';
	return $__finalCompiled;
},
'db_charset' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'value' => null,
		'placeholder' => null,
		'explain' => 'If you specify a character set in the config for the system you are importing, you should specify the same character set here.',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	' . $__templater->callMacro(null, 'db_textbox', array(
		'name' => 'charset',
		'label' => 'Force character set',
		'value' => $__vars['value'],
		'placeholder' => $__vars['placeholder'],
		'explain' => $__vars['explain'],
	), $__vars) . '
';
	return $__finalCompiled;
},
'db_textbox' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'name' => '!',
		'label' => '!',
		'value' => null,
		'placeholder' => null,
		'autocomplete' => null,
		'explain' => null,
	), $__arguments, $__vars);
	$__finalCompiled .= '

	' . $__templater->formTextBoxRow(array(
		'name' => 'config[db][' . $__vars['name'] . ']',
		'value' => $__vars['value'],
		'autocomplete' => $__vars['autocomplete'],
		'placeholder' => $__vars['placeholder'],
	), array(
		'label' => $__templater->escape($__vars['label']),
		'explain' => $__templater->escape($__vars['explain']),
	)) . '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '

' . '

' . '

' . '

' . '

' . '

' . '

';
	return $__finalCompiled;
});