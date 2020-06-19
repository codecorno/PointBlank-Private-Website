<?php
// FROM HASH: 76947047f50d98dec1c7cfcff97c9f23
return array('macros' => array('key_type' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'apiKey' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	if ($__vars['apiKey']['key_type'] == 'super') {
		$__finalCompiled .= '
		' . 'Super user key' . '
	';
	} else if ($__vars['apiKey']['key_type'] == 'user') {
		$__finalCompiled .= '
		' . 'User key' . ':
		' . $__templater->func('username_link', array($__vars['apiKey']['User'], false, array(
			'href' => '',
			'defaultname' => 'Deleted member',
		))) . '
	';
	} else {
		$__finalCompiled .= '
		' . 'Guest key' . '
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'key_type_row' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'apiKey' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	' . $__templater->formRow('
		' . $__templater->callMacro(null, 'key_type', array(
		'apiKey' => $__vars['apiKey'],
	), $__vars) . '
	', array(
		'label' => 'Key type',
		'explain' => 'This cannot be changed after creation. Changes require a new API key to be generated.',
	)) . '
';
	return $__finalCompiled;
},
'copy_key_row' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'apiKey' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	' . $__templater->formRow('
		' . $__templater->callMacro(null, 'copy_key', array(
		'apiKey' => $__vars['apiKey'],
	), $__vars) . '

		<div class="formRow-explain">
			' . $__templater->callMacro(null, 'key_usage', array(
		'apiKey' => $__vars['apiKey'],
	), $__vars) . '
		</div>
	', array(
		'label' => 'API key',
		'rowtype' => 'button',
	)) . '
';
	return $__finalCompiled;
},
'copy_key' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'apiKey' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	<code class="js-copyTarget">' . $__templater->escape($__vars['apiKey']['api_key']) . '</code>
	' . $__templater->button('', array(
		'icon' => 'copy',
		'data-xf-init' => 'copy-to-clipboard',
		'data-copy-target' => '.js-copyTarget',
		'data-success' => 'API key copied to clipboard.',
		'class' => 'button--link',
	), '', array(
	)) . '
';
	return $__finalCompiled;
},
'key_usage' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'apiKey' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	' . 'This key should be provided to API requests via the <code>XF-Api-Key</code> header.' . '

	';
	if ($__vars['apiKey']['key_type'] == 'super') {
		$__finalCompiled .= '
		' . 'As this is a super user key, the user ID that the request will be made as should be included in the <code>XF-Api-User</code> header.' . '
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '

' . '

' . '

' . '

';
	return $__finalCompiled;
});