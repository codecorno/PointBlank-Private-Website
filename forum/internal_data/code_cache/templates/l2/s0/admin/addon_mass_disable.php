<?php
// FROM HASH: 705395537ed7ecf679a747c205464a93
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Confirmar ação');
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if (!$__vars['disabled']) {
		$__compilerTemp1 .= '
					<div class="blockMessage blockMessage--important blockMessage--iconic">
						' . 'Observação: Essa ação substituirá a lista de complementos desativados anteriormente em cache.' . '
					</div>
				';
	}
	$__finalCompiled .= $__templater->form('

	<div class="block-container">
		<div class="block-body">
			' . $__templater->formInfoRow('
				<p>' . 'Tem certeza de que deseja desativar todos os complementos?' . '</p>
				' . $__compilerTemp1 . '
			', array(
		'rowtype' => 'confirm',
	)) . '
		</div>

		' . $__templater->formSubmitRow(array(
		'submit' => 'Desativar tudo',
		'icon' => 'disable',
	), array(
		'rowtype' => 'simple',
	)) . '
	</div>

	' . $__templater->func('redirect_input', array(null, null, true)) . '

', array(
		'action' => $__templater->func('link', array('add-ons/mass-toggle', null, array('enable' => 0, ), ), false),
		'class' => 'block',
		'ajax' => 'true',
	));
	return $__finalCompiled;
});