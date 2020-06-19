<?php
// FROM HASH: 7c847252271696f4d8a61e75a0045a01
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['property'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add property');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit property' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['property']['master_title']));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['property'], 'isUpdate', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array('style-properties/delete', $__vars['property'], ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

';
	$__compilerTemp1 = array(array(
		'value' => '',
		'label' => '&nbsp;',
		'_type' => 'option',
	));
	if ($__templater->isTraversable($__vars['groups'])) {
		foreach ($__vars['groups'] AS $__vars['group']) {
			$__compilerTemp1[] = array(
				'value' => $__vars['group']['group_name'],
				'label' => $__templater->escape($__vars['group']['title']),
				'_type' => 'option',
			);
		}
	}
	$__compilerTemp2 = '';
	if ($__vars['style']['style_id'] == 0) {
		$__compilerTemp2 .= '
				' . $__templater->callMacro('addon_macros', 'addon_edit', array(
			'addOnId' => $__vars['property']['addon_id'],
		), $__vars) . '
			';
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formRow('

				' . $__templater->escape($__vars['style']['title']) . '
				' . $__templater->formHiddenVal('style_id', $__vars['style']['style_id'], array(
	)) . '
			', array(
		'label' => 'Style',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'property_name',
		'value' => $__vars['property']['property_name'],
		'maxlength' => $__templater->func('max_length', array($__vars['property'], 'property_name', ), false),
		'dir' => 'ltr',
	), array(
		'label' => 'Property name',
		'explain' => 'This is how you will refer to the property in a template.',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'title',
		'value' => $__vars['property']['master_title'],
		'maxlength' => $__templater->func('max_length', array($__vars['property'], 'title', ), false),
	), array(
		'label' => 'Title',
	)) . '

			' . $__templater->formTextAreaRow(array(
		'name' => 'description',
		'value' => $__vars['property']['master_description'],
		'autosize' => 'true',
		'maxlength' => $__templater->func('max_length', array($__vars['property'], 'description', ), false),
	), array(
		'label' => 'Description',
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formRadioRow(array(
		'name' => 'property_type',
		'value' => ($__vars['property']['property_type'] ?: 'css'),
	), array(array(
		'value' => 'css',
		'data-xf-init' => 'disabler',
		'data-container' => '.js-propertyTypeCss',
		'data-hide' => 'true',
		'label' => '
					CSS',
		'_type' => 'option',
	),
	array(
		'value' => 'value',
		'data-xf-init' => 'disabler',
		'data-container' => '.js-propertyTypeValue',
		'data-hide' => 'true',
		'label' => '
					' . 'Value',
		'_type' => 'option',
	)), array(
		'label' => 'Type',
	)) . '

			<div class="js-propertyTypeCss">
				' . $__templater->formCheckBoxRow(array(
		'name' => 'css_components',
		'value' => $__vars['property']['css_components'],
		'listclass' => 'listColumns',
	), array(array(
		'value' => 'text',
		'label' => 'Text',
		'_type' => 'option',
	),
	array(
		'value' => 'background',
		'label' => 'Background',
		'_type' => 'option',
	),
	array(
		'value' => 'border',
		'label' => 'Border',
		'_type' => 'option',
	),
	array(
		'value' => 'border_radius',
		'label' => 'Border radius',
		'_type' => 'option',
	),
	array(
		'value' => 'border_width_simple',
		'label' => 'Simple border width',
		'_type' => 'option',
	),
	array(
		'value' => 'border_color_simple',
		'label' => 'Simple border color',
		'_type' => 'option',
	),
	array(
		'value' => 'border_radius_simple',
		'label' => 'Simple border radius',
		'_type' => 'option',
	),
	array(
		'value' => 'padding',
		'label' => 'Padding',
		'_type' => 'option',
	),
	array(
		'value' => 'extra',
		'label' => 'Extra',
		'_type' => 'option',
	)), array(
		'label' => 'CSS components',
		'explain' => 'Simple border options apply only if border and border radius are not selected.',
	)) . '
			</div>
			<div class="js-propertyTypeValue">
				' . $__templater->formRadioRow(array(
		'name' => 'value_type',
		'value' => ($__vars['property']['value_type'] ?: 'string'),
		'listclass' => 'listColumns',
	), array(array(
		'value' => 'string',
		'label' => 'String',
		'_type' => 'option',
	),
	array(
		'value' => 'color',
		'label' => 'Color',
		'_type' => 'option',
	),
	array(
		'value' => 'unit',
		'label' => 'Unit',
		'_type' => 'option',
	),
	array(
		'value' => 'number',
		'label' => 'Number',
		'_type' => 'option',
	),
	array(
		'value' => 'boolean',
		'label' => 'Boolean',
		'_type' => 'option',
	),
	array(
		'value' => 'radio',
		'label' => 'Radio buttons',
		'_type' => 'option',
	),
	array(
		'value' => 'select',
		'label' => 'Select menu',
		'_type' => 'option',
	),
	array(
		'value' => 'template',
		'label' => 'Template',
		'_type' => 'option',
	)), array(
		'label' => 'Value type',
	)) . '

				' . $__templater->formTextAreaRow(array(
		'name' => 'value_parameters',
		'value' => $__vars['property']['value_parameters'],
		'autosize' => 'true',
		'code' => 'true',
	), array(
		'label' => 'Value formatting parameters',
	)) . '

				' . $__templater->formTextBoxRow(array(
		'name' => 'depends_on',
		'value' => $__vars['property']['depends_on'],
		'maxlength' => $__templater->func('max_length', array($__vars['property'], 'depends_on', ), false),
		'dir' => 'ltr',
	), array(
		'label' => 'Depends on',
		'explain' => 'Entering a property name here will disable this property if the named property is disabled. The named property must be a boolean property in the same group as this property.',
	)) . '

				' . $__templater->formTextBoxRow(array(
		'name' => 'value_group',
		'value' => $__vars['property']['value_group'],
		'maxlength' => $__templater->func('max_length', array($__vars['property'], 'value_group', ), false),
		'dir' => 'ltr',
	), array(
		'label' => 'Value grouping',
		'explain' => 'When listed, if this property does not have the same value grouping as the previous property, a visual separator will be inserted.',
	)) . '
			</div>

			<hr class="formRowSep" />

			' . $__templater->formSelectRow(array(
		'name' => 'group_name',
		'value' => $__vars['property']['group_name'],
	), $__compilerTemp1, array(
		'label' => 'Group',
	)) . '

			' . $__templater->callMacro('display_order_macros', 'row', array(
		'value' => $__vars['property']['display_order'],
	), $__vars) . '

			' . $__compilerTemp2 . '
		</div>
		' . $__templater->formSubmitRow(array(
		'icon' => 'save',
		'sticky' => 'true',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('style-properties//save', $__vars['property'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});