<?php
// FROM HASH: bb2635355058b47191c42069536b7c07
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formTextBoxRow(array(
		'name' => 'options[client_id]',
		'value' => $__vars['options']['client_id'],
	), array(
		'label' => 'ID do Cliente',
		'hint' => 'Obrigatório',
		'explain' => 'A inserção de um ID de cliente permitirá que os usuários façam login usando suas contas do Google. Você pode obter um ID de cliente do Google por meio do <a href="https://developers.google.com/console" target="_blank">Console do desenvolvedor</a>',
	)) . '

' . $__templater->formTextBoxRow(array(
		'name' => 'options[client_secret]',
		'value' => $__vars['options']['client_secret'],
	), array(
		'label' => 'Client secret',
		'hint' => 'Obrigatório',
		'explain' => 'O secret que corresponde ao seu ID de cliente do Google.',
	));
	return $__finalCompiled;
});