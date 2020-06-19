<?php
// FROM HASH: 3b5ab0ddc1f768f81611bef1b688ec5d
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__vars['isWatched']) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Deixar de seguir fórum');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Seguir fórum');
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	$__templater->breadcrumbs($__templater->method($__vars['forum'], 'getBreadcrumbs', array()));
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__vars['isWatched']) {
		$__compilerTemp1 .= '
				' . $__templater->formInfoRow('
					' . 'Tem certeza de que deseja deixar de seguir este fórum?' . '
				', array(
			'rowtype' => 'confirm',
		)) . '
				' . $__templater->formHiddenVal('stop', '1', array(
		)) . '
			';
	} else {
		$__compilerTemp1 .= '
				';
		if ($__vars['forum']['allowed_watch_notifications'] != 'none') {
			$__compilerTemp1 .= '
					';
			$__compilerTemp2 = array(array(
				'value' => 'thread',
				'label' => 'Novos tópicos',
				'_type' => 'option',
			));
			if ($__vars['forum']['allowed_watch_notifications'] == 'all') {
				$__compilerTemp2[] = array(
					'value' => 'message',
					'label' => 'Novas mensagens',
					'_type' => 'option',
				);
			}
			$__compilerTemp2[] = array(
				'value' => '',
				'hint' => 'O fórum ainda estará listado na página de fóruns seguidos, que pode ser usada para listar apenas os fóruns em que você está interessado.',
				'label' => 'Não enviar notificações',
				'_type' => 'option',
			);
			$__compilerTemp1 .= $__templater->formRadioRow(array(
				'name' => 'notify',
				'value' => 'thread',
			), $__compilerTemp2, array(
				'label' => 'Enviar notificações para',
			)) . '

					' . $__templater->formCheckBoxRow(array(
			), array(array(
				'name' => 'send_alert',
				'value' => '1',
				'selected' => true,
				'label' => 'Alertas',
				'_type' => 'option',
			),
			array(
				'name' => 'send_email',
				'value' => '1',
				'label' => 'E-mails',
				'_type' => 'option',
			)), array(
				'label' => 'Enviar notificações via',
			)) . '
				';
		}
		$__compilerTemp1 .= '
			';
	}
	$__compilerTemp3 = '';
	if ($__vars['isWatched']) {
		$__compilerTemp3 .= '
			' . $__templater->formSubmitRow(array(
			'submit' => 'Deixar de seguir',
			'icon' => 'notificationsOff',
		), array(
			'rowtype' => 'simple',
		)) . '
		';
	} else {
		$__compilerTemp3 .= '
			' . $__templater->formSubmitRow(array(
			'submit' => 'Seguir',
			'icon' => 'notificationsOn',
		), array(
		)) . '
		';
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__compilerTemp1 . '
		</div>
		' . $__compilerTemp3 . '
	</div>
', array(
		'action' => $__templater->func('link', array('forums/watch', $__vars['forum'], ), false),
		'class' => 'block',
		'ajax' => 'true',
	));
	return $__finalCompiled;
});