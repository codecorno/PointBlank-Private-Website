<?php
// FROM HASH: c4d54a1d35bc00b7de23195d7b65e43f
return array('macros' => array('common_options' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'field' => '!',
		'supportsRequired' => true,
		'supportsUserEditable' => false,
		'supportsEditableOnce' => false,
		'supportsModeratorEditable' => false,
		'supportsGroupEditable' => false,
	), $__arguments, $__vars);
	$__finalCompiled .= '

	';
	$__compilerTemp1 = array();
	if ($__vars['supportsRequired']) {
		$__compilerTemp1[] = array(
			'name' => 'required',
			'selected' => $__vars['field']['required'],
			'label' => 'Field is required',
			'_type' => 'option',
		);
	}
	if ($__vars['supportsUserEditable']) {
		$__compilerTemp2 = array();
		if ($__vars['supportsEditableOnce']) {
			$__compilerTemp2[] = array(
				'name' => 'user_editable',
				'value' => 'once',
				'selected' => $__vars['field']['user_editable'] == 'once',
				'label' => 'Editable only once',
				'_type' => 'option',
			);
		}
		$__compilerTemp1[] = array(
			'name' => 'user_editable',
			'value' => 'yes',
			'selected' => $__vars['field']['user_editable'] != 'never',
			'label' => 'User editable',
			'_dependent' => array($__templater->formCheckBox(array(
		), $__compilerTemp2)),
			'_type' => 'option',
		);
	}
	if ($__vars['supportsModeratorEditable']) {
		$__compilerTemp1[] = array(
			'name' => 'moderator_editable',
			'selected' => $__vars['field']['moderator_editable'],
			'label' => 'Moderator editable',
			'_type' => 'option',
		);
	}
	$__finalCompiled .= $__templater->formCheckBoxRow(array(
		'hideempty' => 'true',
	), $__compilerTemp1, array(
	)) . '

	';
	if ($__vars['supportsGroupEditable']) {
		$__finalCompiled .= '
		' . $__templater->callMacro('helper_user_group_edit', 'checkboxes', array(
			'label' => 'Editable by user groups',
			'id' => 'editable_user_group',
			'selectedUserGroups' => ($__vars['field']['field_id'] ? $__vars['field']['editable_user_group_ids'] : array(-1, )),
		), $__vars) . '
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