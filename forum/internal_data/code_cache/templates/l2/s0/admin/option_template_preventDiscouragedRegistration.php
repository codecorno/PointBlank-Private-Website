<?php
// FROM HASH: 88d4731704743cfce97b0a5bc44a41ce
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => $__vars['inputName'],
		'selected' => $__vars['option']['option_value'],
		'label' => $__templater->escape($__vars['option']['title']),
		'_type' => 'option',
	)), array(
		'hint' => $__templater->escape($__vars['hintHtml']),
		'explain' => 'Você pode impedir que qualquer visitante que navega de <a href="' . $__templater->func('link', array('banning/discouraged-ips', ), true) . '">endereços de IP desencorajados</a> registre novas contas. Eles serão informados de que o registro está atualmente desativado.',
		'html' => $__templater->escape($__vars['listedHtml']),
	));
	return $__finalCompiled;
});