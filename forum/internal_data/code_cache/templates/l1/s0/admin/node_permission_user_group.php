<?php
// FROM HASH: 03c432383b6d123867b95111047cc32a
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped($__templater->escape($__vars['node']['title']) . ' - ' . $__templater->escape($__vars['userGroup']['title']) . ' - ' . 'Permissions');
	$__finalCompiled .= '

' . $__templater->form('

	' . $__templater->callMacro('permission_macros', 'edit_outer', array(
		'type' => 'content',
	), $__vars) . '

	<div class="block-container">
		' . $__templater->callMacro('permission_macros', 'content_edit_groups', array(
		'permissionsGrouped' => $__vars['permissionData']['permissionsGrouped'],
		'interfaceGroups' => $__vars['permissionData']['interfaceGroups'],
		'values' => $__vars['groupEntries'],
	), $__vars) . '
		' . $__templater->formSubmitRow(array(
		'sticky' => 'true',
		'icon' => 'save',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('nodes/permissions/user-group', $__vars['node'], array('user_group_id' => $__vars['userGroup']['user_group_id'], ), ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});