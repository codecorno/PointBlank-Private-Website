<?php
// FROM HASH: d1375bf7b7ad2344e0de6ad1eeff6b9e
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formTextBoxRow(array(
		'name' => 'options[client_id]',
		'value' => $__vars['options']['client_id'],
	), array(
		'label' => 'ID do Cliente',
		'hint' => 'Obrigatório',
		'explain' => 'O ID de cliente associado ao <a href="https://account.live.com/developers/applications/index" target="_blank">aplicativo Microsoft Live</a> para este domínio.',
	)) . '

' . $__templater->formTextBoxRow(array(
		'name' => 'options[client_secret]',
		'value' => $__vars['options']['client_secret'],
	), array(
		'label' => 'Client secret',
		'hint' => 'Obrigatório',
		'explain' => 'O client secret para o aplicativo Microsoft que você criou para esse domínio.',
	));
	return $__finalCompiled;
});