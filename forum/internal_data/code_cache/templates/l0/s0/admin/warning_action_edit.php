<?php
// FROM HASH: b57cba9c4677fbe0a7e8450a59981939
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['action'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add warning action');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit warning action' . $__vars['xf']['language']['label_separator'] . ' ' . 'Points' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['action']['points']));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['action'], 'isUpdate', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array('warnings/actions/delete', $__vars['action'], ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

';
	$__compilerTemp1 = $__templater->mergeChoiceOptions(array(), $__vars['userGroups']);
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formNumberBoxRow(array(
		'name' => 'points',
		'value' => $__vars['action']['points'],
		'min' => '1',
	), array(
		'label' => 'Points threshold',
		'explain' => 'This warning action will only be applied when a user crosses the points threshold. As such, users with this many points or more will not have this action applied until their warning point total drops below this threshold and then crosses it again.',
	)) . '

			' . $__templater->formRadioRow(array(
		'name' => 'action',
		'value' => $__vars['action']['action'],
	), array(array(
		'value' => 'ban',
		'label' => 'Ban',
		'_type' => 'option',
	),
	array(
		'value' => 'discourage',
		'label' => 'Discourage',
		'_type' => 'option',
	),
	array(
		'value' => 'groups',
		'label' => 'Add to selected groups',
		'_dependent' => array($__templater->formCheckBox(array(
		'name' => 'extra_user_group_ids',
		'value' => $__vars['action']['extra_user_group_ids'],
	), $__compilerTemp1)),
		'_type' => 'option',
	)), array(
		'label' => 'Action to take',
	)) . '

			' . $__templater->formRadioRow(array(
		'name' => 'action_length_type_base',
		'value' => $__vars['action']['action_length_type'],
	), array(array(
		'value' => 'points',
		'label' => 'While at or above points threshold',
		'_type' => 'option',
	),
	array(
		'value' => 'permanent',
		'label' => 'Permanent',
		'_type' => 'option',
	),
	array(
		'value' => 'temporary',
		'selected' => ($__vars['action']['action_length_type'] != 'permanent') AND ($__vars['action']['action_length_type'] != 'points'),
		'label' => 'Temporary',
		'_dependent' => array('
						<div class="inputGroup">
							' . $__templater->formNumberBox(array(
		'name' => 'action_length',
		'value' => ($__vars['action']['action_length'] ?: 1),
		'min' => '1',
	)) . '
							<span class="inputGroup-splitter"></span>
							' . $__templater->formSelect(array(
		'name' => 'action_length_type',
		'value' => ((($__vars['action']['action_length_type'] == 'permanent') OR ($__vars['action']['action_length_type'] == 'points')) ? 'months' : $__vars['action']['action_length_type']),
		'class' => 'input--inline',
	), array(array(
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
	),
	array(
		'value' => 'years',
		'label' => 'Years',
		'_type' => 'option',
	))) . '
						</div>
					'),
		'_type' => 'option',
	)), array(
		'label' => 'For time period',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'sticky' => 'true',
		'icon' => 'save',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('warnings/actions/save', $__vars['action'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});