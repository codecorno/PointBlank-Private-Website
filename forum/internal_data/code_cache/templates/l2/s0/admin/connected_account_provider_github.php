<?php
// FROM HASH: 4aa95d07e02e9d59fdee7f6f59f48926
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formTextBoxRow(array(
		'name' => 'options[client_id]',
		'value' => $__vars['options']['client_id'],
	), array(
		'label' => 'ID do Cliente',
		'hint' => 'Obrigatório',
		'explain' => 'O ID de cliente associado ao seu <a href="https://github.com/settings/developers" target="_blank">aplicativo de desenvolvedor GitHub</a> para este domínio.',
	)) . '

' . $__templater->formTextBoxRow(array(
		'name' => 'options[client_secret]',
		'value' => $__vars['options']['client_secret'],
	), array(
		'label' => 'Client secret',
		'hint' => 'Obrigatório',
		'explain' => 'O secret para a aplicação GitHub que você criou para este domínio.',
	));
	return $__finalCompiled;
});