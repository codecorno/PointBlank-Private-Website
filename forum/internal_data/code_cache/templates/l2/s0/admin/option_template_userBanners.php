<?php
// FROM HASH: dab31c707c94216843ce50af193e5c43
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => $__vars['inputName'] . '[showStaff]',
		'selected' => $__vars['option']['option_value']['showStaff'],
		'label' => 'Mostrar banner da equipe',
		'hint' => 'Se ativado, os membros da equipe terão automaticamente um banner adicionado como o banner de maior prioridade.',
		'_type' => 'option',
	),
	array(
		'name' => $__vars['inputName'] . '[displayMultiple]',
		'selected' => $__vars['option']['option_value']['displayMultiple'],
		'label' => 'Permitir o empilhamento de banner',
		'hint' => 'Se ativado, todos os banners aplicáveis a um usuário serão exibidos. Se desativado, somente o banner de maior prioridade será exibido.',
		'_type' => 'option',
	),
	array(
		'name' => $__vars['inputName'] . '[showStaffAndOther]',
		'selected' => $__vars['option']['option_value']['showStaffAndOther'],
		'label' => 'Mostrar banner da equipe e do grupo',
		'hint' => 'Se o empilhamento de banner estiver desativado, os membros da equipe terão apenas o banner da equipe. Se esta opção estiver ativada, eles terão um banner de equipe e o banner de grupo de prioridade mais alta.',
		'_type' => 'option',
	),
	array(
		'name' => $__vars['inputName'] . '[hideUserTitle]',
		'selected' => $__vars['option']['option_value']['hideUserTitle'],
		'label' => 'Ocultar título do usuário padrão',
		'hint' => 'Se ativado, em situações onde um título de usuário é exibido com um banner, o título do usuário será ocultado se um banner será exibido. Os títulos personalizados por usuário nunca serão ocultados.',
		'_type' => 'option',
	)), array(
		'label' => $__templater->escape($__vars['option']['title']),
		'hint' => $__templater->escape($__vars['hintHtml']),
		'explain' => $__templater->escape($__vars['explainHtml']),
		'html' => $__templater->escape($__vars['listedHtml']),
	));
	return $__finalCompiled;
});