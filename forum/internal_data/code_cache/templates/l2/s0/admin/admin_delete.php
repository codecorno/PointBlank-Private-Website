<?php
// FROM HASH: 24b4d8f209c4bae1913dc8adf3b3dd82
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Confirmar ação');
	$__finalCompiled .= '

' . $__templater->form('

	<div class="block-container">
		<div class="block-body">
			' . $__templater->formInfoRow('
				' . 'Confirme que deseja remover o seguinte usuário como administrador' . $__vars['xf']['language']['label_separator'] . '
				<strong><a href="' . $__templater->func('link', array('admins/edit', $__vars['admin'], ), true) . '">' . $__templater->escape($__vars['admin']['username']) . '</a></strong>
			', array(
		'rowtype' => 'confirm',
	)) . '

			' . $__templater->formPasswordBoxRow(array(
		'name' => 'visitor_password',
	), array(
		'label' => 'Sua senha',
		'explain' => 'Você deve digitar sua senha atual para validar esta solicitação.',
	)) . '
		</div>

		' . $__templater->formSubmitRow(array(
		'icon' => 'delete',
	), array(
	)) . '
	</div>

', array(
		'action' => $__templater->func('link', array('admins/delete', $__vars['admin'], ), false),
		'class' => 'block',
		'ajax' => 'true',
	));
	return $__finalCompiled;
});