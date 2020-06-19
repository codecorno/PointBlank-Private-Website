<?php
// FROM HASH: 80205a50dec1a0ed4e011788e99726d6
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formTextBoxRow(array(
		'name' => 'options[primary_account]',
		'value' => $__vars['profile']['options']['primary_account'],
		'type' => 'email',
	), array(
		'label' => 'E-mail da conta principal do PayPal',
		'hint' => 'Obrigatório',
		'explain' => '
		' . 'Este é o endereço de e-mail principal na sua conta do PayPal. Se isso estiver incorreto, os pagamentos podem não ser processados com êxito. Observe que esta deve ser uma conta PayPal Premier ou Business e os IPNs devem estar habilitados.' . '
	',
	)) . '

' . $__templater->formTextAreaRow(array(
		'name' => 'options[alternate_accounts]',
		'value' => $__vars['profile']['options']['alternate_accounts'],
		'autosize' => 'true',
	), array(
		'label' => 'Contas alternativas do PayPal',
		'explain' => 'Insira o endereço de e-mail de qualquer conta PayPal diferente da principal que pode receber pagamentos para atualizações de usuários. Isso pode ser útil se a conta principal for alterada e os pagamentos recorrentes ainda estiverem vindo da conta antiga, por exemplo. Se a conta antiga não estiver listada como uma alternativa válida, os pagamentos não serão aceitos para esta conta. Insira uma conta por linha.',
	)) . '

' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'options[require_address]',
		'selected' => $__vars['profile']['options']['require_address'],
		'label' => 'Require address',
		'hint' => 'If enabled, the payment provider will collect the payee\'s address while taking the payment.',
		'_type' => 'option',
	)), array(
	)) . '

' . $__templater->formHiddenVal('options[legacy]', ($__vars['profile']['options']['legacy'] ? 1 : 0), array(
	));
	return $__finalCompiled;
});