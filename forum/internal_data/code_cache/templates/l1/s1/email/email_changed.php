<?php
// FROM HASH: fd73d72ed41d0e1fb7addbf2721170aa
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<mail:subject>
	' . '' . $__templater->escape($__vars['xf']['options']['boardTitle']) . ' - Email changed' . '
</mail:subject>

' . '<p>' . $__templater->escape($__vars['user']['username']) . ',</p>

<p>Your email at ' . (((('<a href="' . $__templater->func('link', array('canonical:index', ), true)) . '">') . $__templater->escape($__vars['xf']['options']['boardTitle'])) . '</a>') . ' was recently changed to ' . $__templater->escape($__vars['newEmail']) . '. If you made this change, you may ignore this message.</p>

<p>If you did not request this change, please log in and change your password and email address. If you are unable to do this, please contact an administrator.</p>

<p>Your email was changed by the IP ' . $__templater->escape($__vars['ip']) . '.</p>';
	return $__finalCompiled;
});