<?php
// FROM HASH: 954b0a7c43209674a124e132e0db00cb
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '<hr class="formRowSep" />

' . $__templater->formNumberBoxRow(array(
		'name' => 'options[limit]',
		'value' => $__vars['options']['limit'],
		'min' => '0',
	), array(
		'label' => 'Máximo de nomes de usuário',
		'explain' => 'Para impedir que o widget "Membros online" se torne muito grande em um fórum ocupado, você pode limitar o número de nomes antes que um link "... e X mais" seja adicionado para encerrar a lista. Um valor de 0 desativa o limite.',
	)) . '

' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'options[followedOnline]',
		'selected' => $__vars['options']['followedOnline'],
		'label' => 'Ativar linha "Pessoas que você segue"',
		'_type' => 'option',
	),
	array(
		'name' => 'options[staffOnline]',
		'selected' => $__vars['options']['staffOnline'],
		'label' => 'Ativar bloco \'Staff Online\'',
		'_dependent' => array($__templater->formCheckBox(array(
	), array(array(
		'name' => 'options[staffQuery]',
		'selected' => $__vars['options']['staffQuery'],
		'label' => 'Run dedicated staff query when necessary',
		'hint' => 'When more users are online than are allowed to be shown (see above), some online staff members may be omitted. Enabling this option will cause an extra database query to be run when necessary, to ensure that all staff are displayed.',
		'_type' => 'option',
	)))),
		'_type' => 'option',
	)), array(
	));
	return $__finalCompiled;
});