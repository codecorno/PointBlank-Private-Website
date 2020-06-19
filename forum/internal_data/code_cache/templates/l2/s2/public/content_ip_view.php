<?php
// FROM HASH: c47b249dd935cd34d690b79482c757cd
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Informações de endereço IP para conteúdo por ' . ($__templater->escape($__vars['ip']['User']['username']) ?: 'Visitante') . '');
	$__finalCompiled .= '

';
	$__templater->breadcrumbs($__vars['breadcrumbs']);
	$__finalCompiled .= '

' . $__templater->callMacro('helper_ip', 'ip_block', array(
		'ip' => $__vars['ip']['ip'],
		'user' => $__vars['ip']['User'],
	), $__vars);
	return $__finalCompiled;
});