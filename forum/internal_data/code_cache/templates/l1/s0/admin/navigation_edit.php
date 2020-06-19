<?php
// FROM HASH: b3aa6e687a2ef26ed947537325c35c96
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
	$__compilerTemp1 = '';
	if ($__templater->method($__vars['navigation'], 'canDelete', array())) {
		$__compilerTemp1 .= '
		' . $__templater->button('', array(
			'href' => $__templater->func('link', array('navigation/delete', $__vars['navigation'], ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '
	';
	}
	$__compilerTemp2 = '';
	if ($__vars['navigation']['is_customized']) {
		$__compilerTemp2 .= '
		' . $__templater->button('Revert', array(
			'href' => $__templater->func('link', array('navigation/revert', $__vars['navigation'], ), false),
			'overlay' => 'true',
		), '', array(
		)) . '
	';
	}
	$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__compilerTemp1 . '
	' . $__compilerTemp2 . '
');
	$__finalCompiled .= '

';
	if ($__vars['navigation']['is_customized']) {
		$__finalCompiled .= '
	<div class="blockMessage blockMessage--important blockMessage--iconic">
		' . 'This navigation entry has been customized. When its associated add-on is upgraded, your changes will be maintained.' . '
	</div>
';
	}
	$__finalCompiled .= '

';
	if ($__vars['xf']['development'] AND $__vars['navigation']['addon_id']) {
		$__finalCompiled .= '
	<div class="blockMessage blockMessage--warning">
		' . 'User customizations to this navigation entry will be maintained across add-on upgrades.' . '
	</div>
';
	}
	$__finalCompiled .= '

';
	$__compilerTemp3 = array(array(
		'value' => '',
		'label' => '&nbsp;',
		'_type' => 'option',
	));
	$__compilerTemp4 = $__templater->method($__vars['navigationTree'], 'getFlattened', array());
	if ($__templater->isTraversable($__compilerTemp4)) {
		foreach ($__compilerTemp4 AS $__vars['treeEntry']) {
			$__compilerTemp3[] = array(
				'value' => $__vars['treeEntry']['record']['navigation_id'],
				'label' => $__templater->func('repeat', array('--', $__vars['treeEntry']['depth'], ), true) . '
						' . $__templater->escape($__vars['treeEntry']['record']['title']) . '
					',
				'_type' => 'option',
			);
		}
	}
	$__compilerTemp5 = array();
	if ($__templater->isTraversable($__vars['typeHandlers'])) {
		foreach ($__vars['typeHandlers'] AS $__vars['type'] => $__vars['handler']) {
			$__compilerTemp5[] = array(
				'value' => $__vars['type'],
				'data-xf-init' => 'disabler',
				'data-container' => '.js-navTypeForm--' . $__vars['type'],
				'data-hide' => 'true',
				'label' => $__templater->escape($__templater->method($__vars['handler'], 'getTitle', array())),
				'_type' => 'option',
			);
		}
	}
	$__compilerTemp6 = '';
	if ($__templater->isTraversable($__vars['typeHandlers'])) {
		foreach ($__vars['typeHandlers'] AS $__vars['type'] => $__vars['handler']) {
			$__compilerTemp6 .= '
				<div class="js-navTypeForm js-navTypeForm--' . $__templater->escape($__vars['type']) . '">
					' . $__templater->filter($__templater->method($__vars['handler'], 'renderEditForm', array($__vars['navigation'], (($__vars['navigation']['navigation_type_id'] == $__vars['type']) ? $__vars['navigation']['type_config'] : array()), ('config[' . $__vars['type']) . ']', )), array(array('raw', array()),), true) . '
				</div>
			';
		}
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formTextBoxRow(array(
		'name' => 'navigation_id',
		'value' => $__vars['navigation']['navigation_id'],
		'readonly' => (!$__templater->method($__vars['navigation'], 'canEdit', array())),
		'maxlength' => $__templater->func('max_length', array($__vars['navigation'], 'navigation_id', ), false),
		'dir' => 'ltr',
	), array(
		'label' => 'Navigation ID',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'title',
		'value' => ($__templater->method($__vars['navigation'], 'exists', array()) ? $__vars['navigation']['MasterTitle']['phrase_text'] : ''),
		'readonly' => (!$__templater->method($__vars['navigation'], 'canEdit', array())),
	), array(
		'label' => 'Title',
	)) . '

			' . $__templater->formSelectRow(array(
		'name' => 'parent_navigation_id',
		'value' => $__vars['navigation']['parent_navigation_id'],
	), $__compilerTemp3, array(
		'label' => 'Parent navigation entry',
	)) . '

			' . $__templater->callMacro('display_order_macros', 'row', array(
		'value' => $__vars['navigation']['display_order'],
	), $__vars) . '

			' . $__templater->formRadioRow(array(
		'name' => 'navigation_type_id',
		'value' => $__vars['selectedType'],
	), $__compilerTemp5, array(
		'label' => 'Type',
	)) . '

			<hr class="formRowSep" />

			' . $__compilerTemp6 . '

			<hr class="formRowSep" />

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'enabled',
		'selected' => $__vars['navigation']['enabled'],
		'label' => 'Enabled',
		'_type' => 'option',
	)), array(
	)) . '

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
		'action' => $__templater->func('link', array('navigation/save', $__vars['navigation'], ), false),
		'class' => 'block',
		'ajax' => 'true',
	));
	return $__finalCompiled;
});