<?php
// FROM HASH: b898f9ce99be891f13f7b964300bc8b0
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= 'Seu post no tópico ' . ((((('<a href="' . $__templater->func('base_url', array($__vars['extra']['threadLink'], ), true)) . '" class="fauxBlockLink-blockLink">') . $__templater->func('prefix', array('thread', $__vars['extra']['prefix_id'], ), true)) . $__templater->escape($__vars['extra']['title'])) . '</a>') . ' foi excluído.' . '
';
	if ($__vars['extra']['reason']) {
		$__finalCompiled .= 'Motivo' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['extra']['reason']);
	}
	return $__finalCompiled;
});