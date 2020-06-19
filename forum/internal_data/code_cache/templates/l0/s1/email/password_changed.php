<?php
// FROM HASH: 7399f4b060d06d410e1bdd76c5348780
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<mail:subject>
	' . '' . $__templater->escape($__vars['xf']['options']['boardTitle']) . ' - Password changed' . '
</mail:subject>

' . '<p>' . $__templater->escape($__vars['user']['username']) . ',</p>

<p>Your password at ' . (((('<a href="' . $__templater->func('link', array('canonical:index', ), true)) . '">') . $__templater->escape($__vars['xf']['options']['boardTitle'])) . '</a>') . ' was recently changed. If you made this change, you may ignore this message.</p>

<p>If you did not request this change, please use the lost password process to generate a new password. If you are unable to do this, please contact an administrator.</p>

<p>Your password was changed by the IP ' . $__templater->escape($__vars['ip']) . '.</p>';
	return $__finalCompiled;
});