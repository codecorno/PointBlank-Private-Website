<?php
// FROM HASH: 720cde975bfafa4c3fc32dc981a2e6d6
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= 'One of your account upgrades has expired. Renew now!' . '
<push:url>' . $__templater->func('link', array('canonical:account/upgrades', ), true) . '</push:url>';
	return $__finalCompiled;
});