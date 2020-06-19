<?php
// FROM HASH: 459fcfab16b6411f5773b4e8f8c6e7f1
return array('macros' => array('list' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'category' => '!',
		'isPrivate' => '!',
		'userGroups' => '!',
		'users' => '!',
		'entries' => '!',
		'routeBase' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	' . $__templater->form('
		<div class="block-container">
			<div class="block-body">
				' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'private',
		'selected' => $__vars['isPrivate'],
		'label' => 'Private category',
		'hint' => 'If selected, users will only be able to view this category if they are explicitly granted permissions.',
		'_type' => 'option',
	)), array(
	)) . '
			</div>
			' . $__templater->formSubmitRow(array(
		'icon' => 'save',
	), array(
	)) . '
		</div>
		' . $__templater->formHiddenVal('type', 'private', array(
	)) . '
	', array(
		'action' => $__templater->func('link', array($__vars['routeBase'] . '/save', $__vars['category'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	)) . '

	<div class="block">
		<div class="block-container">
			<h3 class="block-header">' . 'User groups' . '</h3>
			<div class="block-body">
				';
	$__compilerTemp1 = '';
	if ($__templater->isTraversable($__vars['userGroups'])) {
		foreach ($__vars['userGroups'] AS $__vars['userGroup']) {
			$__compilerTemp1 .= '
						' . $__templater->dataRow(array(
				'label' => $__templater->escape($__vars['userGroup']['title']),
				'href' => $__templater->func('link', array($__vars['routeBase'] . '/edit', $__vars['category'], array('user_group_id' => $__vars['userGroup']['user_group_id'], ), ), false),
				'rowclass' => ($__vars['entries']['groups'][$__vars['userGroup']['user_group_id']] ? 'dataList-row--custom' : ''),
			), array()) . '
					';
		}
	}
	$__finalCompiled .= $__templater->dataList('
					' . $__compilerTemp1 . '
				', array(
	)) . '
			</div>
		</div>
	</div>

	';
	$__compilerTemp2 = '';
	if (!$__templater->test($__vars['users'], 'empty', array())) {
		$__compilerTemp2 .= '
					';
		$__compilerTemp3 = '';
		if ($__templater->isTraversable($__vars['users'])) {
			foreach ($__vars['users'] AS $__vars['user']) {
				$__compilerTemp3 .= '
							' . $__templater->dataRow(array(
					'label' => $__templater->escape($__vars['user']['username']),
					'href' => $__templater->func('link', array($__vars['routeBase'] . '/edit', $__vars['category'], array('user_id' => $__vars['user']['user_id'], ), ), false),
					'rowclass' => 'dataList-row--custom',
				), array()) . '
						';
			}
		}
		$__compilerTemp2 .= $__templater->dataList('
						' . $__compilerTemp3 . '
					', array(
		)) . '
					<hr class="block-separator" />
				';
	}
	$__finalCompiled .= $__templater->form('
		<div class="block-container">
			<h3 class="block-header">' . 'Users with permissions set' . '</h3>
			<div class="block-body">
				' . $__compilerTemp2 . '
				' . $__templater->formTextBoxRow(array(
		'name' => 'username',
		'ac' => 'single',
	), array(
		'label' => 'Set permissions for user',
	)) . '
			</div>
			' . $__templater->formSubmitRow(array(
		'submit' => 'Proceed' . $__vars['xf']['language']['ellipsis'],
	), array(
	)) . '
		</div>
		' . $__templater->formHiddenVal('type', 'user', array(
	)) . '
	', array(
		'action' => $__templater->func('link', array($__vars['routeBase'] . '/edit', $__vars['category'], ), false),
		'class' => 'block',
	)) . '
';
	return $__finalCompiled;
},
'edit' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'category' => '!',
		'permissionData' => '!',
		'typeEntries' => '!',
		'routeBase' => '!',
		'saveParams' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '

	' . $__templater->form('
		' . $__templater->callMacro('permission_macros', 'edit_outer', array(
		'type' => 'content',
	), $__vars) . '

		<div class="block-container">
			' . $__templater->callMacro('permission_macros', 'content_edit_groups', array(
		'permissionsGrouped' => $__vars['permissionData']['permissionsGrouped'],
		'interfaceGroups' => $__vars['permissionData']['interfaceGroups'],
		'values' => $__vars['typeEntries'],
	), $__vars) . '
			' . $__templater->formSubmitRow(array(
		'sticky' => 'true',
		'icon' => 'save',
	), array(
	)) . '
		</div>
	', array(
		'action' => $__templater->func('link', array($__vars['routeBase'] . '/save', $__vars['category'], $__vars['saveParams'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	)) . '
';
	return $__finalCompiled;
},), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__finalCompiled .= '

';
	return $__finalCompiled;
});