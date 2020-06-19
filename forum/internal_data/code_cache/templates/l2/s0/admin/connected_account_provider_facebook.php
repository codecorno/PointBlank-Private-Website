<?php
// FROM HASH: 2aed4e9192474946e8c188a6bc1b5ed7
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formTextBoxRow(array(
		'name' => 'options[app_id]',
		'value' => $__vars['options']['app_id'],
	), array(
		'label' => 'App ID',
		'hint' => 'Obrigatório',
		'explain' => 'O ID associado ao seu <a href="https://developers.facebook.com/apps" target="_blank">aplicativo Facebook</a> para este domínio.',
	)) . '

' . $__templater->formTextBoxRow(array(
		'name' => 'options[app_secret]',
		'value' => $__vars['options']['app_secret'],
	), array(
		'label' => 'App secret',
		'hint' => 'Obrigatório',
		'explain' => 'O secret para o aplicativo do Facebook que você criou para este domínio.',
	));
	return $__finalCompiled;
});