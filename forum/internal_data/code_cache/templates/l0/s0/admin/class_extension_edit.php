<?php
// FROM HASH: ea5b5c5f43feac1d85f41d31853b310d
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['extension'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add extension');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit extension' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['extension']['to_class']));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['extension'], 'isUpdate', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array('class-extensions/delete', $__vars['extension'], ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

' . $__templater->form('
	<div class="block-container">
		<div class="block-body">
			' . $__templater->formTextBoxRow(array(
		'name' => 'from_class',
		'value' => $__vars['extension']['from_class'],
		'maxlength' => $__templater->func('max_length', array($__vars['extension'], 'from_class', ), false),
		'dir' => 'ltr',
	), array(
		'label' => 'Base class name',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'to_class',
		'value' => $__vars['extension']['to_class'],
		'maxlength' => $__templater->func('max_length', array($__vars['extension'], 'to_class', ), false),
		'dir' => 'ltr',
	), array(
		'label' => 'Extension class name',
	)) . '

			' . $__templater->formNumberBoxRow(array(
		'name' => 'execute_order',
		'value' => $__vars['extension']['execute_order'],
		'min' => '0',
	), array(
		'label' => 'Execution order',
	)) . '

			' . $__templater->formCheckBoxRow(array(
	), array(array(
		'name' => 'active',
		'selected' => $__vars['extension']['active'],
		'label' => 'Enable extension',
		'_type' => 'option',
	)), array(
	)) . '

			' . $__templater->callMacro('addon_macros', 'addon_edit', array(
		'addOnId' => $__vars['extension']['addon_id'],
	), $__vars) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'sticky' => 'true',
		'icon' => 'save',
	), array(
	)) . '
	</div>

', array(
		'action' => $__templater->func('link', array('class-extensions/save', $__vars['extension'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});