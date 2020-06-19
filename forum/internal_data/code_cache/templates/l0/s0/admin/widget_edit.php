<?php
// FROM HASH: f4c0aed96de6a6922ecc958bc9ce72ff
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['widget'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add widget');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit widget' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['widget']['title']));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['widget'], 'isUpdate', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array('widgets/delete', $__vars['widget'], ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

';
	$__compilerTemp1 = array();
	if ($__templater->isTraversable($__vars['widgetPositions'])) {
		foreach ($__vars['widgetPositions'] AS $__vars['position']) {
			$__compilerTemp1[] = array(
				'selected' => $__vars['widget']['positions'][$__vars['position']['position_id']] !== null,
				'data-hide' => 'true',
				'label' => $__templater->escape($__vars['position']['title']),
				'_dependent' => array($__templater->formNumberBox(array(
				'name' => 'positions[' . $__vars['position']['position_id'] . ']',
				'value' => $__templater->filter($__vars['widget']['positions'][$__vars['position']['position_id']], array(array('default', array(1, )),), false),
				'min' => '0',
			))),
				'_type' => 'option',
			);
		}
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">

			' . $__templater->formRow('
				' . $__templater->escape($__vars['widgetDefinition']['title']) . '
				' . $__templater->formHiddenVal('definition_id', $__vars['widgetDefinition']['definition_id'], array(
	)) . '
			', array(
		'label' => 'Widget definition',
		'explain' => $__templater->escape($__vars['widgetDefinition']['description']),
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formTextBoxRow(array(
		'name' => 'widget_key',
		'value' => $__vars['widget']['widget_key'],
		'maxlength' => $__templater->func('max_length', array($__vars['widget'], 'widget_key', ), false),
		'dir' => 'ltr',
	), array(
		'label' => 'Widget key',
		'hint' => 'Required',
		'explain' => 'Enter a unique identifier for this widget. This makes it easier to refer to the widget if added directly to a template.',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'title',
		'value' => ($__vars['widget']['widget_id'] ? $__vars['widget']['MasterTitle']['phrase_text'] : ''),
	), array(
		'label' => 'Title',
		'hint' => 'Optional',
		'explain' => 'You may choose to enter a specific title for this widget. If you do not enter one, the widget will display an appropriate title. Note that not all widgets will display a specific title.',
	)) . '

			' . $__templater->formCheckBoxRow(array(
		'listclass' => 'listColumns',
	), $__compilerTemp1, array(
		'label' => 'Display in positions',
		'explain' => $__templater->filter('Widgets with no position will not appear anywhere, but you can call the configured widget instance directly in templates using the <code>&lt;xf:widget key="widget_key" /&gt;</code> syntax.', array(array('raw', array()),), true),
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'display_condition',
		'value' => $__vars['widget']['display_condition'],
		'code' => 'true',
	), array(
		'label' => 'Display condition',
		'explain' => 'This should be entered as a template-style expression.',
	)) . '

			' . $__templater->filter($__templater->method($__vars['widget'], 'renderOptions', array()), array(array('raw', array()),), true) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'sticky' => 'true',
		'icon' => 'save',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('widgets/save', $__vars['widget'], ), false),
		'class' => 'block',
		'ajax' => 'true',
	));
	return $__finalCompiled;
});