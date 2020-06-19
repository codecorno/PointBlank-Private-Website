<?php
// FROM HASH: ff3182d32670e2218f0315f2e1c804c2
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '' . ($__templater->escape($__vars['user']['username']) ?: $__templater->escape($__vars['alert']['username'])) . ' reacted to your message in the conversation ' . $__templater->escape($__vars['content']['Conversation']['title']) . ' with ' . $__templater->func('reaction_title', array($__vars['extra']['reaction_id'], ), true) . '.' . '
<push:url>' . $__templater->func('link', array('canonical:conversations/messages', $__vars['content'], ), true) . '</push:url>
<push:tag>conversation_message_reaction_' . $__templater->escape($__vars['content']['message_id']) . '_' . $__templater->escape($__vars['extra']['reaction_id']) . '</push:tag>';
	return $__finalCompiled;
});