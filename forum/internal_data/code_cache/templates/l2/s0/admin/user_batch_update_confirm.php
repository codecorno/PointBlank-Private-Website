<?php
// FROM HASH: aefccd3ab6ebf66749fd615dabcfc1cb
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Batch update users');
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if (!$__vars['userIds']) {
		$__compilerTemp1 .= '
					<span role="presentation" aria-hidden="true">&middot;</span>
					<a href="' . $__templater->func('link', array('users/list', null, array('criteria' => $__vars['criteria'], 'all' => true, ), ), true) . '">' . 'View or filter matches' . '</a>
				';
	}
	$__compilerTemp2 = array(array(
		'_type' => 'option',
	));
	if ($__templater->isTraversable($__vars['userGroups'])) {
		foreach ($__vars['userGroups'] AS $__vars['userGroup']) {
			$__compilerTemp2[] = array(
				'value' => $__vars['userGroup']['user_group_id'],
				'label' => $__templater->escape($__vars['userGroup']['title']),
				'_type' => 'option',
			);
		}
	}
	$__compilerTemp3 = array(array(
		'_type' => 'option',
	));
	if ($__templater->isTraversable($__vars['userGroups'])) {
		foreach ($__vars['userGroups'] AS $__vars['userGroup']) {
			$__compilerTemp3[] = array(
				'value' => $__vars['userGroup']['user_group_id'],
				'label' => $__templater->escape($__vars['userGroup']['title']),
				'_type' => 'option',
			);
		}
	}
	$__compilerTemp4 = array(array(
		'_type' => 'option',
	));
	if ($__templater->isTraversable($__vars['userGroups'])) {
		foreach ($__vars['userGroups'] AS $__vars['userGroup']) {
			$__compilerTemp4[] = array(
				'value' => $__vars['userGroup']['user_group_id'],
				'label' => $__templater->escape($__vars['userGroup']['title']),
				'_type' => 'option',
			);
		}
	}
	$__compilerTemp5 = '';
	if ($__vars['userIds']) {
		$__compilerTemp5 .= '
		' . $__templater->formHiddenVal('user_ids', $__templater->filter($__vars['userIds'], array(array('json', array()),), false), array(
		)) . '
	';
	} else {
		$__compilerTemp5 .= '
		' . $__templater->formHiddenVal('criteria', $__templater->filter($__vars['criteria'], array(array('json', array()),), false), array(
		)) . '
	';
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<h2 class="block-header">' . 'Update users' . '</h2>
		<div class="block-body">
			' . $__templater->formRow('
				' . $__templater->filter($__vars['total'], array(array('number', array()),), true) . '
				' . $__compilerTemp1 . '
			', array(
		'label' => 'Matched users',
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formSelectRow(array(
		'name' => 'actions[set_primary_group_id]',
	), $__compilerTemp2, array(
		'label' => 'Definir o grupo de usuário principal',
	)) . '

			' . $__templater->formSelectRow(array(
		'name' => 'actions[add_group_id]',
	), $__compilerTemp3, array(
		'label' => 'Adicionar grupo de usuários secundário',
	)) . '

			' . $__templater->formSelectRow(array(
		'name' => 'actions[remove_group_id]',
	), $__compilerTemp4, array(
		'label' => 'Remover grupo de usuário secundário',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'actions[custom_title]',
	), array(
		'label' => 'Definir título personalizado',
	)) . '

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'actions[discourage]',
		'label' => 'Discourage users',
		'_type' => 'option',
	),
	array(
		'name' => 'actions[undiscourage]',
		'label' => 'Undiscourage users',
		'_type' => 'option',
	),
	array(
		'name' => 'actions[ban]',
		'label' => 'Banir usuários (permanentemente)',
		'_type' => 'option',
	),
	array(
		'name' => 'actions[unban]',
		'label' => 'Unban users',
		'_type' => 'option',
	),
	array(
		'name' => 'actions[remove_avatar]',
		'label' => 'Remover avatares',
		'_type' => 'option',
	),
	array(
		'name' => 'actions[remove_signature]',
		'label' => 'Remover assinaturas',
		'_type' => 'option',
	),
	array(
		'name' => 'actions[remove_website]',
		'label' => 'Remove websites',
		'_type' => 'option',
	)), array(
		'explain' => 'Super administradores não serão atualizados.',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'submit' => 'Update users',
		'icon' => 'save',
	), array(
	)) . '
	</div>

	' . $__compilerTemp5 . '
', array(
		'action' => $__templater->func('link', array('users/batch-update/action', ), false),
		'class' => 'block',
	)) . '

';
	$__compilerTemp6 = '';
	if ($__vars['userIds']) {
		$__compilerTemp6 .= '
		' . $__templater->formHiddenVal('user_ids', $__templater->filter($__vars['userIds'], array(array('json', array()),), false), array(
		)) . '
	';
	} else {
		$__compilerTemp6 .= '
		' . $__templater->formHiddenVal('criteria', $__templater->filter($__vars['criteria'], array(array('json', array()),), false), array(
		)) . '
	';
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<h2 class="block-header">' . 'Excluir usuários' . '</h2>
		<div class="block-body">
			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'actions[delete]',
		'label' => 'Confirmar exclusão de ' . $__templater->filter($__vars['total'], array(array('number', array()),), true) . ' usuários',
		'_type' => 'option',
	)), array(
		'explain' => 'Administradores e moderadores não serão excluídos.',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'name' => 'confirm_delete',
		'icon' => 'delete',
	), array(
	)) . '
	</div>

	' . $__compilerTemp6 . '
', array(
		'action' => $__templater->func('link', array('users/batch-update/action', ), false),
		'class' => 'block',
	));
	return $__finalCompiled;
});