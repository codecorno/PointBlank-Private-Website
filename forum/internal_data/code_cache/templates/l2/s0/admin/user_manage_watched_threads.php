<?php
// FROM HASH: 5714742a47397de9ddad3e68bc822dcb
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Gerenciar tópicos seguidos' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['user']['username']));
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formRadioRow(array(
		'name' => 'action',
	), array(array(
		'value' => 'watch_no_email',
		'checked' => 'checked',
		'label' => 'Desativar notificação por e-mail',
		'_type' => 'option',
	),
	array(
		'value' => '',
		'label' => 'Parar de seguir tópicos',
		'_type' => 'option',
	)), array(
		'label' => 'Ação',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'icon' => 'save',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('users/manage-watched-threads', $__vars['user'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});