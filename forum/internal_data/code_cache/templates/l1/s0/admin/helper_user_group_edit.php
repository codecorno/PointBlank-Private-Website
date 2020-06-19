<?php
// FROM HASH: 9d223fe0eaf25e29cb1d6309fab2781f
return array('macros' => array('checkboxes' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'label' => 'Usable by user groups',
		'id' => 'usable_user_group',
		'userGroups' => '',
		'selectedUserGroups' => '!',
		'withRow' => '1',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	';
	if (!$__vars['userGroups']) {
		$__finalCompiled .= '
		';
		$__vars['userGroupRepo'] = $__templater->method($__vars['xf']['app']['em'], 'getRepository', array('XF:UserGroup', ));
		$__finalCompiled .= '
		';
		$__vars['userGroups'] = $__templater->method($__vars['userGroupRepo'], 'getUserGroupTitlePairs', array());
		$__finalCompiled .= '
	';
	}
	$__finalCompiled .= '
	';
	$__vars['allUserGroups'] = (($__templater->func('array_keys', array($__vars['userGroups'], ), false) == $__vars['selectedUserGroups']) OR $__templater->func('in_array', array('-1', $__vars['selectedUserGroups'], ), false));
	$__finalCompiled .= '

	';
	$__compilerTemp1 = array();
	if ($__templater->isTraversable($__vars['userGroups'])) {
		foreach ($__vars['userGroups'] AS $__vars['userGroupId'] => $__vars['userGroupTitle']) {
			$__compilerTemp1[] = array(
				'value' => $__vars['userGroupId'],
				'selected' => ($__templater->func('in_array', array($__vars['userGroupId'], $__vars['selectedUserGroups'], ), false) OR $__vars['allUserGroups']),
				'label' => '
								' . $__templater->escape($__vars['userGroupTitle']) . '
							',
				'_type' => 'option',
			);
		}
	}
	$__vars['inner'] = $__templater->preEscaped('
		' . $__templater->formRadio(array(
		'name' => $__vars['id'],
		'id' => $__vars['id'],
	), array(array(
		'value' => 'all',
		'selected' => $__vars['allUserGroups'],
		'label' => 'All user groups',
		'_type' => 'option',
	),
	array(
		'value' => 'sel',
		'selected' => !$__vars['allUserGroups'],
		'label' => 'Selected user groups' . $__vars['xf']['language']['label_separator'],
		'_dependent' => array('
					' . $__templater->formCheckBox(array(
		'name' => ($__vars['id'] . '_ids'),
		'listclass' => 'listColumns',
	), $__compilerTemp1) . '

					' . $__templater->formCheckBox(array(
	), array(array(
		'data-xf-init' => 'check-all',
		'data-container' => ('#' . $__vars['id']),
		'label' => 'Select all',
		'_type' => 'option',
	))) . '
				'),
		'_type' => 'option',
	))) . '
	');
	$__finalCompiled .= '

	';
	if ($__vars['withRow']) {
		$__finalCompiled .= '
		' . $__templater->formRow('
			' . $__templater->filter($__vars['inner'], array(array('raw', array()),), true) . '
		', array(
			'label' => $__templater->escape($__vars['label']),
			'name' => $__vars['id'],
			'id' => $__vars['id'],
		)) . '
	';
	} else {
		$__finalCompiled .= '
		' . $__templater->filter($__vars['inner'], array(array('raw', array()),), true) . '
	';
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';

	return $__finalCompiled;
});