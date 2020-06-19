<?php
// FROM HASH: 3420f31c8dbda6cb02c2bb8389e14fff
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__vars['extra']['expiry']) {
		$__finalCompiled .= '
	' . 'Você não poderá responder ao tópico ' . ($__templater->func('prefix', array('thread', $__vars['thread'], 'plain', ), true) . $__templater->escape($__vars['content']['title'])) . ' até ' . $__templater->func('date', array($__vars['extra']['expiry'], ), true) . '.' . '
';
	} else {
		$__finalCompiled .= '
	' . 'Você não pode mais responder ao tópico ' . ($__templater->func('prefix', array('thread', $__vars['thread'], 'plain', ), true) . $__templater->escape($__vars['content']['title'])) . '.' . '
';
	}
	$__finalCompiled .= '
';
	if ($__vars['extra']['reason']) {
		$__finalCompiled .= 'Motivo' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['extra']['reason']);
	}
	$__finalCompiled .= '
<push:url>' . $__templater->func('link', array('canonical:threads', $__vars['content'], ), true) . '</push:url>';
	return $__finalCompiled;
});