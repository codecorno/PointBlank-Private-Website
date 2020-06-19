<?php
// FROM HASH: 72ba94f72626a9f59de533269bc28c45
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formTextBoxRow(array(
		'name' => 'options[consumer_key]',
		'value' => $__vars['options']['consumer_key'],
	), array(
		'label' => 'Consumer key',
		'hint' => 'Obrigatório',
		'explain' => 'Para permitir que os usuários façam login com suas contas do Twitter, você deve criar um <a href="https://apps.twitter.com/" target="_blank"> aplicativo Twitter </a> e digitar a cosumer key e secret key.',
	)) . '

' . $__templater->formTextBoxRow(array(
		'name' => 'options[consumer_secret]',
		'value' => $__vars['options']['consumer_secret'],
	), array(
		'label' => 'Consumer secret',
		'hint' => 'Obrigatório',
		'explain' => 'Se você criou um aplicativo do Twitter para permitir que o Twitter entre, defina o consumer secret do aplicativo aqui.',
	));
	return $__finalCompiled;
});