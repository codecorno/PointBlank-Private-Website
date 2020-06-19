<?php
// FROM HASH: ff899b2eabdbaaffe7ce0692b513c19f
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= 'O fórum está fechado. Somente os administradores podem acessar o fórum.' . '<br />
<a href="' . $__templater->func('link_type', array('admin', 'options/groups', array('group_id' => 'boardActive', ), ), true) . '">' . 'Reabrir via painel de controle de administração' . '</a>';
	return $__finalCompiled;
});