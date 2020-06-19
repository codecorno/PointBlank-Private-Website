<?php
// FROM HASH: e51fcc5ceaf8909f660467d75c29364c
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= 'As tentativas de enviar e-mails para ' . $__templater->escape($__vars['xf']['visitor']['email']) . ' falharam. Atualize seu e-mail.' . '<br />
<a href="' . $__templater->func('link', array('account/email', ), true) . '">' . 'Atualizar seus dados de contato' . '</a>';
	return $__finalCompiled;
});