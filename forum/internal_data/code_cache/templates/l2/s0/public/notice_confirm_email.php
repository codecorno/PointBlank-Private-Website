<?php
// FROM HASH: 87a6f0c0240a3db368a5ab20e6291660
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= 'Sua conta está aguardando confirmação. A confirmação foi enviada para ' . $__templater->escape($__vars['xf']['visitor']['email']) . '.' . '<br />
<a href="' . $__templater->func('link', array('account-confirmation/resend', ), true) . '" data-xf-click="overlay">' . 'Reenviar e-mail de confirmação' . '</a>';
	return $__finalCompiled;
});