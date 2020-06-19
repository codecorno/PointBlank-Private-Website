<?php
// FROM HASH: b9ac6b3903ce8ab73cfbec1a306815cf
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '' . ($__templater->escape($__vars['user']['username']) ?: $__templater->escape($__vars['alert']['username'])) . ' mentioned you in a comment on ' . $__templater->escape($__vars['content']['ProfilePost']['ProfileUser']['username']) . '\'s profile.' . '
<push:url>' . $__templater->func('link', array('canonical:profile-posts/comments', $__vars['content'], ), true) . '</push:url>';
	return $__finalCompiled;
});