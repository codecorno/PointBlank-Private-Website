<?php
// FROM HASH: 2ea78536141961d1ec05cf6b4db1a556
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['content'], 'isFirstPost', array())) {
		$__finalCompiled .= '
	' . '' . ($__templater->escape($__vars['user']['username']) ?: $__templater->escape($__vars['alert']['username'])) . ' iniciou um tópico chamado ' . ($__templater->func('prefix', array('thread', $__vars['content']['Thread'], 'plain', ), true) . $__templater->escape($__vars['content']['Thread']['title'])) . '. Pode haver mais posts depois disso.' . '
	<push:url>' . $__templater->func('link', array('canonical:threads', $__vars['content']['Thread'], ), true) . '</push:url>
	<push:tag>post_insert_forum_' . $__templater->escape($__vars['content']['Thread']['node_id']) . '</push:tag>
';
	} else {
		$__finalCompiled .= '
	' . '' . ($__templater->escape($__vars['user']['username']) ?: $__templater->escape($__vars['alert']['username'])) . ' respondeu ao tópico ' . ($__templater->func('prefix', array('thread', $__vars['content']['Thread'], 'plain', ), true) . $__templater->escape($__vars['content']['Thread']['title'])) . '. Pode haver mais posts depois disso.' . '
	<push:url>' . $__templater->func('link', array('canonical:posts', $__vars['content'], ), true) . '</push:url>
	<push:tag>post_insert_thread_' . $__templater->escape($__vars['content']['thread_id']) . '</push:tag>
';
	}
	return $__finalCompiled;
});