<?php
// FROM HASH: ba26ec2999936cec4496252844b3c30e
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['interfaceGroup'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add interface group');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit interface group' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['interfaceGroup']['title']));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['interfaceGroup'], 'isUpdate', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array('permission-definitions/interface-groups/delete', $__vars['interfaceGroup'], ), false),
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
		'name' => 'interface_group_id',
		'value' => $__vars['interfaceGroup']['interface_group_id'],
		'maxlength' => $__templater->func('max_length', array($__vars['interfaceGroup'], 'interface_group_id', ), false),
		'dir' => 'ltr',
	), array(
		'label' => 'Interface group ID',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'title',
		'value' => ($__templater->method($__vars['interfaceGroup'], 'exists', array()) ? $__vars['interfaceGroup']['MasterTitle']['phrase_text'] : ''),
	), array(
		'label' => 'Title',
	)) . '

			' . $__templater->callMacro('display_order_macros', 'row', array(
		'value' => $__vars['interfaceGroup']['display_order'],
	), $__vars) . '

			' . $__templater->formCheckBoxRow(array(
		'name' => 'is_moderator',
		'value' => $__vars['interfaceGroup']['is_moderator'],
	), array(array(
		'value' => '1',
		'label' => 'This interface group contains moderator permissions',
		'_type' => 'option',
	)), array(
	)) . '

			' . $__templater->callMacro('addon_macros', 'addon_edit', array(
		'addOnId' => $__vars['interfaceGroup']['addon_id'],
	), $__vars) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'sticky' => 'true',
		'icon' => 'save',
	), array(
	)) . '
	</div>

', array(
		'action' => $__templater->func('link', array('permission-definitions/interface-groups/save', $__vars['interfaceGroup'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});