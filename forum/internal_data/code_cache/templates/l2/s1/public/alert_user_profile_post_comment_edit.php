<?php
// FROM HASH: faed5ba7fec5029cadecf44f7714fc3f
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= 'Seu comentário no post de perfil de ' . (((('<a href="' . $__templater->func('base_url', array($__vars['extra']['link'], ), true)) . '" class="fauxBlockLink-blockLink">') . $__templater->escape($__vars['extra']['postUser'])) . '</a>') . ' foi editado.' . '
';
	if ($__vars['extra']['reason']) {
		$__finalCompiled .= 'Motivo' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['extra']['reason']);
	}
	return $__finalCompiled;
});