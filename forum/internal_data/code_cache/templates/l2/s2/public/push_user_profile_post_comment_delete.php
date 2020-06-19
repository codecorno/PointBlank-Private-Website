<?php
// FROM HASH: e21c137ef4ee55c0d2c5849138c7cc92
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= 'Seu comentário no post de perfil de ' . $__templater->escape($__vars['extra']['postUser']) . ' foi excluído.' . '
';
	if ($__vars['extra']['reason']) {
		$__finalCompiled .= 'Motivo' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['extra']['reason']);
	}
	$__finalCompiled .= '
<push:url>' . $__templater->func('base_url', array($__vars['extra']['profilePostLink'], 'canonical', ), true) . '</push:url>';
	return $__finalCompiled;
});