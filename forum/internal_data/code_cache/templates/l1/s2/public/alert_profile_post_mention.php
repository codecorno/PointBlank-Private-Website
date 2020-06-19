<?php
// FROM HASH: 4ee44e0b920c72f80360fc39804d5c8f
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '' . $__templater->func('username_link', array($__vars['user'], false, array('defaultname' => $__vars['alert']['username'], ), ), true) . ' mentioned you in <a ' . (('href="' . $__templater->func('link', array('profile-posts', $__vars['content'], ), true)) . '" class="fauxBlockLink-blockLink"') . '>a message</a> on ' . $__templater->escape($__vars['content']['ProfileUser']['username']) . '\'s profile.';
	return $__finalCompiled;
});