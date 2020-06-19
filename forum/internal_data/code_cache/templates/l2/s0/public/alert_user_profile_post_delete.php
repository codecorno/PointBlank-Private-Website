<?php
// FROM HASH: 815417d69d9aa3edba05b7a8327945f6
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__vars['extra']['profileUserId'] == $__vars['xf']['visitor']['user_id']) {
		$__finalCompiled .= '
	' . 'Sua atualização de status foi excluída.' . '
';
	} else {
		$__finalCompiled .= '
	' . 'Seu post de perfil no perfil de ' . (((('<a href="' . $__templater->func('base_url', array($__vars['extra']['profileLink'], ), true)) . '" class="fauxBlockLink-blockLink">') . $__templater->escape($__vars['extra']['profileUser'])) . '</a>') . ' foi excluído.' . '
';
	}
	$__finalCompiled .= '
';
	if ($__vars['extra']['reason']) {
		$__finalCompiled .= 'Motivo' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['extra']['reason']);
	}
	return $__finalCompiled;
});