<?php
// FROM HASH: 73165bc852499ee687393cc63e71a0ee
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= 'Seu post no tópico ' . ($__templater->func('prefix', array('thread', $__vars['extra']['prefix_id'], 'plain', ), true) . $__templater->escape($__vars['extra']['title'])) . ' foi excluído.' . '
';
	if ($__vars['extra']['reason']) {
		$__finalCompiled .= 'Motivo' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['extra']['reason']);
	}
	$__finalCompiled .= '
<push:url>' . $__templater->func('base_url', array($__vars['extra']['threadLink'], 'canonical', ), true) . '</push:url>';
	return $__finalCompiled;
});