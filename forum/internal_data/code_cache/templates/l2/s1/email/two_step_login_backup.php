<?php
// FROM HASH: 5129e5be2662a25b963897333fdd01cb
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<mail:subject>' . '' . $__templater->escape($__vars['xf']['options']['boardTitle']) . ' - Login via backup code' . '</mail:subject>

' . '<p>' . $__templater->escape($__vars['user']['username']) . ',</p>

<p>Você fez login recentemente em sua conta no ' . (((('<a href="' . $__templater->func('link', array('canonical:index', ), true)) . '">') . $__templater->escape($__vars['xf']['options']['boardTitle'])) . '</a>') . ' e completou a verificação em duas etapas por meio de um código de backup. Um código de backup só deve ser usado quando você não tem acesso a outro método de verificação.</p>

<p>O login foi solicitado pelo IP ' . $__templater->escape($__vars['ip']) . '. Se você não iniciou este pedido, você deve alterar sua senha urgentemente.</p>';
	return $__finalCompiled;
});