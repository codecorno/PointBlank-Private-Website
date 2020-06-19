<?php
// FROM HASH: 1d60ba3d84ffbd468e5a94ded45d07df
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<mail:subject>
	' . 'Account rejected on ' . $__templater->escape($__vars['xf']['options']['boardTitle']) . '' . '
</mail:subject>

' . '<p>' . $__templater->escape($__vars['user']['username']) . ', unfortunately, the account you registered on ' . (((('<a href="' . $__templater->func('link', array('canonical:index', ), true)) . '">') . $__templater->escape($__vars['xf']['options']['boardTitle'])) . '</a>') . ' did not meet membership requirements. Your account is no longer accessible.</p>' . '

';
	if ($__vars['reason']) {
		$__finalCompiled .= '
	<p>' . 'The following reason was given:' . ' ' . $__templater->escape($__vars['reason']) . '</p>
';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
});