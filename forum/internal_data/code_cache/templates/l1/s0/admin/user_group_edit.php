<?php
// FROM HASH: 6595ae0534799c7081c877be9d0fcaf5
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['userGroup'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add user group');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit user group' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['userGroup']['title']));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['userGroup'], 'isUpdate', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array('user-groups/delete', $__vars['userGroup'], ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

';
	$__templater->includeCss('public:app_user_banners.less');
	$__compilerTemp1 = array();
	if ($__templater->isTraversable($__vars['displayStyles'])) {
		foreach ($__vars['displayStyles'] AS $__vars['class']) {
			$__compilerTemp1[] = array(
				'value' => $__vars['class'],
				'label' => '
							<span title="' . $__templater->escape($__vars['class']) . '" class="' . $__templater->escape($__vars['class']) . '" data-xf-init="tooltip" style="display: inline">' . 'Banner' . '</span>
						',
				'_type' => 'option',
			);
		}
	}
	$__compilerTemp1[] = array(
		'value' => '',
		'selected' => $__vars['userGroup']['banner_css_class'] AND (!$__templater->func('in_array', array($__vars['userGroup']['banner_css_class'], $__vars['displayStyles'], ), false)),
		'label' => 'Other, using custom CSS class name',
		'_dependent' => array($__templater->formTextBox(array(
		'name' => 'banner_css_class_other',
		'value' => (($__vars['userGroup']['banner_css_class'] AND (!$__templater->func('in_array', array($__vars['userGroup']['banner_css_class'], $__vars['displayStyles'], ), false))) ? $__vars['userGroup']['banner_css_class'] : ''),
		'maxlength' => $__templater->func('max_length', array($__vars['userGroup'], 'banner_css_class', ), false),
	))),
		'_type' => 'option',
	);
	$__templater->includeJs(array(
		'src' => 'xf/permission.js',
		'min' => '1',
	));
	$__templater->includeCss('permission.less');
	$__finalCompiled .= $__templater->form('

	<div class="block-container">
		<div class="block-body">
			' . $__templater->formTextBoxRow(array(
		'name' => 'title',
		'value' => $__vars['userGroup']['title'],
		'maxlength' => $__templater->func('max_length', array($__vars['userGroup'], 'title', ), false),
	), array(
		'label' => 'Title',
	)) . '

			' . $__templater->formNumberBoxRow(array(
		'name' => 'display_style_priority',
		'value' => $__vars['userGroup']['display_style_priority'],
		'min' => '0',
	), array(
		'label' => 'Display styling priority',
		'explain' => 'If a user is in multiple user groups, their user title and user name CSS will come from the group with the highest display styling priority.',
	)) . '

			' . $__templater->formRadioRow(array(
		'name' => 'user_title_override',
		'value' => ($__vars['userGroup']['user_title'] ? 1 : 0),
	), array(array(
		'value' => '0',
		'label' => 'Use the default user title ladder',
		'_type' => 'option',
	),
	array(
		'value' => '1',
		'label' => 'Use the following user title' . $__vars['xf']['language']['label_separator'],
		'_dependent' => array($__templater->formTextBox(array(
		'name' => 'user_title',
		'value' => $__vars['userGroup']['user_title'],
		'maxlength' => $__templater->func('max_length', array($__vars['userGroup'], 'user_title', ), false),
		'size' => '50',
	))),
		'_type' => 'option',
	)), array(
		'label' => 'User title override',
		'explain' => 'You may use HTML',
	)) . '

			' . $__templater->formCodeEditorRow(array(
		'name' => 'username_css',
		'value' => $__vars['userGroup']['username_css'],
		'mode' => 'css',
		'data-line-wrapping' => 'true',
		'class' => 'codeEditor--autoSize',
	), array(
		'label' => 'User name CSS',
	)) . '

			' . '' . '
			' . $__templater->formTextBoxRow(array(
		'name' => 'banner_text',
		'value' => $__vars['userGroup']['banner_text'],
		'maxlength' => $__templater->func('max_length', array($__vars['userGroup'], 'banner_text', ), false),
	), array(
		'label' => 'User banner text',
		'explain' => 'This will be displayed under the name of members of this group in certain circumstances, such as with posts.',
	)) . '

			' . $__templater->formRow('
				' . $__templater->formRadio(array(
		'name' => 'banner_css_class',
		'value' => ($__vars['userGroup']['banner_css_class'] ? $__vars['userGroup']['banner_css_class'] : 'userBanner userBanner--primary'),
		'listclass' => 'listColumns',
	), $__compilerTemp1) . '
			', array(
		'label' => 'User banner styling',
	)) . '
		</div>

		' . '' . '
		' . '' . '

		' . $__templater->callMacro('permission_macros', 'edit_groups', array(
		'interfaceGroups' => $__vars['permissionData']['interfaceGroups'],
		'permissionsGrouped' => $__vars['permissionData']['permissionsGrouped'],
		'values' => $__vars['permissionData']['values'],
	), $__vars) . '

		' . $__templater->formSubmitRow(array(
		'icon' => 'save',
		'sticky' => 'true',
	), array(
	)) . '
	</div>

', array(
		'action' => $__templater->func('link', array('user-groups/save', $__vars['userGroup'], ), false),
		'ajax' => 'true',
		'class' => 'block',
		'data-xf-init' => 'permission-form',
		'data-permission-type' => 'global',
	));
	return $__finalCompiled;
});