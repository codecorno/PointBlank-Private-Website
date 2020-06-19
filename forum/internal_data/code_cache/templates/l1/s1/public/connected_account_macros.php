<?php
// FROM HASH: dbf4bfd62c43f391e52ef01854a3ca81
return array('macros' => array('associate' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'provider' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	' . $__templater->formRow('

		' . $__templater->callMacro('connected_account_macros', 'button', array(
		'provider' => $__vars['provider'],
		'text' => 'Associate with ' . $__vars['provider']['title'] . '',
	), $__vars) . '
	', array(
		'label' => $__templater->escape($__vars['provider']['title']),
		'explain' => $__templater->filter($__vars['provider']['description'], array(array('raw', array()),), true),
	)) . '
';
	return $__finalCompiled;
},
'disassociate' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'provider' => '!',
		'hasPassword' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	$__vars['explain'] = (((!$__vars['hasPassword']) AND ($__templater->func('count', array($__vars['xf']['visitor']['Profile']['connected_accounts'], ), false) == 1)) ? 'Disassociating with all external accounts will cause a password to be generated for your account and emailed to ' . $__vars['xf']['visitor']['email'] . '.' : '');
	$__finalCompiled .= '
	' . $__templater->formRow('

		<div>' . $__templater->filter($__templater->method($__vars['provider'], 'renderAssociated', array()), array(array('raw', array()),), true) . '</div>

		' . $__templater->form('
			' . $__templater->formCheckBox(array(
	), array(array(
		'name' => 'disassociate',
		'label' => 'Disassociate ' . $__templater->escape($__vars['provider']['title']) . ' account',
		'_dependent' => array('
						' . $__templater->button('Confirm disassociation', array(
		'type' => 'submit',
	), '', array(
	)) . '
					'),
		'_type' => 'option',
	))) . '
		', array(
		'action' => $__templater->func('link', array('account/connected-accounts/disassociate', $__vars['provider'], ), false),
		'class' => 'u-inputSpacer',
	)) . '
	', array(
		'label' => $__templater->escape($__vars['provider']['title']),
		'explain' => $__templater->escape($__vars['explain']),
	)) . '
';
	return $__finalCompiled;
},
'button' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'provider' => '!',
		'link' => null,
		'text' => null,
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	$__compilerTemp1 = '';
	if ($__vars['provider']['icon_url']) {
		$__compilerTemp1 .= '
			<img class="button-icon" alt="" src="' . $__templater->escape($__vars['provider']['icon_url']) . '" />
		';
	}
	$__finalCompiled .= $__templater->button('
		' . $__compilerTemp1 . '
		' . ($__templater->escape($__vars['text']) ?: $__templater->escape($__vars['provider']['title'])) . '
	', array(
		'href' => ($__vars['link'] ?: $__templater->func('link', array('register/connected-accounts', $__vars['provider'], array('setup' => true, ), ), false)),
		'class' => 'button--provider button--provider--' . $__vars['provider']['provider_id'],
	), '', array(
	)) . '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '

' . '

';
	return $__finalCompiled;
});