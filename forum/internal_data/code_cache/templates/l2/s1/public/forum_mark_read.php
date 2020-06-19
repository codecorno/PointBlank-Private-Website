<?php
// FROM HASH: 7ab1fe31e8b9451ffb33565a3d35b788
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__vars['forum']) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Marcar fórum como lido');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Marcar fóruns como lidos');
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__vars['forum']) {
		$__compilerTemp1 .= '
			<div class="block-body">
				' . $__templater->formInfoRow('
					' . 'Tem certeza de que deseja marcar este fórum como lido?' . '
					<strong><a href="' . $__templater->func('link', array('forums', $__vars['forum'], ), true) . '">' . $__templater->escape($__vars['forum']['title']) . '</a></strong>
				', array(
			'rowtype' => 'confirm',
		)) . '
			</div>
			' . $__templater->formSubmitRow(array(
			'submit' => 'Marcar fórum como lido',
			'icon' => 'markRead',
		), array(
			'rowtype' => 'simple',
		)) . '
		';
	} else {
		$__compilerTemp1 .= '
			<div class="block-body">
				' . $__templater->formInfoRow('Tem certeza de que deseja marcar todos os fóruns como lidos?', array(
			'rowtype' => 'confirm',
		)) . '
			</div>
			' . $__templater->formSubmitRow(array(
			'submit' => 'Marcar fóruns como lidos',
			'icon' => 'markRead',
		), array(
			'rowtype' => 'simple',
		)) . '
		';
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		' . $__compilerTemp1 . '
	</div>
', array(
		'action' => $__templater->func('link', array('forums/mark-read', $__vars['forum'], array('date' => $__vars['date'], ), ), false),
		'class' => 'block',
		'ajax' => 'true',
	));
	return $__finalCompiled;
});