<?php
// FROM HASH: 74a0c6248ddb4ecb6be7e55c861d9124
return array('macros' => array('edit_outer' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'type' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	$__templater->includeJs(array(
		'src' => 'xf/permission.js',
		'min' => '1',
	));
	$__finalCompiled .= '
	';
	$__templater->includeCss('permission.less');
	$__finalCompiled .= '

	<div class="block-outer"
		data-xf-init="permission-form"
		data-form="< form"
		data-permission-type="' . $__templater->escape($__vars['type']) . '">

		<div class="block-outer-main js-globalPermissionQuickSet">
			' . $__templater->button('Quick set', array(
		'class' => 'button--link menuTrigger',
		'data-xf-click' => 'menu',
		'aria-expanded' => 'false',
		'aria-haspopup' => 'true',
	), '', array(
	)) . '
			' . $__templater->callMacro(null, 'quick_set_menu', array(
		'type' => $__vars['type'],
	), $__vars) . '
		</div>
		<div class="block-outer-opposite quickFilter">
			<input type="text" class="input js-permissionFilterInput" placeholder="' . $__templater->filter('Filter' . $__vars['xf']['language']['ellipsis'], array(array('for_attr', array()),), true) . '" />
		</div>
	</div>

';
	return $__finalCompiled;
},
'quick_set_menu' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'type' => '!',
		'target' => '',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	<div class="menu" data-menu="menu" aria-hidden="true">
		<div class="menu-content">
			<h3 class="menu-header">' . 'Quick set' . '</h3>
			';
	if ($__vars['type'] == 'global') {
		$__finalCompiled .= '
				<a class="menu-linkRow js-permissionQuickSet" data-target="' . $__templater->escape($__vars['target']) . '" data-value="allow" tabindex="0">
					' . 'Set all to: ' . 'Yes' . '' . '
				</a>
				<a class="menu-linkRow js-permissionQuickSet" data-target="' . $__templater->escape($__vars['target']) . '" data-value="unset" tabindex="0">
					' . 'Set all to: ' . 'No' . '' . '
				</a>
				<a class="menu-linkRow js-permissionQuickSet" data-target="' . $__templater->escape($__vars['target']) . '" data-value="deny" tabindex="0">
					' . 'Set all to: ' . 'Never' . '' . '
				</a>
			';
	} else if ($__vars['type'] == 'content') {
		$__finalCompiled .= '
				<a class="menu-linkRow js-permissionQuickSet" data-target="' . $__templater->escape($__vars['target']) . '" data-value="unset" tabindex="0">
					' . 'Set all to: ' . 'Inherit' . '' . '
				</a>
				<a class="menu-linkRow js-permissionQuickSet" data-target="' . $__templater->escape($__vars['target']) . '" data-value="content_allow" tabindex="0">
					' . 'Set all to: ' . 'Yes' . '' . '
				</a>
				<a class="menu-linkRow js-permissionQuickSet" data-target="' . $__templater->escape($__vars['target']) . '" data-value="reset" tabindex="0">
					' . 'Set all to: ' . 'No' . '' . '
				</a>
				<a class="menu-linkRow js-permissionQuickSet" data-target="' . $__templater->escape($__vars['target']) . '" data-value="deny" tabindex="0">
					' . 'Set all to: ' . 'Never' . '' . '
				</a>
			';
	}
	$__finalCompiled .= '
		</div>
	</div>
';
	return $__finalCompiled;
},
'edit_groups' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'interfaceGroups' => '!',
		'permissionsGrouped' => '!',
		'values' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	if ($__templater->isTraversable($__vars['interfaceGroups'])) {
		foreach ($__vars['interfaceGroups'] AS $__vars['interfaceGroupId'] => $__vars['interfaceGroup']) {
			$__finalCompiled .= '
		';
			if (!$__templater->test($__vars['permissionsGrouped'][$__vars['interfaceGroupId']], 'empty', array())) {
				$__finalCompiled .= '
			<h3 class="block-formSectionHeader">
				<span class="collapseTrigger collapseTrigger--block is-active" data-xf-click="toggle" data-target="< :up:next">
					<span class="block-formSectionHeader-aligner">' . $__templater->escape($__vars['interfaceGroup']['title']) . '</span>
				</span>
			</h3>
			<div class="block-body block-body--collapsible is-active"
				data-moderator-permissions="' . $__templater->escape($__vars['interfaceGroup']['is_moderator']) . '"
				id="permGroup-' . $__templater->escape($__vars['interfaceGroupId']) . '">

				';
				if ($__templater->func('count', array($__vars['permissionsGrouped'][$__vars['interfaceGroupId']], ), false) > 2) {
					$__finalCompiled .= '
					' . $__templater->formRow('
						' . $__templater->button('Quick set', array(
						'class' => 'button--link menuTrigger',
						'data-xf-click' => 'menu',
						'aria-expanded' => 'false',
						'aria-haspopup' => 'true',
					), '', array(
					)) . '
						' . $__templater->callMacro(null, 'quick_set_menu', array(
						'type' => 'global',
						'target' => '#permGroup-' . $__vars['interfaceGroupId'],
					), $__vars) . '
					', array(
						'rowclass' => 'formRow--permissionQuickSet',
					)) . '
				';
				}
				$__finalCompiled .= '
				';
				if ($__templater->isTraversable($__vars['permissionsGrouped'][$__vars['interfaceGroupId']])) {
					foreach ($__vars['permissionsGrouped'][$__vars['interfaceGroupId']] AS $__vars['permission']) {
						$__finalCompiled .= '
					' . $__templater->callMacro(null, 'edit_permission', array(
							'permission' => $__vars['permission'],
							'value' => $__vars['values'][$__vars['permission']['permission_group_id']][$__vars['permission']['permission_id']],
						), $__vars) . '
				';
					}
				}
				$__finalCompiled .= '
			</div>
		';
			}
			$__finalCompiled .= '
	';
		}
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'edit_permission' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'permission' => '!',
		'value' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	$__vars['inputName'] = 'permissions[' . $__vars['permission']['permission_group_id'] . '][' . $__vars['permission']['permission_id'] . ']';
	$__finalCompiled .= '
	';
	$__compilerTemp1 = '';
	if ($__vars['permission']['permission_type'] == 'flag') {
		$__compilerTemp1 .= '
			<ul class="permissionChoices permissionChoices--flag">
				<li class="permissionChoices-choice permissionChoices-choice--yes">
					' . $__templater->formRadio(array(
			'name' => $__vars['inputName'],
			'value' => $__vars['value'],
			'standalone' => 'true',
		), array(array(
			'value' => 'allow',
			'label' => 'Yes',
			'_type' => 'option',
		))) . '
				</li>
				<li class="permissionChoices-choice permissionChoices-choice--no">
					' . $__templater->formRadio(array(
			'name' => $__vars['inputName'],
			'value' => ((($__vars['value'] == 'unset') OR (!$__vars['value'])) ? 'unset' : ''),
			'standalone' => 'true',
		), array(array(
			'value' => 'unset',
			'label' => 'No',
			'_type' => 'option',
		))) . '
				</li>
				<li class="permissionChoices-choice permissionChoices-choice--never">
					' . $__templater->formRadio(array(
			'name' => $__vars['inputName'],
			'value' => $__vars['value'],
			'standalone' => 'true',
		), array(array(
			'value' => 'deny',
			'label' => 'Never',
			'_type' => 'option',
		))) . '
				</li>
			</ul>
		';
	} else {
		$__compilerTemp1 .= '
			<ul class="permissionChoices permissionChoices--int">
				<li class="permissionChoices-choice permissionChoices-choice--yes">
					' . $__templater->formRadio(array(
			'name' => $__vars['inputName'],
			'value' => $__vars['value'],
			'standalone' => 'true',
		), array(array(
			'value' => '-1',
			'label' => 'Unlimited',
			'_type' => 'option',
		))) . '
				</li>
				<li class="permissionChoices-choice permissionChoices-choice--int">
					<div class="inputGroup inputGroup--inline inputGroup--joined inputNumber" data-xf-init="number-box">
						<span class="inputGroup-text">
							' . $__templater->formRadio(array(
			'name' => $__vars['inputName'],
			'value' => $__vars['value'],
			'standalone' => 'true',
		), array(array(
			'value' => '0',
			'selected' => ($__vars['value'] >= 0),
			'data-xf-init' => 'disabler',
			'data-container' => '< li',
			'label' => 'As follows' . $__vars['xf']['language']['label_separator'],
			'hiddenlabel' => 'true',
			'id' => 'perm_' . $__vars['permission']['permission_group_id'] . '_' . $__vars['permission']['permission_id'] . '_radio',
			'_type' => 'option',
		))) . '
							<label class="permissionChoices-choiceIntLabel" for="perm_' . $__templater->escape($__vars['permission']['permission_group_id']) . '_' . $__templater->escape($__vars['permission']['permission_id']) . '_radio"></label>
						</span>
						' . $__templater->formTextBox(array(
			'name' => $__vars['inputName'],
			'value' => (($__vars['value'] >= 0) ? $__templater->filter($__vars['value'], array(array('default', array(0, )),), false) : 0),
			'type' => 'number',
			'class' => 'input--number js-numberBoxTextInput js-permissionIntInput',
			'min' => '0',
			'pattern' => '\\d*',
		)) . '
					</div>
				</li>
			</ul>
		';
	}
	$__finalCompiled .= $__templater->formRow('

		' . $__compilerTemp1 . '
	', array(
		'label' => $__templater->escape($__vars['permission']['title']),
		'rowclass' => 'js-permission',
		'data-xf-init' => 'permission-choice',
		'data-permission-type' => $__vars['permission']['permission_type'],
	)) . '
';
	return $__finalCompiled;
},
'content_edit_groups' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'interfaceGroups' => '!',
		'permissionsGrouped' => '!',
		'values' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	if ($__templater->isTraversable($__vars['interfaceGroups'])) {
		foreach ($__vars['interfaceGroups'] AS $__vars['interfaceGroupId'] => $__vars['interfaceGroup']) {
			$__finalCompiled .= '
		';
			if (!$__templater->test($__vars['permissionsGrouped'][$__vars['interfaceGroupId']], 'empty', array())) {
				$__finalCompiled .= '
			<h3 class="block-formSectionHeader">
				<span class="collapseTrigger collapseTrigger--block is-active" data-xf-click="toggle" data-target="< :up:next">
					<span class="block-formSectionHeader-aligner">' . $__templater->escape($__vars['interfaceGroup']['title']) . '</span>
				</span>
			</h3>
			<div class="block-body block-body--collapsible is-active"
				data-moderator-permissions="' . $__templater->escape($__vars['interfaceGroup']['is_moderator']) . '"
				id="permGroup-' . $__templater->escape($__vars['interfaceGroupId']) . '">

				';
				if ($__templater->func('count', array($__vars['permissionsGrouped'][$__vars['interfaceGroupId']], ), false) > 2) {
					$__finalCompiled .= '
					' . $__templater->formRow('
						' . $__templater->button('Quick set', array(
						'class' => 'button--link menuTrigger',
						'data-xf-click' => 'menu',
						'aria-expanded' => 'false',
						'aria-haspopup' => 'true',
					), '', array(
					)) . '
						' . $__templater->callMacro(null, 'quick_set_menu', array(
						'type' => 'content',
						'target' => '#permGroup-' . $__vars['interfaceGroupId'],
					), $__vars) . '
					', array(
						'rowclass' => 'formRow--permissionQuickSet',
					)) . '
				';
				}
				$__finalCompiled .= '
				';
				if ($__templater->isTraversable($__vars['permissionsGrouped'][$__vars['interfaceGroupId']])) {
					foreach ($__vars['permissionsGrouped'][$__vars['interfaceGroupId']] AS $__vars['permission']) {
						$__finalCompiled .= '
					' . $__templater->callMacro(null, 'content_edit_permission', array(
							'permission' => $__vars['permission'],
							'value' => $__vars['values'][$__vars['permission']['permission_group_id']][$__vars['permission']['permission_id']],
						), $__vars) . '
				';
					}
				}
				$__finalCompiled .= '
			</div>
		';
			}
			$__finalCompiled .= '
	';
		}
	}
	$__finalCompiled .= '
';
	return $__finalCompiled;
},
'content_edit_permission' => function($__templater, array $__arguments, array $__vars)
{
	$__vars = $__templater->setupBaseParamsForMacro($__vars, false);
	$__finalCompiled = '';
	$__vars = $__templater->mergeMacroArguments(array(
		'permission' => '!',
		'value' => '!',
	), $__arguments, $__vars);
	$__finalCompiled .= '
	';
	$__vars['inputName'] = 'permissions[' . $__vars['permission']['permission_group_id'] . '][' . $__vars['permission']['permission_id'] . ']';
	$__finalCompiled .= '
	';
	$__compilerTemp1 = '';
	if ($__vars['permission']['permission_type'] == 'flag') {
		$__compilerTemp1 .= '
			<ul class="permissionChoices permissionChoices--flag">
				<li class="permissionChoices-choice permissionChoices-choice--inherit">
					' . $__templater->formRadio(array(
			'name' => $__vars['inputName'],
			'value' => (((!$__vars['value']) OR ($__vars['value'] == 'unset')) ? 'unset' : ''),
			'standalone' => 'true',
		), array(array(
			'value' => 'unset',
			'label' => 'Inherit',
			'_type' => 'option',
		))) . '
				</li>
				<li class="permissionChoices-choice permissionChoices-choice--yes">
					' . $__templater->formRadio(array(
			'name' => $__vars['inputName'],
			'value' => $__vars['value'],
			'standalone' => 'true',
		), array(array(
			'value' => 'content_allow',
			'label' => 'Yes',
			'_type' => 'option',
		))) . '
				</li>
				<li class="permissionChoices-choice permissionChoices-choice--no">
					' . $__templater->formRadio(array(
			'name' => $__vars['inputName'],
			'value' => $__vars['value'],
			'standalone' => 'true',
		), array(array(
			'value' => 'reset',
			'label' => 'No',
			'_type' => 'option',
		))) . '
				</li>
				<li class="permissionChoices-choice permissionChoices-choice--never">
					' . $__templater->formRadio(array(
			'name' => $__vars['inputName'],
			'value' => $__vars['value'],
			'standalone' => 'true',
		), array(array(
			'value' => 'deny',
			'label' => 'Never',
			'_type' => 'option',
		))) . '
				</li>
			</ul>
		';
	} else {
		$__compilerTemp1 .= '
			<ul class="permissionChoices permissionChoices--int">
				<li class="permissionChoices-choice permissionChoices-choice--inherit">
					' . $__templater->formRadio(array(
			'name' => $__vars['inputName'],
			'value' => (((!$__vars['value']) OR ($__vars['value'] == 0)) ? 0 : null),
			'standalone' => 'true',
		), array(array(
			'value' => '0',
			'label' => 'Inherit',
			'_type' => 'option',
		))) . '
				</li>
				<li class="permissionChoices-choice permissionChoices-choice--yes">
					' . $__templater->formRadio(array(
			'name' => $__vars['inputName'],
			'value' => $__vars['value'],
			'standalone' => 'true',
		), array(array(
			'value' => '-1',
			'label' => 'Unlimited',
			'_type' => 'option',
		))) . '
				</li>
				<li class="permissionChoices-choice permissionChoices-choice--int">
					<div class="inputGroup inputGroup--inline inputGroup--joined inputNumber" data-xf-init="number-box">
						<span class="inputGroup-text">
							' . $__templater->formRadio(array(
			'name' => $__vars['inputName'],
			'standalone' => 'true',
		), array(array(
			'value' => '1',
			'selected' => ($__vars['value'] >= 1),
			'data-xf-init' => 'disabler',
			'data-container' => '< li',
			'label' => 'As follows' . $__vars['xf']['language']['label_separator'],
			'hiddenlabel' => 'true',
			'id' => 'perm_' . $__vars['permission']['permission_group_id'] . '_' . $__vars['permission']['permission_id'] . '_radio',
			'_type' => 'option',
		))) . '
							<label class="permissionChoices-choiceIntLabel" for="perm_' . $__templater->escape($__vars['permission']['permission_group_id']) . '_' . $__templater->escape($__vars['permission']['permission_id']) . '_radio"></label>
						</span>
						' . $__templater->formTextBox(array(
			'type' => 'number',
			'name' => $__vars['inputName'],
			'value' => (($__vars['value'] >= 1) ? $__templater->filter($__vars['value'], array(array('default', array(1, )),), false) : 1),
			'class' => 'input--number js-numberBoxTextInput js-permissionIntInput',
			'min' => '0',
			'pattern' => '\\d*',
		)) . '
					</div>
				</li>
			</ul>
		';
	}
	$__finalCompiled .= $__templater->formRow('

		' . $__compilerTemp1 . '
	', array(
		'label' => $__templater->escape($__vars['permission']['title']),
		'rowclass' => 'js-permission',
		'data-xf-init' => 'permission-choice',
		'data-permission-type' => $__vars['permission']['permission_type'],
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

' . '

';
	return $__finalCompiled;
});