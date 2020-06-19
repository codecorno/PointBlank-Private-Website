<?php
// FROM HASH: 5429fdcf96730d3743d975e1e7b72f3e
return array('macros' => array(), 'code' => function($__templater, array $__vars)
{
	$__finalCompiled = '';
	if ($__templater->method($__vars['group'], 'isInsert', array())) {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Add property group');
		$__finalCompiled .= '
';
	} else {
		$__finalCompiled .= '
	';
		$__templater->pageParams['pageTitle'] = $__templater->preEscaped('Edit property group' . $__vars['xf']['language']['label_separator'] . ' ' . $__templater->escape($__vars['group']['master_title']));
		$__finalCompiled .= '
';
	}
	$__finalCompiled .= '

';
	if ($__templater->method($__vars['group'], 'isUpdate', array())) {
		$__templater->pageParams['pageAction'] = $__templater->preEscaped('
	' . $__templater->button('', array(
			'href' => $__templater->func('link', array('style-properties/groups/delete', $__vars['group'], ), false),
			'icon' => 'delete',
			'overlay' => 'true',
		), '', array(
		)) . '
');
	}
	$__finalCompiled .= '

';
	$__compilerTemp1 = '';
	if ($__vars['style']['style_id'] == 0) {
		$__compilerTemp1 .= '
				' . $__templater->callMacro('addon_macros', 'addon_edit', array(
			'addOnId' => $__vars['group']['addon_id'],
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
		'name' => 'group_name',
		'value' => $__vars['group']['group_name'],
		'maxlength' => $__templater->func('max_length', array($__vars['group'], 'group_name', ), false),
		'dir' => 'ltr',
	), array(
		'label' => 'Group name',
		'explain' => 'This is the unique identifier for this group.',
	)) . '

			' . $__templater->formTextBoxRow(array(
		'name' => 'title',
		'value' => $__vars['group']['master_title'],
		'maxlength' => $__templater->func('max_length', array($__vars['group'], 'title', ), false),
	), array(
		'label' => 'Title',
	)) . '

			' . $__templater->formTextAreaRow(array(
		'name' => 'description',
		'value' => $__vars['group']['master_description'],
		'maxlength' => $__templater->func('max_length', array($__vars['group'], 'description', ), false),
	), array(
		'label' => 'Description',
	)) . '

			' . $__templater->callMacro('display_order_macros', 'row', array(
		'value' => $__vars['group']['display_order'],
	), $__vars) . '

			' . $__compilerTemp1 . '
		</div>
		' . $__templater->formSubmitRow(array(
		'icon' => 'save',
		'sticky' => 'true',
	), array(
	)) . '
	</div>
', array(
		'action' => $__templater->func('link', array('style-properties/groups/save', $__vars['group'], ), false),
		'ajax' => 'true',
		'class' => 'block',
	));
	return $__finalCompiled;
});