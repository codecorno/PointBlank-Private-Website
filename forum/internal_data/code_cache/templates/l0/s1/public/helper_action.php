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
			'label' => 'Edit silently',
			'hint' => 'If selected, no "last edited" note will be added for this edit.',
			'_dependent' => array($__templater->formCheckBox(array(
		), array(array(
			'name' => $__vars['clearEditName'],
			'checked' => $__vars['clearEdit'],
			'label' => 'Clear last edit information',
			'hint' => 'If selected, any existing "last edited" note will be removed.',
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
			'label' => 'Remove from public view',
			'_dependent' => array($__templater->formTextBox(array(
			'name' => $__vars['reasonName'],
			'placeholder' => 'Reason' . $__vars['xf']['language']['ellipsis'],
			'maxlength' => $__templater->func('max_length', array('XF:DeletionLog', 'delete_reason', ), false),
		))),
			'_type' => 'option',
		),
		array(
			'value' => '1',
			'label' => 'Permanently delete',
			'hint' => 'Selecting this option will permanently and irreversibly delete the item.',
			'_type' => 'option',
		)), array(
			'label' => 'Deletion type',
		)) . '
	';
	} else {
		$__finalCompiled .= '
		' . $__templater->formTextBoxRow(array(
			'name' => $__vars['reasonName'],
			'maxlength' => $__templater->func('max_length', array('XF:DeletionLog', 'delete_reason', ), false),
		), array(
			'label' => 'Reason for deletion',
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
		'label' => 'Notify author of this action.' . ' ' . 'Reason' . $__vars['xf']['language']['label_separator'],
		'_dependent' => array($__templater->formTextBox(array(
		'name' => $__vars['reasonName'],
		'placeholder' => 'Optional',
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
		'label' => 'Notify thread starter of this action.' . ' ' . 'Reason' . $__vars['xf']['language']['label_separator'],
		'_dependent' => array($__templater->formTextBox(array(
		'name' => $__vars['reasonName'],
		'placeholder' => 'Optional',
	))),
		'afterhint' => 'Note that the thread starter will see this alert even if they can no longer view their thread.',
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
		'label' => 'Do not leave a redirect',
		'_type' => 'option',
	),
	array(
		'value' => 'permanent',
		'label' => 'Leave a permanent redirect',
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
		'label' => 'Hours',
		'_type' => 'option',
	),
	array(
		'value' => 'days',
		'label' => 'Days',
		'_type' => 'option',
	),
	array(
		'value' => 'weeks',
		'label' => 'Weeks',
		'_type' => 'option',
	),
	array(
		'value' => 'months',
		'label' => 'Months',
		'_type' => 'option',
	))) . '
				</div>
			'),
		'_type' => 'option',
	)), array(
		'label' => 'Redirection notice',
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