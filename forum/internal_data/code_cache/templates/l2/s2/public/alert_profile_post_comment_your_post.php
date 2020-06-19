<?php
// FROM HASH: a426295e0e02d3b6722ee97847c49331
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '' . $__templater->func('username_link', array($__vars['user'], false, array('defaultname' => $__vars['alert']['username'], ), ), true) . ' comentou em <a ' . (('href="' . $__templater->func('link', array('profile-posts/comments', $__vars['content'], ), true)) . '" class="fauxBlockLink-blockLink"') . '>seu post</a> no perfil de ' . $__templater->escape($__vars['content']['ProfilePost']['ProfileUser']['username']) . '.';
	return $__finalCompiled;
});