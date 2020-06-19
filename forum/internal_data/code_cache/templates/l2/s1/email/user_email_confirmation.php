<?php
// FROM HASH: faa943c4749072dae44bb3f53a4c2b4f
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<mail:subject>
	' . '' . $__templater->escape($__vars['xf']['options']['boardTitle']) . ' - Account confirmation required' . '
</mail:subject>

' . '<p>' . $__templater->escape($__vars['user']['username']) . ', para completar o seu registo ou reativar a sua conta no ' . (((('<a href="' . $__templater->func('link', array('canonical:index', ), true)) . '">') . $__templater->escape($__vars['xf']['options']['boardTitle'])) . '</a>') . ', tem de confirmar o seu endereço de e-mail clicando no botão abaixo.</p>' . '

<p><a href="' . $__templater->func('link', array('canonical:account-confirmation/email', $__vars['user'], array('c' => $__vars['confirmation']['confirmation_key'], ), ), true) . '" class="button">' . 'Confirmar seu e-mail' . '</a></p>';
	return $__finalCompiled;
});