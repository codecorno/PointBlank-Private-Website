<?php
// FROM HASH: 477b8422e476bf78d0c165bb07a51d01
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['prefix'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add prefix');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit prefix' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['prefix']['title']));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['prefix'], 'isUpdate', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array($__vars['linkPrefix'] . '/delete', $__vars['prefix'], ), false),
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
		'name' => 'title',
		'value' => ($__vars['prefix']['prefix_id'] ? $__vars['prefix']['MasterTitle']['phrase_text'] : ''),
	), array(
		'label' => 'Title',
	)) . '

			' . $__templater->callMacro('base_prefix_edit_macros', 'display_style', array(
		'prefix' => $__vars['prefix'],
		'displayStyles' => $__vars['displayStyles'],
	), $__vars) . '

			<hr class="formRowSep" />

			' . $__templater->callMacro('base_prefix_edit_macros', 'prefix_groups', array(
		'prefix' => $__vars['prefix'],
		'prefixGroups' => $__vars['prefixGroups'],
	), $__vars) . '

			' . $__templater->callMacro('display_order_macros', 'row', array(
		'value' => $__vars['prefix']['display_order'],
	), $__vars) . '

			<hr class="formRowSep" />

			' . $__templater->callMacro('helper_user_group_edit', 'checkboxes', array(
		'selectedUserGroups' => ($__vars['prefix']['prefix_id'] ? $__vars['prefix']['allowed_user_group_ids'] : array(-1, )),
	), $__vars) . '

			' . $__templater->filter($__vars['extraOptions'], array(array('raw', array()),), true) . '
		</div>
		' . $__templater->formSubmitRow(array(
		'sticky' => 'true',
		'icon' => 'save',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array($__vars['linkPrefix'] . '/save', $__vars['prefix'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});