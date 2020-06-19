<?php
// FROM HASH: a7bf856494ff92c9b4899fd742284339
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formTextBoxRow(array(
		'name' => 'options[client_id]',
		'value' => $__vars['options']['client_id'],
	), array(
		'label' => 'ID do Cliente',
		'hint' => 'Obrigatório',
		'explain' => 'O ID do cliente associado à sua <a href="https://www.linkedin.com/developer/apps/" target="_blank">aplicação LinkedIn</a> para este domínio.',
	)) . '

' . $__templater->formTextBoxRow(array(
		'name' => 'options[client_secret]',
		'value' => $__vars['options']['client_secret'],
	), array(
		'label' => 'Client secret',
		'hint' => 'Obrigatório',
		'explain' => 'O client secret para o aplicativo do LinkedIn que você criou para este domínio.',
	));
	return $__finalCompiled;
});