<?php
// FROM HASH: 256fd5f668eb3a5df46276d337c6b8dd
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '' . $__templater->func('username_link', array($__vars['user'], false, array('defaultname' => $__vars['alert']['username'], ), ), true) . ' is now following you.' . '
';
	if ($__vars['user']) {
		$__finalCompiled .= '<a href="' . $__templater->func('link', array('members', $__vars['user'], ), true) . '" class="fauxBlockLink-blockLink"></a>';
	}
	return $__finalCompiled;
});