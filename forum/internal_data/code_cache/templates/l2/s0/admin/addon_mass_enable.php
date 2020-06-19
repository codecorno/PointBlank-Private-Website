<?php
// FROM HASH: 1ad8af8e45141c2ddbed4f4678373f1e
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Confirmar ação');
	$__finalCompiled .= '

';
	$__compilerTemp1 = array();
	if ($__templater->isTraversable($__vars['addOns'])) {
		foreach ($__vars['addOns'] AS $__vars['addOnId'] => $__vars['addOn']) {
			$__compilerTemp1[] = array(
				'value' => $__vars['addOnId'],
				'selected' => $__templater->func('in_array', array($__vars['addOnId'], $__vars['disabled'], ), false),
				'label' => $__templater->escape($__vars['addOn']['title']),
				'hint' => ($__vars['addOn']['active'] ? 'Esse complemento já está ativado.' : ''),
				'_type' => 'option',
			);
		}
	}
	$__finalCompiled .= $__templater->form('

	<div class="block-container">
		<div class="block-body">
			' . $__templater->formInfoRow('
				<p>' . 'Tem certeza de que deseja reativar os complementos desativados anteriormente?' . '</p>
				<p>' . 'Você pode controlar quais complementos são reativados na lista abaixo' . $__vars['xf']['language']['label_separator'] . '</p>
			', array(
		'rowtype' => 'confirm',
	)) . '

			' . $__templater->formCheckBoxRow(array(
		'name' => 'to_enable',
	), $__compilerTemp1, array(
	)) . '
		</div>

		' . $__templater->formSubmitRow(array(
		'submit' => 'Enable',
		'icon' => 'disable',
	), array(
	)) . '
	</div>

	' . $__templater->func('redirect_input', array(null, null, true)) . '

', array(
		'action' => $__templater->func('link', array('add-ons/mass-toggle', null, array('enable' => 1, ), ), false),
		'class' => 'block',
		'ajax' => 'true',
	));
	return $__finalCompiled;
});