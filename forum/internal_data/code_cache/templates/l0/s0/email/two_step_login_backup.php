<?php
// FROM HASH: 5129e5be2662a25b963897333fdd01cb
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<mail:subject>' . '' . $__templater->escape($__vars['xf']['options']['boardTitle']) . ' - Login via backup code' . '</mail:subject>

' . '<p>' . $__templater->escape($__vars['user']['username']) . ',</p>

<p>You recently logged into your account at ' . (((('<a href="' . $__templater->func('link', array('canonical:index', ), true)) . '">') . $__templater->escape($__vars['xf']['options']['boardTitle'])) . '</a>') . ' and completed two-step verification via a backup code. A backup code should only be used when you don\'t have access to another method of verification.</p>

<p>The login was requested via the IP ' . $__templater->escape($__vars['ip']) . '. If you did not initiate this request, you should change your password urgently.</p>';
	return $__finalCompiled;
});