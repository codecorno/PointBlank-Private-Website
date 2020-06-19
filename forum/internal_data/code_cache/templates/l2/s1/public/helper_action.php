<?php
// FROM HASH: 14ed92df14b43da25d62aaa5c6fb51ed
return array('macros' => array('edit_type' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'canEditSilently' => false,
		'silentName' => 'silent',
		'clearEditName' => 'clear_edit',
		'silentEdit' => false,
		'clearEdit' => false,
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	if ($__vars['canEditSilently']) {
		$__finalCompiled .= '
		' . $__templater->formCheckBox(array(
		), array(array(
			'name' => $__vars['silentName'],
			'checked' => $__vars['silentEdit'],
			'label' => 'Editar silenciosamente',
			'hint' => 'Se selecionado, nenhuma nota "última edição" será adicionada para esta edição.',
			'_dependent' => array($__templater->formCheckBox(array(
		), array(array(
			'name' => $__vars['clearEditName'],
			'checked' => $__vars['clearEdit'],
			'label' => 'Limpar as últimas informações de edição',
			'hint' => 'Se selecionada, qualquer nota "editada pela última vez" será removida.',
			'_type' => 'option',
		)))),
			'_type' => 'option',
		))) . '
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'delete_type' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'canHardDelete' => false,
		'typeName' => 'hard_delete',
		'reasonName' => 'reason',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	if ($__vars['canHardDelete']) {
		$__finalCompiled .= '
		' . $__templater->formRadioRow(array(
			'name' => $__vars['typeName'],
			'value' => '0',
		), array(array(
			'value' => '0',
			'label' => 'Remover da vista do público',
			'_dependent' => array($__templater->formTextBox(array(
			'name' => $__vars['reasonName'],
			'placeholder' => 'Motivo' . $__vars['xf']['language']['ellipsis'],
			'maxlength' => $__templater->func('max_length', array('XF:DeletionLog', 'delete_reason', ), false),
		))),
			'_type' => 'option',
		),
		array(
			'value' => '1',
			'label' => 'Excluir permanentemente',
			'hint' => 'Selecionando essa opção, o item será excluído permanentemente e irreversivelmente.',
			'_type' => 'option',
		)), array(
			'label' => 'Tipo de exclusão',
		)) . '
	';
	} else {
		$__finalCompiled .= '
		' . $__templater->formTextBoxRow(array(
			'name' => $__vars['reasonName'],
			'maxlength' => $__templater->func('max_length', array('XF:DeletionLog', 'delete_reason', ), false),
		), array(
			'label' => 'Motivo da exclusão',
		)) . '

		' . $__templater->formHiddenVal($__vars['typeName'], '0', array(
		)) . '
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'author_alert' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'selected' => false,
		'alertName' => 'author_alert',
		'reasonName' => 'author_alert_reason',
		'row' => true,
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	$__vars['checkbox'] = $__templater->preEscaped('
		' . $__templater->formCheckBox(array(
	), array(array(
		'name' => $__vars['alertName'],
		'selected' => $__vars['selected'],
		'label' => 'Notificar o autor desta ação.' . ' ' . 'Motivo' . $__vars['xf']['language']['label_separator'],
		'_dependent' => array($__templater->formTextBox(array(
		'name' => $__vars['reasonName'],
		'placeholder' => 'Opcional',
	))),
		'afterhint' => 'Note that the author will see this alert even if they can no longer view their content.',
		'_type' => 'option',
	))) . '
	');
	$__finalCompiled .= '
	';
	if ($__vars['row']) {
		$__finalCompiled .= '
		' . $__templater->formRow('
			' . $__templater->filter($__vars['checkbox'], array(array('raw', array()),), true) . '
		', array(
		)) . '
	';
	} else {
		$__finalCompiled .= '
		' . $__templater->filter($__vars['checkbox'], array(array('raw', array()),), true) . '
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'thread_alert' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'selected' => false,
		'alertName' => 'starter_alert',
		'reasonName' => 'starter_alert_reason',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => $__vars['alertName'],
		'selected' => $__vars['selected'],
		'label' => 'Notificar o criador do tópico nesta ação.' . ' ' . 'Motivo' . $__vars['xf']['language']['label_separator'],
		'_dependent' => array($__templater->formTextBox(array(
		'name' => $__vars['reasonName'],
		'placeholder' => 'Opcional',
	))),
		'afterhint' => 'Observe que o criador do tópico verá esse alerta mesmo que não consiga mais visualizar o tópico.',
		'_type' => 'option',
	)), array(
	)) . '
';
	return $__finalCompiled;
},
'thread_redirect' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'label' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	' . $__templater->formRadioRow(array(
		'name' => 'redirect_type',
		'value' => 'none',
	), array(array(
		'value' => 'none',
		'label' => 'Não deixe um redirecionamento',
		'_type' => 'option',
	),
	array(
		'value' => 'permanent',
		'label' => 'Deixe um redirecionamento permanente',
		'_type' => 'option',
	),
	array(
		'value' => 'temporary',
		'label' => 'Leave a redirect that expires after' . $__vars['xf']['language']['label_separator'],
		'_dependent' => array('
				<div class="inputGroup">
					' . $__templater->formNumberBox(array(
		'name' => 'redirect_length[amount]',
		'value' => '1',
		'min' => '0',
	)) . '
					<span class="inputGroup-splitter"></span>
					' . $__templater->formSelect(array(
		'name' => 'redirect_length[unit]',
		'value' => 'days',
		'class' => 'input--inline',
	), array(array(
		'value' => 'hours',
		'label' => 'Horas',
		'_type' => 'option',
	),
	array(
		'value' => 'days',
		'label' => 'Dias',
		'_type' => 'option',
	),
	array(
		'value' => 'weeks',
		'label' => 'Semanas',
		'_type' => 'option',
	),
	array(
		'value' => 'months',
		'label' => 'Meses',
		'_type' => 'option',
	))) . '
				</div>
			'),
		'_type' => 'option',
	)), array(
		'label' => 'Aviso de redirecionamento',
	)) . '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '

' . '

' . '

' . '

';
	return $__finalCompiled;
});