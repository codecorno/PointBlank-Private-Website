<?php
// FROM HASH: 498e7853c3a6e5a071e6942d3dba8ef4
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formTextBoxRow(array(
		'name' => 'options[client_id]',
		'value' => $__vars['options']['client_id'],
	), array(
		'label' => 'ID do Cliente',
		'hint' => 'Obrigatório',
		'explain' => 'O ID de cliente associado ao seu <a href="https://developer.yahoo.com/apps" target="_blank">aplicativo Yahoo</a> para este domínio.',
	)) . '

' . $__templater->formTextBoxRow(array(
		'name' => 'options[client_secret]',
		'value' => $__vars['options']['client_secret'],
	), array(
		'label' => 'Client secret',
		'hint' => 'Obrigatório',
		'explain' => 'O secret do cliente para o aplicativo do Yahoo que você criou para esse domínio.',
	));
	return $__finalCompiled;
});