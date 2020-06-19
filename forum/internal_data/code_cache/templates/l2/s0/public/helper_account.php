<?php
// FROM HASH: 5c7c8d30b080774d577ba518fddcf6e1
return array('macros' => array('dob_privacy_row' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'hint' => '',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	' . $__templater->formCheckBoxRow(array(
	), array(array(
		'value' => '1',
		'name' => 'option[show_dob_date]',
		'checked' => $__vars['xf']['visitor']['Option']['show_dob_date'],
		'label' => 'Mostrar dia e mês de nascimento',
		'_dependent' => array($__templater->formCheckBox(array(
	), array(array(
		'value' => '1',
		'name' => 'option[show_dob_year]',
		'checked' => $__vars['xf']['visitor']['Option']['show_dob_year'],
		'label' => 'Mostrar ano de nascimento',
		'hint' => 'Isso permitirá que as pessoas vejam sua idade.',
		'_type' => 'option',
	)))),
		'_type' => 'option',
	)), array(
		'hint' => ($__vars['hint'] ? $__templater->escape($__vars['hint']) : ''),
	)) . '
';
	return $__finalCompiled;
},
'activity_privacy_row' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(), $__arguments, $__vars);
	$__finalCompiled .= '
	' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'user[visible]',
		'checked' => $__vars['xf']['visitor']['visible'],
		'label' => 'Mostre seu status online',
		'hint' => 'Isso permitirá que outras pessoas vejam quando você está online.',
		'_dependent' => array($__templater->formCheckBox(array(
	), array(array(
		'name' => 'user[activity_visible]',
		'checked' => $__vars['xf']['visitor']['activity_visible'],
		'label' => 'Mostrar a sua atividade atual',
		'hint' => 'Isso permitirá que outras pessoas vejam qual página você está visualizando atualmente.',
		'_type' => 'option',
	)))),
		'_type' => 'option',
	)), array(
		'label' => 'Privacy options',
	)) . '
';
	return $__finalCompiled;
},
'email_options_row' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'showExplain' => false,
		'showConversationOption' => false,
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	$__compilerTemp1 = array(array(
		'name' => 'option[receive_admin_email]',
		'checked' => $__vars['xf']['visitor']['Option']['receive_admin_email'],
		'label' => 'Receive news and update emails',
		'hint' => '',
		'_type' => 'option',
	));
	if ($__vars['showConversationOption']) {
		$__compilerTemp1[] = array(
			'name' => 'option[email_on_conversation]',
			'checked' => $__vars['xf']['visitor']['Option']['email_on_conversation'],
			'label' => 'Receber e-mail quando uma nova mensagem de conversa é recebida',
			'_type' => 'option',
		);
	}
	$__compilerTemp2 = '';
	if ($__vars['showExplain']) {
		$__compilerTemp2 .= 'You may find additional email options under <a href="' . $__templater->func('link', array('account/preferences', ), true) . '">Preferences</a>.';
	}
	$__finalCompiled .= $__templater->formCheckBoxRow(array(
	), $__compilerTemp1, array(
		'label' => 'Email options',
		'explain' => $__compilerTemp2,
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