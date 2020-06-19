<?php
// FROM HASH: 75c0fa0389c6fffe339ef7d5cc2a9191
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= 'Your post in the thread ' . $__templater->escape($__vars['extra']['title']) . ' was moved to ' . ($__templater->func('prefix', array('thread', $__vars['extra']['prefix_id'], 'plain', ), true) . $__templater->escape($__vars['extra']['targetTitle'])) . '.' . '
';
	if ($__vars['extra']['reason']) {
		$__finalCompiled .= 'Reason' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['extra']['reason']);
	}
	$__finalCompiled .= '
<push:url>' . $__templater->func('base_url', array($__vars['extra']['targetLink'], 'canonical', ), true) . '</push:url>';
	return $__finalCompiled;
});