<?php
// FROM HASH: 6625a6a3dbb30b7419c8c13a5c94ca9c
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Confirmar o cancelamento da inscrição' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['purchasableItem']['title']));
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formInfoRow('
				' . 'Confirme que deseja cancelar a seguinte inscrição' . $__vars['xf']['language']['label_separator'] . '
				<strong>' . $__templater->escape($__vars['purchasableItem']['title']) . '</strong>
			', array(
		'rowtype' => 'confirm',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'submit' => 'Cancelar',
	), array(
		'rowtype' => 'simple',
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('purchase/cancel-recurring', null, array('request_key' => $__vars['purchaseRequest']['request_key'], ), ), false),
		'class' => 'block',
		'ajax' => 'true',
	));
	return $__finalCompiled;
});