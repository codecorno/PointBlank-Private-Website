<?php
// FROM HASH: 984a1896bba388ca67c1724983fd7cc8
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->includeJs(array(
		'src' => 'xf/captcha.js',
		'min' => '1',
	));
	$__finalCompiled .= '

<div class="keycaptcha-root" data-xf-init="key-captcha" data-user="' . $__templater->escape($__vars['keyUserId']) . '" data-session="' . $__templater->escape($__vars['sessionId']) . '" data-sign="' . $__templater->escape($__vars['sign']) . '" data-sign2="' . $__templater->escape($__vars['sign2']) . '">
	<noscript>' . 'Please enable JavaScript to continue.' . '</noscript>
	<span class="u-jsOnly">' . 'Loading' . $__vars['xf']['language']['ellipsis'] . '</span>
</div>
' . $__templater->formHiddenVal('keycaptcha_code', '', array(
	));
	return $__finalCompiled;
});