<?php
// FROM HASH: d59b9d2b1c37d9a7bcc1cae845ca150d
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['navigation'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add navigation');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit navigation' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['navigation']['title']));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['navigation'], 'isUpdate', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array('admin-navigation/delete', $__vars['navigation'], ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

';
	$__compilerTemp1 = array(array(
		'label' => '&nbsp;',
		'_type' => 'option',
	));
	$__compilerTemp2 = $__templater->method($__vars['navigationTree'], 'getFlattened', array());
	if ($__templater->isTraversable($__compilerTemp2)) {
		foreach ($__compilerTemp2 AS $__vars['treeEntry']) {
			$__compilerTemp1[] = array(
				'value' => $__vars['treeEntry']['record']['navigation_id'],
				'label' => $__templater->func('repeat', array('--', $__vars['treeEntry']['depth'], ), true) . '
						' . $__templater->escape($__vars['treeEntry']['record']['title']) . '
					',
				'_type' => 'option',
			);
		}
	}
	$__compilerTemp3 = array(array(
		'label' => '&nbsp;',
		'_type' => 'option',
	));
	if ($__templater->isTraversable($__vars['adminPermissions'])) {
		foreach ($__vars['adminPermissions'] AS $__vars['adminPermission']) {
			$__compilerTemp3[] = array(
				'value' => $__vars['adminPermission']['admin_permission_id'],
				'label' => $__templater->escape($__vars['adminPermission']['title']),
				'_type' => 'option',
			);
		}
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formTextBoxRow(array(
		'name' => 'navigation_id',
		'value' => $__vars['navigation']['navigation_id'],
		'maxlength' => $__templater->func('max_length', array($__vars['navigation'], 'navigation_id', ), false),
		'dir' => 'ltr',
	), array(
		'label' => 'Admin navigation ID',
	)) . '
			' . $__templater->formTextBoxRow(array(
		'name' => 'title',
		'value' => ($__templater->method($__vars['navigation'], 'exists', array()) ? $__vars['navigation']['MasterTitle']['phrase_text'] : ''),
	), array(
		'label' => 'Title',
	)) . '
			' . $__templater->formTextBoxRow(array(
		'name' => 'link',
		'value' => $__vars['navigation']['link'],
		'maxlength' => $__templater->func('max_length', array($__vars['navigation'], 'link', ), false),
		'dir' => 'ltr',
	), array(
		'label' => 'Link',
	)) . '
			' . $__templater->formTextBoxRow(array(
		'name' => 'icon',
		'value' => $__vars['navigation']['icon'],
		'maxlength' => $__templater->func('max_length', array($__vars['navigation'], 'icon', ), false),
		'dir' => 'ltr',
	), array(
		'label' => 'Font Awesome icon',
		'explain' => 'Optional Font Awesome icon name',
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formSelectRow(array(
		'name' => 'parent_navigation_id',
		'value' => $__vars['navigation']['parent_navigation_id'],
	), $__compilerTemp1, array(
		'label' => 'Parent navigation entry',
	)) . '

			' . $__templater->callMacro('display_order_macros', 'row', array(
		'value' => $__vars['navigation']['display_order'],
	), $__vars) . '

			<hr class="formRowSep" />

			' . $__templater->formSelectRow(array(
		'name' => 'admin_permission_id',
		'value' => $__vars['navigation']['admin_permission_id'],
	), $__compilerTemp3, array(
		'label' => 'Required admin permission',
	)) . '
			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'debug_only',
		'selected' => $__vars['navigation']['debug_only'],
		'label' => 'Display this navigation in debug mode only' . '
				',
		'_type' => 'option',
	),
	array(
		'name' => 'development_only',
		'selected' => $__vars['navigation']['debug_only'],
		'label' => 'Display this navigation in development mode only' . '
				',
		'_type' => 'option',
	),
	array(
		'name' => 'hide_no_children',
		'selected' => $__vars['navigation']['hide_no_children'],
		'label' => 'Hide this navigation when there are no viewable children' . '
				',
		'_type' => 'option',
	)), array(
	)) . '

			<hr class="formRowSep" />

			' . $__templater->callMacro('addon_macros', 'addon_edit', array(
		'addOnId' => $__vars['navigation']['addon_id'],
	), $__vars) . '

		</div>

		' . $__templater->formSubmitRow(array(
		'sticky' => 'true',
		'icon' => 'save',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('admin-navigation/save', $__vars['navigation'], ), false),
		'class' => 'block',
		'ajax' => 'true',
	));
	return $__finalCompiled;
});