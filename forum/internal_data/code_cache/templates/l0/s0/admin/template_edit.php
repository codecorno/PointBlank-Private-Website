<?php
// FROM HASH: 102ce5b38b1faf4617c475ec29080c3f
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['template'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add template');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit template' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['template']['title']));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	$__templater->setPageParam('breadcrumbPath', 'styles');
	$__finalCompiled .= '
';
	$__templater->breadcrumb($__templater->preEscaped($__templater->escape($__vars['style']['title']) . ' - ' . 'Templates'), $__templater->func('link', array('styles/templates', $__vars['style'], array('type' => $__vars['template']['type'], ), ), false), array(
	));
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['template'], 'isUpdate', array()) AND ($__vars['template']['style_id'] == $__vars['style']['style_id'])) {
		$__compilerTemp1 = '';
		if ($__vars['style']['style_id']) {
			$__compilerTemp1 .= '
		' . $__templater->button('View custom changes', array(
				'href' => $__templater->func('link', array('templates/compare', $__vars['template'], ), false),
				'data-xf-click' => 'overlay',
				'data-cache' => 'false',
			), '', array(
			)) . '
		' . $__templater->button('Revert', array(
				'href' => $__templater->func('link', array('templates/delete', $__vars['template'], array('_xfRedirect' => $__vars['redirect'], ), ), false),
				'overlay' => 'true',
			), '', array(
			)) . '
	';
		} else {
			$__compilerTemp1 .= '
		' . $__templater->button('', array(
				'href' => $__templater->func('link', array('templates/delete', $__vars['template'], array('_xfRedirect' => $__vars['redirect'], ), ), false),
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
	if ($__vars['hasHistory']) {
		$__compilerTemp2 .= '
						<div class="js-historyTarget toggleTarget" data-href="trigger-href"></div>
					';
	}
	$__compilerTemp3 = '';
	if (!$__vars['style']['style_id']) {
		$__compilerTemp3 .= '
				' . $__templater->callMacro('addon_macros', 'addon_edit', array(
			'addOnId' => $__vars['template']['addon_id'],
		), $__vars) . '
			';
	} else {
		$__compilerTemp3 .= '
				' . $__templater->formHiddenVal('addon_id', $__vars['template']['addon_id'], array(
		)) . '
			';
	}
	$__compilerTemp4 = '';
	if ($__vars['hasHistory']) {
		$__compilerTemp4 .= '
					' . $__templater->button('View history', array(
			'href' => $__templater->func('link', array('templates/history', $__vars['template'], ), false),
			'class' => 'blockLink',
			'icon' => 'history',
			'data-xf-click' => 'toggle',
			'data-target' => '.js-historyTarget',
		), '', array(
		)) . '
				';
	}
	$__finalCompiled .= $__templater->form('

	' . $__templater->formHiddenVal('style_id', $__vars['style']['style_id'], array(
	)) . '
	' . $__templater->formHiddenVal('type', $__vars['template']['type'], array(
	)) . '

	<div class="block-outer">
		<div class="block-outer-main">
			' . $__templater->callMacro('style_macros', 'style_change_menu', array(
		'styleTree' => $__vars['styleTree'],
		'currentStyle' => $__vars['style'],
		'route' => ($__templater->method($__vars['template'], 'isInsert', array()) ? 'styles/add-template' : 'styles/edit-template'),
		'routeParams' => array('template_id' => $__vars['template']['template_id'], 'type' => $__vars['template']['type'], ),
	), $__vars) . '
		</div>
		<div class="block-outer-opposite"><span class="block-outer-hint">' . 'Template type' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['types'][$__vars['template']['type']]) . '</span></div>
	</div>

	<div class="block-container">

		<div class="block-body" data-xf-init="code-editor-switcher-container" data-template-suffix-mode="1">

			' . $__templater->formTextBoxRow(array(
		'name' => 'title',
		'value' => $__vars['template']['title'],
		'class' => 'js-codeEditorSwitcher formRow--noLabel',
		'maxlength' => $__templater->func('max_length', array($__vars['template'], 'title', ), false),
		'autofocus' => 'autofocus',
		'dir' => 'ltr',
	), array(
		'rowtype' => 'fullWidth',
		'label' => 'Template name',
		'hint' => 'Must be unique',
	)) . '

			' . $__templater->formCodeEditorRow(array(
		'name' => 'template',
		'value' => $__vars['template']['template'],
		'mode' => 'html',
		'data-submit-selector' => '.js-submitButton',
	), array(
		'rowtype' => 'fullWidth noLabel',
		'rowclass' => 'js-codeEditorContainer',
		'label' => 'Template',
		'explain' => 'You may use XenForo template syntax here.',
		'finalhtml' => '
					' . $__compilerTemp2 . '
				',
	)) . '

			' . $__compilerTemp3 . '
		</div>

		' . $__templater->formSubmitRow(array(
		'icon' => 'save',
		'class' => 'js-submitButton',
		'data-ajax-redirect' => (($__templater->method($__vars['template'], 'isInsert', array()) OR ($__vars['template']['style_id'] != $__vars['style']['style_id'])) ? '1' : '0'),
	), array(
		'html' => '
				' . $__templater->button('Save and exit', array(
		'type' => 'submit',
		'name' => 'exit',
		'icon' => 'save',
	), '', array(
	)) . '
				' . $__compilerTemp4 . '
			',
	)) . '
	</div>
	' . $__templater->func('redirect_input', array(null, null, true)) . '
', array(
		'action' => $__templater->func('link', array('templates/save', $__vars['template'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});