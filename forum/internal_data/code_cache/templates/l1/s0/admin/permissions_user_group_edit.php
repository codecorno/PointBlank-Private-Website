<?php
// FROM HASH: 93e9452a7d7ee3d860dc304932158e11
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped($__templater->escape($__vars['userGroup']['title']));
	$__finalCompiled .= '

' . $__templater->form('
	' . $__templater->callMacro('permission_macros', 'edit_outer', array(
		'type' => 'global',
	), $__vars) . '

	<div class="block-container">
		' . $__templater->callMacro('permission_macros', 'edit_groups', array(
		'interfaceGroups' => $__vars['permissionData']['interfaceGroups'],
		'permissionsGrouped' => $__vars['permissionData']['permissionsGrouped'],
		'values' => $__vars['permissionData']['values'],
	), $__vars) . '

		' . $__templater->formSubmitRow(array(
		'sticky' => 'true',
		'icon' => 'save',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('permissions/user-groups/save', $__vars['userGroup'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});