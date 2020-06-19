<?php
// FROM HASH: 7993899661b3ae3b6675f02f2ac90673
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['field'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add content type field');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit content type field' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['field']['content_type']));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['field'], 'isUpdate', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array('content-types/delete', $__vars['field'], ), false),
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
		'name' => 'content_type',
		'value' => $__vars['field']['content_type'],
		'maxlength' => $__templater->func('max_length', array($__vars['field'], 'content_type', ), false),
		'dir' => 'ltr',
	), array(
		'label' => 'Content type',
	)) . '
			' . $__templater->formTextBoxRow(array(
		'name' => 'field_name',
		'value' => $__vars['field']['field_name'],
		'maxlength' => $__templater->func('max_length', array($__vars['field'], 'field_name', ), false),
		'dir' => 'ltr',
	), array(
		'label' => 'Field',
	)) . '
			' . $__templater->formTextBoxRow(array(
		'name' => 'field_value',
		'value' => $__vars['field']['field_value'],
		'maxlength' => $__templater->func('max_length', array($__vars['field'], 'field_value', ), false),
		'dir' => 'ltr',
	), array(
		'label' => 'Value',
	)) . '

			' . $__templater->callMacro('addon_macros', 'addon_edit', array(
		'addOnId' => $__vars['field']['addon_id'],
	), $__vars) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'sticky' => 'true',
		'icon' => 'save',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('content-types/save', $__vars['field'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});