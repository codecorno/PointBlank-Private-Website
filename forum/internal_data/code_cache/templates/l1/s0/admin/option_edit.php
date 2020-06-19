<?php
// FROM HASH: e4b9258b09fe50b7da951fd516b1bcfa
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['option'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add option');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit option' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['option']['title']));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__vars['group']) {
		$__finalCompiled .= '
	';
		$__templater->breadcrumb($__templater->preEscaped($__templater->escape($__vars['group']['title'])), $__templater->func('link', array('options/groups', $__vars['group'], ), false), array(
		));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['option'], 'isUpdate', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array('options/delete', $__vars['option'], array('_xfRedirect' => $__vars['redirect'], ), ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

';
	$__compilerTemp1 = array();
	if ($__templater->isTraversable($__vars['groups'])) {
		foreach ($__vars['groups'] AS $__vars['group']) {
			$__compilerTemp1[] = array(
				'name' => 'relations[' . $__vars['group']['group_id'] . '][selected]',
				'selected' => $__vars['relations'][$__vars['group']['group_id']] !== null,
				'label' => $__templater->escape($__vars['group']['title']),
				'data-hide' => 'true',
				'_dependent' => array($__templater->callMacro('display_order_macros', 'input', array(
				'name' => 'relations[' . $__vars['group']['group_id'] . '][display_order]',
				'value' => $__templater->filter($__vars['relations'][$__vars['group']['group_id']], array(array('default', array(1, )),), false),
			), $__vars)),
				'_type' => 'option',
			);
		}
	}
	$__finalCompiled .= $__templater->form('
	<div class="block-container">
		<div class="block-body">

			' . $__templater->formTextBoxRow(array(
		'name' => 'option_id',
		'value' => $__vars['option']['option_id'],
		'dir' => 'ltr',
	), array(
		'label' => 'Option ID',
		'hint' => 'A-Z, a-z, 0-9, and _ only',
	)) . '

			' . $__templater->callMacro('addon_macros', 'addon_edit', array(
		'addOnId' => $__vars['option']['addon_id'],
	), $__vars) . '

			<hr class="formRowSep" />

			' . $__templater->formTextBoxRow(array(
		'name' => 'title',
		'value' => ($__templater->method($__vars['option'], 'exists', array()) ? $__vars['option']['MasterTitle']['phrase_text'] : ''),
	), array(
		'label' => 'Title',
	)) . '

			' . $__templater->formTextAreaRow(array(
		'name' => 'explain',
		'value' => ($__templater->method($__vars['option'], 'exists', array()) ? $__vars['option']['MasterExplain']['phrase_text'] : ''),
		'autosize' => 'true',
	), array(
		'label' => 'Explanation',
		'hint' => 'You may use HTML',
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formSelectRow(array(
		'name' => 'edit_format',
		'value' => $__vars['option']['edit_format'],
	), array(array(
		'value' => 'textbox',
		'label' => 'Text box',
		'_type' => 'option',
	),
	array(
		'value' => 'spinbox',
		'label' => 'Spin box',
		'_type' => 'option',
	),
	array(
		'value' => 'onoff',
		'label' => 'On/off check box',
		'_type' => 'option',
	),
	array(
		'value' => 'onofftextbox',
		'label' => 'On/off check box with text box',
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
		'value' => 'checkbox',
		'label' => 'Check boxes',
		'_type' => 'option',
	),
	array(
		'value' => 'template',
		'label' => 'Named template',
		'_type' => 'option',
	),
	array(
		'value' => 'callback',
		'label' => 'PHP callback',
		'_type' => 'option',
	),
	array(
		'value' => 'username',
		'label' => 'User name input',
		'_type' => 'option',
	)), array(
		'label' => 'Edit format',
	)) . '

			' . $__templater->formTextAreaRow(array(
		'name' => 'edit_format_params',
		'value' => $__vars['option']['edit_format_params'],
		'autosize' => 'true',
		'code' => 'true',
	), array(
		'label' => 'Format parameters',
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formSelectRow(array(
		'name' => 'data_type',
		'value' => ($__vars['option']['data_type'] ?: 'string'),
	), array(array(
		'value' => 'boolean',
		'label' => 'Boolean',
		'_type' => 'option',
	),
	array(
		'value' => 'string',
		'label' => 'String',
		'_type' => 'option',
	),
	array(
		'value' => 'integer',
		'label' => 'Integer',
		'_type' => 'option',
	),
	array(
		'value' => 'unsigned_integer',
		'label' => 'Unsigned integer',
		'_type' => 'option',
	),
	array(
		'value' => 'positive_integer',
		'label' => 'Positive integer',
		'_type' => 'option',
	),
	array(
		'value' => 'numeric',
		'label' => 'Numeric',
		'_type' => 'option',
	),
	array(
		'value' => 'unsigned_numeric',
		'label' => 'Unsigned numeric',
		'_type' => 'option',
	),
	array(
		'value' => 'array',
		'label' => 'Array',
		'_type' => 'option',
	)), array(
		'label' => 'Data type',
	)) . '

			' . $__templater->formTextAreaRow(array(
		'name' => 'default_value',
		'value' => (($__vars['option']['data_type'] == 'array') ? $__templater->filter($__vars['option']['default_value'], array(array('json', array()),), false) : $__vars['option']['default_value']),
		'autosize' => 'true',
		'code' => 'true',
	), array(
		'label' => 'Default value',
	)) . '

			' . $__templater->formTextAreaRow(array(
		'name' => 'sub_options',
		'autosize' => 'true',
		'value' => $__templater->filter($__vars['option']['sub_options'], array(array('join', array('
', )),), false),
		'dir' => 'ltr',
	), array(
		'label' => 'Array sub-options',
	)) . '

			' . $__templater->formRow('
				' . $__templater->callMacro('helper_callback_fields', 'callback_fields', array(
		'namePrefix' => 'validation',
		'data' => $__vars['option'],
	), $__vars) . '
			', array(
		'rowtype' => 'input',
		'label' => 'Validation callback',
	)) . '

			<hr class="formRowSep" />

			' . $__templater->formCheckBoxRow(array(
		'listclass' => 'listColumns',
	), $__compilerTemp1, array(
		'label' => 'Display in groups',
	)) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'icon' => 'save',
		'sticky' => 'true',
	), array(
	)) . '
	</div>
	' . $__templater->func('redirect_input', array($__vars['redirect'], null, true)) . '
', array(
		'action' => $__templater->func('link', array('options/save', $__vars['option'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});