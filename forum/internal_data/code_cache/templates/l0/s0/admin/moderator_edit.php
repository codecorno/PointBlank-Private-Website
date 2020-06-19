<?php
// FROM HASH: db67697da465e0abad77c1c0f5d3a36f
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit moderator' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['user']['username']));
	$__finalCompiled .= '

';
	if (($__vars['contentModerator'] ? $__templater->method($__vars['contentModerator'], 'isUpdate', array()) : $__templater->method($__vars['generalModerator'], 'isUpdate', array()))) {
		$__compilerTemp1 = '';
		if ($__vars['contentModerator']) {
			$__compilerTemp1 .= '
		' . $__templater->button('', array(
				'href' => $__templater->func('link', array('moderators/content/delete', $__vars['contentModerator'], ), false),
				'icon' => 'delete',
				'overlay' => 'true',
			), '', array(
			)) . '
	';
		} else {
			$__compilerTemp1 .= '
		' . $__templater->button('', array(
				'href' => $__templater->func('link', array('moderators/super/delete', $__vars['generalModerator'], ), false),
				'icon' => 'delete',
				'overlay' => 'true',
			), '', array(
			)) . '
	';
		}
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__compilerTemp1 . '
');
	}
	$__finalCompiled .= '

';
	$__compilerTemp2 = '';
	if ($__vars['contentTitle']) {
		$__compilerTemp2 .= '
					' . $__templater->escape($__vars['contentTitle']) . '
					';
	} else {
		$__compilerTemp2 .= '
					' . 'Super moderator' . '
				';
	}
	$__compilerTemp3 = $__templater->mergeChoiceOptions(array(), $__vars['userGroups']);
	$__compilerTemp4 = '';
	if ($__templater->isTraversable($__vars['interfaceGroups'])) {
		foreach ($__vars['interfaceGroups'] AS $__vars['interfaceGroupId'] => $__vars['interfaceGroup']) {
			$__compilerTemp4 .= '
					';
			if ($__templater->isTraversable($__vars['globalPermissions'][$__vars['interfaceGroupId']])) {
				foreach ($__vars['globalPermissions'][$__vars['interfaceGroupId']] AS $__vars['permission']) {
					$__compilerTemp4 .= '
						' . $__templater->formHiddenVal('globalPermissions[' . $__vars['permission']['permission_group_id'] . '][' . $__vars['permission']['permission_id'] . ']', 'unset', array(
					)) . '
					';
				}
			}
			$__compilerTemp4 .= '
					';
			if ($__templater->isTraversable($__vars['contentPermissions'][$__vars['interfaceGroupId']])) {
				foreach ($__vars['contentPermissions'][$__vars['interfaceGroupId']] AS $__vars['permission']) {
					$__compilerTemp4 .= '
						' . $__templater->formHiddenVal('contentPermissions[' . $__vars['permission']['permission_group_id'] . '][' . $__vars['permission']['permission_id'] . ']', 'unset', array(
					)) . '
					';
				}
			}
			$__compilerTemp4 .= '
				';
		}
	}
	$__compilerTemp5 = '';
	if ($__templater->isTraversable($__vars['interfaceGroups'])) {
		foreach ($__vars['interfaceGroups'] AS $__vars['interfaceGroupId'] => $__vars['interfaceGroup']) {
			$__compilerTemp5 .= '
					';
			if ($__vars['globalPermissions'][$__vars['interfaceGroupId']]) {
				$__compilerTemp5 .= '
						<hr class="formRowSep" />

						';
				$__compilerTemp6 = array();
				if ($__templater->isTraversable($__vars['globalPermissions'][$__vars['interfaceGroupId']])) {
					foreach ($__vars['globalPermissions'][$__vars['interfaceGroupId']] AS $__vars['permission']) {
						$__compilerTemp6[] = array(
							'name' => 'globalPermissions[' . $__vars['permission']['permission_group_id'] . '][' . $__vars['permission']['permission_id'] . ']',
							'value' => 'allow',
							'selected' => ($__vars['existingValues'][$__vars['permission']['permission_group_id']][$__vars['permission']['permission_id']] == 'allow'),
							'label' => $__templater->escape($__vars['permission']['title']),
							'_type' => 'option',
						);
					}
				}
				$__compilerTemp5 .= $__templater->formCheckBoxRow(array(
					'listclass' => 'listColumns',
				), $__compilerTemp6, array(
					'label' => $__templater->escape($__vars['interfaceGroup']['title']),
					'hint' => '
								' . $__templater->formCheckBox(array(
					'standalone' => 'true',
				), array(array(
					'check-all' => '< .formRow',
					'label' => 'Select all',
					'_type' => 'option',
				))) . '
							',
				)) . '
					';
			}
			$__compilerTemp5 .= '
				';
		}
	}
	$__compilerTemp7 = '';
	if ($__templater->isTraversable($__vars['interfaceGroups'])) {
		foreach ($__vars['interfaceGroups'] AS $__vars['interfaceGroupId'] => $__vars['interfaceGroup']) {
			$__compilerTemp7 .= '
					';
			if ($__vars['contentPermissions'][$__vars['interfaceGroupId']]) {
				$__compilerTemp7 .= '
						<hr class="formRowSep" />

						';
				$__compilerTemp8 = array();
				if ($__templater->isTraversable($__vars['contentPermissions'][$__vars['interfaceGroupId']])) {
					foreach ($__vars['contentPermissions'][$__vars['interfaceGroupId']] AS $__vars['permission']) {
						$__compilerTemp8[] = array(
							'name' => 'contentPermissions[' . $__vars['permission']['permission_group_id'] . '][' . $__vars['permission']['permission_id'] . ']',
							'value' => 'content_allow',
							'selected' => ($__vars['existingValues'][$__vars['permission']['permission_group_id']][$__vars['permission']['permission_id']] == 'content_allow'),
							'label' => $__templater->escape($__vars['permission']['title']),
							'_type' => 'option',
						);
					}
				}
				$__compilerTemp7 .= $__templater->formCheckBoxRow(array(
					'listclass' => 'listColumns',
				), $__compilerTemp8, array(
					'label' => $__templater->escape($__vars['interfaceGroup']['title']),
					'hint' => '
								' . $__templater->formCheckBox(array(
					'standalone' => 'true',
				), array(array(
					'check-all' => '< .formRow',
					'label' => 'Select all',
					'_type' => 'option',
				))) . '
							',
				)) . '
					';
			}
			$__compilerTemp7 .= '
				';
		}
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formRow('
				' . $__compilerTemp2 . '
			', array(
		'label' => 'Type of moderator',
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'is_staff',
		'selected' => $__vars['isStaff'],
		'label' => 'Display user as staff',
		'hint' => 'If selected, this user will be listed publicly as a staff member.',
		'_type' => 'option',
	)), array(
	)) . '

			' . $__templater->formCheckBoxRow(array(
		'name' => 'extra_user_group_ids[]',
		'value' => $__vars['generalModerator']['extra_user_group_ids'],
		'listclass' => 'listColumns',
	), $__compilerTemp3, array(
		'rowid' => 'addUserGroups',
		'label' => 'Add moderator to user groups',
		'hint' => '
					' . $__templater->formCheckBox(array(
		'standalone' => 'true',
	), array(array(
		'check-all' => '#addUserGroups',
		'label' => 'Select all',
		'_type' => 'option',
	))) . '
				',
	)) . '

			<hr class="formRowSep" />

			<div id="piGroups">

				' . $__templater->formCheckBoxRow(array(
	), array(array(
		'check-all' => '#piGroups',
		'label' => 'Select all',
		'_type' => 'option',
	)), array(
	)) . '

				' . $__compilerTemp4 . '

				' . $__compilerTemp5 . '

				' . $__compilerTemp7 . '

			</div>
		</div>
		' . $__templater->formSubmitRow(array(
		'icon' => 'save',
	), array(
	)) . '
	</div>

	' . $__templater->formHiddenVal('user_id', $__vars['generalModerator']['user_id'], array(
	)) . '
	' . $__templater->formHiddenVal('content_type', $__vars['contentModerator']['content_type'], array(
	)) . '
	' . $__templater->formHiddenVal('content_id', $__vars['contentModerator']['content_id'], array(
	)) . '
', array(
		'action' => $__templater->func('link', array('moderators/save', ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});