<?php
// FROM HASH: db93e1b74be8d582da511c4373b90a9a
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['admin'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add administrator');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit administrator' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['admin']['username']));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['admin'], 'isUpdate', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array('admins/delete', $__vars['admin'], ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if (!$__vars['admin']['user_id']) {
		$__compilerTemp1 .= '
				' . $__templater->formTextBoxRow(array(
			'name' => 'username',
			'ac' => 'single',
		), array(
			'label' => 'User',
		)) . '
			';
	}
	$__compilerTemp2 = $__templater->mergeChoiceOptions(array(), $__vars['userGroups']);
	$__compilerTemp3 = $__templater->mergeChoiceOptions(array(), $__vars['permissions']);
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formPasswordBoxRow(array(
		'name' => 'visitor_password',
	), array(
		'label' => 'Your password',
		'explain' => 'You must enter your current password to validate this request.',
	)) . '

			<hr class="formRowSep" />

			' . $__compilerTemp1 . '

			' . $__templater->formCheckBoxRow(array(
		'name' => 'extra_user_group_ids',
		'value' => $__vars['admin']['extra_user_group_ids'],
		'listclass' => 'listColumns',
	), $__compilerTemp2, array(
		'label' => 'Add user to user groups',
		'hint' => '<br />
					' . $__templater->formCheckBox(array(
		'standalone' => 'true',
	), array(array(
		'check-all' => '< .formRow',
		'label' => 'Select all',
		'_type' => 'option',
	))) . '
				',
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formRadioRow(array(
		'name' => 'is_super_admin',
		'value' => $__vars['admin']['is_super_admin'],
	), array(array(
		'value' => '1',
		'label' => 'Super administrator',
		'hint' => 'Super administrators have all administrator permissions and can manage other admins.',
		'_type' => 'option',
	),
	array(
		'value' => '0',
		'data-hide' => 'true',
		'data-xf-init' => 'disabler',
		'data-container' => '.js-adminPermissions',
		'label' => 'Regular administrator',
		'_type' => 'option',
	)), array(
		'label' => 'Administrator type',
	)) . '

			<div class="js-adminPermissions">
				' . $__templater->formCheckBoxRow(array(
		'name' => 'permission_cache',
		'value' => $__templater->func('array_keys', array($__vars['admin']['permission_cache'], ), false),
		'listclass' => 'listColumns',
	), $__compilerTemp3, array(
		'label' => 'Permissions',
		'hint' => '<br />
						' . $__templater->formCheckBox(array(
		'standalone' => 'true',
	), array(array(
		'check-all' => '< .formRow',
		'label' => 'Select all',
		'_type' => 'option',
	))) . '
					',
	)) . '
			</div>
		</div>
		' . $__templater->formSubmitRow(array(
		'sticky' => 'true',
		'icon' => 'save',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('admins/save', $__vars['admin'], ), false),
		'class' => 'block',
		'ajax' => 'true',
	));
	return $__finalCompiled;
});