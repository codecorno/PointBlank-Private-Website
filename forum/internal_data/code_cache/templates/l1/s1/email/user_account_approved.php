<?php
// FROM HASH: c1c6f62979bd3c293fa1ae116e26dfba
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<mail:subject>
	' . 'Account approved on ' . $__templater->escape($__vars['xf']['options']['boardTitle']) . '' . '
</mail:subject>

' . '<p>' . $__templater->escape($__vars['user']['username']) . ', the account you registered at ' . (((('<a href="' . $__templater->func('link', array('canonical:index', ), true)) . '">') . $__templater->escape($__vars['xf']['options']['boardTitle'])) . '</a>') . ' has now been approved. You may now visit our site as a registered member. </p>' . '

<h2>' . '<a href="' . $__templater->func('link', array('canonical:index', ), true) . '">Visit ' . $__templater->escape($__vars['xf']['options']['boardTitle']) . '</a>' . '</h2>';
	return $__finalCompiled;
});