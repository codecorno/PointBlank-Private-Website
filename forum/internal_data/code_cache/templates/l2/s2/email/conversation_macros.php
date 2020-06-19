<?php
// FROM HASH: c0e48021b94c07bd5b869cf19e6e6720
return array('macros' => array('footer' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'conversation' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	' . '<p class="minorText">Esta mensagem foi enviada para você porque suas preferências estão configuradas para receber e-mails quando uma nova mensagem de conversa é recebida. <a href="' . $__templater->func('link', array('canonical:email-stop/conversation', $__vars['xf']['toUser'], ), true) . '">Parar de receber esses e-mails</a>.</p>' . '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';

	return $__finalCompiled;
});