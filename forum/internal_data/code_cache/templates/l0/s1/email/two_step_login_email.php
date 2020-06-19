<?php
// FROM HASH: 7fe42d9e26ffce7b5691e135aa0dc1ba
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<mail:subject>' . '' . $__templater->escape($__vars['xf']['options']['boardTitle']) . ' - Login verification' . '</mail:subject>

' . '<p>' . $__templater->escape($__vars['user']['username']) . ',</p>

<p>To complete the login to your account (or to complete two-step verification setup) at ' . (((('<a href="' . $__templater->func('link', array('canonical:index', ), true)) . '">') . $__templater->escape($__vars['xf']['options']['boardTitle'])) . '</a>') . ', you must enter the following code:</p>' . '

<h2>' . $__templater->escape($__vars['code']) . '</h2>

' . '<p>This code is valid for 15 minutes.</p>

<p>The login was requested via the IP ' . $__templater->escape($__vars['ip']) . '. If you did not initiate this request, you should change your password urgently.</p>';
	return $__finalCompiled;
});