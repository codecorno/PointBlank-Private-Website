<?php
// FROM HASH: d18f2262b8a173244232ed00935f698a
return array('macros' => array('step_users_config' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'config' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'step_config[users][merge_email]',
		'selected' => $__vars['config']['merge_email'],
		'label' => 'Automatically merge users with the same email',
		'_type' => 'option',
	),
	array(
		'name' => 'step_config[users][merge_name]',
		'selected' => $__vars['config']['merge_name'],
		'label' => 'Automatically merge users with the same user name',
		'hint' => 'Note that names which differ only by accents may be considered to be identical.',
		'_type' => 'option',
	)), array(
		'label' => 'Users',
	)) . '
';
	return $__finalCompiled;
},
'step_smilies_config' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'config' => '',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	' . $__templater->formTextBoxRow(array(
		'name' => 'step_config[smilies][filename]',
		'value' => $__vars['config']['filename'],
		'placeholder' => $__vars['config']['filename'],
	), array(
		'label' => 'Output XML file name',
		'explain' => 'Smilies from the source system are not imported directly into XenForo, but rather all the available information is processed and placed into an XML file, which you can import using the <a href="' . $__templater->func('link', array('smilies/import', ), true) . '" target="_blank">Import smilies</a> system later.<br />
<br />
The XML file will be placed into your \'internal-data\' folder, using the file name you specify here.',
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