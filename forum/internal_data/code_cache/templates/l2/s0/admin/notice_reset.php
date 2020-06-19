<?php
// FROM HASH: 84687b08e1697f07078849e525204f09
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Confirmar ação');
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formInfoRow('
				' . 'Confirme que deseja redefinir o seguinte aviso' . $__vars['xf']['language']['label_separator'] . '
				<strong><a href="' . $__templater->func('link', array('notices/edit', $__vars['notice'], ), true) . '">' . $__templater->escape($__vars['notice']['title']) . '</a></strong>
				' . 'A redefinição deste aviso irá exibi-lo para todos os usuários que correspondam aos critérios de aviso, mesmo que eles tenham dispensado anteriormente. Observe que isso não restaurará o aviso aos visitantes que o demitiram.' . '
			', array(
		'rowtype' => 'confirm',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'submit' => 'Resetar',
	), array(
		'rowtype' => 'simple',
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('notices/reset', $__vars['notice'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});