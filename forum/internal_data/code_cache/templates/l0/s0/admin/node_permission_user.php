<?php
// FROM HASH: 6b076fda82353ee022a4a24ca7cfeb0f
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped($__templater->escape($__vars['node']['title']) . ' - ' . $__templater->escape($__vars['user']['username']) . ' - ' . 'Permissions');
	$__finalCompiled .= '

' . $__templater->form('
	' . $__templater->callMacro('permission_macros', 'edit_outer', array(
		'type' => 'content',
	), $__vars) . '

	<div class="block-container">
		' . $__templater->callMacro('permission_macros', 'content_edit_groups', array(
		'interfaceGroups' => $__vars['permissionData']['interfaceGroups'],
		'permissionsGrouped' => $__vars['permissionData']['permissionsGrouped'],
		'values' => $__vars['userEntries'],
	), $__vars) . '
		' . $__templater->formSubmitRow(array(
		'sticky' => 'true',
		'icon' => 'save',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('nodes/permissions/user', $__vars['node'], array('user_id' => $__vars['user']['user_id'], ), ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});